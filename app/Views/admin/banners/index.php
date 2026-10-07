<div class="card">
    <div class="card-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div>
            <h3><?= icon('image', 22) ?> پوسترها و بنرهای وب‌سایت (<?= fa_num(count($banners)) ?>)</h3>
            <p class="muted small" style="margin-top: 4px;">مدیریت کامل پوسترها، اسلایدرها و بنرهای تبلیغاتی تمام بخش‌های فروشگاه</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <a href="/admin/banners/create" class="btn btn-primary btn-sm">
                <?= icon('plus', 16) ?> افزودن پوستر جدید
            </a>
        </div>
    </div>

    <!-- فیلتر جایگاه -->
    <div class="filter-strip" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; padding: 12px; background: var(--surface-2); border-radius: 12px;">
        <a href="/admin/banners" class="btn btn-sm <?= empty($pos) ? 'btn-primary' : 'btn-outline' ?>">همه جایگاه‌ها</a>
        <?php foreach ($positions as $key => $pLabel): ?>
            <a href="/admin/banners?position=<?= urlencode($key) ?>" class="btn btn-sm <?= $pos === $key ? 'btn-primary' : 'btn-outline' ?>">
                <?= e($pLabel) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 100px;">تصویر پوستر</th>
                    <th>عنوان و زیرعنوان</th>
                    <th>نشان (Badge)</th>
                    <th>جایگاه نمایش</th>
                    <th>لینک مقصد</th>
                    <th>ترتیب</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($banners)): ?>
                <?php foreach ($banners as $b): ?>
                    <tr>
                        <td>
                            <div style="width: 90px; height: 56px; border-radius: 10px; overflow: hidden; border: 1.5px solid var(--border); background: var(--surface-2);">
                                <img src="<?= e(banner_image($b['image'])) ?>" alt="<?= e($b['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                            </div>
                        </td>
                        <td>
                            <strong><?= e($b['title']) ?></strong>
                            <?php if (!empty($b['title_en'])): ?>
                                <small class="muted" style="display: block; direction: ltr; text-align: right;"><?= e($b['title_en']) ?></small>
                            <?php endif; ?>
                            <?php if (!empty($b['subtitle'])): ?>
                                <span class="muted small" style="display: block; margin-top: 2px;"><?= e(mb_substr($b['subtitle'], 0, 48)) ?><?= mb_strlen($b['subtitle']) > 48 ? '...' : '' ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($b['badge'])): ?>
                                <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: var(--primary); font-size: 11px; padding: 4px 10px; border-radius: 12px; font-weight: 700;">
                                    <?= e($b['badge']) ?>
                                </span>
                            <?php else: ?>
                                <span class="muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge" style="font-size: 11px; padding: 4px 8px; border-radius: 8px; background: var(--surface-2); color: var(--text-2);">
                                <?= e($positions[$b['position']] ?? $b['position']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= e($b['link']) ?>" target="_blank" class="muted small" dir="ltr" style="display: inline-flex; align-items: center; gap: 4px;">
                                <?= e($b['link']) ?> <?= icon('external', 12) ?>
                            </a>
                        </td>
                        <td><?= fa_num($b['sort_order']) ?></td>
                        <td>
                            <form method="post" action="/admin/banners/<?= (int)$b['id'] ?>/toggle" class="ajax-toggle">
                                <?= csrf_field() ?>
                                <button type="submit" class="status-chip <?= $b['is_active'] ? 'paid' : 'pending' ?>" style="cursor: pointer; border: none;">
                                    <?= $b['is_active'] ? 'فعال' : 'غیرفعال' ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="/admin/banners/<?= (int)$b['id'] ?>/edit" class="icon-btn" title="ویرایش"><?= icon('edit', 16) ?></a>
                                <form method="post" action="/admin/banners/<?= (int)$b['id'] ?>/delete" class="inline-form" onsubmit="return confirm('آیا از حذف این پوستر اطمینان دارید؟')">
                                    <?= csrf_field() ?>
                                    <button class="icon-btn text-danger" title="حذف"><?= icon('trash', 16) ?></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px;">
                        <p class="muted">هیچ پوستری در این بخش ثبت نشده است.</p>
                        <a href="/admin/banners/create" class="btn btn-outline btn-sm" style="margin-top: 10px;">افزودن اولین پوستر</a>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
