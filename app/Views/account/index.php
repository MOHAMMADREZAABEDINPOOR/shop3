<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [['label' => 'حساب کاربری']]]); ?>

    <div class="account-layout">
        <aside class="account-sidebar card">
            <div class="account-user">
                <span class="avatar lg"><?= e(mb_substr($user['name'], 0, 1)) ?></span>
                <div>
                    <strong><?= e($user['name']) ?></strong>
                    <span class="muted small"><?= e($user['email']) ?></span>
                </div>
            </div>
            <nav class="account-nav">
                <a href="/account" class="active"><?= icon('user', 18) ?> پروفایل</a>
                <a href="/account/orders"><?= icon('package', 18) ?> سفارش‌های من</a>
                <a href="/account/wishlist"><?= icon('heart', 18) ?> علاقه‌مندی‌ها</a>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="/admin"><?= icon('dashboard', 18) ?> پنل مدیریت</a>
                <?php endif; ?>
            </nav>
        </aside>

        <div class="account-content">
            <div class="stats-row">
                <div class="stat-card card"><span class="stat-icon" style="--c:#6366f1"><?= icon('package', 22) ?></span><div><strong><?= fa_num($stats['orders']) ?></strong><span>سفارش</span></div></div>
                <div class="stat-card card"><span class="stat-icon" style="--c:#059669"><?= icon('wallet', 22) ?></span><div><strong><?= price($stats['spent']) ?></strong><span>مجموع خرید</span></div></div>
                <div class="stat-card card"><span class="stat-icon" style="--c:#e11d48"><?= icon('heart', 22) ?></span><div><strong><?= fa_num($stats['wishlist']) ?></strong><span>علاقه‌مندی</span></div></div>
                <div class="stat-card card"><span class="stat-icon" style="--c:#0891b2"><?= icon('message', 22) ?></span><div><strong><?= fa_num($stats['reviews']) ?></strong><span>دیدگاه</span></div></div>
            </div>

            <div class="card form-card">
                <h3><?= icon('settings', 20) ?> ویرایش اطلاعات</h3>
                <form method="post" action="/account/profile">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group"><label>نام و نام خانوادگی</label><input type="text" name="name" value="<?= e($user['name']) ?>" required></div>
                        <div class="form-group"><label>شماره موبایل</label><input type="tel" name="phone" value="<?= e($user['phone'] ?? '') ?>" dir="ltr" placeholder="۰۹۱۲۳۴۵۶۷۸۹"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>شهر</label><input type="text" name="city" value="<?= e($user['city'] ?? '') ?>"></div>
                        <div class="form-group"><label>کد پستی</label><input type="text" name="postal_code" value="<?= e($user['postal_code'] ?? '') ?>" dir="ltr" maxlength="10"></div>
                    </div>
                    <div class="form-group"><label>آدرس</label><textarea name="address" rows="2"><?= e($user['address'] ?? '') ?></textarea></div>
                    <button class="btn btn-primary"><?= icon('check', 18) ?> ذخیره تغییرات</button>
                </form>
            </div>

            <div class="card form-card">
                <h3><?= icon('lock', 20) ?> تغییر رمز عبور</h3>
                <form method="post" action="/account/password">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group"><label>رمز فعلی</label><input type="password" name="current_password" required dir="ltr"></div>
                        <div class="form-group"><label>رمز جدید</label><input type="password" name="password" required minlength="6" dir="ltr"></div>
                        <div class="form-group"><label>تکرار رمز جدید</label><input type="password" name="password_confirm" required minlength="6" dir="ltr"></div>
                    </div>
                    <button class="btn btn-outline"><?= icon('lock', 16) ?> تغییر رمز</button>
                </form>
            </div>

            <?php if ($recentOrders): ?>
            <div class="card">
                <div class="card-head">
                    <h3><?= icon('package', 20) ?> آخرین سفارش‌ها</h3>
                    <a href="/account/orders" class="btn btn-ghost btn-sm">مشاهده همه <?= icon('arrow-left', 14) ?></a>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>کد سفارش</th><th>تاریخ</th><th>مبلغ</th><th>وضعیت</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($recentOrders as $o): $st = order_status($o['status']); ?>
                            <tr>
                                <td><b dir="ltr"><?= e($o['order_code']) ?></b></td>
                                <td><?= jdate($o['created_at'], 'short') ?></td>
                                <td><?= price($o['total']) ?></td>
                                <td><span class="status-badge st-<?= $st['color'] ?>"><?= $st['icon'] ?> <?= $st['label'] ?></span></td>
                                <td><a href="/account/orders/<?= e($o['order_code']) ?>" class="btn btn-ghost btn-sm">جزئیات</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
