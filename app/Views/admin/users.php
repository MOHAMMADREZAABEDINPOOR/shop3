<div class="card">
    <div class="card-head wrap">
        <h3><?= icon('users', 20) ?> کاربران (<?= fa_num(count($users)) ?>)</h3>
        <form method="get" class="admin-search">
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="نام، ایمیل یا موبایل...">
            <button class="btn btn-outline btn-sm"><?= icon('search', 16) ?></button>
        </form>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>کاربر</th><th>موبایل</th><th>سفارش‌ها</th><th>مجموع خرید</th><th>نقش</th><th>عضویت</th><th>عملیات</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <div class="table-product">
                            <span class="avatar sm"><?= e(mb_substr($u['name'], 0, 1)) ?></span>
                            <div>
                                <strong><?= e($u['name']) ?></strong>
                                <small class="muted" dir="ltr"><?= e($u['email']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td dir="ltr"><?= $u['phone'] ? fa_num($u['phone']) : '—' ?></td>
                    <td><?= fa_num($u['orders_count']) ?></td>
                    <td><?= price($u['spent']) ?></td>
                    <td>
                        <?php if ($u['role'] === 'admin'): ?>
                            <span class="status-badge st-primary">مدیر</span>
                        <?php else: ?>
                            <span class="status-badge st-muted">مشتری</span>
                        <?php endif; ?>
                    </td>
                    <td><?= jdate($u['created_at'], 'short') ?></td>
                    <td>
                        <?php if ((int)$u['id'] !== (int)auth()['id']): ?>
                        <div class="table-actions">
                            <form method="post" action="/admin/users/<?= (int)$u['id'] ?>/role" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="role" value="<?= $u['role'] === 'admin' ? 'customer' : 'admin' ?>">
                                <button class="icon-btn" title="<?= $u['role'] === 'admin' ? 'تنزل به مشتری' : 'ارتقا به مدیر' ?>"><?= icon('award', 16) ?></button>
                            </form>
                            <form method="post" action="/admin/users/<?= (int)$u['id'] ?>/delete" class="inline-form" onsubmit="return confirm('کاربر «<?= e($u['name']) ?>» حذف شود؟ سفارش‌های او نیز حذف می‌شوند.')">
                                <?= csrf_field() ?>
                                <button class="icon-btn text-danger" title="حذف"><?= icon('trash', 16) ?></button>
                            </form>
                        </div>
                        <?php else: ?>
                            <span class="muted small">شما</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
