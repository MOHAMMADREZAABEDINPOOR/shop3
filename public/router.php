<?php
/**
 * روتر برای وب‌سرور داخلی PHP (php -S)
 * فایل‌های استاتیک را مستقیم سرو می‌کند و بقیه را به index.php می‌فرستد.
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// مسدود کردن فایل‌های مخفی و حساس حتی اگر در داک‌روت باشند
if (preg_match('#/\.(?!well-known)#', $path) || preg_match('#\.(env|sqlite|sqlite-[a-z]+|log|bak)$#i', $path)) {
    http_response_code(403);
    exit('Forbidden');
}
$file = __DIR__ . $path;
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false; // فایل استاتیک — خود PHP سروش می‌کند
}
require __DIR__ . '/index.php';
