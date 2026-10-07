<?php
namespace App\Core;

/**
 * رندر قالب‌ها با پشتیبانی از layout
 */
class View
{
    private static string $basePath = __DIR__ . '/../Views/';

    public static function render(string $name, array $data = [], ?string $layout = 'main'): void
    {
        $content = self::capture($name, $data);
        if ($layout === null) {
            echo $content;
            return;
        }
        $data['content'] = $content;
        echo self::capture('layouts/' . $layout, $data);
    }

    public static function partial(string $name, array $data = []): void
    {
        echo self::capture($name, $data);
    }

    public static function capture(string $name, array $data = []): string
    {
        $file = self::$basePath . str_replace('.', '/', $name) . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("قالب پیدا نشد: {$name}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            include $file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return ob_get_clean();
    }
}
