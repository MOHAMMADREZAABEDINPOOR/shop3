<?php
/**
 * لودر ساده فایل .env — کلیدها و اسرار فقط از محیط/فایل خوانده می‌شوند
 * و هرگز نباید در گیت کامیت شوند (.env در .gitignore است).
 */

if (!function_exists('loadEnv')) {
    function loadEnv(string $dir): void
    {
        $file = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($file) || !is_readable($file)) {
            return;
        }
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }
            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));
            if (strlen($val) >= 2 && (($val[0] === '"' && $val[-1] === '"') || ($val[0] === "'" && $val[-1] === "'"))) {
                $val = substr($val, 1, -1);
            }
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                $_SERVER[$key] = $val;
                $_ENV[$key] = $val;
            }
        }
    }
}

if (!function_exists('env')) {
    /** خواندن متغیر محیطی با مقدار پیش‌فرض */
    function env(string $key, $default = null)
    {
        $val = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key);
        if ($val === false || $val === null || $val === '') {
            return $default;
        }
        return match (strtolower((string)$val)) {
            'true', '(true)'   => true,
            'false', '(false)' => false,
            'null', '(null)'   => null,
            'empty', '(empty)' => '',
            default            => $val,
        };
    }
}

if (!function_exists('env_bool')) {
    /** تبدیل مقدار محیطی به بولین */
    function env_bool(string $key, bool $default = false): bool
    {
        $val = env($key, null);
        if ($val === null) {
            return $default;
        }
        return in_array(mb_strtolower(trim((string)$val)), ['1', 'true', 'yes', 'on'], true);
    }
}
