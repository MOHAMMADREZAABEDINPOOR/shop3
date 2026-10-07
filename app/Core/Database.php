<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * لایه اتصال به دیتابیس (PDO) — پشتیبانی از SQLite و MySQL
 */
class Database
{
    private static ?PDO $pdo = null;
    private static string $driver = 'sqlite';

    public static function connect(array $config): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        self::$driver = $config['driver'] ?? 'sqlite';
        $freshSqlite = false;

        try {
            if (self::$driver === 'mysql') {
                $m   = $config['mysql'];
                $dsn = "mysql:host={$m['host']};port={$m['port']};dbname={$m['name']};charset={$m['charset']}";
                self::$pdo = new PDO($dsn, $m['user'], $m['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } else {
                $path = $config['sqlite_path'];
                $dir  = dirname($path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
                $freshSqlite = !file_exists($path);
                self::$pdo = new PDO('sqlite:' . $path, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                self::$pdo->exec('PRAGMA foreign_keys = ON');
                self::$pdo->exec('PRAGMA journal_mode = WAL');
            }
        } catch (PDOException $e) {
            http_response_code(500);
            die('<div style="font-family:tahoma;direction:rtl;padding:40px;text-align:center">خطا در اتصال به دیتابیس: ' . htmlspecialchars($e->getMessage()) . '</div>');
        }

        // ساخت خودکار جدول‌ها و داده‌های نمونه در اولین اجرا
        if (!self::tablesExist()) {
            Schema::create(self::$pdo, self::$driver);
            Seeder::run(self::$pdo);
        }

        return self::$pdo;
    }

    public static function pdo(): PDO
    {
        return self::$pdo;
    }

    public static function driver(): string
    {
        return self::$driver;
    }

    private static function tablesExist(): bool
    {
        try {
            if (self::$driver === 'mysql') {
                $stmt = self::$pdo->query("SHOW TABLES LIKE 'products'");
            } else {
                $stmt = self::$pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='products'");
            }
            return (bool)$stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ---------- توابع کمکی برای کوئری ----------

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function value(string $sql, array $params = [])
    {
        return self::query($sql, $params)->fetchColumn();
    }

    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $sql  = "INSERT INTO {$table} (" . implode(',', $cols) . ") VALUES (" . implode(',', array_map(fn($c) => ':' . $c, $cols)) . ")";
        self::query($sql, $data);
        $id = (int)self::$pdo->lastInsertId();

        // Sync directly to MongoDB collection
        try {
            $doc = $data;
            $doc['id'] = $id;
            $doc['_id'] = (string)$id;
            MongoDatabase::insert($table, $doc);
        } catch (\Throwable $e) {}

        return $id;
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(', ', array_map(fn($c) => "{$c} = :{$c}", array_keys($data)));
        $stmt = self::query("UPDATE {$table} SET {$set} WHERE {$where}", array_merge($data, $whereParams));

        // Sync directly to MongoDB collection
        try {
            if (isset($whereParams['id'])) {
                MongoDatabase::update($table, ['id' => (int)$whereParams['id']], $data);
            }
        } catch (\Throwable $e) {}

        return $stmt->rowCount();
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        $stmt = self::query("DELETE FROM {$table} WHERE {$where}", $params);

        // Sync directly to MongoDB collection
        try {
            if (isset($params[0]) && is_numeric($params[0])) {
                MongoDatabase::delete($table, ['id' => (int)$params[0]]);
            } elseif (isset($params['id'])) {
                MongoDatabase::delete($table, ['id' => (int)$params['id']]);
            }
        } catch (\Throwable $e) {}

        return $stmt->rowCount();
    }
}

