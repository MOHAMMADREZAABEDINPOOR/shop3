<div class="admin-grid-2">
    <div class="card">
        <div class="card-head"><h3><?= icon('ticket', 20) ?> کدهای تخفیف (<?= fa_num(count($coupons)) ?>)</h3></div>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>کد</th><th>مقدار</th><th>حداقل خرید</th><th>استفاده</th><th>انقضا</th><th>وضعیت</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($coupons as $c):
                    $expired = $c['expires_at'] && strtotime($c['expires_at']) < time();
                ?>
                    <tr>
                        <td><code class="coupon-code" dir="ltr"><?= e($c['code']) ?></code></td>
                        <td><strong><?= $c['type'] === 'percent' ? '٪' . fa_num($c['value']) : price($c['value']) ?></strong></td>
                        <td><?= $c['min_total'] ? price($c['min_total']) : '—' ?></td>
                        <td><?= fa_num($c['used_count']) ?><?= $c['max_uses'] ? ' / ' . fa_num($c['max_uses']) : '' ?></td>
                        <td><?= $c['expires_at'] ? jdate($c['expires_at'], 'short') : '—' ?><?= $expired ? ' <span class="text-danger small">(منقضی)</span>' : '' ?></td>
                        <td>
                            <form method="post" action="/admin/coupons/<?= (int)$c['id'] ?>/toggle" class="inline-form">
                                <?= csrf_field() ?>
                                <label class="switch"><input type="checkbox" <?= $c['is_active'] ? 'checked' : '' ?> onchange="this.closest('form').submit()"><i></i></label>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="/admin/coupons/<?= (int)$c['id'] ?>/delete" class="inline-form" onsubmit="return confirm('کد «<?= e($c['code']) ?>» حذف شود؟')">
                                <?= csrf_field() ?>
                                <button class="icon-btn text-danger" title="حذف"><?= icon('trash', 16) ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card form-card">
        <h3><?= icon('plus', 20) ?> کد تخفیف جدید</h3>
        <form method="post" action="/admin/coupons">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>کد *</label>
                <input type="text" name="code" required dir="ltr" placeholder="مثلاً: SUMMER20" style="text-transform:uppercase">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>نوع</label>
                    <select name="type">
                        <option value="percent">درصدی (٪)</option>
                        <option value="fixed">مبلغ ثابت (تومان)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>مقدار *</label>
                    <input type="text" name="value" required inputmode="numeric" placeholder="مثلاً: ۱۰">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>حداقل مبلغ خرید</label>
                    <input type="text" name="min_total" inputmode="numeric" placeholder="۰ = بدون محدودیت">
                </div>
                <div class="form-group">
                    <label>حداکثر استفاده</label>
                    <input type="text" name="max_uses" inputmode="numeric" placeholder="خالی = نامحدود">
                </div>
            </div>
            <div class="form-group">
                <label>تاریخ انقضا</label>
                <input type="date" name="expires_at">
            </div>
            <button class="btn btn-primary btn-block"><?= icon('check', 18) ?> ایجاد کد تخفیف</button>
        </form>
    </div>
</div>
