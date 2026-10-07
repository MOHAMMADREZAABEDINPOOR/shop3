<div class="card">
    <div class="card-head wrap">
        <h3><?= icon('box', 20) ?> محصولات (<?= fa_num(count($products)) ?>)</h3>
        <div class="card-actions">
            <form method="get" class="admin-search">
                <input type="search" name="q" value="<?= e($q) ?>" placeholder="جستجوی نام یا برند...">
                <select name="category" onchange="this.form.submit()">
                    <option value="0">همه دسته‌ها</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $cat === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-outline btn-sm"><?= icon('search', 16) ?></button>
            </form>
            <a href="/admin/products/create" class="btn btn-primary btn-sm"><?= icon('plus', 16) ?> محصول جدید</a>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr><th>محصول</th><th>دسته</th><th>قیمت</th><th>موجودی</th><th>فروش</th><th>وضعیت</th><th>عملیات</th></tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <div class="table-product">
                            <img src="<?= e(product_image($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                            <div>
                                <a href="/product/<?= e($p['slug']) ?>" target="_blank"><strong><?= e($p['name']) ?></strong></a>
                                <small class="muted"><?= e($p['brand'] ?? '—') ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= e($p['category_name']) ?></td>
                    <td>
                        <?php if ($p['discount_price']): ?>
                            <del class="muted small"><?= price($p['price'], false) ?></del><br>
                            <strong><?= price($p['discount_price']) ?></strong>
                        <?php else: ?>
                            <?= price($p['price']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="<?= $p['stock'] <= 5 ? 'text-danger' : '' ?>"><?= fa_num($p['stock']) ?></span>
                        <?php if ($p['stock'] <= 5): ?> <?= icon('alert', 14, 'text-danger') ?><?php endif; ?>
                    </td>
                    <td><?= fa_num($p['sold']) ?></td>
                    <td>
                        <form method="post" action="/admin/products/<?= (int)$p['id'] ?>/toggle" class="inline-form ajax-toggle">
                            <?= csrf_field() ?>
                            <label class="switch"><input type="checkbox" <?= $p['is_active'] ? 'checked' : '' ?> onchange="this.closest('form').requestSubmit()"><i></i></label>
                        </form>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="/admin/products/<?= (int)$p['id'] ?>/edit" class="icon-btn" title="ویرایش"><?= icon('edit', 16) ?></a>
                            <form method="post" action="/admin/products/<?= (int)$p['id'] ?>/delete" class="inline-form" onsubmit="return confirm('محصول «<?= e($p['name']) ?>» حذف شود؟ این عمل قابل بازگشت نیست.')">
                                <?= csrf_field() ?>
                                <button class="icon-btn text-danger" title="حذف"><?= icon('trash', 16) ?></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$products): ?>
                <tr><td colspan="7" class="empty-cell">محصولی پیدا نشد.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
