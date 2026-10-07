<?php
namespace App\Core;

/**
 * محدودیت نرخ مبتنی بر فایل (مستقل از سشن — با عوض کردن سشن دور زده نمی‌شود).
 * مثال: RateLimiter::tooManyAttempts('login:1.2.3.4', 5, 600)
 */
class RateLimiter
{
    private static function dir(): string
    {
        $dir = __DIR__ . '/../../database/ratelimit';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir;
    }

    private static function file(string $key): string
    {
        return self::dir() . '/' . sha1($key) . '.json';
    }

    /** true یعنی هنوز اجازه دارد؛ وگرنه باید بلاک شود */
    public static function attempt(string $key, int $max, int $windowSeconds): bool
    {
        $file = self::file($key);
        $now = time();
        $state = ['count' => 0, 'reset' => $now + $windowSeconds];
        if (is_file($file)) {
            $raw = @json_decode((string)file_get_contents($file), true);
            if (is_array($raw) && isset($raw['count'], $raw['reset'])) {
                $state = $raw;
            }
        }
        if ($state['reset'] <= $now) {
            $state = ['count' => 0, 'reset' => $now + $windowSeconds];
        }
        if ($state['count'] >= $max) {
            return false;
        }
        $state['count']++;
        $fp = fopen($file, 'c');
        if ($fp) {
            flock($fp, LOCK_EX);
            ftruncate($fp, 0);
            fwrite($fp, json_encode($state));
            flock($fp, LOCK_UN);
            fclose($fp);
        }
        return true;
    }

    public static function remaining(string $key, int $max): int
    {
        $file = self::file($key);
        if (!is_file($file)) {
            return $max;
        }
        $raw = @json_decode((string)file_get_contents($file), true);
        if (!is_array($raw) || ($raw['reset'] ?? 0) <= time()) {
            return $max;
        }
        return max(0, $max - (int)($raw['count'] ?? 0));
    }

    public static function retryAfter(string $key): int
    {
        $file = self::file($key);
        if (!is_file($file)) {
            return 0;
        }
        $raw = @json_decode((string)file_get_contents($file), true);
        return max(0, (int)($raw['reset'] ?? time()) - time());
    }

    public static function clear(string $key): void
    {
        @unlink(self::file($key));
    }

    public static function clientIp(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'cli';
    }
}
