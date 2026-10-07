<?php
declare(strict_types=1);

namespace App\Core;

/**
 * MongoDB Data Layer & Document Store for NextShop
 * Supports native MongoDB extension (if loaded) and transparent high-performance
 * Document Store JSON collections in database/mongodb/ ensuring 100% persistent data.
 */
class MongoDatabase
{
    private static ?object $client = null;
    private static string $dbName = 'nextshop';
    private static string $storageDir = '';
    private static bool $isNative = false;

    public static function init(?array $config = null): void
    {
        self::$storageDir = __DIR__ . '/../../database/mongodb';
        if (!is_dir(self::$storageDir)) {
            mkdir(self::$storageDir, 0775, true);
        }

        self::$dbName = $config['db_name'] ?? 'nextshop';

        // Check if native MongoDB extension and class exist
        if (extension_loaded('mongodb') && class_exists('\MongoDB\Driver\Manager')) {
            try {
                $uri = $config['uri'] ?? 'mongodb://127.0.0.1:27017';
                self::$client = new \MongoDB\Driver\Manager($uri);
                self::$isNative = true;
            } catch (\Throwable $e) {
                self::$isNative = false;
            }
        }
    }

    public static function isNative(): bool
    {
        return self::$isNative;
    }

    /**
     * Get all documents from a collection
     */
    public static function collection(string $name): array
    {
        if (empty(self::$storageDir)) {
            self::init();
        }

        $file = self::$storageDir . '/' . $name . '.json';
        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Bulk save entire collection
     */
    public static function saveCollection(string $collection, array $documents): void
    {
        if (empty(self::$storageDir)) {
            self::init();
        }

        $file = self::$storageDir . '/' . $collection . '.json';
        file_put_contents($file, json_encode($documents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Insert a document into collection
     */
    public static function insert(string $collection, array $document): string
    {
        if (empty(self::$storageDir)) {
            self::init();
        }

        $docs = self::collection($collection);
        if (empty($document['_id'])) {
            $document['_id'] = isset($document['id']) ? (string)$document['id'] : (string)bin2hex(random_bytes(12));
        }
        $document['created_at'] = $document['created_at'] ?? date('c');

        $docs[] = $document;
        self::saveCollection($collection, $docs);

        // Native MongoDB insert if connected
        if (self::$isNative && self::$client) {
            try {
                $bulk = new \MongoDB\Driver\BulkWrite();
                $bulk->insert($document);
                self::$client->executeBulkWrite(self::$dbName . '.' . $collection, $bulk);
            } catch (\Throwable $e) {}
        }

        return (string)$document['_id'];
    }

    /**
     * Update documents matching filter in collection
     */
    public static function update(string $collection, array $filter, array $updateData): int
    {
        if (empty(self::$storageDir)) {
            self::init();
        }

        $docs = self::collection($collection);
        $count = 0;
        foreach ($docs as &$doc) {
            $match = true;
            foreach ($filter as $k => $v) {
                if (!isset($doc[$k]) || (string)$doc[$k] !== (string)$v) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                foreach ($updateData as $uk => $uv) {
                    $doc[$uk] = $uv;
                }
                $doc['updated_at'] = date('c');
                $count++;
            }
        }
        unset($doc);

        if ($count > 0) {
            self::saveCollection($collection, $docs);
        }

        if (self::$isNative && self::$client) {
            try {
                $bulk = new \MongoDB\Driver\BulkWrite();
                $bulk->update($filter, ['$set' => $updateData], ['multi' => true]);
                self::$client->executeBulkWrite(self::$dbName . '.' . $collection, $bulk);
            } catch (\Throwable $e) {}
        }

        return $count;
    }

    /**
     * Delete documents matching filter in collection
     */
    public static function delete(string $collection, array $filter): int
    {
        if (empty(self::$storageDir)) {
            self::init();
        }

        $docs = self::collection($collection);
        $initialCount = count($docs);
        $docs = array_values(array_filter($docs, function ($doc) use ($filter) {
            foreach ($filter as $k => $v) {
                if (isset($doc[$k]) && (string)$doc[$k] === (string)$v) {
                    return false; // Remove
                }
            }
            return true;
        }));

        $deleted = $initialCount - count($docs);
        if ($deleted > 0) {
            self::saveCollection($collection, $docs);
        }

        if (self::$isNative && self::$client) {
            try {
                $bulk = new \MongoDB\Driver\BulkWrite();
                $bulk->delete($filter);
                self::$client->executeBulkWrite(self::$dbName . '.' . $collection, $bulk);
            } catch (\Throwable $e) {}
        }

        return $deleted;
    }

    /**
     * Find documents matching criteria
     */
    public static function find(string $collection, array $filter = [], array $options = []): array
    {
        $docs = self::collection($collection);
        if (empty($filter)) {
            $result = $docs;
        } else {
            $result = array_filter($docs, function ($doc) use ($filter) {
                foreach ($filter as $k => $v) {
                    if (!isset($doc[$k]) || (string)$doc[$k] !== (string)$v) {
                        return false;
                    }
                }
                return true;
            });
        }

        // Sorting
        if (!empty($options['sort'])) {
            $sortKey = key($options['sort']);
            $sortDir = current($options['sort']);
            usort($result, function ($a, $b) use ($sortKey, $sortDir) {
                $va = $a[$sortKey] ?? null;
                $vb = $b[$sortKey] ?? null;
                if ($va == $vb) return 0;
                return ($sortDir > 0) ? ($va > $vb ? 1 : -1) : ($va < $vb ? 1 : -1);
            });
        }

        // Limit
        if (!empty($options['limit'])) {
            $result = array_slice($result, $options['offset'] ?? 0, (int)$options['limit']);
        }

        return array_values($result);
    }

    /**
     * Find single document
     */
    public static function findOne(string $collection, array $filter): ?array
    {
        $matches = self::find($collection, $filter, ['limit' => 1]);
        return $matches[0] ?? null;
    }

    /**
     * Generate an executable MongoDB setup / migration JavaScript file (setup_mongodb.js)
     * Compatible with mongosh and MongoDB Compass
     */
    public static function generateMongoDbJsScript(): void
    {
        if (empty(self::$storageDir)) {
            self::init();
        }

        $collections = ['categories', 'products', 'reviews', 'coupons'];
        $js = "// NextShop MongoDB Initialization & Seeding Script\n";
        $js .= "// Run with: mongosh " . self::$dbName . " setup_mongodb.js\n\n";
        $js .= "db = db.getSiblingDB('" . self::$dbName . "');\n\n";

        foreach ($collections as $col) {
            $docs = self::collection($col);
            $js .= "// --- Collection: {$col} (" . count($docs) . " documents) ---\n";
            $js .= "db.{$col}.drop();\n";
            if (!empty($docs)) {
                $json = json_encode($docs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $js .= "db.{$col}.insertMany({$json});\n\n";
            }
        }

        $js .= "// --- Indexes ---\n";
        $js .= "db.products.createIndex({ slug: 1 }, { unique: true });\n";
        $js .= "db.products.createIndex({ category_id: 1 });\n";
        $js .= "db.categories.createIndex({ slug: 1 }, { unique: true });\n";
        $js .= "db.reviews.createIndex({ product_id: 1 });\n";
        $js .= "print('NextShop MongoDB database populated successfully with " . count(self::collection('products')) . " products!');\n";

        file_put_contents(self::$storageDir . '/setup_mongodb.js', $js);
    }
}
