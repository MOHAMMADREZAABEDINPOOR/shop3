<?php
/**
 * راه‌اندازی اولیه برنامه: env، سشن امن، دیتابیس، هدرهای امنیتی
 */
declare(strict_types=1);

mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Tehran');

require_once __DIR__ . '/Core/Env.php';
loadEnv(__DIR__ . '/..');

$config = require __DIR__ . '/config.php';

if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Autoloader ساده بر اساس namespace App\
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

require_once __DIR__ . '/Core/helpers.php';
require_once __DIR__ . '/Core/icons.php';


// Class aliases for global access
if (!class_exists('I18n', false)) {
    class_alias(\App\Core\I18n::class, 'I18n');
}
if (!class_exists('DB', false)) {
    class_alias(\App\Core\Database::class, 'DB');
}
if (!class_exists('MongoDatabase', false)) {
    class_alias(\App\Core\MongoDatabase::class, 'MongoDatabase');
}

\App\Core\MongoDatabase::init($config['mongodb'] ?? null);


// ---------- HTTPS اجباری (فقط وقتی فعال و روی هاست واقعی) ----------
$isCli = PHP_SAPI === 'cli';
$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = $host === '' || str_starts_with($host, 'localhost') || str_starts_with($host, '127.') || str_ends_with($host, '.local');
if (!$isCli && ($config['security']['force_https'] ?? false) && !$isHttps && !$isLocal) {
    header('Location: https://' . $host . ($_SERVER['REQUEST_URI'] ?? '/'), true, 301);
    exit;
}

// ---------- هدرهای امنیتی ----------
if (!$isCli && !headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    if ($isHttps) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// ---------- سشن امن ----------
$lifetime = (int)$config['session']['lifetime'];
ini_set('session.gc_maxlifetime', (string)max($lifetime, (int)$config['session']['remember_days'] * 86400));
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',
    'httponly' => true,
    'secure'   => $isHttps,
    'samesite' => 'Lax',
]);
session_name($config['session']['name'] ?? 'nextshop_session');
session_start();

// راه‌اندازی موتور چندزبانه و دیتابیس مونگو
\App\Core\I18n::boot();
\App\Core\MongoDatabase::init();

// «مرا به خاطر بسپار»: تمدید کوکی نشست تا N روز آینده
$remember = !empty($_SESSION['remember_until']) && $_SESSION['remember_until'] > time();
if ($remember && !$isCli && !headers_sent()) {
    setcookie(session_name(), session_id(), [
        'expires'  => time() + (int)$config['session']['remember_days'] * 86400,
        'path'     => '/',
        'httponly' => true,
        'secure'   => $isHttps,
        'samesite' => 'Lax',
    ]);
}

// انقضای بیکاری و سقف مطلق نشست (به‌جز «مرا به خاطر بسپار»)
$idleTimeout = (int)$config['session']['idle_timeout'];
$now = time();
if (isset($_SESSION['user_id'])) {
    $lastActive = (int)($_SESSION['last_activity'] ?? $now);
    $created = (int)($_SESSION['login_at'] ?? $now);
    $expired = (!$remember && $idleTimeout > 0 && ($now - $lastActive) > $idleTimeout)
        || (!$remember && ($now - $created) > $lifetime);
    if ($expired) {
        unset($_SESSION['user_id']);
        \App\Core\Auth::refresh();
        flash('warning', 'نشست شما به دلیل عدم فعالیت منقضی شد. لطفاً دوباره وارد شوید.', 'warning');
    }
}
if (!$remember && isset($_SESSION['remember_until'])) {
    unset($_SESSION['remember_until']);
}
$_SESSION['last_activity'] = $now;

// راه‌اندازی ماژول چندزبانه و زبان پیش‌فرض (انگلیسی)
\App\Core\I18n::boot();

// اتصال دیتابیس (در اولین اجرا جدول‌ها و داده‌های نمونه ساخته می‌شوند)

\App\Core\Database::connect($config['db']);

// پاک‌کردن مقادیر old و خطاهای فرم پس از یک درخواست GET
if (isset($_SESSION['_old']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $GLOBALS['_old_to_clear'] = true;
}
if (isset($_SESSION['_errors']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $GLOBALS['_errors_to_clear'] = true;
}
register_shutdown_function(function () {
    if (!empty($GLOBALS['_old_to_clear'])) {
        unset($_SESSION['_old']);
    }
    if (!empty($GLOBALS['_errors_to_clear'])) {
        unset($_SESSION['_errors']);
    }
});
