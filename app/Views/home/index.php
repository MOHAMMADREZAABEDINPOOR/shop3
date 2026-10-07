<?php
use App\Core\I18n;

$isEn = !I18n::isRtl();
$heroProduct = $featured[0] ?? null;
$heroSecond = $deals[0] ?? null;
$arrowIcon = $isEn ? 'arrow-right' : 'arrow-left';
?>
<!-- هیرو -->
<?php if (!empty($heroBanners)): ?>
<section class="container hero-slider-wrap">
    <div class="hero-slider">
        <?php foreach ($heroBanners as $idx => $b): ?>
            <?php
                $bTitle = $isEn && !empty($b['title_en']) ? $b['title_en'] : $b['title'];
                $bSub = $isEn && !empty($b['subtitle_en']) ? $b['subtitle_en'] : $b['subtitle'];
                $bBadge = $isEn && !empty($b['badge_en']) ? $b['badge_en'] : ($b['badge'] ?: ($isEn ? 'Special Offer' : 'فروش ویژه'));
                $bBtn = $isEn && !empty($b['button_text_en']) ? $b['button_text_en'] : ($b['button_text'] ?: ($isEn ? 'Shop Now' : 'مشاهده و خرید'));
                $bBg = $b['color'] ?: '#4f46e5';
            ?>
            <div class="hero-slide <?= $idx === 0 ? 'active' : '' ?>" style="background: linear-gradient(135deg, <?= e($bBg) ?>, #0a0618 85%);">
                <div class="hero-slide-content">
                    <span class="hero-slide-badge"><?= icon('zap', 14) ?> <?= e($bBadge) ?></span>
                    <h2><?= e($bTitle) ?></h2>
                    <p><?= e($bSub) ?></p>
                    <div class="hero-actions">
                        <a href="<?= e($b['link'] ?: '/shop') ?>" class="btn btn-primary btn-lg"><?= icon('cart', 18) ?> <?= e($bBtn) ?> <?= icon($arrowIcon, 16) ?></a>
                        <a href="/shop" class="btn btn-ghost btn-lg"><?= __('browse_all', 'مشاهده همه محصولات') ?></a>
                    </div>
                </div>
                <div class="hero-slide-visual">
                    <?php if ($b['image']): ?>
                        <img src="<?= e(banner_image($b['image'])) ?>" alt="<?= e($bTitle) ?>">
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($heroBanners) > 1): ?>
        <button class="hero-slider-nav prev" aria-label="Previous"><?= icon($isEn ? 'arrow-left' : 'arrow-right', 20) ?></button>
        <button class="hero-slider-nav next" aria-label="Next"><?= icon($isEn ? 'arrow-right' : 'arrow-left', 20) ?></button>
        <div class="hero-slider-dots">
            <?php foreach ($heroBanners as $idx => $b): ?>
                <button class="hero-slider-dot <?= $idx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php else: ?>
