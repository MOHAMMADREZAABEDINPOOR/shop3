<?php
namespace App\Core;

/**
 * کپتچای ریاضی ساده سمت سرور (بدون سرویس خارجی) برای فرم‌های حساس.
 * استفاده: Captcha::question() در فرم + Captcha::check($input) هنگام ثبت.
 */
class Captcha
{
    private const SESSION_KEY = '_captcha';
    private const TTL = 600; // ۱۰ دقیقه

    /** ساخت سوال جدید و ذخیره جواب هش‌شده در سشن */
    public static function question(): string
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);
        $op = random_int(0, 1) === 0 ? '+' : '−';
        $answer = $op === '+' ? $a + $b : $a + abs($a - $b);
        if ($op === '−' && $b > $a) {
            [$a, $b] = [$b, $a];
            $answer = $a - $b;
        }
        $_SESSION[self::SESSION_KEY] = [
            'hash'    => hash_hmac('sha256', (string)$answer, csrf_token()),
            'expires' => time() + self::TTL,
        ];
        if (I18n::isRtl()) {
            $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            $f = fn($n) => str_replace(range(0, 9), $fa, (string)$n);
            return $f($a) . ' ' . $op . ' ' . $f($b) . ' = ؟';
        }
        return $a . ' ' . ($op === '−' ? '-' : '+') . ' ' . $b . ' = ?';
    }

    /** بررسی پاسخ کاربر (یک‌بارمصرف) */
    public static function check($input): bool
    {
        $stored = $_SESSION[self::SESSION_KEY] ?? null;
        unset($_SESSION[self::SESSION_KEY]);
        if (!is_array($stored) || ($stored['expires'] ?? 0) < time()) {
            return false;
        }
        $answer = trim((string)en_num((string)$input));
        if ($answer === '' || !ctype_digit($answer)) {
            return false;
        }
        return hash_equals((string)$stored['hash'], hash_hmac('sha256', $answer, csrf_token()));
    }
}
