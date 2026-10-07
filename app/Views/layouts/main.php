<?php
use App\Core\I18n;

$categoriesNav = \App\Core\Database::fetchAll("SELECT * FROM categories ORDER BY sort_order");
$cartCount = cart()->count();
$user = auth();
$flashes = [];
foreach (['success', 'error', 'warning', 'info'] as $k) {
    if ($f = flash($k)) { $flashes[] = $f; }
}
$isEn = !I18n::isRtl();
$pageTitle = $title ?? ($isEn ? 'NextShop' : 'نکست‌شاپ');
$metaDesc = $meta_description ?? ($isEn ? 'NextShop — Premium Online Shopping' : config('app_name') . '؛ ' . config('tagline'));
$ogImage = $og_image ?? rtrim((string)config('app_url'), '/') . '/assets/img/og-cover.jpg';
$contact = config('contact', []);
$gaId = config('marketing.ga_id', '');
?>
<!DOCTYPE html>
<html lang="<?= I18n::locale() ?>" dir="<?= I18n::dir() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($metaDesc) ?>">
    <link rel="canonical" href="<?= e(canonical_url()) ?>">
    <?php if (!empty($noindex)): ?><meta name="robots" content="noindex, nofollow"><?php endif; ?>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta name="theme-color" content="#5b21b6">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="<?= $isEn ? 'en_US' : 'fa_IR' ?>">
    <meta property="og:site_name" content="<?= e(__('site_title', 'NextShop')) ?>">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($metaDesc) ?>">
    <meta property="og:url" content="<?= e(canonical_url()) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle) ?>">
    <meta name="twitter:description" content="<?= e($metaDesc) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/assets/img/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>">
    <script>
        (function(){try{var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.setAttribute('data-theme','dark');}}catch(e){}})();
        window.NS_CONSENT = (function(){try{return localStorage.getItem('ns_consent')==='all';}catch(e){return false;}})();
    </script>
    <?php if ($gaId): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($gaId) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        if (window.NS_CONSENT) { gtag('config', '<?= e($gaId) ?>'); }
        window.addEventListener('ns:consent', function(){ gtag('config', '<?= e($gaId) ?>'); });
    </script>
    <?php endif; ?>
</head>
<body>
<div class="promo-bar">
    <div class="container">
        <span><?= icon('truck', 16) ?> <?= __('promo_free_shipping', 'ارسال رایگان', ['amount' => price(config('free_shipping_threshold'))]) ?></span>
        <span class="promo-sep">|</span>
        <span><?= icon('gift', 16) ?> <?= __('promo_discount_code', 'با کد تخفیف', ['code' => 'WELCOME10']) ?></span>
    </div>
</div>