<section class="hero">
    <div class="hero-bg"><div class="hero-orb o1"></div><div class="hero-orb o2"></div><div class="hero-grid"></div></div>
    <div class="container hero-inner">
        <div class="hero-text">
            <span class="hero-badge"><?= icon('zap', 14) ?> <?= $isEn ? 'Special Season Sale — Up to 30% Off + Free Shipping' : 'فروش ویژه فصل — تا ۳۰ درصد تخفیف + ارسال رایگان' ?></span>
            <h1>
                <?php if ($isEn): ?>
                    Authentic Shopping from a Store<br><span class="gradient-text">You Can Always Trust</span>
                <?php else: ?>
                    خرید مطمئن از فروشگاهی<br><span class="gradient-text">که به آن اعتماد دارید</span>
                <?php endif; ?>
            </h1>
            <p>
                <?php if ($isEn): ?>
                    Over 70+ authentic tech and lifestyle products across 16 specialized categories; with 100% genuine guarantee, 7-day return policy and fast nationwide delivery.
                <?php else: ?>
                    بیش از ۷۰ کالای اورجینال در ۱۶ دسته‌بندی تخصصی؛ با ضمانت اصالت، ۷ روز مهلت بازگشت و ارسال سریع به سراسر کشور.
                <?php endif; ?>
            </p>
            <div class="hero-actions">
                <a href="/shop" class="btn btn-primary btn-lg"><?= icon('cart', 20) ?> <?= __('start_shopping', 'شروع خرید') ?></a>
                <a href="/shop?discount=1&sort=discount" class="btn btn-ghost btn-lg"><?= $isEn ? 'View Deals' : 'مشاهده تخفیف‌ها' ?> <?= icon($arrowIcon, 18) ?></a>
            </div>
            <div class="hero-stats">
                <div><strong><?= l_num('+70') ?></strong><span><?= $isEn ? 'Authentic Items' : 'کالای اورجینال' ?></span></div>
                <div><strong><?= l_num('16') ?></strong><span><?= $isEn ? 'Categories' : 'دسته‌بندی تخصصی' ?></span></div>
                <div><strong><?= l_num('98%') ?></strong><span><?= $isEn ? 'Customer Satisfaction' : 'رضایت مشتریان' ?></span></div>
            </div>
        </div>
        <?php if ($heroProduct): ?>
        <div class="hero-card">
            <div class="hero-card-glow"></div>
            <a href="/product/<?= e($heroProduct['slug']) ?>" class="hero-product">
                <img src="<?= e(product_image($heroProduct['image'])) ?>" alt="<?= e(product_name($heroProduct)) ?>">
                <div class="hero-product-info">
                    <span class="hero-product-tag"><?= $isEn ? "Editor's Choice" : 'پرفروش‌ترین هفته' ?></span>
                    <h3><?= e(product_name($heroProduct)) ?></h3>
                    <div class="hero-product-price">
                        <?php if ($heroProduct['discount_price']): ?><del><?= price($heroProduct['price']) ?></del><?php endif; ?>
                        <strong><?= price(final_price($heroProduct)) ?></strong>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- نوار خدمات -->
<section class="section strip-section">
    <div class="container">
        <div class="service-strip">
            <div class="service-item"><?= icon('truck', 26) ?><div><strong><?= __('fast_delivery_title', 'ارسال سریع سراسری') ?></strong><span><?= __('fast_delivery_desc', 'تحویل اکسپرس') ?></span></div></div>
            <div class="service-item"><?= icon('shield', 26) ?><div><strong><?= __('original_guarantee_title', 'ضمانت اصالت کالا') ?></strong><span><?= __('original_guarantee_desc', 'تضمین اورجینال بودن') ?></span></div></div>
            <div class="service-item"><?= icon('refresh', 26) ?><div><strong><?= __('money_back_title', '۷ روز مهلت بازگشت') ?></strong><span><?= __('money_back_desc', 'بدون قید و شرط') ?></span></div></div>
            <div class="service-item"><?= icon('credit-card', 26) ?><div><strong><?= __('secure_payment', 'پرداخت امن') ?></strong><span><?= __('secure_payment', 'درگاه معتبر بانکی') ?></span></div></div>
        </div>
    </div>
</section>

<!-- دسته‌بندی‌ها -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2><?= __('categories', 'خرید بر اساس دسته‌بندی') ?></h2>
                <p><?= $isEn ? '16 specialized categories from smartphones to PC parts and home tech' : '۱۶ دسته تخصصی، از موبایل تا قطعات کامپیوتر و ابزار' ?></p>
            </div>
            <a href="/shop" class="btn btn-outline btn-sm"><?= __('browse_all', 'مشاهده همه کالاها') ?> <?= icon($arrowIcon, 16) ?></a>
        </div>
        <div class="category-grid cols-6">
            <?php foreach ($categories as $c): ?>
                <a href="/category/<?= e($c['slug']) ?>" class="category-card" style="--c:<?= e($c['color']) ?>">
                    <span class="category-icon"><?= icon($c['icon'] ?? 'box', 26) ?></span>
                    <strong><?= e(category_name($c)) ?></strong>
                    <span class="category-count"><?= l_num($c['product_count']) ?> <?= $isEn ? 'items' : 'کالا' ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- پیشنهاد شگفت‌انگیز با شمارش معکوس -->
