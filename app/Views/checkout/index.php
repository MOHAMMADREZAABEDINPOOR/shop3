<?php
use App\Core\I18n;

$isEn = !I18n::isRtl();
?>
<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [
        ['label' => __('home', 'خانه'), 'url' => '/'],
        ['label' => __('cart', 'سبد خرید'), 'url' => '/cart'],
        ['label' => __('checkout', 'تسویه حساب')]
    ]]); ?>
    <h1 class="page-title"><?= icon('credit-card', 28) ?> <?= __('checkout', 'تکمیل سفارش') ?></h1>

    <div class="steps" aria-label="<?= __('checkout', 'مراحل خرید') ?>">
        <div class="step done"><span class="step-n"><?= icon('check', 14) ?></span> <?= __('cart', 'سبد خرید') ?></div>
        <div class="step-line"></div>
        <div class="step current"><span class="step-n"><?= l_num(2) ?></span> <?= $isEn ? 'Shipping & Payment' : 'اطلاعات و پرداخت' ?></div>
        <div class="step-line"></div>
        <div class="step"><span class="step-n"><?= l_num(3) ?></span> <?= $isEn ? 'Confirmation' : 'ثبت نهایی' ?></div>
    </div>

    <form method="post" action="/checkout" class="checkout-layout" novalidate>
        <?= csrf_field() ?>
        <div class="checkout-form">
            <div class="card form-card">
                <h3><?= icon('map-pin', 20) ?> <?= __('shipping_address', 'آدرس تحویل') ?></h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="fName"><?= __('full_name', 'نام و نام خانوادگی گیرنده') ?> *</label>
                        <input type="text" name="receiver_name" id="fName" required value="<?= e(old('receiver_name', $user['name'])) ?>" placeholder="<?= $isEn ? 'e.g. John Doe' : 'مثلاً: سارا محمدی' ?>" maxlength="120" <?= field_error('receiver_name') ? 'aria-invalid="true"' : '' ?>>
                        <?php if ($e = field_error('receiver_name')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="fPhone"><?= __('phone_number', 'شماره موبایل') ?> *</label>
                        <input type="tel" name="receiver_phone" id="fPhone" required value="<?= e(old('receiver_phone', $user['phone'] ?? '')) ?>" placeholder="09123456789" dir="ltr" maxlength="15" <?= field_error('receiver_phone') ? 'aria-invalid="true"' : '' ?>>
                        <?php if ($e = field_error('receiver_phone')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="fCity"><?= __('city', 'شهر') ?> *</label>
                        <input type="text" name="city" id="fCity" required value="<?= e(old('city', $user['city'] ?? '')) ?>" placeholder="<?= $isEn ? 'e.g. New York / Tehran' : 'مثلاً: تهران' ?>" maxlength="100" <?= field_error('city') ? 'aria-invalid="true"' : '' ?>>
                        <?php if ($e = field_error('city')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="fPostal"><?= __('postal_code', 'کد پستی') ?> (<?= $isEn ? 'Optional' : 'اختیاری' ?>)</label>
                        <input type="text" name="postal_code" id="fPostal" value="<?= e(old('postal_code', $user['postal_code'] ?? '')) ?>" placeholder="<?= $isEn ? 'Postal Code' : '۱۰ رقم' ?>" dir="ltr" maxlength="10" <?= field_error('postal_code') ? 'aria-invalid="true"' : '' ?>>
                        <?php if ($e = field_error('postal_code')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fAddr"><?= $isEn ? 'Full Street Address *' : 'آدرس کامل *' ?></label>
                    <textarea name="address" id="fAddr" rows="3" required placeholder="<?= $isEn ? 'Street, building, apartment...' : 'خیابان، کوچه، پلاک، واحد...' ?>" maxlength="500" <?= field_error('address') ? 'aria-invalid="true"' : '' ?>><?= e(old('address', $user['address'] ?? '')) ?></textarea>
                    <?php if ($e = field_error('address')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="fNote"><?= __('order_notes', 'یادداشت سفارش (اختیاری)') ?></label>
                    <textarea name="note" id="fNote" rows="2" placeholder="<?= $isEn ? 'Special instructions for delivery...' : 'توضیحات برای پیک یا فروشگاه...' ?>" maxlength="500"><?= e(old('note')) ?></textarea>
                </div>
            </div>

            <div class="card form-card">
                <h3><?= icon('wallet', 20) ?> <?= __('payment_method', 'روش پرداخت') ?></h3>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="online" checked>
                    <span class="payment-card">
                        <?= icon('credit-card', 24) ?>
                        <span><strong><?= __('pay_online', 'پرداخت آنلاین') ?></strong><small><?= $isEn ? 'Secure instant payment' : 'درگاه امن بانکی — همه کارت‌های شتاب' ?></small></span>
                    </span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cod" <?= old('payment_method') === 'cod' ? 'checked' : '' ?>>
                    <span class="payment-card">
                        <?= icon('package', 24) ?>
                        <span><strong><?= $isEn ? 'Cash on Delivery (COD)' : 'پرداخت در محل' ?></strong><small><?= $isEn ? 'Pay upon receiving your package' : 'هنگام تحویل سفارش پرداخت کنید' ?></small></span>
                    </span>
                </label>
                <label class="terms-check" style="margin-top:12px">
                    <input type="checkbox" name="terms" value="1">
                    <span><?= $isEn ? 'I have read and agree to the <a href="/terms" target="_blank">Terms of Service</a>. *' : '<a href="/terms" target="_blank">قوانین و مقررات</a> فروشگاه را خوانده‌ام و می‌پذیرم. *' ?></span>
                </label>
                <?php if ($e = field_error('terms')): ?><p class="form-error-msg"><?= icon('alert', 14) ?> <?= e($e) ?></p><?php endif; ?>
            </div>
        </div>

        <aside class="cart-summary card">
            <h3><?= __('order_summary', 'سفارش شما') ?></h3>
            <div class="checkout-items">
                <?php foreach ($items as $item):
                    $p = $item['product'];
                    $pName = product_name($p);
                ?>
                    <div class="checkout-item">
                        <img src="<?= e(product_image($p['image'])) ?>" alt="<?= e($pName) ?>" loading="lazy">
                        <div>
                            <span class="checkout-item-name"><?= e($pName) ?></span>
                            <span class="muted small"><?= l_num($item['qty']) ?> × <?= price($item['unit']) ?></span>
                        </div>
                        <strong><?= price($item['subtotal']) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-rows">
                <div class="summary-row"><span><?= __('subtotal', 'قیمت کالاها') ?></span><span><?= price($cart->subtotal()) ?></span></div>
                <?php if ($cart->couponDiscount()): ?>
                    <div class="summary-row text-danger"><span><?= __('discount', 'تخفیف') ?> (<?= e($cart->coupon()['code']) ?>)</span><span>-<?= price($cart->couponDiscount()) ?></span></div>
                <?php endif; ?>
                <div class="summary-row"><span><?= __('shipping', 'هزینه ارسال') ?></span><span><?= $cart->shipping() === 0 ? __('free', 'رایگان') : price($cart->shipping()) ?></span></div>
                <div class="summary-row total"><span><?= __('total', 'مبلغ قابل پرداخت') ?></span><strong><?= price($cart->total()) ?></strong></div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block"><?= icon('check-circle', 20) ?> <?= __('place_order', 'ثبت نهایی سفارش') ?></button>
            <p class="secure-note"><?= icon('lock', 14) ?> <?= __('secure_payment', 'اطلاعات شما با اتصال امن محافظت می‌شود') ?></p>
        </aside>
    </form>
</div>
