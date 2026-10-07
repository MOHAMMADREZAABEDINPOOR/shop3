<?php
namespace App\Core;

/**
 * مدیریت احراز هویت کاربران
 */
class Auth
{
    private static ?array $user = null;
    private static bool $loaded = false;

    public static function user(): ?array
    {
        if (!self::$loaded) {
            self::$loaded = true;
            if (!empty($_SESSION['user_id'])) {
                self::$user = Database::fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
                if (!self::$user) {
                    unset($_SESSION['user_id']);
                }
            }
        }
        return self::$user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? '') === 'admin';
    }

    public static function attempt(string $email, string $password): bool
    {
        $user = Database::fetch("SELECT * FROM users WHERE email = ?", [mb_strtolower(trim($email))]);
        if ($user && password_verify($password, $user['password'])) {
            self::login($user);
            return true;
        }
        return false;
    }

    public static function login(array $user, bool $remember = false): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login_at'] = time();
        $_SESSION['last_activity'] = time();
        if ($remember) {
            $days = (int)config('session.remember_days', 30);
            $_SESSION['remember_until'] = time() + $days * 86400;
        } else {
            unset($_SESSION['remember_until']);
        }
        self::$user = $user;
        self::$loaded = true;
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
        self::$user = null;
        session_regenerate_id(true);
    }

    public static function refresh(): void
    {
        self::$loaded = false;
        self::user();
    }
}