<?php if ($deals): ?>
<section class="section deals-section">
    <div class="container">
        <div class="deals-box">
            <div class="section-head">
                <div>
                    <h2><?= icon('zap', 22) ?> <?= __('deal_of_the_day', 'پیشنهاد شگفت‌انگیز') ?></h2>
                    <p><?= $isEn ? 'Top discounts — Limited stock available' : 'بیشترین تخفیف‌ها — موجودی محدود' ?></p>
                </div>
                <div class="deals-timer" id="dealsTimer" data-hours="14">
                    <span class="dt-box"><b id="dtH"><?= l_num('14') ?></b><small><?= __('hours', 'ساعت') ?></small></span>
                    <span class="dt-sep">:</span>
                    <span class="dt-box"><b id="dtM"><?= l_num('32') ?></b><small><?= __('minutes', 'دقیقه') ?></small></span>
                    <span class="dt-sep">:</span>
                    <span class="dt-box"><b id="dtS"><?= l_num('10') ?></b><small><?= __('seconds', 'ثانیه') ?></small></span>
                </div>
            </div>
            <div class="product-grid deals-grid">
                <?php foreach ($deals as $product): ?>
                    <?php partial('partials/product-card', ['product' => $product]); ?>
                <?php endforeach; ?>
            </div>
            <div class="center mt-3"><a href="/shop?discount=1&sort=discount" class="btn btn-outline"><?= __('special_offers', 'مشاهده همه تخفیف‌ها') ?> <?= icon($arrowIcon, 16) ?></a></div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- بنرهای تبلیغاتی ته‌کاشی / پویا -->
<section class="section">
    <div class="container">
        <div class="promo-grid">
            <?php if (!empty($gridBanners)): ?>
                <?php foreach ($gridBanners as $b): ?>
                    <?php
                        $bTitle = $isEn && !empty($b['title_en']) ? $b['title_en'] : $b['title'];
                        $bBadge = $isEn && !empty($b['badge_en']) ? $b['badge_en'] : ($b['badge'] ?: ($isEn ? 'Special' : 'ویژه'));
                        $bBtn = $isEn && !empty($b['button_text_en']) ? $b['button_text_en'] : ($b['button_text'] ?: ($isEn ? 'Shop Now' : 'مشاهده و خرید'));
                        $bgColor = $b['color'] ?: '#2563eb';
                    ?>
                    <a href="<?= e($b['link'] ?: '/shop') ?>" class="promo-poster-card" style="background: linear-gradient(135deg, <?= e($bgColor) ?>, #0b0f19 90%);">
                        <div class="promo-poster-text">
                            <span class="promo-poster-tag"><?= e($bBadge) ?></span>
                            <h3><?= e($bTitle) ?></h3>
                            <span class="promo-poster-link"><?= e($bBtn) ?> <?= icon($arrowIcon, 16) ?></span>
                        </div>
                        <div class="promo-poster-art">
                            <?php if ($b['image']): ?>
                                <img src="<?= e(banner_image($b['image'])) ?>" alt="<?= e($bTitle) ?>">
                            <?php else: ?>
                                <?= icon('zap', 64) ?>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <a href="/category/mobile" class="promo-tile tile-blue">
                    <div>
                        <span class="promo-tag"><?= $isEn ? 'Latest Flagships' : 'جدیدترین‌ها' ?></span>
                        <h3><?= $isEn ? 'Smartphones & Tablets<br>Official Warranty' : 'موبایل و تبلت<br>با گارانتی شرکتی' ?></h3>
                        <span class="promo-link"><?= __('shop_now', 'مشاهده و خرید') ?> <?= icon($arrowIcon, 16) ?></span>
                    </div>
                    <div class="promo-art"><?= icon('mobile', 84) ?></div>
                </a>
                <a href="/category/gaming" class="promo-tile tile-dark">
                    <div>
                        <span class="promo-tag"><?= $isEn ? 'Gaming World' : 'دنیای بازی' ?></span>
                        <h3><?= $isEn ? 'Consoles & Pro<br>Gaming Hardware' : 'کنسول و تجهیزات<br>گیمینگ حرفه‌ای' ?></h3>
                        <span class="promo-link"><?= __('shop_now', 'مشاهده و خرید') ?> <?= icon($arrowIcon, 16) ?></span>
                    </div>
                    <div class="promo-art"><?= icon('gaming', 84) ?></div>
                </a>
                <a href="/category/camera" class="promo-tile tile-green">
                    <div>
                        <span class="promo-tag"><?= $isEn ? 'Pro Visual' : 'عکاسی حرفه‌ای' ?></span>
                        <h3><?= $isEn ? 'Cameras & Drones<br>Leading Brands' : 'دوربین و هلی‌شات<br>برندهای معتبر' ?></h3>
                        <span class="promo-link"><?= __('shop_now', 'مشاهده و خرید') ?> <?= icon($arrowIcon, 16) ?></span>
                    </div>
                    <div class="promo-art"><?= icon('camera', 84) ?></div>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- بنر میانی کد تخفیف / پویا -->
