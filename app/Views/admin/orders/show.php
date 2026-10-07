<?php $st = order_status($order['status']); ?>
<div class="order-detail-head">
    <div>
        <h2>سفارش <b dir="ltr"><?= e($order['order_code']) ?></b></h2>
        <span class="muted"><?= jdate($order['created_at'], 'datetime') ?></span>
    </div>
    <a href="/admin/orders" class="btn btn-ghost btn-sm"><?= icon('arrow-right', 16) ?> بازگشت به لیست</a>
</div>

<div class="order-detail-layout">
    <div>
        <div class="card">
            <div class="card-head">
                <h3><?= icon('package', 20) ?> اقلام سفارش</h3>
                <span class="status-badge st-<?= $st['color'] ?> lg"><?= $st['icon'] ?> <?= $st['label'] ?></span>
            </div>
            <div class="track-items">
                <?php foreach ($items as $it): ?>
                    <div class="checkout-item">
                        <img src="<?= e(product_image($it['product_image'])) ?>" alt="<?= e($it['product_name']) ?>" loading="lazy">
                        <div>
                            <?php if (!empty($it['slug'])): ?>
                                <a href="/product/<?= e($it['slug']) ?>" target="_blank" class="checkout-item-name"><?= e($it['product_name']) ?></a>
                            <?php else: ?>
                                <span class="checkout-item-name"><?= e($it['product_name']) ?></span>
                            <?php endif; ?>
                            <span class="muted small"><?= fa_num($it['qty']) ?> × <?= price($it['price']) ?><?= $it['stock'] !== null ? ' — موجودی فعلی: ' . fa_num($it['stock']) : '' ?></span>
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
            <h3><?= icon('user', 20) ?> اطلاعات مشتری و تحویل</h3>
            <div class="info-list">
                <div><span>حساب کاربری</span><b><?= e($order['user_name']) ?> (<?= e($order['user_email']) ?>)</b></div>
                <div><span>گیرنده</span><b><?= e($order['receiver_name']) ?></b></div>
                <div><span>موبایل</span><b dir="ltr"><?= fa_num($order['receiver_phone']) ?></b></div>
                <div><span>شهر</span><b><?= e($order['city']) ?></b></div>
                <div><span>آدرس</span><b><?= e($order['address']) ?></b></div>
                <?php if ($order['postal_code']): ?><div><span>کد پستی</span><b dir="ltr"><?= fa_num($order['postal_code']) ?></b></div><?php endif; ?>
                <div><span>روش پرداخت</span><b><?= $order['payment_method'] === 'cod' ? 'پرداخت در محل' : 'پرداخت آنلاین' ?></b></div>
                <?php if ($order['payment_ref']): ?><div><span>کد رهگیری پرداخت</span><b dir="ltr"><?= payment_ref_masked($order['payment_ref']) ?></b></div><?php endif; ?>
                <?php if ($order['paid_at']): ?><div><span>زمان پرداخت</span><b><?= jdate($order['paid_at'], 'datetime') ?></b></div><?php endif; ?>
                <?php if ($order['note']): ?><div><span>یادداشت مشتری</span><b><?= e($order['note']) ?></b></div><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card form-card">
        <h3><?= icon('settings', 20) ?> تغییر وضعیت سفارش</h3>
        <form method="post" action="/admin/orders/<?= (int)$order['id'] ?>/status">
            <?= csrf_field() ?>
            <div class="status-options">
                <?php foreach (order_statuses() as $s): $o = order_status($s); ?>
                    <label class="payment-option">
                        <input type="radio" name="status" value="<?= $s ?>" <?= $order['status'] === $s ? 'checked' : '' ?>>
                        <span class="payment-card sm">
                            <span><?= $o['icon'] ?> <?= $o['label'] ?></span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-primary btn-block"><?= icon('check', 18) ?> اعمال وضعیت</button>
        </form>
        <p class="muted small" style="margin-top:12px">توجه: با لغو سفارش، موجودی محصولات به انبار باز می‌گردد.</p>
    </div>
</div>
