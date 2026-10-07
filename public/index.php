<?php
/**
 * نقطه ورود اصلی فروشگاه نکست‌شاپ
 */
require_once __DIR__ . '/../app/bootstrap.php';


use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ShopController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\AuthController;
use App\Controllers\AccountController;
use App\Controllers\ReviewController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\Admin\CouponController as AdminCouponController;
use App\Controllers\Admin\BannerController as AdminBannerController;

$router = new Router();

// ---------- صفحات عمومی ----------
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->get('/privacy', [HomeController::class, 'privacy']);
$router->get('/terms', [HomeController::class, 'terms']);
$router->get('/sitemap.xml', [HomeController::class, 'sitemap']);
$router->post('/newsletter', [HomeController::class, 'newsletter']);

// ---------- تغییر زبان ----------
$router->get('/lang/{code}', function (string $code) {
    \App\Core\I18n::setLocale($code);
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    $parsed = parse_url($referer);
    $path = $parsed['path'] ?? '/';
    if (!empty($parsed['query'])) {
        parse_str($parsed['query'], $q);
        unset($q['lang']);
        $query = http_build_query($q);
        $redirectUrl = $path . ($query ? '?' . $query : '');
    } else {
        $redirectUrl = $path;
    }
    header('Location: ' . $redirectUrl);
    exit;
});

// ---------- فروشگاه ----------
$router->get('/shop', [ShopController::class, 'index']);
$router->get('/category/{slug}', [ShopController::class, 'category']);
$router->get('/product/{slug}', [ShopController::class, 'show']);
$router->get('/search', [ShopController::class, 'search']);
$router->get('/api/search', [ShopController::class, 'apiSearch']);

// ---------- سبد خرید ----------
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/coupon', [CartController::class, 'coupon']);
$router->post('/cart/coupon/remove', [CartController::class, 'removeCoupon']);
$router->get('/api/cart', [CartController::class, 'summary']);

// ---------- تسویه حساب و پرداخت ----------
$router->get('/checkout', [CheckoutController::class, 'index'], ['auth']);
$router->post('/checkout', [CheckoutController::class, 'place'], ['auth']);
$router->get('/payment/{code}', [CheckoutController::class, 'payment'], ['auth']);
$router->post('/payment/{code}/verify', [CheckoutController::class, 'verify'], ['auth']);
$router->get('/order/success/{code}', [CheckoutController::class, 'success'], ['auth']);
$router->get('/track', [CheckoutController::class, 'track']);

// ---------- احراز هویت ----------
$router->get('/login', [AuthController::class, 'loginForm'], ['guest']);
$router->post('/login', [AuthController::class, 'login'], ['guest']);
$router->get('/register', [AuthController::class, 'registerForm'], ['guest']);
$router->post('/register', [AuthController::class, 'register'], ['guest']);
$router->post('/logout', [AuthController::class, 'logout']);

// ---------- حساب کاربری ----------
$router->get('/account', [AccountController::class, 'index'], ['auth']);
$router->post('/account/profile', [AccountController::class, 'updateProfile'], ['auth']);
$router->post('/account/password', [AccountController::class, 'updatePassword'], ['auth']);
$router->get('/account/orders', [AccountController::class, 'orders'], ['auth']);
$router->get('/account/orders/{code}', [AccountController::class, 'order'], ['auth']);
$router->get('/account/wishlist', [AccountController::class, 'wishlist'], ['auth']);
$router->post('/wishlist/toggle', [AccountController::class, 'toggleWishlist'], ['auth']);
$router->post('/review', [ReviewController::class, 'store'], ['auth']);

// ---------- پنل مدیریت ----------
$router->get('/admin', [DashboardController::class, 'index'], ['admin']);
$router->get('/admin/products', [AdminProductController::class, 'index'], ['admin']);
$router->get('/admin/products/create', [AdminProductController::class, 'create'], ['admin']);
$router->post('/admin/products', [AdminProductController::class, 'store'], ['admin']);
$router->get('/admin/products/{id}/edit', [AdminProductController::class, 'edit'], ['admin']);
$router->post('/admin/products/{id}', [AdminProductController::class, 'update'], ['admin']);
$router->post('/admin/products/{id}/delete', [AdminProductController::class, 'delete'], ['admin']);
$router->post('/admin/products/{id}/toggle', [AdminProductController::class, 'toggle'], ['admin']);

$router->get('/admin/categories', [AdminCategoryController::class, 'index'], ['admin']);
$router->post('/admin/categories', [AdminCategoryController::class, 'store'], ['admin']);
$router->post('/admin/categories/{id}', [AdminCategoryController::class, 'update'], ['admin']);
$router->post('/admin/categories/{id}/delete', [AdminCategoryController::class, 'delete'], ['admin']);

$router->get('/admin/orders', [AdminOrderController::class, 'index'], ['admin']);
$router->get('/admin/orders/{id}', [AdminOrderController::class, 'show'], ['admin']);
$router->post('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus'], ['admin']);

$router->get('/admin/users', [AdminUserController::class, 'index'], ['admin']);
$router->post('/admin/users/{id}/role', [AdminUserController::class, 'updateRole'], ['admin']);
$router->post('/admin/users/{id}/delete', [AdminUserController::class, 'delete'], ['admin']);

$router->get('/admin/coupons', [AdminCouponController::class, 'index'], ['admin']);
$router->post('/admin/coupons', [AdminCouponController::class, 'store'], ['admin']);
$router->post('/admin/coupons/{id}/toggle', [AdminCouponController::class, 'toggle'], ['admin']);
$router->post('/admin/coupons/{id}/delete', [AdminCouponController::class, 'delete'], ['admin']);

$router->get('/admin/reviews', [DashboardController::class, 'reviews'], ['admin']);
$router->post('/admin/reviews/{id}/toggle', [DashboardController::class, 'toggleReview'], ['admin']);
$router->post('/admin/reviews/{id}/delete', [DashboardController::class, 'deleteReview'], ['admin']);

$router->get('/admin/banners', [AdminBannerController::class, 'index'], ['admin']);
$router->get('/admin/banners/create', [AdminBannerController::class, 'create'], ['admin']);
$router->post('/admin/banners', [AdminBannerController::class, 'store'], ['admin']);
$router->get('/admin/banners/{id}/edit', [AdminBannerController::class, 'edit'], ['admin']);
$router->post('/admin/banners/{id}', [AdminBannerController::class, 'update'], ['admin']);
$router->post('/admin/banners/{id}/delete', [AdminBannerController::class, 'delete'], ['admin']);
$router->post('/admin/banners/{id}/toggle', [AdminBannerController::class, 'toggle'], ['admin']);

// ---------- ۴۰۴ ----------
$router->notFound(function () {
    http_response_code(404);
    view('errors/404', ['title' => 'صفحه پیدا نشد']);
});

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (\Throwable $e) {
    http_response_code(500);
    if (config('debug')) {
        echo '<pre style="direction:ltr;text-align:left;padding:20px;background:#111;color:#f88;font:13px/1.6 monospace">'
            . e($e->getMessage()) . "\n\n" . e($e->getTraceAsString()) . '</pre>';
    } else {
        view('errors/500', ['title' => 'خطای سرور']);
    }
}
