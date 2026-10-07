<?php
namespace App\Core;

/**
 * میان‌افزارها: احراز هویت، دسترسی ادمین، مهمان
 */
class Middleware
{
    public static function handle(string $name): void
    {
        switch ($name) {
            case 'auth':
                if (!Auth::check()) {
                    if (is_ajax()) {
                        json_response(['ok' => false, 'message' => 'برای این عملیات باید وارد حساب کاربری شوید.', 'redirect' => '/login'], 401);
                    }
                    $_SESSION['_intended'] = current_url();
                    flash('warning', 'برای ادامه ابتدا وارد حساب کاربری خود شوید.', 'warning');
                    redirect('/login');
                }
                break;

            case 'admin':
                if (!Auth::check()) {
                    $_SESSION['_intended'] = current_url();
                    redirect('/login');
                }
                if (!Auth::isAdmin()) {
                    http_response_code(403);
                    view('errors/403', ['title' => 'دسترسی غیرمجاز']);
                    exit;
                }
                break;

            case 'guest':
                if (Auth::check()) {
                    redirect('/account');
                }
                break;
        }
    }
}