<section class="section">
    <div class="container">
        <?php if (!empty($middleBanner)): ?>
            <?php
                $mb = $middleBanner;
                $mTitle = $isEn && !empty($mb['title_en']) ? $mb['title_en'] : $mb['title'];
                $mSub = $isEn && !empty($mb['subtitle_en']) ? $mb['subtitle_en'] : $mb['subtitle'];
                $mBadge = $isEn && !empty($mb['badge_en']) ? $mb['badge_en'] : ($mb['badge'] ?: ($isEn ? 'Discount' : 'تخفیف ویژه'));
                $mBtn = $isEn && !empty($mb['button_text_en']) ? $mb['button_text_en'] : ($mb['button_text'] ?: ($isEn ? 'Shop Now' : 'همین حالا خرید کنید'));
                $mColor = $mb['color'] ?: '#4f46e5';
            ?>
            <div class="promo-banner" style="background: linear-gradient(120deg, <?= e($mColor) ?>, #1e1b4b 90%);">
                <div class="promo-banner-text">
                    <span class="promo-banner-tag"><?= icon('gift', 16) ?> <?= e($mBadge) ?></span>
                    <h3><?= e($mTitle) ?></h3>
                    <p><?= e($mSub) ?></p>
                </div>
                <?php if ($mb['image']): ?>
                <div style="max-width: 140px; margin: 0 10px;">
                    <img src="<?= e(banner_image($mb['image'])) ?>" alt="<?= e($mTitle) ?>" style="max-height: 90px; filter: drop-shadow(0 6px 14px rgba(0,0,0,0.3));">
                </div>
                <?php endif; ?>
                <a href="<?= e($mb['link'] ?: '/shop') ?>" class="btn btn-light btn-lg"><?= e($mBtn) ?></a>
            </div>
        <?php else: ?>
            <div class="promo-banner">
                <div class="promo-banner-text">
                    <span class="promo-banner-tag"><?= icon('gift', 16) ?> <?= $isEn ? 'Welcome Coupon Code' : 'کد تخفیف اولین خرید' ?></span>
                    <h3><?= $isEn ? 'Get 10% Off with code <b class="coupon-code">WELCOME10</b>' : 'با کد <b class="coupon-code">WELCOME10</b> ده درصد تخفیف بگیرید' ?></h3>
                    <p><?= __('promo_free_shipping', 'ارسال رایگان', ['amount' => price(config('free_shipping_threshold'))]) ?>. <?= $isEn ? 'Use code MEGA20 for orders above 5,000,000 Tomans.' : 'کد MEGA20 برای خریدهای بالای ۵ میلیون.' ?></p>
                </div>
                <a href="/shop" class="btn btn-light btn-lg"><?= __('shop_now', 'همین حالا خرید کنید') ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- محصولات ویژه -->
