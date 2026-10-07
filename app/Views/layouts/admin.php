<?php
$user = auth();
$flashes = [];
foreach (['success', 'error', 'warning', 'info'] as $k) {
    if ($f = flash($k)) { $flashes[] = $f; }
}
$nav = [
    ['url' => '/admin',             'icon' => 'dashboard', 'label' => 'داشبورد'],
    ['url' => '/admin/products',    'icon' => 'box',       'label' => 'محصولات'],
    ['url' => '/admin/categories',  'icon' => 'layers',    'label' => 'دسته‌بندی‌ها'],
    ['url' => '/admin/banners',     'icon' => 'image',     'label' => 'پوسترها و بنرها'],
    ['url' => '/admin/orders',      'icon' => 'package',   'label' => 'سفارش‌ها'],
    ['url' => '/admin/users',       'icon' => 'users',     'label' => 'کاربران'],
    ['url' => '/admin/coupons',     'icon' => 'ticket',    'label' => 'کدهای تخفیف'],
    ['url' => '/admin/reviews',     'icon' => 'message',   'label' => 'نظرات'],
];
?>
<!DOCTYPE html>
<html lang="<?= \App\Core\I18n::locale() ?>" dir="<?= \App\Core\I18n::dir() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? __('admin_panel', 'پنل مدیریت')) ?> | <?= e(__('site_title', 'NextShop')) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta name="theme-color" content="#5b21b6">
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/assets/img/favicon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function(){try{var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.setAttribute('data-theme','dark');}}catch(e){}})();
    </script>
</head>
<body class="admin-body">
<aside class="admin-sidebar" id="adminSidebar">
    <a href="/admin" class="logo admin-logo">
        <span class="logo-mark"><?= icon('sparkles', 22) ?></span>
        <span class="logo-text"><?= e(__('site_title', 'NextShop')) ?></span>
    </a>
    <nav class="admin-nav">
        <?php foreach ($nav as $item): ?>
            <a href="<?= $item['url'] ?>" class="<?= is_active($item['url']) && ($item['url'] !== '/admin' || parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/admin') ? 'active' : '' ?>">
                <?= icon($item['icon'], 20) ?> <?= e($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-footer">
        <a href="/" class="admin-back-shop"><?= icon('external', 18) ?> مشاهده فروشگاه</a>
    </div>
</aside>
<div class="drawer-overlay" id="drawerOverlay"></div>

<div class="admin-main">
    <header class="admin-topbar">
        <button class="icon-btn mobile-only" id="menuToggle"><?= icon('menu', 24) ?></button>
        <h1 class="admin-title"><?= e($title ?? 'پنل مدیریت') ?></h1>
        <div class="admin-topbar-actions">
            <button class="icon-btn" id="themeToggle" aria-label="تغییر تم">
                <span class="theme-sun"><?= icon('sun', 20) ?></span>
                <span class="theme-moon"><?= icon('moon', 20) ?></span>
            </button>
            <div class="dropdown">
                <button class="icon-btn user-btn">
                    <span class="avatar"><?= e(mb_substr($user['name'], 0, 1)) ?></span>
                    <?= icon('chevron-down', 14) ?>
                </button>
                <div class="dropdown-menu">
                    <div class="dropdown-header"><strong><?= e($user['name']) ?></strong><small><?= e($user['email']) ?></small></div>
                    <a href="/account"><?= icon('user', 18) ?> حساب کاربری</a>
                    <form action="/logout" method="post"><?= csrf_field() ?>
                        <button type="submit" class="text-danger"><?= icon('logout', 18) ?> خروج</button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    <div class="admin-content">
        <?= $content ?>
    </div>
</div>

<div class="toast-container" id="toasts"></div>
<script>window.NS = { csrf: '<?= csrf_token() ?>', flashes: <?= json_encode($flashes, JSON_UNESCAPED_UNICODE) ?> };</script>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/app.js') ?>"></script>
<?= $scripts ?? '' ?>
</body>
</html>
