<?php
namespace App\Controllers;

use App\Core\Database as DB;
use App\Core\Auth;
use App\Core\RateLimiter;
use App\Core\Captcha;

class AuthController
{
    public function loginForm(): void
    {
        $isEn = !\App\Core\I18n::isRtl();
        view('auth/login', [
            'title' => $isEn ? 'Sign In' : 'ورود به حساب کاربری',
            'meta_description' => $isEn 
                ? 'Sign in to your account at ' . __('site_title', 'NextShop') . ' to manage your orders, wishlist, and profile.'
                : 'وارد حساب کاربری خود در ' . __('site_title', 'نکست‌شاپ') . ' شوید و سفارش‌ها، علاقه‌مندی‌ها و خریدهای خود را مدیریت کنید.',
            'captcha' => Captcha::question(),
        ], 'auth');
    }

    public function login(): void
    {
        abort_csrf();
        $isEn = !\App\Core\I18n::isRtl();
        $ip = RateLimiter::clientIp();
        $email    = mb_strtolower(trim((string)input('email', '')));
        $password = (string)input('password', '');
        $remember = input('remember') === '1';
        $errors = [];

        if (honeypot_filled()) {
            flash('error', $isEn ? 'Your request was flagged as suspicious. Please try again later.' : 'درخواست شما مشکوک تشخیص داده شد. چند دقیقه بعد تلاش کنید.', 'error');
            redirect('/login');
        }

        $key = 'login:' . $ip;
        if (!RateLimiter::attempt($key, (int)config('security.login_max_attempts', 5), (int)config('security.login_lock_minutes', 10) * 60)) {
            $mins = max(1, (int)ceil(RateLimiter::retryAfter($key) / 60));
            flash('error', $isEn ? "Too many attempts. Please try again in about {$mins} minutes." : 'تلاش‌های ناموفق زیاد است. لطفاً حدود ' . fa_num($mins) . ' دقیقه دیگر تلاش کنید.', 'error');
            redirect('/login');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
            $errors['email'] = $isEn ? 'Please enter a valid email address.' : 'ایمیل معتبر وارد کنید.';
        }
        if ($password === '' || strlen($password) > 255) {
            $errors['password'] = $isEn ? 'Please enter your password.' : 'رمز عبور را وارد کنید.';
        }
        if (!Captcha::check(input('captcha', ''))) {
            $errors['captcha'] = $isEn ? 'Incorrect security question answer. Please try again.' : 'پاسخ سوال امنیتی درست نیست. دوباره تلاش کنید.';
        }

        if (!$errors && Auth::attempt($email, $password)) {
            RateLimiter::clear($key);
            Auth::login(Auth::user(), $remember);
            flash('success', $isEn ? ('Welcome back, ' . e(Auth::user()['name'])) : ('خوش آمدید ' . e(Auth::user()['name'])));
            $intended = $_SESSION['_intended'] ?? null;
            unset($_SESSION['_intended']);
            redirect($intended ?: (Auth::isAdmin() ? '/admin' : '/account'));
        }

        if (!$errors) {
            $errors['email'] = $isEn ? 'Invalid email or password.' : 'ایمیل یا رمز عبور اشتباه است.';
        }
        keep_old($_POST);
        set_field_errors($errors);
        flash('error', implode('<br>', array_map('e', $errors)), 'error');
        redirect('/login');
    }

    public function registerForm(): void
    {
        $isEn = !\App\Core\I18n::isRtl();
        view('auth/register', [
            'title' => $isEn ? 'Create Account' : 'ایجاد حساب کاربری',
            'meta_description' => $isEn
                ? 'Join ' . __('site_title', 'NextShop') . ' for fast checkout, order tracking, and exclusive discounts.'
                : 'در ' . __('site_title', 'نکست‌شاپ') . ' ثبت‌نام کنید: خرید سریع‌تر، پیگیری سفارش و پیشنهادهای اختصاصی.',
            'captcha' => Captcha::question(),
        ], 'auth');
    }

    public function register(): void
    {
        abort_csrf();
        $isEn = !\App\Core\I18n::isRtl();
        $ip = RateLimiter::clientIp();
        if (honeypot_filled()) {
            flash('error', $isEn ? 'Your request was flagged as suspicious. Please try again later.' : 'درخواست شما مشکوک تشخیص داده شد. چند دقیقه بعد تلاش کنید.', 'error');
            redirect('/register');
        }
        if (!RateLimiter::attempt('register:' . $ip, 10, 3600)) {
            flash('error', $isEn ? 'Too many registration attempts. Please try again in an hour.' : 'تعداد ثبت‌نام از این دستگاه زیاد است. یک ساعت دیگر تلاش کنید.', 'error');
            redirect('/register');
        }

        $name     = trim((string)input('name', ''));
        $email    = mb_strtolower(trim((string)input('email', '')));
        $phone    = en_num(trim((string)input('phone', '')));
        $password = (string)input('password', '');
        $confirm  = (string)input('password_confirm', '');

        $errors = [];
        if (mb_strlen($name) < 3 || mb_strlen($name) > 120) $errors['name'] = $isEn ? 'Full name must be between 3 and 120 characters.' : 'نام و نام خانوادگی باید بین ۳ تا ۱۲۰ حرف باشد.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) $errors['email'] = $isEn ? 'Please enter a valid email address.' : 'ایمیل معتبر نیست.';
        if ($phone !== '' && !preg_match('/^09\d{9}$/', $phone)) $errors['phone'] = $isEn ? 'Please enter a valid phone number.' : 'شماره موبایل معتبر نیست.';
        if (strlen($password) < 8 || strlen($password) > 255) $errors['password'] = $isEn ? 'Password must be at least 8 characters.' : 'رمز عبور باید حداقل ۸ کاراکتر باشد.';
        if ($password !== $confirm) $errors['password_confirm'] = $isEn ? 'Passwords do not match.' : 'تکرار رمز عبور مطابقت ندارد.';
        if (input('terms') !== '1') $errors['terms'] = $isEn ? 'You must agree to the Terms of Service and Privacy Policy.' : 'برای ثبت‌نام باید قوانین و حریم خصوصی را بپذیرید.';
        if (!Captcha::check(input('captcha', ''))) $errors['captcha'] = $isEn ? 'Incorrect security question answer.' : 'پاسخ سوال امنیتی درست نیست.';
        if (!$errors && DB::fetch("SELECT id FROM users WHERE email = ?", [$email])) {
            $errors['email'] = $isEn ? 'This email is already registered.' : 'این ایمیل قبلاً ثبت شده است.';
        }

        if ($errors) {
            keep_old($_POST);
            set_field_errors($errors);
            flash('error', implode('<br>', array_map('e', $errors)), 'error');
            redirect('/register');
        }

        $id = DB::insert('users', [
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone ?: null,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'customer',
        ]);
        $user = DB::fetch("SELECT * FROM users WHERE id = ?", [$id]);
        Auth::login($user);
        flash('success', $isEn ? 'Your account has been created successfully. Welcome!' : 'حساب کاربری شما با موفقیت ایجاد شد. خوش آمدید!');
        redirect('/account');
    }

    public function logout(): void
    {
        abort_csrf();
        $isEn = !\App\Core\I18n::isRtl();
        Auth::logout();
        flash('success', $isEn ? 'You have successfully logged out.' : 'با موفقیت از حساب خود خارج شدید.');
        redirect('/');
    }
}