<?php if ($featured): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2><?= __('featured_products', 'پیشنهاد سردبیر') ?></h2>
                <p><?= $isEn ? 'Expertly curated top products from our collection' : 'منتخب کارشناسان فروشگاه از میان پرفروش‌ترین‌ها' ?></p>
            </div>
            <a href="/shop?sort=popular" class="btn btn-outline btn-sm"><?= __('view_all', 'مشاهده همه') ?> <?= icon($arrowIcon, 16) ?></a>
        </div>
        <div class="product-grid">
            <?php foreach ($featured as $product): ?>
                <?php partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- بنرهای دوگانه تبلیغاتی -->
<?php if (!empty($dualBanners)): ?>
<section class="section">
    <div class="container">
        <div class="dual-banners">
            <?php foreach ($dualBanners as $db): ?>
                <?php
                    $dTitle = $isEn && !empty($db['title_en']) ? $db['title_en'] : $db['title'];
                    $dSub = $isEn && !empty($db['subtitle_en']) ? $db['subtitle_en'] : $db['subtitle'];
                    $dBadge = $isEn && !empty($db['badge_en']) ? $db['badge_en'] : ($db['badge'] ?: ($isEn ? 'Featured' : 'پیشنهاد'));
                    $dBtn = $isEn && !empty($db['button_text_en']) ? $db['button_text_en'] : ($db['button_text'] ?: ($isEn ? 'Explore' : 'مشاهده بیشتر'));
                    $dColor = $db['color'] ?: '#0284c7';
                ?>
                <a href="<?= e($db['link'] ?: '/shop') ?>" class="dual-banner-card" style="background: linear-gradient(130deg, <?= e($dColor) ?>, #0c0a1d 92%);">
                    <div class="dual-banner-text">
                        <span class="dual-banner-tag"><?= icon('zap', 14) ?> <?= e($dBadge) ?></span>
                        <h3><?= e($dTitle) ?></h3>
                        <p><?= e($dSub) ?></p>
                        <span class="btn btn-light btn-sm"><?= e($dBtn) ?> <?= icon($arrowIcon, 14) ?></span>
                    </div>
                    <div class="dual-banner-art">
                        <?php if ($db['image']): ?>
                            <img src="<?= e(banner_image($db['image'])) ?>" alt="<?= e($dTitle) ?>">
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- برندهای منتخب -->
<?php if (!empty($brands)): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2><?= $isEn ? 'Featured Brands' : 'برندهای منتخب' ?></h2>
                <p><?= $isEn ? 'Official partners and authorized warranties' : 'نمایندگی رسمی و ضمانت شرکتی' ?></p>
            </div>
        </div>
        <div class="brand-strip">
            <?php foreach ($brands as $b): ?>
                <a href="/shop?brand=<?= urlencode($b['brand']) ?>" class="brand-chip-lg">
                    <?= e($b['brand']) ?>
                    <small><?= l_num($b['cnt']) ?> <?= $isEn ? 'products' : 'کالا' ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- پرفروش‌ترین‌ها -->
<?php if (!empty($bestseller)): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2><?= __('best_sellers', 'پرفروش‌ترین‌های هفته') ?></h2>
                <p><?= $isEn ? 'Preferred choice of thousands of happy shoppers' : 'انتخاب هزاران مشتری فروشگاه' ?></p>
            </div>
            <a href="/shop?sort=popular" class="btn btn-outline btn-sm"><?= __('view_all', 'مشاهده همه') ?> <?= icon($arrowIcon, 16) ?></a>
        </div>
        <div class="product-grid">
            <?php foreach (array_slice($bestseller, 0, 4) as $product): ?>
                <?php partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- جدیدترین‌ها -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2><?= __('latest_products', 'تازه رسیده‌ها') ?></h2>
                <p><?= $isEn ? 'Brand new items added to our catalog' : 'جدیدترین کالاهای اضافه‌شده به فروشگاه' ?></p>
            </div>
            <a href="/shop" class="btn btn-outline btn-sm"><?= __('view_all', 'مشاهده همه') ?> <?= icon($arrowIcon, 16) ?></a>
        </div>
        <div class="product-grid">
            <?php foreach ($newest as $product): ?>
                <?php partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- نظر مشتریان -->
