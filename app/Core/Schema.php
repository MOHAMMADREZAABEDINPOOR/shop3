<?php
namespace App\Core;

use PDO;

/**
 * ساخت جدول‌های دیتابیس (سازگار با SQLite و MySQL و پشتیبانی دو زبانه)
 */
class Schema
{
    public static function create(PDO $pdo, string $driver): void
    {
        $pk   = $driver === 'mysql' ? 'INT AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
        $ts   = $driver === 'mysql' ? 'DATETIME DEFAULT CURRENT_TIMESTAMP' : 'TEXT DEFAULT CURRENT_TIMESTAMP';
        $text = 'TEXT';
        $eng  = $driver === 'mysql' ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : '';

        $tables = [

            "CREATE TABLE IF NOT EXISTS users (
                id {$pk},
                name VARCHAR(120) NOT NULL,
                email VARCHAR(190) NOT NULL UNIQUE,
                phone VARCHAR(20) DEFAULT NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) NOT NULL DEFAULT 'customer',
                address {$text} DEFAULT NULL,
                city VARCHAR(100) DEFAULT NULL,
                postal_code VARCHAR(20) DEFAULT NULL,
                created_at {$ts}
            ){$eng}",

            "CREATE TABLE IF NOT EXISTS categories (
                id {$pk},
                name VARCHAR(120) NOT NULL,
                name_en VARCHAR(120) DEFAULT NULL,
                name_fa VARCHAR(120) DEFAULT NULL,
                slug VARCHAR(150) NOT NULL UNIQUE,
                icon VARCHAR(30) DEFAULT 'box',
                color VARCHAR(20) DEFAULT '#6366f1',
                description {$text} DEFAULT NULL,
                description_en {$text} DEFAULT NULL,
                description_fa {$text} DEFAULT NULL,
                sort_order INT DEFAULT 0
            ){$eng}",

            "CREATE TABLE IF NOT EXISTS products (
                id {$pk},
                category_id INT NOT NULL,
                name VARCHAR(200) NOT NULL,
                name_en VARCHAR(200) DEFAULT NULL,
                name_fa VARCHAR(200) DEFAULT NULL,
                slug VARCHAR(220) NOT NULL UNIQUE,
                short_description {$text} DEFAULT NULL,
                short_description_en {$text} DEFAULT NULL,
                short_description_fa {$text} DEFAULT NULL,
                description {$text} DEFAULT NULL,
                description_en {$text} DEFAULT NULL,
                description_fa {$text} DEFAULT NULL,
                price BIGINT NOT NULL DEFAULT 0,
                discount_price BIGINT DEFAULT NULL,
                stock INT NOT NULL DEFAULT 0,
                image VARCHAR(255) DEFAULT NULL,
                gallery {$text} DEFAULT NULL,
                specs {$text} DEFAULT NULL,
                specs_en {$text} DEFAULT NULL,
                specs_fa {$text} DEFAULT NULL,
                brand VARCHAR(100) DEFAULT NULL,
                is_featured TINYINT DEFAULT 0,
                is_active TINYINT DEFAULT 1,
                views INT DEFAULT 0,
                sold INT DEFAULT 0,
                rating_avg DECIMAL(3,2) DEFAULT 0,
                rating_count INT DEFAULT 0,
                created_at {$ts},
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            ){$eng}",

            "CREATE TABLE IF NOT EXISTS coupons (
                id {$pk},
                code VARCHAR(50) NOT NULL UNIQUE,
                type VARCHAR(10) NOT NULL DEFAULT 'percent',
                value BIGINT NOT NULL,
                min_total BIGINT DEFAULT 0,
                max_uses INT DEFAULT NULL,
                used_count INT DEFAULT 0,
                expires_at {$text} DEFAULT NULL,
                is_active TINYINT DEFAULT 1
            ){$eng}",

            "CREATE TABLE IF NOT EXISTS orders (
                id {$pk},
                user_id INT NOT NULL,
                order_code VARCHAR(30) NOT NULL UNIQUE,
                status VARCHAR(30) NOT NULL DEFAULT 'pending',
                subtotal BIGINT NOT NULL DEFAULT 0,
                discount BIGINT NOT NULL DEFAULT 0,
                shipping BIGINT NOT NULL DEFAULT 0,
                total BIGINT NOT NULL DEFAULT 0,
                coupon_code VARCHAR(50) DEFAULT NULL,
                payment_method VARCHAR(30) DEFAULT 'online',
                payment_ref VARCHAR(60) DEFAULT NULL,
                paid_at {$text} DEFAULT NULL,
                receiver_name VARCHAR(120) NOT NULL,
                receiver_phone VARCHAR(20) NOT NULL,
                city VARCHAR(100) NOT NULL,
                address {$text} NOT NULL,
                postal_code VARCHAR(20) DEFAULT NULL,
                note {$text} DEFAULT NULL,
                created_at {$ts},
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ){$eng}",

            "CREATE TABLE IF NOT EXISTS order_items (
                id {$pk},
                order_id INT NOT NULL,
                product_id INT DEFAULT NULL,
                product_name VARCHAR(200) NOT NULL,
                product_image VARCHAR(255) DEFAULT NULL,
                price BIGINT NOT NULL,
                qty INT NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            ){$eng}",

            "CREATE TABLE IF NOT EXISTS reviews (
                id {$pk},
                product_id INT NOT NULL,
                user_id INT NOT NULL,
                rating INT NOT NULL DEFAULT 5,
                title VARCHAR(150) DEFAULT NULL,
                comment {$text} NOT NULL,
                comment_en {$text} DEFAULT NULL,
                comment_fa {$text} DEFAULT NULL,
                is_approved TINYINT DEFAULT 1,
                created_at {$ts},
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ){$eng}",

            "CREATE TABLE IF NOT EXISTS wishlists (
                id {$pk},
                user_id INT NOT NULL,
                product_id INT NOT NULL,
                created_at {$ts},
                UNIQUE (user_id, product_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            ){$eng}",

            "CREATE TABLE IF NOT EXISTS newsletter (
                id {$pk},
                email VARCHAR(190) NOT NULL UNIQUE,
                created_at {$ts}
            ){$eng}",

            "CREATE TABLE IF NOT EXISTS banners (
                id {$pk},
                title VARCHAR(200) NOT NULL,
                title_en VARCHAR(200) DEFAULT NULL,
                subtitle {$text} DEFAULT NULL,
                subtitle_en {$text} DEFAULT NULL,
                badge VARCHAR(100) DEFAULT NULL,
                badge_en VARCHAR(100) DEFAULT NULL,
                image VARCHAR(255) NOT NULL,
                link VARCHAR(255) DEFAULT '/shop',
                button_text VARCHAR(100) DEFAULT 'مشاهده و خرید',
                button_text_en VARCHAR(100) DEFAULT 'Shop Now',
                position VARCHAR(50) NOT NULL DEFAULT 'home_hero',
                color VARCHAR(50) DEFAULT 'gradient-purple',
                sort_order INT DEFAULT 0,
                is_active TINYINT DEFAULT 1,
                created_at {$ts}
            ){$eng}",
        ];

        foreach ($tables as $sql) {
            $pdo->exec($sql);
        }

        // افزودن ستون‌های دو زبانه در صورتی که دیتابیس از قبل وجود داشته باشد
        self::ensureBilingualColumns($pdo);
    }

    public static function ensureBilingualColumns(PDO $pdo): void
    {
        $cols = [
            'categories' => [
                'name_en' => 'VARCHAR(120) DEFAULT NULL',
                'name_fa' => 'VARCHAR(120) DEFAULT NULL',
                'description_en' => 'TEXT DEFAULT NULL',
                'description_fa' => 'TEXT DEFAULT NULL',
            ],
            'products' => [
                'name_en' => 'VARCHAR(200) DEFAULT NULL',
                'name_fa' => 'VARCHAR(200) DEFAULT NULL',
                'short_description_en' => 'TEXT DEFAULT NULL',
                'short_description_fa' => 'TEXT DEFAULT NULL',
                'description_en' => 'TEXT DEFAULT NULL',
                'description_fa' => 'TEXT DEFAULT NULL',
                'specs_en' => 'TEXT DEFAULT NULL',
                'specs_fa' => 'TEXT DEFAULT NULL',
            ],
            'reviews' => [
                'comment_en' => 'TEXT DEFAULT NULL',
                'comment_fa' => 'TEXT DEFAULT NULL',
            ],
        ];

        foreach ($cols as $table => $tableCols) {
            foreach ($tableCols as $colName => $colType) {
                try {
                    $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$colName} {$colType}");
                } catch (\Throwable $e) {
                    // Column already exists, safe to ignore
                }
            }
        }
    }
}
