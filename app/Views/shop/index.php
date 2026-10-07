<?php
use App\Core\I18n;

$isEn = !I18n::isRtl();
$f = $filters;
$catName = $category ? category_name($category) : null;
$crumbs = [
    ['label' => __('home', 'خانه'), 'url' => '/'],
    ['label' => __('all_products', 'فروشگاه'), 'url' => '/shop']
];
if ($category) { $crumbs[] = ['label' => $catName]; }
?>
<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => $crumbs]); ?>

    <div class="shop-layout">
        <!-- سایدبار فیلترها -->
        <aside class="filters" id="filtersPanel">
            <div class="filters-head">
                <h3><?= icon('filter', 18) ?> <?= __('filters', 'فیلترها') ?></h3>
                <button class="icon-btn mobile-only" id="filtersClose"><?= icon('x', 20) ?></button>
            </div>
            <form method="get" action="<?= $category ? '/category/' . e($category['slug']) : '/shop' ?>" id="filterForm">
                <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
                <input type="hidden" name="sort" value="<?= e($sort) ?>">

                <div class="filter-box">
                    <h4><?= __('filter_by_category', 'دسته‌بندی') ?></h4>
                    <div class="filter-list">
                        <label class="filter-item">
                            <input type="radio" name="category" value="" <?= $f['catSlug'] === '' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <span><?= __('all_categories', 'همه دسته‌ها') ?></span>
                        </label>
                        <?php foreach ($categories as $c): ?>
                            <label class="filter-item">
                                <input type="radio" name="category" value="<?= e($c['slug']) ?>" <?= $f['catSlug'] === $c['slug'] ? 'checked' : '' ?> <?= $category ? 'disabled' : '' ?> onchange="this.form.submit()">
                                <span><?= e(category_name($c)) ?> <em>(<?= l_num($c['product_count']) ?>)</em></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-box">
                    <h4><?= __('filter_by_price', 'محدوده قیمت') ?></h4>
                    <div class="price-inputs">
                        <input type="text" name="min_price" inputmode="numeric" placeholder="<?= $isEn ? 'From $' . round($priceRange['min_p']/60000) : 'از ' . fa_num(number_format((float)$priceRange['min_p'])) ?>" value="<?= $f['minP'] ? ($isEn ? $f['minP'] : fa_num(number_format($f['minP']))) : '' ?>">
                        <span><?= $isEn ? 'to' : 'تا' ?></span>
                        <input type="text" name="max_price" inputmode="numeric" placeholder="<?= $isEn ? 'To $' . round($priceRange['max_p']/60000) : 'تا ' . fa_num(number_format((float)$priceRange['max_p'])) ?>" value="<?= $f['maxP'] ? ($isEn ? $f['maxP'] : fa_num(number_format($f['maxP']))) : '' ?>">
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm btn-block"><?= __('apply_filters', 'اعمال قیمت') ?></button>
                </div>

                <div class="filter-box">
                    <h4><?= __('filter_by_brand', 'برند') ?></h4>
                    <div class="filter-list">
                        <label class="filter-item">
                            <input type="radio" name="brand" value="" <?= $f['brand'] === '' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <span><?= $isEn ? 'All Brands' : 'همه برندها' ?></span>
                        </label>
                        <?php foreach ($brands as $b): ?>
                            <label class="filter-item">
                                <input type="radio" name="brand" value="<?= e($b['brand']) ?>" <?= $f['brand'] === $b['brand'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                <span><?= e($b['brand']) ?> <em>(<?= l_num($b['cnt']) ?>)</em></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-box">
                    <h4><?= $isEn ? 'Options' : 'گزینه‌ها' ?></h4>
                    <label class="filter-item switch-item">
                        <span><?= $isEn ? 'Discounted Only' : 'فقط کالاهای تخفیف‌دار' ?></span>
                        <span class="switch"><input type="checkbox" name="discount" value="1" <?= $f['onlyDiscount'] ? 'checked' : '' ?> onchange="this.form.submit()"><i></i></span>
                    </label>
                    <label class="filter-item switch-item">
                        <span><?= $isEn ? 'In-Stock Only' : 'فقط کالاهای موجود' ?></span>
                        <span class="switch"><input type="checkbox" name="instock" value="1" <?= $f['onlyStock'] ? 'checked' : '' ?> onchange="this.form.submit()"><i></i></span>
                    </label>
                </div>

                <a href="<?= $category ? '/category/' . e($category['slug']) : '/shop' ?>" class="btn btn-ghost btn-sm btn-block"><?= icon('x', 14) ?> <?= __('clear_filters', 'حذف همه فیلترها') ?></a>
            </form>
        </aside>

        <!-- لیست محصولات -->
        <div class="shop-content">
            <?php if (!empty($shopBanner)): ?>
                <?php
                    $sb = $shopBanner;
                    $sTitle = $isEn && !empty($sb['title_en']) ? $sb['title_en'] : $sb['title'];
                    $sSub = $isEn && !empty($sb['subtitle_en']) ? $sb['subtitle_en'] : $sb['subtitle'];
                    $sBadge = $isEn && !empty($sb['badge_en']) ? $sb['badge_en'] : ($sb['badge'] ?: ($isEn ? 'Featured' : 'پیشنهاد ویژه'));
                    $sBtn = $isEn && !empty($sb['button_text_en']) ? $sb['button_text_en'] : ($sb['button_text'] ?: ($isEn ? 'Shop Now' : 'مشاهده تخفیف‌ها'));
                    $sColor = $sb['color'] ?: '#6d28d9';
                ?>
                <div class="shop-header-banner" style="background: linear-gradient(120deg, <?= e($sColor) ?>, #1e1b4b 90%);">
                    <div class="shop-header-banner-text">
                        <span class="shop-header-banner-tag"><?= icon('gift', 15) ?> <?= e($sBadge) ?></span>
                        <h3><?= e($sTitle) ?></h3>
                        <?php if ($sSub): ?><p><?= e($sSub) ?></p><?php endif; ?>
                    </div>
                    <?php if ($sb['image']): ?>
                    <div class="shop-header-banner-art">
                        <img src="<?= e(banner_image($sb['image'])) ?>" alt="<?= e($sTitle) ?>">
                    </div>
                    <?php endif; ?>
                    <a href="<?= e($sb['link'] ?: '/shop?discount=1&sort=discount') ?>" class="btn btn-light btn-sm"><?= e($sBtn) ?></a>
                </div>
            <?php elseif (!$category && $q === ''): ?>
            <div class="promo-banner shop-cta">
                <div class="promo-banner-text">
                    <span class="promo-banner-tag"><?= icon('gift', 16) ?> <?= $isEn ? 'First Purchase?' : 'اولین خرید؟' ?></span>
                    <h3><?= $isEn ? 'Get 10% Off with code <b class="coupon-code">WELCOME10</b>' : 'با کد <b class="coupon-code">WELCOME10</b> ده درصد تخفیف بگیرید' ?></h3>
                </div>
                <a href="/shop?discount=1&sort=discount" class="btn btn-light"><?= $isEn ? 'View Discounts' : 'دیدن تخفیف‌ها' ?></a>
            </div>
            <?php endif; ?>
            <div class="shop-toolbar">
                <button class="btn btn-outline btn-sm mobile-only" id="filtersOpen"><?= icon('filter', 16) ?> <?= __('filters', 'فیلترها') ?></button>
                <div class="shop-count">
                    <strong><?= l_num($total) ?></strong> <?= $isEn ? 'products found' : 'کالا' ?>
                </div>
                <div class="sort-box">
                    <label><?= __('sort_by', 'مرتب‌سازی:') ?></label>
                    <select onchange="location.href='<?= e(url_with(['sort' => '__S__', 'page' => null])) ?>'.replace('__S__', this.value)">
                        <option value="newest"   <?= $sort === 'newest' ? 'selected' : '' ?>><?= __('sort_newest', 'جدیدترین') ?></option>
                        <option value="popular"  <?= $sort === 'popular' ? 'selected' : '' ?>><?= __('sort_popular', 'پرفروش‌ترین') ?></option>
                        <option value="rating"   <?= $sort === 'rating' ? 'selected' : '' ?>><?= $isEn ? 'Highest Rated' : 'بالاترین امتیاز' ?></option>
                        <option value="cheapest" <?= $sort === 'cheapest' ? 'selected' : '' ?>><?= __('sort_price_low', 'ارزان‌ترین') ?></option>
                        <option value="expensive" <?= $sort === 'expensive' ? 'selected' : '' ?>><?= __('sort_price_high', 'گران‌ترین') ?></option>
                        <option value="discount" <?= $sort === 'discount' ? 'selected' : '' ?>><?= __('sort_discount', 'بیشترین تخفیف') ?></option>
                    </select>
                </div>
            </div>

            <?php if ($products): ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <?php partial('partials/product-card', ['product' => $product]); ?>
                    <?php endforeach; ?>
                </div>
                <?php partial('partials/pagination', ['page' => $page, 'pages' => $pages]); ?>
            <?php else: ?>
                <div class="empty-state card">
                    <span class="empty-icon empty-svg"><?= icon('search', 48) ?></span>
                    <h3><?= __('no_results', 'محصولی پیدا نشد') ?></h3>
                    <p><?= $isEn ? 'No products matched your active filters. Try changing filter criteria or clearing keywords.' : 'با فیلترهای فعلی کالایی یافت نشد. فیلترها را تغییر دهید یا عبارت دیگری جستجو کنید.' ?></p>
                    <a href="/shop" class="btn btn-primary"><?= __('all_products', 'مشاهده همه محصولات') ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