<section class="section">
    <div class="container">
        <div class="section-head center">
            <div>
                <span class="eyebrow"><?= $isEn ? 'Customer Stories' : 'اعتماد شما' ?></span>
                <h2><?= $isEn ? 'What Our Shoppers Say' : 'مشتریان چه می‌گویند' ?></h2>
                <p><?= $isEn ? 'Over 98% customer satisfaction rate' : 'بیش از ۹۸ درصد رضایت از خرید' ?></p>
            </div>
        </div>
        <div class="testi-grid">
            <div class="testi-card">
                <?= stars(5) ?>
                <blockquote><?= $isEn ? '"Order arrived in two days, packaging was pristine and the product was 100% authentic. One of the best online tech buying experiences."' : '«سفارشم دو روزه رسید، بسته‌بندی عالی و کالا کاملاً اورجینال بود. اولین بار بود انقدر راحت آنلاین خرید می‌کردم.»' ?></blockquote>
                <div class="testi-who">
                    <span class="avatar"><?= $isEn ? 'S' : 'س' ?></span>
                    <div><strong><?= $isEn ? 'Sarah Miller' : 'سارا محمدی' ?></strong><small><?= $isEn ? 'Mobile Buyer — Verified Purchase' : 'خریدار موبایل — اصفهان' ?></small></div>
                </div>
            </div>
            <div class="testi-card">
                <?= stars(5) ?>
                <blockquote><?= $isEn ? '"Prices were more competitive than anywhere else, and customer support was genuinely responsive. Hassle-free warranty."' : '«قیمت‌ها از همه‌جا بهتر بود و پشتیبانی واقعاً جواب می‌دهد. مرجوعی یک کالا را هم بدون هیچ سوالی قبول کردند.»' ?></blockquote>
                <div class="testi-who">
                    <span class="avatar"><?= $isEn ? 'A' : 'ع' ?></span>
                    <div><strong><?= $isEn ? 'Alex Reza' : 'علی رضایی' ?></strong><small><?= $isEn ? 'Laptop Buyer — Verified Purchase' : 'خریدار لپ‌تاپ — شیراز' ?></small></div>
                </div>
            </div>
            <div class="testi-card">
                <?= stars(4.5) ?>
                <blockquote><?= $isEn ? '"Incredible product variety from PC hardware to smart home devices. Order tracking is crystal clear and accurate."' : '«تنوع دسته‌بندی‌ها فوق‌العاده است؛ از سوپرمارکت تا ابزار همه‌چیز یکجاست. پیگیری سفارش هم خیلی شفاف است.»' ?></blockquote>
                <div class="testi-who">
                    <span class="avatar"><?= $isEn ? 'M' : 'م' ?></span>
                    <div><strong><?= $isEn ? 'Maria Karimi' : 'مریم کریمی' ?></strong><small><?= $isEn ? 'Smart Home Buyer — Verified Purchase' : 'خریدار لوازم خانگی — تهران' ?></small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- بنر پانورامای انتهای صفحه -->
<?php if (!empty($bottomBanner)): ?>
<?php
    $bb = $bottomBanner;
    $bbTitle = $isEn && !empty($bb['title_en']) ? $bb['title_en'] : $bb['title'];
    $bbSub = $isEn && !empty($bb['subtitle_en']) ? $bb['subtitle_en'] : $bb['subtitle'];
    $bbBadge = $isEn && !empty($bb['badge_en']) ? $bb['badge_en'] : ($bb['badge'] ?: ($isEn ? 'Grand Festival' : 'جشنواره بزرگ فروش'));
    $bbBtn = $isEn && !empty($bb['button_text_en']) ? $bb['button_text_en'] : ($bb['button_text'] ?: ($isEn ? 'Explore Campaign' : 'ورود به جشنواره و خرید'));
    $bbColor = $bb['color'] ?: '#ec4899';
