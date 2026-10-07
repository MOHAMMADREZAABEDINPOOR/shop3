<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [['label' => 'حساب کاربری', 'url' => '/account'], ['label' => 'سفارش‌های من']]]); ?>
    <h1 class="page-title"><?= icon('package', 28) ?> سفارش‌های من</h1>

    <?php if (!$orders): ?>
        <div class="empty-state card">
            <span class="empty-icon empty-svg"><?= icon('package', 48) ?></span>
            <h3>هنوز سفارشی ثبت نشده است</h3>
            <p>اولین خرید خود را انجام دهید و از مزایای فروشگاه بهره‌مند شوید.</p>
            <a href="/shop" class="btn btn-primary">شروع خرید</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $o): $st = order_status($o['status']); ?>
                <a href="/account/orders/<?= e($o['order_code']) ?>" class="order-row card">
                    <div class="order-row-main">
                        <b dir="ltr"><?= e($o['order_code']) ?></b>
                        <span class="muted small"><?= jdate($o['created_at'], 'datetime') ?></span>
                    </div>
                    <span class="muted"><?= fa_num($o['items_count']) ?> قلم</span>
                    <strong><?= price($o['total']) ?></strong>
                    <span class="status-badge st-<?= $st['color'] ?>"><?= $st['icon'] ?> <?= $st['label'] ?></span>
                    <?= icon('chevron-left', 18, 'muted') ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
