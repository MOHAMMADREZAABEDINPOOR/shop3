<div class="card">
    <div class="card-head"><h3><?= icon('message', 20) ?> نظرات کاربران (<?= fa_num(count($reviews)) ?>)</h3></div>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>کاربر</th><th>محصول</th><th>امتیاز</th><th>نظر</th><th>تاریخ</th><th>وضعیت</th><th>عملیات</th></tr></thead>
            <tbody>
            <?php foreach ($reviews as $r): ?>
                <tr>
                    <td><strong><?= e($r['user_name']) ?></strong></td>
                    <td><a href="/product/<?= e($r['slug']) ?>" target="_blank"><?= e($r['product_name']) ?></a></td>
                    <td><?= stars((float)$r['rating']) ?></td>
                    <td class="review-cell">
                        <?php if ($r['title']): ?><strong><?= e($r['title']) ?></strong><br><?php endif; ?>
                        <?= e(excerpt($r['comment'], 80)) ?>
                    </td>
                    <td><?= jdate($r['created_at'], 'short') ?></td>
                    <td>
                        <span class="status-badge <?= $r['is_approved'] ? 'st-success' : 'st-muted' ?>"><?= $r['is_approved'] ? 'تأیید شده' : 'مخفی' ?></span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <form method="post" action="/admin/reviews/<?= (int)$r['id'] ?>/toggle" class="inline-form">
                                <?= csrf_field() ?>
                                <button class="icon-btn" title="<?= $r['is_approved'] ? 'مخفی کردن' : 'تأیید' ?>"><?= icon($r['is_approved'] ? 'eye-off' : 'eye', 16) ?></button>
                            </form>
                            <form method="post" action="/admin/reviews/<?= (int)$r['id'] ?>/delete" class="inline-form" onsubmit="return confirm('این نظر حذف شود؟')">
                                <?= csrf_field() ?>
                                <button class="icon-btn text-danger" title="حذف"><?= icon('trash', 16) ?></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$reviews): ?>
                <tr><td colspan="7" class="empty-cell">نظری ثبت نشده است.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