?>
<section class="section">
    <div class="container">
        <div class="bottom-mega-banner" style="background: linear-gradient(120deg, <?= e($bbColor) ?>, #3b0764 55%, #050510 95%);">
            <div class="bottom-mega-content">
                <span class="bottom-mega-badge"><?= icon('zap', 15) ?> <?= e($bbBadge) ?></span>
                <h2><?= e($bbTitle) ?></h2>
                <p><?= e($bbSub) ?></p>
                <a href="<?= e($bb['link'] ?: '/shop') ?>" class="btn btn-light btn-lg"><?= e($bbBtn) ?> <?= icon($arrowIcon, 18) ?></a>
            </div>
            <div class="bottom-mega-art">
                <?php if ($bb['image']): ?>
                    <img src="<?= e(banner_image($bb['image'])) ?>" alt="<?= e($bbTitle) ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- چرا فروشگاه ما -->
<section class="section why-section">
    <div class="container">
        <div class="section-head center">
            <div>
                <h2><?= __('why_choose_us', 'چرا از ما خرید کنید؟') ?></h2>
                <p><?= $isEn ? 'High standards of a modern, reliable e-commerce store' : 'استانداردهای یک فروشگاه اینترنتی حرفه‌ای' ?></p>
            </div>
        </div>
        <div class="why-grid">
            <div class="why-card">
                <span class="why-icon why-svg"><?= icon('truck', 34) ?></span>
                <h3><?= __('fast_delivery_title', 'ارسال اکسپرس') ?></h3>
                <p><?= __('fast_delivery_desc', 'تحویل سریع در سراسر کشور با بسته‌بندی ایمن') ?></p>
            </div>
            <div class="why-card">
                <span class="why-icon why-svg"><?= icon('shield', 34) ?></span>
                <h3><?= __('original_guarantee_title', 'ضمانت اصالت') ?></h3>
                <p><?= __('original_guarantee_desc', 'تمامی کالاها اورجینال با گارانتی معتبر شرکتی') ?></p>
            </div>
            <div class="why-card">
                <span class="why-icon why-svg"><?= icon('refresh', 34) ?></span>
                <h3><?= __('money_back_title', '۷ روز بازگشت') ?></h3>
                <p><?= __('money_back_desc', 'در صورت عدم رضایت کالا را بازگردانید') ?></p>
            </div>
            <div class="why-card">
                <span class="why-icon why-svg"><?= icon('headphones', 34) ?></span>
                <h3><?= __('support_247_title', 'پشتیبانی ۲۴/۷') ?></h3>
                <p><?= __('support_247_desc', 'تیم پشتیبانی متخصص ما همواره پاسخگوی شماست') ?></p>
            </div>
        </div>
    </div>
</section>

<script>
(function(){
    var h = 14, m = 32, s = 10;
    var isRtl = <?= I18n::isRtl() ? 'true' : 'false' ?>;
    function pad(n){ return (n < 10 ? '0' : '') + n; }
    function fmt(n){
        var str = pad(n);
        if (!isRtl) return str;
        return String(str).replace(/\d/g, function(d){ return '۰۱۲۳۴۵۶۷۸۹'[d]; });
    }
    setInterval(function(){
        if (s > 0) { s--; }
        else { s = 59; if (m > 0) { m--; } else { m = 59; if (h > 0) { h--; } } }
        var H = document.getElementById('dtH'), M = document.getElementById('dtM'), S = document.getElementById('dtS');
        if (H) H.textContent = fmt(h);
        if (M) M.textContent = fmt(m);
        if (S) S.textContent = fmt(s);
    }, 1000);
})();
</script>