<header class="site-header">
    <div class="container header-inner">
        <button class="icon-btn mobile-only" id="menuToggle" aria-label="منو"><?= icon('menu', 24) ?></button>

        <a href="/" class="logo" aria-label="<?= e(__('site_title', 'NextShop')) ?>">
            <span class="logo-mark"><?= icon('sparkles', 22) ?></span>
            <span class="logo-text"><?= e(__('site_title', 'NextShop')) ?></span>
        </a>

        <form action="/search" method="get" class="search-form" id="searchForm" autocomplete="off" role="search">
            <span class="search-icon"><?= icon('search', 20) ?></span>
            <input type="search" name="q" id="searchInput" placeholder="<?= e(__('search_placeholder', 'جستجو در میان هزاران محصول...')) ?>" value="<?= e($q ?? '') ?>" aria-label="<?= e(__('search', 'جستجو')) ?>" maxlength="100">
            <button type="submit" class="search-btn"><?= __('search', 'جستجو') ?></button>
            <div class="search-results" id="searchResults" aria-live="polite"></div>
        </form>

        <div class="header-actions">
            <!-- سوییچر زبان انگلیسی / فارسی -->
            <a href="/lang/<?= $isEn ? 'fa' : 'en' ?>" class="lang-switch-btn" title="<?= $isEn ? 'تغییر به زبان فارسی' : 'Switch to English' ?>" aria-label="Language">
                <?= icon('globe', 18) ?>
                <span><?= $isEn ? 'FA' : 'EN' ?></span>
            </a>

            <button class="icon-btn" id="themeToggle" aria-label="تغییر تم" title="تغییر تم">
                <span class="theme-sun"><?= icon('sun', 22) ?></span>
                <span class="theme-moon"><?= icon('moon', 22) ?></span>
            </button>

            <?php if ($user): ?>
                <div class="dropdown">
                    <button class="icon-btn user-btn" aria-label="<?= __('my_account', 'حساب کاربری') ?>">
                        <span class="avatar"><?= e(mb_substr($user['name'], 0, 1)) ?></span>
                        <span class="desktop-only user-name"><?= e(explode(' ', $user['name'])[0]) ?></span>
                        <?= icon('chevron-down', 16, 'desktop-only') ?>
                    </button>
                    <div class="dropdown-menu">
                        <div class="dropdown-header">
                            <strong><?= e($user['name']) ?></strong>
                            <small><?= e($user['email']) ?></small>
                        </div>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="/admin"><?= icon('dashboard', 18) ?> <?= __('admin_panel', 'پنل مدیریت') ?></a>
                        <?php endif; ?>
                        <a href="/account"><?= icon('user', 18) ?> <?= __('my_account', 'حساب کاربری') ?></a>
                        <a href="/account/orders"><?= icon('package', 18) ?> <?= __('my_orders', 'سفارش‌های من') ?></a>
                        <a href="/account/wishlist"><?= icon('heart', 18) ?> <?= __('wishlist', 'علاقه‌مندی‌ها') ?></a>
                        <form action="/logout" method="post"><?= csrf_field() ?>
                            <button type="submit" class="text-danger"><?= icon('logout', 18) ?> <?= __('logout', 'خروج') ?></button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="btn btn-outline btn-sm desktop-only"><?= icon('user', 18) ?> <?= __('login_register', 'ورود / ثبت‌نام') ?></a>
                <a href="/login" class="icon-btn mobile-only" aria-label="<?= __('login', 'ورود') ?>"><?= icon('user', 22) ?></a>
            <?php endif; ?>

            <a href="/cart" class="icon-btn cart-btn" aria-label="<?= __('cart', 'سبد خرید') ?>">
                <?= icon('cart', 22) ?>
                <span class="badge" id="cartBadge" <?= $cartCount ? '' : 'hidden' ?>><?= l_num($cartCount) ?></span>
            </a>
        </div>
    </div>

    <nav class="main-nav" aria-label="ناوبری اصلی">
        <div class="container nav-inner">
            <div class="dropdown cat-dropdown">
                <button class="nav-link cat-toggle" type="button"><?= icon('grid', 18) ?> <?= __('categories', 'دسته‌بندی‌ها') ?> <?= icon('chevron-down', 14) ?></button>
                <div class="dropdown-menu cat-menu">
                    <?php foreach ($categoriesNav as $c): ?>
                        <a href="/category/<?= e($c['slug']) ?>">
                            <span class="cat-icon" style="--c:<?= e($c['color']) ?>"><?= icon($c['icon'] ?? 'box', 16) ?></span> 
                            <?= e(category_name($c)) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="/" class="nav-link <?= is_active('/') ? 'active' : '' ?>"><?= __('home', 'خانه') ?></a>
            <a href="/shop" class="nav-link <?= is_active('/shop') ? 'active' : '' ?>"><?= __('all_products', 'همه محصولات') ?></a>
            <a href="/shop?discount=1&sort=discount" class="nav-link hot"><?= icon('percent', 16) ?> <?= __('special_offers', 'پیشنهاد ویژه') ?></a>
            <a href="/track" class="nav-link <?= is_active('/track') ? 'active' : '' ?>"><?= __('track_order', 'پیگیری سفارش') ?></a>
            <a href="/about" class="nav-link <?= is_active('/about') ? 'active' : '' ?>"><?= __('about_us', 'درباره ما') ?></a>
            <a href="/contact" class="nav-link <?= is_active('/contact') ? 'active' : '' ?>"><?= __('contact_us', 'تماس با ما') ?></a>
            <span class="nav-spacer"></span>
            <span class="nav-support desktop-only"><?= icon('headphones', 18) ?> <?= __('customer_support', 'پشتیبانی') ?>: <b dir="ltr"><?= l_num($contact['phone'] ?? '') ?></b></span>
        </div>
    </nav>
