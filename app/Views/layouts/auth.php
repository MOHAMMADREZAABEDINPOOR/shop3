<?php
use App\Core\I18n;

$flashes = [];
foreach (['success', 'error', 'warning', 'info'] as $k) {
    if ($f = flash($k)) { $flashes[] = $f; }
}
$isEn = !I18n::isRtl();
$siteTitle = __('site_title', 'NextShop');
?>
<!DOCTYPE html>
<html lang="<?= I18n::locale() ?>" dir="<?= I18n::dir() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? $siteTitle) ?> | <?= e($siteTitle) ?></title>
    <?php if (!empty($meta_description)): ?><meta name="description" content="<?= e($meta_description) ?>"><?php endif; ?>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta name="theme-color" content="#5b21b6">
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/assets/img/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>">
    <script>
        (function(){try{var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.setAttribute('data-theme','dark');}}catch(e){}})();
    </script>
</head>
<body class="auth-body">
    <div class="auth-top-bar">
        <a href="/lang/<?= $isEn ? 'fa' : 'en' ?>" class="lang-switch-btn" title="<?= $isEn ? 'تغییر به زبان فارسی' : 'Switch to English' ?>" aria-label="Language">
            <?= icon('globe', 18) ?>
            <span><?= $isEn ? 'FA' : 'EN' ?></span>
        </a>
        <button class="icon-btn" id="themeToggle" aria-label="Toggle Theme" title="Theme">
            <span class="theme-sun"><?= icon('sun', 20) ?></span>
            <span class="theme-moon"><?= icon('moon', 20) ?></span>
        </button>
    </div>

    <div class="auth-bg">
        <div class="auth-orb orb-1"></div>
        <div class="auth-orb orb-2"></div>
        <div class="auth-orb orb-3"></div>
    </div>
    <div class="auth-wrapper">
        <a href="/" class="auth-logo">
            <span class="logo-mark"><?= icon('sparkles', 26) ?></span>
            <span class="logo-text"><?= e($siteTitle) ?></span>
        </a>
        <div class="auth-card">
            <?= $content ?>
        </div>
        <a href="/" class="auth-back"><?= icon($isEn ? 'arrow-left' : 'arrow-right', 16) ?> <?= __('back_to_shop', $isEn ? 'Back to Shop' : 'بازگشت به فروشگاه') ?></a>
    </div>
    <div class="toast-container" id="toasts"></div>
    <script>window.NS = { csrf: '<?= csrf_token() ?>', flashes: <?= json_encode($flashes, JSON_UNESCAPED_UNICODE) ?> };</script>
    <script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/app.js') ?>"></script>
</body>
</html>
