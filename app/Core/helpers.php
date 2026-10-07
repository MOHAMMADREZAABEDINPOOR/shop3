<?php
/**
 * توابع کمکی عمومی (فارسی‌سازی اعداد، قیمت، تاریخ شمسی، امنیت، ...)
 */

use App\Core\Database;

function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }
    $parts = explode('.', $key);
    $val = $config;
    foreach ($parts as $p) {
        if (!is_array($val) || !array_key_exists($p, $val)) {
            return $default;
        }
        $val = $val[$p];
    }
    if ($key === 'app_name' && class_exists(\App\Core\I18n::class)) {
        return \App\Core\I18n::trans('site_title', 'NextShop');
    }
    if ($key === 'tagline' && class_exists(\App\Core\I18n::class)) {
        return \App\Core\I18n::trans('tagline', 'Premium Online Shopping Experience');
    }
    return $val;
}

/** escape خروجی HTML */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** تبدیل اعداد انگلیسی به فارسی */
function fa_num($value): string
{
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    return str_replace($en, $fa, (string)$value);
}

/** تبدیل اعداد فارسی/عربی به انگلیسی (برای ورودی‌های فرم) */
function en_num($value): string
{
    $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($fa, $en, (string)$value);
}

/** ترجمه با سیستم چندزبانه */
function __(string $key, ?string $default = null, array $replace = []): string
{
    return \App\Core\I18n::trans($key, $default, $replace);
}

/** تبدیل اعداد متناسب با زبان فعال (فارسی یا انگلیسی) */
function l_num($value): string
{
    return \App\Core\I18n::isRtl() ? fa_num($value) : en_num($value);
}

/** فرمت قیمت متناسب با زبان (دلار در انگلیسی، تومان در فارسی) */
function price($amount, bool $withUnit = true): string
{
    if (!\App\Core\I18n::isRtl()) {
        $rate = (float)config('usd_rate', 60000);
        $usd = $rate > 0 ? ((float)$amount / $rate) : (float)$amount;
        $formatted = '$' . number_format($usd, ($usd < 20 && floor($usd) != $usd) ? 2 : 0);
        return $formatted;
    }

    $formatted = fa_num(number_format((float)$amount, 0, '.', '،'));
    return $withUnit ? $formatted . ' ' . config('currency') : $formatted;
}

/** نام محصول بر اساس زبان جاری */
function product_name(array $product): string
{
    if (!\App\Core\I18n::isRtl()) {
        return !empty($product['name_en']) ? (string)$product['name_en'] : (string)($product['name'] ?? '');
    }
    return !empty($product['name_fa']) ? (string)$product['name_fa'] : (string)($product['name'] ?? '');
}

/** توضیح کوتاه محصول بر اساس زبان جاری */
function product_short(array $product): string
{
    if (!\App\Core\I18n::isRtl()) {
        return !empty($product['short_description_en']) ? (string)$product['short_description_en'] : (string)($product['short_description'] ?? '');
    }
    return !empty($product['short_description_fa']) ? (string)$product['short_description_fa'] : (string)($product['short_description'] ?? '');
}

/** توضیح کامل محصول بر اساس زبان جاری */
function product_desc(array $product): string
{
    if (!\App\Core\I18n::isRtl()) {
        return !empty($product['description_en']) ? (string)$product['description_en'] : (string)($product['description'] ?? '');
    }
    return !empty($product['description_fa']) ? (string)$product['description_fa'] : (string)($product['description'] ?? '');
}

/** مشخصات فنی محصول بر اساس زبان جاری */
function product_specs(array $product): array
{
    $field = !\App\Core\I18n::isRtl() ? 'specs_en' : 'specs_fa';
    if (!empty($product[$field])) {
        $decoded = is_string($product[$field]) ? json_decode($product[$field], true) : $product[$field];
        if (is_array($decoded) && !empty($decoded)) return $decoded;
    }
    if (!empty($product['specs'])) {
        $decoded = is_string($product['specs']) ? json_decode($product['specs'], true) : $product['specs'];
        if (is_array($decoded)) return $decoded;
    }
    return [];
}

/** نام دسته‌بندی بر اساس زبان جاری */
function category_name(array $category): string
{
    if (!\App\Core\I18n::isRtl()) {
        return !empty($category['name_en']) ? (string)$category['name_en'] : (string)($category['name'] ?? '');
    }
    return !empty($category['name_fa']) ? (string)$category['name_fa'] : (string)($category['name'] ?? '');
}

/** توضیح دسته‌بندی بر اساس زبان جاری */
function category_desc(array $category): string
{
    if (!\App\Core\I18n::isRtl()) {
        return !empty($category['description_en']) ? (string)$category['description_en'] : (string)($category['description'] ?? '');
    }
    return !empty($category['description_fa']) ? (string)$category['description_fa'] : (string)($category['description'] ?? '');
}

