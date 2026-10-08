<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Internationalization (i18n) Engine for NextShop
 * Supports English ('en') as default, Persian ('fa'), with session/cookie persistence
 * and direction handling (LTR for en, RTL for fa).
 */
class I18n
{
    private static string $locale = 'en';
    private static array $translations = [];
    private static bool $booted = false;

    /**
     * Supported locales
     */
    public const SUPPORTED = ['en', 'fa'];

    /**
     * Initialize localization from request / session / cookie
     */
    public static function boot(): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        $chosen = null;

        // 1. Query parameter ?lang=xx
        if (!empty($_GET['lang']) && in_array(strtolower(trim((string)$_GET['lang'])), self::SUPPORTED, true)) {
            $chosen = strtolower(trim((string)$_GET['lang']));
        }
        // 2. Session
        elseif (!empty($_SESSION['ns_lang']) && in_array($_SESSION['ns_lang'], self::SUPPORTED, true)) {
            $chosen = $_SESSION['ns_lang'];
        }
        // 3. Cookie
        elseif (!empty($_COOKIE['ns_lang']) && in_array($_COOKIE['ns_lang'], self::SUPPORTED, true)) {
            $chosen = $_COOKIE['ns_lang'];
        }

        // Default locale from env (default 'fa'), fallback to 'en'
        $defaultLocale = strtolower((string)env('APP_LOCALE', 'fa'));
        self::$locale = $chosen ?? (in_array($defaultLocale, self::SUPPORTED, true) ? $defaultLocale : 'fa');

        // Persist in session and cookie
        $_SESSION['ns_lang'] = self::$locale;
        if (!headers_sent()) {
            setcookie('ns_lang', self::$locale, [
                'expires'  => time() + 365 * 86400,
                'path'     => '/',
                'httponly' => false,
                'samesite' => 'Lax',
            ]);
        }

        self::loadDictionary(self::$locale);
    }

    /**
     * Set active locale
     */
    public static function setLocale(string $locale): void
    {
        $locale = strtolower(trim($locale));
        if (in_array($locale, self::SUPPORTED, true)) {
            self::$locale = $locale;
            $_SESSION['ns_lang'] = $locale;
            if (!headers_sent()) {
                setcookie('ns_lang', $locale, [
                    'expires'  => time() + 365 * 86400,
                    'path'     => '/',
                    'httponly' => false,
                    'samesite' => 'Lax',
                ]);
            }
            self::loadDictionary($locale);
        }
    }

    /**
     * Current locale code ('en' or 'fa')
     */
    public static function locale(): string
    {
        return self::$locale;
    }

    /**
     * Is the active locale Right-to-Left?
     */
    public static function isRtl(): bool
    {
        return self::$locale === 'fa';
    }

    /**
     * HTML dir attribute ('ltr' or 'rtl')
     */
    public static function dir(): string
    {
        return self::isRtl() ? 'rtl' : 'ltr';
    }

    /**
     * Load language dictionary file
     */
    private static function loadDictionary(string $locale): void
    {
        $file = __DIR__ . '/../lang/' . $locale . '.php';
        if (file_exists($file)) {
            self::$translations[$locale] = require $file;
        } else {
            self::$translations[$locale] = [];
        }
    }

    /**
     * Translate a key with optional replacement parameters
     */
    public static function trans(string $key, ?string $default = null, array $replace = []): string
    {
        $loc = self::$locale;
        if (!isset(self::$translations[$loc])) {
            self::loadDictionary($loc);
        }

        $text = self::$translations[$loc][$key] ?? null;

        // Fallback to English dictionary if not found in current locale
        if ($text === null && $loc !== 'en') {
            if (!isset(self::$translations['en'])) {
                self::loadDictionary('en');
            }
            $text = self::$translations['en'][$key] ?? null;
        }

        if ($text === null) {
            $text = $default ?? $key;
        }

        if (!empty($replace)) {
            foreach ($replace as $placeholder => $value) {
                $text = str_replace(':' . $placeholder, (string)$value, $text);
            }
        }

        return $text;
    }
}
