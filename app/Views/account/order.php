<?php $st = order_status($order['status']); ?>
<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [['label' => 'حساب کاربری', 'url' => '/account'], ['label' => 'سفارش‌های من', 'url' => '/account/orders'], ['label' => $order['order_code']]]]); ?>

    <div class="order-detail-head">
        <div>
            <h1 class="page-title">سفارش <b dir="ltr"><?= e($order['order_code']) ?></b></h1>
            <span class="muted"><?= jdate($order['created_at'], 'datetime') ?></span>
        </div>
        <span class="status-badge st-<?= $st['color'] ?> lg"><?= $st['icon'] ?> <?= $st['label'] ?></span>
    </div>

    <?php if ($order['status'] !== 'cancelled'): ?>
    <div class="card">
        <div class="order-status-track">
            <?php
            $steps = ['paid' => 'پرداخت', 'processing' => 'پردازش', 'shipped' => 'ارسال', 'delivered' => 'تحویل'];
            $orderIdx = ['pending' => 0, 'paid' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
            $current = $orderIdx[$order['status']] ?? 0;
            $i = 1;
            foreach ($steps as $key => $label): $done = $current >= $i; ?>
                <div class="track-step <?= $done ? 'done' : '' ?> <?= $current === $i ? 'current' : '' ?>">
                    <span class="track-dot"><?= $done ? icon('check', 14) : fa_num($i) ?></span>
                    <span><?= e($label) ?></span>
                </div>
            <?php $i++; endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="order-detail-layout">
        <div class="card">
            <h3><?= icon('package', 20) ?> اقلام سفارش</h3>
            <div class="track-items">
                <?php foreach ($items as $it): ?>
                    <div class="checkout-item">
                        <img src="<?= e(product_image($it['product_image'])) ?>" alt="<?= e($it['product_name']) ?>" loading="lazy">
                        <div>
                            <?php if (!empty($it['slug'])): ?>
                                <a href="/product/<?= e($it['slug']) ?>" class="checkout-item-name"><?= e($it['product_name']) ?></a>
                            <?php else: ?>
                                <span class="checkout-item-name"><?= e($it['product_name']) ?></span>
                            <?php endif; ?>
                            <span class="muted small"><?= fa_num($it['qty']) ?> × <?= price($it['price']) ?></span>
                        </div>
                        <strong><?= price($it['price'] * $it['qty']) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-rows">
                <div class="summary-row"><span>قیمت کالاها</span><span><?= price($order['subtotal']) ?></span></div>
                <?php if ($order['discount'] > 0): ?>
                    <div class="summary-row text-danger"><span>تخفیف<?= $order['coupon_code'] ? ' (' . e($order['coupon_code']) . ')' : '' ?></span><span>-<?= price($order['discount']) ?></span></div>
                <?php endif; ?>
                <div class="summary-row"><span>هزینه ارسال</span><span><?= $order['shipping'] == 0 ? 'رایگان' : price($order['shipping']) ?></span></div>
                <div class="summary-row total"><span>مبلغ نهایی</span><strong><?= price($order['total']) ?></strong></div>
            </div>
        </div>

        <div class="card">
            <h3><?= icon('map-pin', 20) ?> اطلاعات تحویل</h3>
            <div class="info-list">
                <div><span>گیرنده</span><b><?= e($order['receiver_name']) ?></b></div>
                <div><span>موبایل</span><b dir="ltr"><?= fa_num($order['receiver_phone']) ?></b></div>
                <div><span>شهر</span><b><?= e($order['city']) ?></b></div>
                <div><span>آدرس</span><b><?= e($order['address']) ?></b></div>
                <?php if ($order['postal_code']): ?><div><span>کد پستی</span><b dir="ltr"><?= fa_num($order['postal_code']) ?></b></div><?php endif; ?>
                <div><span>روش پرداخت</span><b><?= $order['payment_method'] === 'cod' ? 'پرداخت در محل' : 'پرداخت آنلاین' ?></b></div>
                <?php if ($order['payment_ref']): ?><div><span>کد رهگیری</span><b dir="ltr"><?= payment_ref_masked($order['payment_ref']) ?></b></div><?php endif; ?>
                <?php if ($order['note']): ?><div><span>یادداشت</span><b><?= e($order['note']) ?></b></div><?php endif; ?>
            </div>
            <?php if ($order['status'] === 'pending'): ?>
                <a href="/payment/<?= e($order['order_code']) ?>" class="btn btn-primary btn-block"><?= icon('credit-card', 18) ?> پرداخت سفارش</a>
            <?php endif; ?>
        </div>
    </div>
</div>