/** تاریخ متناسب با زبان (میلادی در انگلیسی، شمسی در فارسی) */
function localized_date(int $timestamp, string $format = 'M j, Y'): string
{
    if (!\App\Core\I18n::isRtl()) {
        return date($format, $timestamp);
    }
    return jdate($timestamp);
}

/** درصد تخفیف */
function discount_percent($price, $discount): int
{
    if (!$discount || $discount >= $price || $price <= 0) {
        return 0;
    }
    return (int)round((($price - $discount) / $price) * 100);
}

/** قیمت نهایی محصول */
function final_price(array $product)
{
    return ($product['discount_price'] && $product['discount_price'] < $product['price'])
        ? $product['discount_price']
        : $product['price'];
}

/** آدرس تصویر محصول */
function product_image(?string $image): string
{
    if (!$image) {
        return '/assets/img/placeholder.svg';
    }
    if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
        return $image;
    }
    return config('upload_url') . '/' . $image;
}

/** آدرس تصویر بنر / پوستر */
function banner_image(?string $image): string
{
    if (!$image) {
        return '/assets/img/placeholder.svg';
    }
    if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
        return $image;
    }
    $bannerFile = __DIR__ . '/../../public/uploads/banners/' . $image;
    if (file_exists($bannerFile)) {
        return '/uploads/banners/' . $image;
    }
    return '/uploads/products/' . $image;
}

// ---------------- تاریخ شمسی ----------------

function gregorian_to_jalali(int $gy, int $gm, int $gd): array
{
    $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    $jy = ($gy <= 1600) ? 0 : 979;
    $gy -= ($gy <= 1600) ? 621 : 1600;
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = (365 * $gy) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400) - 80 + $gd + $g_d_m[$gm - 1];
    $jy += 33 * intdiv($days, 12053);
    $days %= 12053;
    $jy += 4 * intdiv($days, 1461);
    $days %= 1461;
    $jy += intdiv($days - 1, 365);
    if ($days > 365) {
        $days = ($days - 1) % 365;
    }
    $jm = ($days < 186) ? 1 + intdiv($days, 31) : 7 + intdiv($days - 186, 30);
    $jd = 1 + (($days < 186) ? ($days % 31) : (($days - 186) % 30));
    return [$jy, $jm, $jd];
}

/** تاریخ شمسی خوانا. $format: 'full' | 'short' | 'datetime' | 'relative' */
function jdate($datetime, string $format = 'full'): string
{
    if (!$datetime) {
        return '—';
    }
    $ts = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
    if ($ts === false) {
        return '—';
    }
    $months = ['', 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
    $weekdays = ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'];
    [$jy, $jm, $jd] = gregorian_to_jalali((int)date('Y', $ts), (int)date('n', $ts), (int)date('j', $ts));

    switch ($format) {
        case 'short':
            return fa_num(sprintf('%04d/%02d/%02d', $jy, $jm, $jd));
        case 'datetime':
            return fa_num("{$jd} {$months[$jm]} {$jy} - " . date('H:i', $ts));
        case 'relative':
            $diff = time() - $ts;
            if ($diff < 60) return 'لحظاتی پیش';
            if ($diff < 3600) return fa_num(intdiv($diff, 60)) . ' دقیقه پیش';
            if ($diff < 86400) return fa_num(intdiv($diff, 3600)) . ' ساعت پیش';
            if ($diff < 86400 * 30) return fa_num(intdiv($diff, 86400)) . ' روز پیش';
            return fa_num("{$jd} {$months[$jm]} {$jy}");
        case 'weekday':
            return $weekdays[(int)date('w', $ts)] . ' ' . fa_num("{$jd} {$months[$jm]} {$jy}");
        default:
            return fa_num("{$jd} {$months[$jm]} {$jy}");
    }
}

// ---------------- Session / Flash / CSRF ----------------

function flash(string $key, ?string $message = null, string $type = 'success')
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = ['message' => $message, 'type' => $type];
        return null;
    }
    if (isset($_SESSION['_flash'][$key])) {
        $val = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
    return null;
}

function old(string $key, $default = '')
{
    return $_SESSION['_old'][$key] ?? $default;
}

function keep_old(array $data): void
{
    unset($data['password'], $data['password_confirm'], $data['_token']);
    $_SESSION['_old'] = $data;
}

function csrf_token(): string
{
    if (empty($_SESSION['_token'])) {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return is_string($token) && hash_equals(csrf_token(), $token);
}

function abort_csrf(): void
{
    if (!verify_csrf()) {
        if (is_ajax()) {
            json_response(['ok' => false, 'message' => 'توکن امنیتی نامعتبر است. صفحه را رفرش کنید.'], 419);
        }
        http_response_code(419);
        echo 'توکن امنیتی نامعتبر است.';
        exit;
    }
}

// ---------------- Request / Response ----------------

function is_ajax(): bool
{
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));
}

