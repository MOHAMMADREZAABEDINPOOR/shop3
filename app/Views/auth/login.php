<?php
use App\Core\I18n;
$isEn = !I18n::isRtl();
?>
<div class="auth-head">
    <div class="error-art" style="width:84px;height:84px;border-radius:26px;"><?= icon('user', 38) ?></div>
    <h2 class="auth-title"><?= $isEn ? 'Welcome Back' : 'خوش برگشتید' ?></h2>
    <p class="auth-sub"><?= $isEn ? 'Sign in to your account to continue shopping' : 'وارد حساب کاربری خود شوید تا خرید را ادامه دهید' ?></p>
</div>

<form method="post" action="/login" class="auth-form" novalidate>
    <?= csrf_field() ?>
    <?= honeypot_field() ?>
    <div class="form-group">
        <label for="loginEmail"><?= __('email', 'ایمیل') ?></label>
        <input type="email" name="email" id="loginEmail" required value="<?= e(old('email')) ?>" placeholder="you@example.com" dir="ltr" autofocus maxlength="190" <?= field_error('email') ? 'aria-invalid="true"' : '' ?>>
        <?php if ($e = field_error('email')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
    </div>
    <div class="form-group">
        <label for="loginPass"><?= __('password', 'رمز عبور') ?></label>
        <div class="password-field">
            <input type="password" name="password" id="loginPass" required placeholder="<?= __('password', 'رمز عبور') ?>" dir="ltr" maxlength="255" <?= field_error('password') ? 'aria-invalid="true"' : '' ?>>
            <button type="button" class="icon-btn toggle-pass" data-target="loginPass" aria-label="Show password"><?= icon('eye', 18) ?></button>
        </div>
        <?php if ($e = field_error('password')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
    </div>
    <div class="form-group">
        <label for="loginCaptcha"><?= __('security_question', $isEn ? 'Security Question' : 'سوال امنیتی') ?></label>
        <div class="captcha-box">
            <span class="captcha-q" dir="ltr"><?= e($captcha ?? '') ?></span>
            <input type="text" name="captcha" id="loginCaptcha" required inputmode="numeric" placeholder="<?= $isEn ? 'Answer' : 'پاسخ' ?>" autocomplete="off" maxlength="10" <?= field_error('captcha') ? 'aria-invalid="true"' : '' ?>>
        </div>
        <?php if ($e = field_error('captcha')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
    </div>
    <label class="remember-check"><input type="checkbox" name="remember" value="1"> <?= __('remember_me', 'مرا به خاطر بسپار') ?></label>
    <button type="submit" class="btn btn-primary btn-lg btn-block"><?= icon('user', 18) ?> <?= __('login', 'ورود به حساب') ?></button>
</form>

<div class="auth-demo card">
    <strong><?= $isEn ? 'Demo Accounts:' : 'حساب‌های نمایشی:' ?></strong>
    <div class="demo-accounts">
        <button type="button" class="demo-fill" data-email="admin@nextshop.ir" data-pass="admin123"><strong><?= $isEn ? 'Admin' : 'مدیر' ?>:</strong> admin@nextshop.ir / admin123</button>
        <button type="button" class="demo-fill" data-email="sara@example.com" data-pass="123456"><strong><?= $isEn ? 'Customer' : 'کاربر' ?>:</strong> sara@example.com / 123456</button>
    </div>
</div>

<p class="auth-switch"><?= __('dont_have_account', 'حساب کاربری ندارید؟') ?> <a href="/register"><?= __('register', 'ثبت‌نام کنید') ?></a></p>