</header>

<!-- منوی موبایل -->
<div class="drawer-overlay" id="drawerOverlay"></div>
<aside class="drawer" id="mobileDrawer" aria-label="منوی موبایل">
    <div class="drawer-header">
        <a href="/" class="logo"><span class="logo-mark"><?= icon('sparkles', 20) ?></span><span class="logo-text"><?= e(__('site_title', 'NextShop')) ?></span></a>
        <button class="icon-btn" id="drawerClose" aria-label="بستن"><?= icon('x', 22) ?></button>
    </div>
    <nav class="drawer-nav">
        <a href="/"><?= icon('home', 18) ?> <?= __('home', 'خانه') ?></a>
        <a href="/shop"><?= icon('grid', 18) ?> <?= __('all_products', 'همه محصولات') ?></a>
        <a href="/shop?discount=1&sort=discount"><?= icon('percent', 18) ?> <?= __('special_offers', 'پیشنهاد ویژه') ?></a>
        <div class="drawer-section"><?= __('categories', 'دسته‌بندی‌ها') ?></div>
        <?php foreach ($categoriesNav as $c): ?>
            <a href="/category/<?= e($c['slug']) ?>">
                <span class="cat-icon" style="--c:<?= e($c['color']) ?>"><?= icon($c['icon'] ?? 'box', 16) ?></span> 
                <?= e(category_name($c)) ?>
            </a>
        <?php endforeach; ?>
        <div class="drawer-section"><?= __('my_account', 'حساب کاربری') ?></div>
        <?php if ($user): ?>
            <a href="/account"><?= icon('user', 18) ?> <?= __('my_account', 'حساب کاربری') ?></a>
            <a href="/account/orders"><?= icon('package', 18) ?> <?= __('my_orders', 'سفارش‌های من') ?></a>
            <a href="/account/wishlist"><?= icon('heart', 18) ?> <?= __('wishlist', 'علاقه‌مندی‌ها') ?></a>
            <?php if ($user['role'] === 'admin'): ?><a href="/admin"><?= icon('dashboard', 18) ?> <?= __('admin_panel', 'پنل مدیریت') ?></a><?php endif; ?>
        <?php else: ?>
            <a href="/login"><?= icon('user', 18) ?> <?= __('login_register', 'ورود / ثبت‌نام') ?></a>
        <?php endif; ?>
        <a href="/track"><?= icon('truck', 18) ?> <?= __('track_order', 'پیگیری سفارش') ?></a>
        <a href="/about"><?= icon('info', 18) ?> <?= __('about_us', 'درباره ما') ?></a>
        <a href="/contact"><?= icon('phone', 18) ?> <?= __('contact_us', 'تماس با ما') ?></a>
    </nav>
</aside>

<main class="site-main">
    <?= $content ?>
</main>

<?= $sticky_cta ?? '' ?>

