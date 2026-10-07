<?php
$isEdit = $banner !== null;
$action = $isEdit ? '/admin/banners/' . (int)$banner['id'] : '/admin/banners';
$v = fn($key, $default = '') => e(old($key, $isEdit ? ($banner[$key] ?? $default) : $default));
?>
<form method="post" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>
    <div class="form-layout">
        <div class="form-main">
            <div class="card form-card">
                <h3><?= icon('image', 20) ?> محتوای متنی پوستر</h3>
                
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label>عنوان پوستر (فارسی) *</label>
                        <input type="text" name="title" required value="<?= $v('title') ?>" placeholder="مثلاً: جشنواره شگفت‌انگیز پاییزه">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>عنوان انگلیسی (اختیاری)</label>
                        <input type="text" name="title_en" dir="ltr" value="<?= $v('title_en') ?>" placeholder="e.g. Autumn Mega Season Sale">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label>توضیح / زیرعنوان (فارسی)</label>
                        <input type="text" name="subtitle" value="<?= $v('subtitle') ?>" placeholder="مثلاً: تا ۴۰٪ تخفیف روی تمامی لوازم جانبی و دیجیتال">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>توضیح انگلیسی</label>
                        <input type="text" name="subtitle_en" dir="ltr" value="<?= $v('subtitle_en') ?>" placeholder="e.g. Up to 40% Off on all authentic tech items">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label>نشان / تگ بالای پوستر (فارسی)</label>
                        <input type="text" name="badge" value="<?= $v('badge') ?>" placeholder="مثلاً: فروش ویژه فصل">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>نشان انگلیسی</label>
                        <input type="text" name="badge_en" dir="ltr" value="<?= $v('badge_en') ?>" placeholder="e.g. Special Season Sale">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label>متن دکمه (فارسی)</label>
                        <input type="text" name="button_text" value="<?= $v('button_text', 'مشاهده و خرید') ?>" placeholder="مشاهده و خرید">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>متن دکمه انگلیسی</label>
                        <input type="text" name="button_text_en" dir="ltr" value="<?= $v('button_text_en', 'Shop Now') ?>" placeholder="Shop Now">
                    </div>
                </div>

                <div class="form-group">
                    <label>لینک مقصد (کلیک روی پوستر)</label>
                    <input type="text" name="link" dir="ltr" required value="<?= $v('link', '/shop') ?>" placeholder="/shop یا /category/mobile">
                    <small class="muted">می‌توانید آدرس داخلی مثل <code>/shop?discount=1</code> یا لینک مستقیم دسته‌بندی قرار دهید.</small>
                </div>
            </div>
        </div>

        <div class="form-side">
            <div class="card form-card">
                <h3><?= icon('settings', 20) ?> تنظیمات و جایگاه</h3>
                
                <div class="form-group">
                    <label>جایگاه نمایش پوستر *</label>
                    <select name="position" required id="bannerPosition">
                        <?php foreach ($positions as $pKey => $pName): ?>
                            <option value="<?= $pKey ?>" <?= old('position', $isEdit ? $banner['position'] : 'home_hero') === $pKey ? 'selected' : '' ?>>
                                <?= e($pName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>تم رنگی پوستر</label>
                    <select name="color">
                        <option value="gradient-purple" <?= old('color', $isEdit ? $banner['color'] : '') === 'gradient-purple' ? 'selected' : '' ?>>بنفش رویال (پیش‌فرض)</option>
                        <option value="gradient-blue" <?= old('color', $isEdit ? $banner['color'] : '') === 'gradient-blue' ? 'selected' : '' ?>>آبی لاجوردی و فیروزه‌ای</option>
                        <option value="gradient-dark" <?= old('color', $isEdit ? $banner['color'] : '') === 'gradient-dark' ? 'selected' : '' ?>>تاریک گیمینگ کربنی</option>
                        <option value="gradient-emerald" <?= old('color', $isEdit ? $banner['color'] : '') === 'gradient-emerald' ? 'selected' : '' ?>>سبز زمردی نئونی</option>
                        <option value="gradient-amber" <?= old('color', $isEdit ? $banner['color'] : '') === 'gradient-amber' ? 'selected' : '' ?>>طلایی و کهربایی</option>
                        <option value="gradient-rose" <?= old('color', $isEdit ? $banner['color'] : '') === 'gradient-rose' ? 'selected' : '' ?>>رزگلد و سرخابی</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>ترتیب نمایش (اولویت)</label>
                    <input type="text" name="sort_order" inputmode="numeric" value="<?= $v('sort_order', '0') ?>">
                    <small class="muted">عدد کوچکتر ابتدا نمایش داده می‌شود.</small>
                </div>

                <label class="filter-item switch-item">
                    <span>پوستر فعال باشد</span>
                    <span class="switch"><input type="checkbox" name="is_active" value="1" <?= old('is_active', $isEdit ? $banner['is_active'] : 1) ? 'checked' : '' ?>><i></i></span>
                </label>
            </div>

            <div class="card form-card">
                <h3><?= icon('upload', 20) ?> تصویر پوستر</h3>
                <?php if ($isEdit && !empty($banner['image'])): ?>
                    <div style="margin-bottom: 12px; border-radius: 12px; overflow: hidden; border: 2px solid var(--border);">
                        <img src="<?= e(banner_image($banner['image'])) ?>" class="image-preview" style="width: 100%; height: 140px; object-fit: cover;" alt="<?= e($banner['title']) ?>">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>آپلود فایل پوستر</label>
                    <input type="file" name="image" accept="image/*">
                    <small class="muted">فرمت‌های مجاز: jpg, png, webp, svg</small>
                </div>

                <div class="form-group">
                    <label>یا آدرس اینترنتی تصویر (URL)</label>
                    <input type="url" name="image_url" dir="ltr" placeholder="https://... یا /uploads/...">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">
                <?= icon('check-circle', 20) ?> <?= $isEdit ? 'ذخیره تغییرات پوستر' : 'ایجاد پوستر' ?>
            </button>
            <a href="/admin/banners" class="btn btn-ghost btn-block">انصراف</a>
        </div>
    </div>
</form>
