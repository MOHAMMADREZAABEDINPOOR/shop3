<?php
use App\Core\I18n;
$isEn = !I18n::isRtl();
?>
<div class="auth-head">
    <div class="error-art" style="width:84px;height:84px;border-radius:26px;"><?= icon('check-circle', 38) ?></div>
    <h2 class="auth-title"><?= __('register', 'ساخت حساب کاربری') ?></h2>
    <p class="auth-sub"><?= $isEn ? 'Takes less than a minute — fast, free and secure' : 'کمتر از یک دقیقه طول می‌کشد — رایگان و بدون تعهد' ?></p>
</div>

<form method="post" action="/register" class="auth-form" novalidate>
    <?= csrf_field() ?>
    <?= honeypot_field() ?>
    <div class="form-group">
        <label for="regName"><?= __('full_name', 'نام و نام خانوادگی') ?> *</label>
        <input type="text" name="name" id="regName" required value="<?= e(old('name')) ?>" placeholder="<?= $isEn ? 'e.g. John Smith' : 'مثلاً: رضا احمدی' ?>" autofocus maxlength="120" <?= field_error('name') ? 'aria-invalid="true"' : '' ?>>
        <?php if ($e = field_error('name')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
    </div>
    <div class="form-group">
        <label for="regEmail"><?= __('email', 'ایمیل') ?> *</label>
        <input type="email" name="email" id="regEmail" required value="<?= e(old('email')) ?>" placeholder="you@example.com" dir="ltr" maxlength="190" <?= field_error('email') ? 'aria-invalid="true"' : '' ?>>
        <?php if ($e = field_error('email')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
    </div>
    <div class="form-group">
        <label for="regPhone"><?= __('phone_number', 'شماره موبایل') ?> (<?= $isEn ? 'Optional' : 'اختیاری' ?>)</label>
        <input type="tel" name="phone" id="regPhone" value="<?= e(old('phone')) ?>" placeholder="09123456789" dir="ltr" maxlength="15" <?= field_error('phone') ? 'aria-invalid="true"' : '' ?>>
        <?php if ($e = field_error('phone')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="regPass"><?= __('password', 'رمز عبور') ?> *</label>
            <div class="password-field">
                <input type="password" name="password" id="regPass" required minlength="8" placeholder="<?= $isEn ? 'Min 8 characters' : 'حداقل ۸ کاراکتر' ?>" dir="ltr" maxlength="255" <?= field_error('password') ? 'aria-invalid="true"' : '' ?>>
                <button type="button" class="icon-btn toggle-pass" data-target="regPass" aria-label="Show password"><?= icon('eye', 18) ?></button>
            </div>
            <?php if ($e = field_error('password')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
        </div>
        <div class="form-group">
            <label for="regPass2"><?= __('confirm_password', 'تکرار رمز عبور') ?> *</label>
            <input type="password" name="password_confirm" id="regPass2" required minlength="8" placeholder="<?= __('confirm_password', 'تکرار رمز') ?>" dir="ltr" maxlength="255" <?= field_error('password_confirm') ? 'aria-invalid="true"' : '' ?>>
            <?php if ($e = field_error('password_confirm')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="regCaptcha"><?= __('security_question', $isEn ? 'Security Question' : 'سوال امنیتی') ?></label>
        <div class="captcha-box">
            <span class="captcha-q" dir="ltr"><?= e($captcha ?? '') ?></span>
            <input type="text" name="captcha" id="regCaptcha" required inputmode="numeric" placeholder="<?= $isEn ? 'Answer' : 'پاسخ' ?>" autocomplete="off" maxlength="10" <?= field_error('captcha') ? 'aria-invalid="true"' : '' ?>>
        </div>
        <?php if ($e = field_error('captcha')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
    </div>
    <label class="terms-check">
        <input type="checkbox" name="terms" value="1" <?= old('terms') ? 'checked' : '' ?>>
        <span><?= $isEn ? 'I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a>. *' : '<a href="/terms" target="_blank">قوانین و مقررات</a> و <a href="/privacy" target="_blank">حریم خصوصی</a> را خوانده‌ام و می‌پذیرم. *' ?></span>
    </label>
    <?php if ($e = field_error('terms')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
    <button type="submit" class="btn btn-primary btn-lg btn-block"><?= icon('check-circle', 18) ?> <?= __('register', 'ایجاد حساب کاربری') ?></button>
</form>

<p class="auth-switch"><?= __('already_have_account', 'قبلاً ثبت‌نام کرده‌اید؟') ?> <a href="/login"><?= __('login', 'وارد شوید') ?></a></p>
