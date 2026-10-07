<?php $st = order_status($order['status']); ?>
<div class="container page">
    <div class="success-page card reveal in">
        <div class="success-icon success-check"><?= icon('check-circle', 64) ?></div>
        <span class="eyebrow">سفارش ثبت شد</span>
        <h1>ممنون از خرید شما!</h1>
        <p class="muted">سفارش شما با موفقیت ثبت شد و جزئیات آن برایتان پیامک و ایمیل می‌شود.</p>

        <div class="success-code">
            <span>کد پیگیری سفارش:</span>
            <b dir="ltr"><?= e($order['order_code']) ?></b>
            <button class="btn btn-outline btn-sm" id="copyOrderCode" data-code="<?= e($order['order_code']) ?>">کپی</button>
        </div>

        <div class="order-status-track">
            <?php
            $steps = ['paid' => 'پرداخت', 'processing' => 'پردازش', 'shipped' => 'ارسال', 'delivered' => 'تحویل'];
            $orderIdx = ['pending' => 0, 'paid' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
            $current = $order['status'] === 'cancelled' ? -1 : ($orderIdx[$order['status']] ?? 0);
            $i = 1;
            foreach ($steps as $key => $label):
                $done = $current >= $i;
            ?>
                <div class="track-step <?= $done ? 'done' : '' ?> <?= $current === $i ? 'current' : '' ?>">
                    <span class="track-dot"><?= $done ? icon('check', 14) : fa_num($i) ?></span>
                    <span><?= e($label) ?></span>
                </div>
            <?php $i++; endforeach; ?>
        </div>

        <div class="success-details">
            <div class="summary-row"><span>وضعیت</span><span class="status-badge st-<?= $st['color'] ?>"><?= e($st['label']) ?></span></div>
            <div class="summary-row"><span>تعداد اقلام</span><span><?= fa_num(array_sum(array_column($items, 'qty'))) ?> قلم</span></div>
            <div class="summary-row"><span>روش پرداخت</span><span><?= $order['payment_method'] === 'cod' ? 'پرداخت در محل' : 'پرداخت آنلاین' ?></span></div>
            <?php if ($order['payment_ref']): ?><div class="summary-row"><span>کد رهگیری پرداخت</span><b dir="ltr"><?= payment_ref_masked($order['payment_ref']) ?></b></div><?php endif; ?>
            <div class="summary-row total"><span>مبلغ</span><strong><?= price($order['total']) ?></strong></div>
        </div>

        <div class="success-actions">
            <a href="/account/orders/<?= e($order['order_code']) ?>" class="btn btn-primary"><?= icon('package', 18) ?> پیگیری این سفارش</a>
            <a href="/shop" class="btn btn-outline">ادامه خرید</a>
        </div>
    </div>
</div>
