<?php
use App\Core\I18n;

$isEn = !I18n::isRtl();
?>
<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [
        ['label' => __('home', 'خانه'), 'url' => '/'],
        ['label' => __('cart', 'سبد خرید')]
    ]]); ?>
    <h1 class="page-title"><?= icon('cart', 28) ?> <?= __('cart', 'سبد خرید') ?></h1>

    <?php if (!$items): ?>
        <div class="empty-state card">
            <span class="empty-icon empty-svg"><?= icon('cart', 56) ?></span>
            <h3><?= __('cart_empty', 'سبد خرید شما خالی است') ?></h3>
            <p><?= $isEn ? 'You have no items in your cart. Explore our store and discover great deals.' : 'هنوز کالایی به سبد اضافه نکرده‌اید. از میان هزاران کالای فروشگاه انتخاب کنید.' ?></p>
            <a href="/shop" class="btn btn-primary btn-lg"><?= icon('grid', 18) ?> <?= __('start_shopping', 'مشاهده محصولات') ?></a>
        </div>
    <?php else: ?>
    <div class="cart-layout">
        <div class="cart-items" id="cartItems">
            <?php foreach ($items as $item):
                $p = $item['product'];
                $pName = product_name($p);
            ?>
            <div class="cart-item card" data-product="<?= (int)$p['id'] ?>">
                <a href="/product/<?= e($p['slug']) ?>" class="cart-item-img">
                    <img src="<?= e(product_image($p['image'])) ?>" alt="<?= e($pName) ?>">
                </a>
                <div class="cart-item-info">
                    <a href="/product/<?= e($p['slug']) ?>" class="cart-item-name"><?= e($pName) ?></a>
                    <?php if ($p['brand']): ?><span class="muted small"><?= e($p['brand']) ?></span><?php endif; ?>
                    <div class="cart-item-price">
                        <?php if ($p['discount_price'] && $p['discount_price'] < $p['price']): ?>
                            <del><?= price($p['price']) ?></del>
                        <?php endif; ?>
                        <strong><?= price($item['unit']) ?></strong>
                    </div>
                </div>
                <div class="cart-item-actions">
                    <div class="qty-stepper sm">
                        <button type="button" class="qty-btn cart-qty" data-step="1"><?= icon('plus', 14) ?></button>
                        <input type="text" class="cart-qty-input" value="<?= l_num($item['qty']) ?>" inputmode="numeric" data-max="<?= (int)$p['stock'] ?>">
                        <button type="button" class="qty-btn cart-qty" data-step="-1"><?= icon('minus', 14) ?></button>
                    </div>
                    <div class="cart-item-subtotal"><?= price($item['subtotal']) ?></div>
                    <button class="icon-btn cart-remove" title="<?= __('remove', 'حذف') ?>"><?= icon('trash', 18) ?></button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <aside class="cart-summary card">
            <h3><?= __('order_summary', 'خلاصه سفارش') ?></h3>

            <?php $coupon = $cart->coupon(); ?>
            <div class="coupon-box">
                <?php if ($coupon): ?>
                    <div class="coupon-applied">
                        <span><?= icon('ticket', 16) ?> <b><?= e($coupon['code']) ?></b> <?= $isEn ? 'applied' : 'اعمال شد' ?></span>
                        <button class="icon-btn" id="removeCoupon" title="<?= __('remove', 'حذف') ?>"><?= icon('x', 16) ?></button>
                    </div>
                <?php else: ?>
                    <div class="coupon-form">
                        <input type="text" id="couponInput" placeholder="<?= __('coupon_code', 'کد تخفیف') ?>" dir="ltr">
                        <button class="btn btn-outline btn-sm" id="applyCoupon"><?= __('apply_coupon', 'اعمال') ?></button>
                    </div>
                    <small class="muted"><?= $isEn ? 'Example: ' : 'مثال: ' ?><b>WELCOME10</b> <?= $isEn ? 'or' : 'یا' ?> <b>OFF200</b></small>
                <?php endif; ?>
            </div>

            <div class="summary-rows">
                <div class="summary-row">
                    <span><?= __('subtotal', 'قیمت کالاها') ?> (<?= l_num($cart->count()) ?>)</span>
                    <span id="sumSubtotal"><?= price($cart->subtotal()) ?></span>
                </div>
                <?php if ($cart->productSavings() > 0): ?>
                    <div class="summary-row text-success">
                        <span><?= __('your_savings', 'سود شما از تخفیف') ?></span>
                        <span><?= price($cart->productSavings()) ?></span>
                    </div>
                <?php endif; ?>
                <div class="summary-row text-danger" id="rowCoupon" <?= $cart->couponDiscount() ? '' : 'hidden' ?>>
                    <span><?= __('discount', 'تخفیف کد') ?></span>
                    <span id="sumDiscount">-<?= price($cart->couponDiscount()) ?></span>
                </div>
                <div class="summary-row">
                    <span><?= __('shipping', 'هزینه ارسال') ?></span>
                    <span id="sumShipping"><?= $cart->shipping() === 0 ? __('free', 'رایگان') : price($cart->shipping()) ?></span>
                </div>
                <?php if ($cart->shipping() > 0): ?>
                    <div class="free-shipping-hint">
                        <div class="progress"><i style="width:<?= min(100, round($cart->subtotal() / config('free_shipping_threshold') * 100)) ?>%"></i></div>
                        <small><?= $isEn ? ('Add ' . price(config('free_shipping_threshold') - $cart->subtotal()) . ' more for FREE shipping!') : (price(config('free_shipping_threshold') - $cart->subtotal()) . ' تا ارسال رایگان مونده!') ?></small>
                    </div>
                <?php endif; ?>
                <div class="summary-row total">
                    <span><?= __('total', 'مبلغ قابل پرداخت') ?></span>
                    <strong id="sumTotal"><?= price($cart->total()) ?></strong>
                </div>
            </div>

            <a href="/checkout" class="btn btn-primary btn-lg btn-block"><?= icon('credit-card', 20) ?> <?= __('checkout', 'ادامه جهت تسویه‌حساب') ?></a>
            <div class="summary-badges">
                <span><?= icon('shield', 14) ?> <?= __('guarantee_original', 'ضمانت اصالت') ?></span>
                <span><?= icon('refresh', 14) ?> <?= __('return_policy', '۷ روز بازگشت') ?></span>
            </div>
        </aside>
    </div>
    <?php endif; ?>
</div>