function json_response($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function redirect(string $url, int $code = 302): void
{
    header('Location: ' . $url, true, $code);
    exit;
}

function back(): void
{
    redirect($_SERVER['HTTP_REFERER'] ?? '/');
}

function input(string $key, $default = null)
{
    $val = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($val) ? trim($val) : $val;
}

function current_url(): string
{
    return $_SERVER['REQUEST_URI'] ?? '/';
}

function is_active(string $path): bool
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return $path === '/' ? $uri === '/' : str_starts_with($uri, $path);
}

/** ساخت آدرس با پارامترهای GET تغییر یافته */
function url_with(array $params): string
{
    $query = array_merge($_GET, $params);
    $query = array_filter($query, fn($v) => $v !== null && $v !== '');
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return $path . ($query ? '?' . http_build_query($query) : '');
}

/** اسلاگ‌سازی با پشتیبانی از فارسی */
function slugify(string $text): string
{
    $text = trim($text);
    $text = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', $text);
    $text = preg_replace('/[\s\-]+/u', '-', $text);
    $text = mb_strtolower($text, 'UTF-8');
    return trim($text, '-') ?: 'item';
}

/** وضعیت سفارش به فارسی */
function order_status(string $status): array
{
    $map = [
        'pending'    => ['label' => 'در انتظار پرداخت', 'color' => 'warning'],
        'paid'       => ['label' => 'پرداخت شده',        'color' => 'info'],
        'processing' => ['label' => 'در حال پردازش',     'color' => 'primary'],
        'shipped'    => ['label' => 'ارسال شده',          'color' => 'accent'],
        'delivered'  => ['label' => 'تحویل شده',          'color' => 'success'],
        'cancelled'  => ['label' => 'لغو شده',            'color' => 'danger'],
    ];
    $row = $map[$status] ?? ['label' => $status, 'color' => 'muted'];
    $row['icon'] = '';
    return $row;
}

function order_statuses(): array
{
    return ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];
}

/** رندر ستاره‌های امتیاز */
function stars(float $rating, int $max = 5): string
{
    $html = '<span class="stars" title="' . fa_num(number_format($rating, 1)) . '">';
    for ($i = 1; $i <= $max; $i++) {
        if ($rating >= $i) {
            $html .= '<i class="star full">★</i>';
        } elseif ($rating >= $i - 0.5) {
            $html .= '<i class="star half">★</i>';
        } else {
            $html .= '<i class="star">★</i>';
        }
    }
    return $html . '</span>';
}

/** خلاصه‌سازی متن */
function excerpt(?string $text, int $length = 90): string
{
    $text = trim(strip_tags((string)$text));
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $length)) . '…';
}

/** رندر قالب */
function view(string $name, array $data = [], ?string $layout = 'main'): void
{
    \App\Core\View::render($name, $data, $layout);
}

function partial(string $name, array $data = []): void
{
    \App\Core\View::partial($name, $data);
}

function auth(): ?array
{
    return \App\Core\Auth::user();
}

function is_admin(): bool
{
    return \App\Core\Auth::isAdmin();
}

function cart(): \App\Core\Cart
{
    return \App\Core\Cart::instance();
}

/** تابع تصادفی مناسب هر دیتابیس */
function sql_random(): string
{
    return Database::driver() === 'mysql' ? 'RAND()' : 'RANDOM()';
}

// ---------------- امنیت فرم‌ها ----------------

/** نام فیلد هانی‌پات (ربات‌ها پرش می‌کنند، انسان‌ها نه) */
function honeypot_name(): string
{
    return 'website_url';
}

/** رندر فیلد هانی‌پات نامرئی */
function honeypot_field(): string
{
    $name = honeypot_name();
    return '<div class="hp-field" aria-hidden="true" tabindex="-1">'
        . '<label>این فیلد را خالی بگذارید</label>'
        . '<input type="text" name="' . $name . '" value="" autocomplete="off">'
        . '</div>';
}

/** true یعنی احتمالاً ربات است */
function honeypot_filled(): bool
{
    $v = trim((string)($_POST[honeypot_name()] ?? ''));
    return $v !== '';
}

/** خطاهای اعتبارسنجی ذخیره‌شده برای نمایش کنار فیلد */
function field_error(string $field): ?string
{
    return $_SESSION['_errors'][$field] ?? null;
}

function set_field_errors(array $errors): void
{
    $_SESSION['_errors'] = $errors;
}

function clear_field_errors(): void
{
    unset($_SESSION['_errors']);
}

/** نمایش ماسک‌شده کد رهگیری پرداخت (رمزگشایی امن) */
function payment_ref_masked(?string $stored): string
{
    $ref = \App\Core\Encrypter::decrypt($stored);
    if ($ref === '') {
        return '—';
    }
    $len = mb_strlen($ref);
    if ($len <= 6) {
        return e($ref);
    }
    return e(mb_substr($ref, 0, 3) . '•••' . mb_substr($ref, -3));
}

/** آدرس canonical صفحه جاری */
function canonical_url(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return rtrim((string)config('app_url'), '/') . $path;
}
