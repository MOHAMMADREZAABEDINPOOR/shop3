<?php
/**
 * تنظیمات اصلی فروشگاه — همه مقادیر حساس از محیط (.env) خوانده می‌شوند.
 * هرگز رمز و کلید واقعی را در این فایل کامیت نکنید؛ از .env استفاده کنید.
 */
return [
    'app_name' => env('APP_NAME', 'نکست‌شاپ'),
    'tagline'  => env('APP_TAGLINE', 'خرید آنلاین با تجربه‌ای متفاوت'),
    'app_url'  => rtrim((string)env('APP_URL', 'http://localhost:8000'), '/'),
    'debug'    => env_bool('APP_DEBUG', true),

    // کلید رمزنگاری داده‌های حساس (۳۲ بایت، base64). با دستور زیر بسازید:
    // php -r "echo base64_encode(random_bytes(32)), PHP_EOL;"
    'app_key' => (string)env('APP_KEY', ''),

    // دیتابیس — پیش‌فرض SQLite. برای MySQL مقادیر را در .env بگذارید.
    'db' => [
        'driver'      => env('DB_DRIVER', 'sqlite'),
        'sqlite_path' => __DIR__ . '/../database/shop.sqlite',
        'mysql' => [
            'host'    => env('DB_HOST', '127.0.0.1'),
            'port'    => (int)env('DB_PORT', 3306),
            'name'    => env('DB_NAME', 'nextshop'),
            'user'    => env('DB_USER', 'root'),
            'pass'    => env('DB_PASS', ''),
            'charset' => 'utf8mb4',
        ],
    ],

    // ---------- نشست (سشن) ----------
    // مدت ماندگاری لاگین: پیش‌فرض ۱۲ ساعت؛ «مرا به خاطر بسپار» ۳۰ روز.
    'session' => [
        'name'          => 'nextshop_session',
        'lifetime'      => (int)env('SESSION_LIFETIME', 43200),
        'idle_timeout'  => (int)env('SESSION_IDLE_TIMEOUT', 2700),
        'remember_days' => (int)env('SESSION_REMEMBER_DAYS', 30),
    ],

    // ---------- امنیت ----------
    'security' => [
        'force_https'        => env_bool('FORCE_HTTPS', false),
        'login_max_attempts' => 5,
        'login_lock_minutes' => 10,
    ],

    // ---------- فروشگاه ----------
    'currency'                 => 'تومان',
    'usd_rate'                 => (int)env('USD_RATE', 60000),
    'shipping_cost'            => 45000,
    'free_shipping_threshold'  => 1500000,
    'tax_percent'              => 0,
    'per_page'                 => 12,

    // ---------- تماس (در فوتر و صفحه تماس استفاده می‌شود) ----------
    'contact' => [
        'phone'      => env('CONTACT_PHONE', '021-91000000'),
        'email'      => env('CONTACT_EMAIL', 'info@nextshop.ir'),
        'address'    => env('CONTACT_ADDRESS', 'تهران، خیابان ولیعصر، برج نکست، طبقه ۳'),
        'work_hours' => env('CONTACT_HOURS', 'شنبه تا پنجشنبه، ۹ تا ۲۱'),
    ],

    // ---------- بازاریابی ----------
    'marketing' => [
        'ga_id' => env('GA_ID', ''), // شناسه GA4، مثل G-XXXXXXXXXX (خالی = غیرفعال)
    ],

    // مسیر آپلود تصاویر محصولات
    'upload_dir' => __DIR__ . '/../public/uploads/products',
    'upload_url' => '/uploads/products',
];