<footer class="site-footer">
    <div class="container">
        <div class="footer-features">
            <div class="feature"><?= icon('truck', 28) ?><div><strong><?= __('fast_delivery_title', 'ارسال سریع') ?></strong><span><?= __('fast_delivery_desc', 'تحویل اکسپرس') ?></span></div></div>
            <div class="feature"><?= icon('shield', 28) ?><div><strong><?= __('original_guarantee_title', 'ضمانت اصالت') ?></strong><span><?= __('original_guarantee_desc', 'تضمین اورجینال بودن کالا') ?></span></div></div>
            <div class="feature"><?= icon('refresh', 28) ?><div><strong><?= __('money_back_title', '۷ روز بازگشت') ?></strong><span><?= __('money_back_desc', 'بدون قید و شرط') ?></span></div></div>
            <div class="feature"><?= icon('headphones', 28) ?><div><strong><?= __('support_247_title', 'پشتیبانی ۲۴/۷') ?></strong><span><?= __('support_247_desc', 'همیشه در کنار شما') ?></span></div></div>
            <div class="feature"><?= icon('credit-card', 28) ?><div><strong><?= __('secure_payment', 'پرداخت امن') ?></strong><span><?= __('secure_payment', 'درگاه بانکی معتبر') ?></span></div></div>
        </div>
        <div class="footer-grid">
            <div class="footer-about">
                <a href="/" class="logo"><span class="logo-mark"><?= icon('sparkles', 20) ?></span><span class="logo-text"><?= e(__('site_title', 'NextShop')) ?></span></a>
                <p><?= e(__('footer_about', 'NextShop is the premier online destination for electronics and tech.')) ?></p>
                <div class="footer-contact">
                    <span><?= icon('phone', 16) ?> <?= __('customer_support', 'پشتیبانی') ?>: <b dir="ltr"><?= l_num($contact['phone'] ?? '') ?></b></span>
                    <span><?= icon('mail', 16) ?> <span dir="ltr"><?= e($contact['email'] ?? '') ?></span></span>
                    <span><?= icon('map-pin', 16) ?> <?= e($contact['address'] ?? '') ?></span>
                    <span><?= icon('clock', 16) ?> <?= e($contact['work_hours'] ?? '') ?></span>
                </div>
            </div>
            <div>
                <h4><?= __('quick_links', 'دسترسی سریع') ?></h4>
                <a href="/shop"><?= __('all_products', 'همه محصولات') ?></a>
                <a href="/shop?discount=1"><?= __('special_offers', 'تخفیف‌ها') ?></a>
                <a href="/track"><?= __('track_order', 'پیگیری سفارش') ?></a>
                <a href="/account"><?= __('my_account', 'حساب کاربری') ?></a>
            </div>
            <div>
                <h4><?= __('about_us', 'خدمات مشتریان') ?></h4>
                <a href="/about"><?= __('about_us', 'درباره ما') ?></a>
                <a href="/contact"><?= __('contact_us', 'تماس با ما') ?></a>
                <a href="/privacy"><?= __('privacy_policy', 'حریم خصوصی') ?></a>
                <a href="/terms"><?= __('terms_of_service', 'قوانین و مقررات') ?></a>
            </div>
            <div class="footer-newsletter">
                <h4><?= __('newsletter_title', 'عضویت در خبرنامه') ?></h4>
                <p><?= __('newsletter_desc', 'از تخفیف‌ها و کالاهای جدید زودتر از همه باخبر شوید.') ?></p>
                <form id="newsletterForm" class="newsletter-form" novalidate>
                    <?= csrf_field() ?>
                    <?= honeypot_field() ?>
                    <input type="email" name="email" placeholder="<?= e(__('newsletter_placeholder', 'ایمیل شما')) ?>" required aria-label="Email" maxlength="190">
                    <button type="submit" class="btn btn-primary"><?= __('newsletter_button', 'عضویت') ?></button>
                </form>
                <div class="trust-badges">
                    <span class="trust"><?= icon('shield', 14) ?> <?= __('original_guarantee_title', 'نماد اعتماد') ?></span>
                    <span class="trust"><?= icon('credit-card', 14) ?> <?= __('secure_payment', 'پرداخت امن بانکی') ?></span>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span><?= __('all_rights_reserved', 'تمامی حقوق محفوظ است.', ['year' => l_num(date('Y'))]) ?></span>
            <span><a href="/privacy"><?= __('privacy_policy', 'حریم خصوصی') ?></a> · <a href="/terms"><?= __('terms_of_service', 'قوانین') ?></a></span>
        </div>
    </div>
</footer>

<div class="toast-container" id="toasts" aria-live="polite"></div>
<script>
    window.NS = { csrf: '<?= csrf_token() ?>', flashes: <?= json_encode($flashes, JSON_UNESCAPED_UNICODE) ?>, loggedIn: <?= $user ? 'true' : 'false' ?> };
</script>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/app.js') ?>"></script>
<?= $scripts ?? '' ?>
</body>
</html>
