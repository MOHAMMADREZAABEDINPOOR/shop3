<div class="admin-grid-2">
    <div class="card">
        <div class="card-head"><h3><?= icon('layers', 20) ?> دسته‌بندی‌ها (<?= fa_num(count($categories)) ?>)</h3></div>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>نام</th><th>اسلاگ</th><th>محصولات</th><th>ترتیب</th><th>عملیات</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td>
                            <span class="cat-icon" style="--c:<?= e($c['color']) ?>"><?= icon($c['icon'] ?? 'box', 16) ?></span>
                            <strong><?= e($c['name']) ?></strong>
                        </td>
                        <td><code dir="ltr"><?= e($c['slug']) ?></code></td>
                        <td><?= fa_num($c['product_count']) ?></td>
                        <td><?= fa_num($c['sort_order']) ?></td>
                        <td>
                            <div class="table-actions">
                                <button class="icon-btn" title="ویرایش" onclick='editCategory(<?= json_encode($c, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'><?= icon('edit', 16) ?></button>
                                <form method="post" action="/admin/categories/<?= (int)$c['id'] ?>/delete" class="inline-form" onsubmit="return confirm('دسته‌بندی «<?= e($c['name']) ?>» حذف شود؟')">
                                    <?= csrf_field() ?>
                                    <button class="icon-btn text-danger" title="حذف"><?= icon('trash', 16) ?></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card form-card">
        <h3 id="catFormTitle"><?= icon('plus', 20) ?> دسته‌بندی جدید</h3>
        <form method="post" action="/admin/categories" id="catForm">
            <?= csrf_field() ?>
            <div class="form-group"><label>نام *</label><input type="text" name="name" id="catName" required placeholder="مثلاً: موبایل و تبلت"></div>
            <div class="form-group"><label>اسلاگ</label><input type="text" name="slug" id="catSlug" dir="ltr" placeholder="خودکار از نام ساخته می‌شود"></div>
            <div class="form-row">
                <div class="form-group"><label>آیکون</label><select name="icon" id="catIcon">
                    <?php foreach (['mobile','laptop','audio','wearable','home','gaming','fashion','beauty','sport','kids','book','tools','box','grid','tag'] as $ic): ?>
                        <option value="<?= $ic ?>"><?= $ic ?></option>
                    <?php endforeach; ?>
                </select></div>
                <div class="form-group"><label>رنگ</label><input type="color" name="color" id="catColor" value="#6366f1"></div>
            </div>
            <div class="form-group"><label>ترتیب نمایش</label><input type="text" name="sort_order" id="catSort" inputmode="numeric" value="0"></div>
            <div class="form-group"><label>توضیح</label><textarea name="description" id="catDesc" rows="2"></textarea></div>
            <button type="submit" class="btn btn-primary btn-block" id="catSubmit"><?= icon('check', 18) ?> افزودن دسته‌بندی</button>
            <button type="button" class="btn btn-ghost btn-block" id="catCancel" hidden>انصراف از ویرایش</button>
        </form>
    </div>
</div>

<?php ob_start(); ?>
<script>
function editCategory(c) {
    document.getElementById('catForm').action = '/admin/categories/' + c.id;
    document.getElementById('catName').value = c.name;
    document.getElementById('catSlug').value = c.slug;
    document.getElementById('catIcon').value = c.icon;
    document.getElementById('catColor').value = c.color;
    document.getElementById('catSort').value = c.sort_order;
    document.getElementById('catDesc').value = c.description || '';
    document.getElementById('catFormTitle').textContent = 'ویرایش دسته‌بندی';
    document.getElementById('catSubmit').textContent = 'ذخیره تغییرات';
    document.getElementById('catCancel').hidden = false;
    window.scrollTo({top: 0, behavior: 'smooth'});
}
document.getElementById('catCancel').addEventListener('click', function () {
    var f = document.getElementById('catForm');
    f.action = '/admin/categories';
    f.reset();
    document.getElementById('catFormTitle').textContent = 'دسته‌بندی جدید';
    document.getElementById('catSubmit').textContent = 'افزودن دسته‌بندی';
    this.hidden = true;
});
</script>
<?php $scripts = ob_get_clean(); ?>
