<?php
$isEdit = $product !== null;
$action = $isEdit ? '/admin/products/' . (int)$product['id'] : '/admin/products';
$v = fn($key, $default = '') => e(old($key, $isEdit ? ($product[$key] ?? $default) : $default));
?>
<form method="post" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>
    <div class="form-layout">
        <div class="form-main">
            <div class="card form-card">
                <h3><?= icon('box', 20) ?> اطلاعات اصلی</h3>
                <div class="form-group">
                    <label>نام محصول *</label>
                    <input type="text" name="name" required value="<?= $v('name') ?>" placeholder="مثلاً: گوشی آیفون ۱۵ پرو">
                </div>
                <div class="form-group">
                    <label>اسلاگ (آدرس صفحه)</label>
                    <input type="text" name="slug" value="<?= $v('slug') ?>" placeholder="خالی بگذارید تا خودکار ساخته شود" dir="ltr">
                    <small class="muted">مثال: iphone-15-pro</small>
                </div>
                <div class="form-group">
                    <label>توضیح کوتاه</label>
                    <input type="text" name="short_description" value="<?= $v('short_description') ?>" placeholder="یک جمله جذاب درباره محصول">
                </div>
                <div class="form-group">
                    <label>توضیحات کامل</label>
                    <textarea name="description" rows="6" placeholder="توضیحات کامل محصول..."><?= $v('description') ?></textarea>
                </div>
            </div>

            <div class="card form-card">
                <h3><?= icon('list', 20) ?> مشخصات فنی</h3>
                <div id="specsList">
                    <?php
                    $oldKeys = $_POST['spec_key'] ?? array_keys($specs);
                    $oldVals = $_POST['spec_val'] ?? array_values($specs);
                    foreach ((array)$oldKeys as $i => $k):
                        $val = $oldVals[$i] ?? '';
                        if (trim((string)$k) === '') continue;
                    ?>
                        <div class="spec-row">
                            <input type="text" name="spec_key[]" value="<?= e($k) ?>" placeholder="مثلاً: حافظه داخلی">
                            <input type="text" name="spec_val[]" value="<?= e($val) ?>" placeholder="مثلاً: ۲۵۶ گیگابایت">
                            <button type="button" class="icon-btn text-danger spec-remove"><?= icon('trash', 16) ?></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-outline btn-sm" id="addSpec"><?= icon('plus', 14) ?> افزودن مشخصه</button>
            </div>
        </div>

        <div class="form-side">
            <div class="card form-card">
                <h3><?= icon('settings', 20) ?> تنظیمات</h3>
                <div class="form-group">
                    <label>دسته‌بندی *</label>
                    <select name="category_id" required>
                        <option value="">انتخاب کنید...</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int)$c['id'] ?>" <?= (int)old('category_id', $isEdit ? $product['category_id'] : 0) === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['icon'] . ' ' . $c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>برند</label>
                    <input type="text" name="brand" value="<?= $v('brand') ?>" placeholder="مثلاً: اپل">
                </div>
                <label class="filter-item switch-item">
                    <span>محصول فعال باشد</span>
                    <span class="switch"><input type="checkbox" name="is_active" value="1" <?= old('is_active', $isEdit ? $product['is_active'] : 1) ? 'checked' : '' ?>><i></i></span>
                </label>
                <label class="filter-item switch-item">
                    <span>محصول ویژه (صفحه اصلی)</span>
                    <span class="switch"><input type="checkbox" name="is_featured" value="1" <?= old('is_featured', $isEdit ? $product['is_featured'] : 0) ? 'checked' : '' ?>><i></i></span>
                </label>
            </div>

            <div class="card form-card">
                <h3><?= icon('tag', 20) ?> قیمت و موجودی</h3>
                <div class="form-group">
                    <label>قیمت (تومان) *</label>
                    <input type="text" name="price" required inputmode="numeric" value="<?= $v('price') ?>" placeholder="مثلاً: ۲۵۰۰۰۰۰۰">
                </div>
                <div class="form-group">
                    <label>قیمت با تخفیف (اختیاری)</label>
                    <input type="text" name="discount_price" inputmode="numeric" value="<?= $v('discount_price') ?>" placeholder="خالی = بدون تخفیف">
                </div>
                <div class="form-group">
                    <label>موجودی انبار *</label>
                    <input type="text" name="stock" required inputmode="numeric" value="<?= $v('stock', '0') ?>">
                </div>
            </div>

            <div class="card form-card">
                <h3><?= icon('upload', 20) ?> تصویر محصول</h3>
                <?php if ($isEdit && $product['image']): ?>
                    <img src="<?= e(product_image($product['image'])) ?>" class="image-preview" alt="<?= e($product['name']) ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>آپلود تصویر</label>
                    <input type="file" name="image" accept="image/*">
                    <small class="muted">فقط jpg, png, webp, gif — حداکثر ۴ مگابایت (SVG به دلایل امنیتی مجاز نیست)</small>
                </div>
                <div class="form-group">
                    <label>یا آدرس تصویر (URL)</label>
                    <input type="url" name="image_url" dir="ltr" placeholder="https://...">
                </div>
                <small class="muted">اگر تصویری انتخاب نکنید، به‌صورت خودکار یک تصویر زیبا ساخته می‌شود.</small>
            </div>

            <div class="card form-card">
                <h3><?= icon('image', 20) ?> گالری تصاویر محصول</h3>
                <?php
                $currentGallery = ($isEdit && !empty($product['gallery'])) ? (json_decode($product['gallery'], true) ?: []) : [];
                if (!empty($currentGallery)):
                ?>
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 6px; display: block;">تصاویر فعلی گالری (برای حذف، کلیک کنید):</label>
                    <div class="admin-gallery-preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                        <?php foreach ($currentGallery as $gItem): ?>
                            <div class="admin-gal-item" style="position: relative; width: 72px; height: 72px; border-radius: 12px; overflow: hidden; border: 2px solid var(--border);">
                                <img src="<?= e(product_image($gItem)) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Gallery preview">
                                <label style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(220, 38, 38, 0.88); color: #fff; font-size: 10.5px; text-align: center; cursor: pointer; padding: 2px 0;">
                                    <input type="checkbox" name="remove_gallery[]" value="<?= e($gItem) ?>" style="display: none;" onchange="this.closest('.admin-gal-item').style.opacity = this.checked ? '0.3' : '1';">
                                    حذف
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>آپلود چند تصویر جدید برای گالری</label>
                    <input type="file" name="gallery_files[]" multiple accept="image/*">
                    <small class="muted">می‌توانید همزمان چند عکس را انتخاب و آپلود کنید (jpg, png, webp)</small>
                </div>

                <div class="form-group">
                    <label>یا افزودن آدرس تصویر اینترنتی (URL)</label>
                    <textarea name="gallery_urls" rows="2" dir="ltr" placeholder="https://... (هر آدرس در یک سطر)"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block"><?= icon('check-circle', 20) ?> <?= $isEdit ? 'ذخیره تغییرات' : 'افزودن محصول' ?></button>
            <a href="/admin/products" class="btn btn-ghost btn-block">انصراف</a>
        </div>
    </div>
</form>

<?php ob_start(); ?>
<script>
document.getElementById('addSpec').addEventListener('click', function () {
    var row = document.createElement('div');
    row.className = 'spec-row';
    row.innerHTML = '<input type="text" name="spec_key[]" placeholder="مثلاً: حافظه داخلی"><input type="text" name="spec_val[]" placeholder="مثلاً: ۲۵۶ گیگابایت"><button type="button" class="icon-btn text-danger spec-remove">✕</button>';
    document.getElementById('specsList').appendChild(row);
});
document.addEventListener('click', function (e) {
    if (e.target.closest('.spec-remove')) e.target.closest('.spec-row').remove();
});
</script>
<?php $scripts = ob_get_clean(); ?>
