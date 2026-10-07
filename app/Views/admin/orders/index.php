<div class="card">
    <div class="card-head wrap">
        <h3><?= icon('package', 20) ?> سفارش‌ها</h3>
        <form method="get" class="admin-search">
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="کد سفارش، نام، موبایل...">
            <button class="btn btn-outline btn-sm"><?= icon('search', 16) ?></button>
        </form>
    </div>

    <div class="status-tabs">
        <a href="/admin/orders" class="<?= $status === '' ? 'active' : '' ?>">همه (<?= fa_num(array_sum($counts)) ?>)</a>
        <?php foreach (order_statuses() as $s): $st = order_status($s); ?>
            <a href="/admin/orders?status=<?= $s ?>" class="<?= $status === $s ? 'active' : '' ?>"><?= $st['icon'] ?> <?= $st['label'] ?> (<?= fa_num($counts[$s] ?? 0) ?>)</a>
        <?php endforeach; ?>
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>کد سفارش</th><th>مشتری</th><th>اقلام</th><th>مبلغ</th><th>پرداخت</th><th>وضعیت</th><th>تاریخ</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): $st = order_status($o['status']); ?>
                <tr>
                    <td><b dir="ltr"><?= e($o['order_code']) ?></b></td>
                    <td>
                        <div><strong><?= e($o['user_name']) ?></strong></div>
                        <small class="muted"><?= e($o['user_email']) ?></small>
                    </td>
                    <td><?= fa_num($o['items_count']) ?> قلم</td>
                    <td><strong><?= price($o['total']) ?></strong></td>
                    <td><?= $o['payment_method'] === 'cod' ? 'در محل' : 'آنلاین' ?></td>
                    <td><span class="status-badge st-<?= $st['color'] ?>"><?= $st['icon'] ?> <?= $st['label'] ?></span></td>
                    <td><?= jdate($o['created_at'], 'short') ?></td>
                    <td><a href="/admin/orders/<?= (int)$o['id'] ?>" class="btn btn-ghost btn-sm">جزئیات</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$orders): ?>
                <tr><td colspan="8" class="empty-cell">سفارشی پیدا نشد.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
