<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [['label' => 'پیگیری سفارش']]]); ?>
    <div class="page-hero">
        <h1><?= icon('truck', 30) ?> پیگیری سفارش</h1>
        <p>کد سفارش و شماره موبایل گیرنده را وارد کنید تا وضعیت سفارش را مشاهده کنید.</p>
    </div>

    <div class="track-layout">
        <div class="card track-form-card">
            <form method="get" action="/track">
                <div class="form-group">
                    <label>کد سفارش</label>
                    <input type="text" name="code" required value="<?= e($code) ?>" placeholder="NS-240101-ABC123" dir="ltr">
                </div>
                <div class="form-group">
                    <label>شماره موبایل گیرنده</label>
                    <input type="tel" name="phone" required value="<?= e($phone) ?>" placeholder="۰۹۱۲۳۴۵۶۷۸۹" dir="ltr">
                </div>
                <button class="btn btn-primary btn-block"><?= icon('search', 18) ?> پیگیری سفارش</button>
            </form>
        </div>

        <?php if ($order): $st = order_status($order['status']); ?>
        <div class="card track-result">
            <div class="track-result-head">
                <div>
                    <h3>سفارش <b dir="ltr"><?= e($order['order_code']) ?></b></h3>
                    <span class="muted"><?= jdate($order['created_at'], 'datetime') ?></span>
                </div>
                <span class="status-badge st-<?= $st['color'] ?>"><?= $st['icon'] ?> <?= $st['label'] ?></span>
            </div>

            <div class="order-status-track">
                <?php
                $steps = ['paid' => 'پرداخت', 'processing' => 'پردازش', 'shipped' => 'ارسال', 'delivered' => 'تحویل'];
                $orderIdx = ['pending' => 0, 'paid' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
                $current = $order['status'] === 'cancelled' ? -1 : ($orderIdx[$order['status']] ?? 0);
                $i = 1;
                foreach ($steps as $key => $label): $done = $current >= $i; ?>
                    <div class="track-step <?= $done ? 'done' : '' ?> <?= $current === $i ? 'current' : '' ?>">
                        <span class="track-dot"><?= $done ? icon('check', 14) : fa_num($i) ?></span>
                        <span><?= e($label) ?></span>
                    </div>
                <?php $i++; endforeach; ?>
            </div>

            <div class="track-items">
                <?php foreach ($items as $it): ?>
                    <div class="checkout-item">
                        <img src="<?= e(product_image($it['product_image'])) ?>" alt="<?= e($it['product_name']) ?>" loading="lazy">
                        <div><span class="checkout-item-name"><?= e($it['product_name']) ?></span><span class="muted small"><?= fa_num($it['qty']) ?> × <?= price($it['price']) ?></span></div>
                        <strong><?= price($it['price'] * $it['qty']) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-row total"><span>مبلغ سفارش</span><strong><?= price($order['total']) ?></strong></div>
        </div>
        <?php endif; ?>
    </div>
</div>
