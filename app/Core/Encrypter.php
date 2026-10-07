<?php
namespace App\Core;

/**
 * رمزنگاری داده‌های حساس در دیتابیس (AES-256-CBC + HMAC).
 * کلید فقط از APP_KEY خوانده می‌شود؛ بدون کلید، openssl موجود نباشد
 * یا خطا رخ دهد، مقدار با پیشوند plain ذخیره می‌شود تا چیزی گم نشود.
 */
class Encrypter
{
    private static function key(): ?string
    {
        $raw = (string)config('app_key', '');
        if ($raw === '') {
            return null;
        }
        $key = base64_decode($raw, true);
        if ($key === false || strlen($key) !== 32) {
            return null;
        }
        return $key;
    }

    public static function hasKey(): bool
    {
        return self::key() !== null && function_exists('openssl_encrypt');
    }

    public static function encrypt(string $plain): string
    {
        $key = self::key();
        if ($key === null || !function_exists('openssl_encrypt')) {
            return 'plain:' . $plain;
        }
        $iv = random_bytes(16);
        $cipher = openssl_encrypt($plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($cipher === false) {
            return 'plain:' . $plain;
        }
        $payload = $iv . $cipher;
        $mac = hash_hmac('sha256', $payload, $key, true);
        return 'enc:' . base64_encode($mac . $payload);
    }

    public static function decrypt(?string $stored): string
    {
        if ($stored === null || $stored === '') {
            return '';
        }
        if (str_starts_with($stored, 'plain:')) {
            return substr($stored, 6);
        }
        if (!str_starts_with($stored, 'enc:')) {
            return $stored; // داده قدیمی رمزنگاری‌نشده
        }
        $key = self::key();
        if ($key === null || !function_exists('openssl_decrypt')) {
            return '';
        }
        $raw = base64_decode(substr($stored, 4), true);
        if ($raw === false || strlen($raw) < 48) {
            return '';
        }
        $mac = substr($raw, 0, 32);
        $payload = substr($raw, 32);
        if (!hash_equals($mac, hash_hmac('sha256', $payload, $key, true))) {
            return '';
        }
        $iv = substr($payload, 0, 16);
        $cipher = substr($payload, 16);
        $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $plain === false ? '' : $plain;
    }
}
