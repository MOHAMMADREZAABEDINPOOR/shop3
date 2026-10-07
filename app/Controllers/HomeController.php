<?php
namespace App\Controllers;

use App\Core\Database as DB;

class HomeController
{
    public function index(): void
    {
        $categories = DB::fetchAll("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1) AS product_count FROM categories c ORDER BY sort_order");
        $featured   = DB::fetchAll("SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY sold DESC LIMIT 8");
        $newest     = DB::fetchAll("SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC, id DESC LIMIT 8");
        $bestseller = DB::fetchAll("SELECT * FROM products WHERE is_active = 1 ORDER BY sold DESC LIMIT 8");
        $deals      = DB::fetchAll("SELECT * FROM products WHERE is_active = 1 AND discount_price IS NOT NULL AND discount_price < price ORDER BY (price - discount_price) * 1.0 / price DESC LIMIT 6");
        $brands     = DB::fetchAll("SELECT brand, COUNT(*) AS cnt FROM products WHERE is_active = 1 AND brand IS NOT NULL GROUP BY brand ORDER BY cnt DESC LIMIT 10");

        $heroBanners  = DB::fetchAll("SELECT * FROM banners WHERE is_active = 1 AND position = 'home_hero' ORDER BY sort_order ASC, id ASC");
        $gridBanners  = DB::fetchAll("SELECT * FROM banners WHERE is_active = 1 AND position = 'home_promo_grid' ORDER BY sort_order ASC, id ASC LIMIT 3");
        $middleBanner = DB::fetch("SELECT * FROM banners WHERE is_active = 1 AND position = 'home_middle' ORDER BY sort_order ASC, id ASC LIMIT 1");
        $dualBanners  = DB::fetchAll("SELECT * FROM banners WHERE is_active = 1 AND position = 'home_dual' ORDER BY sort_order ASC, id ASC LIMIT 2");
        $bottomBanner = DB::fetch("SELECT * FROM banners WHERE is_active = 1 AND position = 'home_bottom' ORDER BY sort_order ASC, id ASC LIMIT 1");

        $isEn = !\App\Core\I18n::isRtl();
        view('home/index', [
            'title'      => __('site_title', 'NextShop') . ' | ' . __('tagline', 'Premium Online Shopping Experience'),
            'meta_description' => $isEn 
                ? 'Shop authentic electronics, smartphones, laptops, audio gear, and lifestyle products with official warranty at ' . __('site_title', 'NextShop') . '.'
                : config('app_name') . '؛ خرید آنلاین ' . count($categories) . ' دسته کالای اورجینال با ضمانت اصالت، ارسال سریع و ۷ روز مهلت بازگشت.',
            'categories'    => $categories,
            'featured'      => $featured,
            'newest'        => $newest,
            'bestseller'    => $bestseller,
            'deals'         => $deals,
            'brands'        => $brands,
            'heroBanners'   => $heroBanners,
            'gridBanners'   => $gridBanners,
            'middleBanner'  => $middleBanner,
            'dualBanners'   => $dualBanners,
            'bottomBanner'  => $bottomBanner,
        ]);
    }

    public function about(): void
    {
        $isEn = !\App\Core\I18n::isRtl();
        view('home/about', [
            'title' => __('about_us', 'درباره ما'),
            'meta_description' => $isEn
                ? 'About ' . __('site_title', 'NextShop') . ': Authentic online tech store with official warranty, fast delivery and real customer support.'
                : 'درباره ' . config('app_name') . ': فروشگاه آنلاین با ضمانت اصالت کالا، ارسال سریع و پشتیبانی واقعی.',
        ]);
    }

    public function contact(): void
    {
        $isEn = !\App\Core\I18n::isRtl();
        view('home/contact', [
            'title' => __('contact_us', 'تماس با ما'),
            'meta_description' => $isEn
                ? 'Contact ' . __('site_title', 'NextShop') . ': Customer support phone, email, and headquarters address.'
                : 'راه‌های تماس با ' . config('app_name') . ': تلفن پشتیبانی، ایمیل و آدرس دفتر مرکزی.',
        ]);
    }

    public function privacy(): void
    {
        $isEn = !\App\Core\I18n::isRtl();
        view('home/privacy', [
            'title' => __('privacy_policy', 'سیاست حفظ حریم خصوصی'),
            'meta_description' => $isEn
                ? 'Privacy Policy of ' . __('site_title', 'NextShop') . ': What data we collect and how we protect your personal information.'
                : 'سیاست حفظ حریم خصوصی ' . config('app_name') . ': چه داده‌ای جمع می‌کنیم و چگونه از آن محافظت می‌کنیم.',
        ]);
    }

    public function terms(): void
    {
        $isEn = !\App\Core\I18n::isRtl();
        view('home/terms', [
            'title' => __('terms_of_service', 'قوانین و مقررات'),
            'meta_description' => $isEn
                ? 'Terms of Service at ' . __('site_title', 'NextShop') . ': Shopping rules, refund policies, warranties and user obligations.'
                : 'قوانین و مقررات خرید، بازگشت کالا و استفاده از خدمات ' . config('app_name') . '.',
        ]);
    }

    /** نقشه سایت داینامیک برای موتورهای جستجو */
    public function sitemap(): void
    {
        $base = rtrim((string)config('app_url'), '/');
        $urls = [
            ['loc' => $base . '/', 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => $base . '/shop', 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => $base . '/about', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $base . '/contact', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $base . '/track', 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => $base . '/privacy', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => $base . '/terms', 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];
        foreach (DB::fetchAll("SELECT slug FROM categories ORDER BY sort_order") as $c) {
            $urls[] = ['loc' => $base . '/category/' . $c['slug'], 'changefreq' => 'weekly', 'priority' => '0.8'];
        }
        foreach (DB::fetchAll("SELECT slug FROM products WHERE is_active = 1") as $p) {
            $urls[] = ['loc' => $base . '/product/' . $p['slug'], 'changefreq' => 'weekly', 'priority' => '0.7'];
        }
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo '  <url><loc>' . e($u['loc']) . '</loc>'
                . '<changefreq>' . $u['changefreq'] . '</changefreq>'
                . '<priority>' . $u['priority'] . '</priority></url>' . "\n";
        }
        echo '</urlset>';
        exit;
    }

    public function newsletter(): void
    {
        abort_csrf();
        if (honeypot_filled()) {
            json_response(['ok' => true, 'message' => 'عضویت شما در خبرنامه با موفقیت انجام شد.']);
        }
        $ip = \App\Core\RateLimiter::clientIp();
        if (!\App\Core\RateLimiter::attempt('newsletter:' . $ip, 5, 3600)) {
            json_response(['ok' => false, 'message' => 'تعداد درخواست زیاد است. یک ساعت دیگر تلاش کنید.']);
        }
        $email = mb_strtolower(trim((string)input('email', '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
            json_response(['ok' => false, 'message' => 'ایمیل وارد شده معتبر نیست.']);
        }
        $exists = DB::fetch("SELECT id FROM newsletter WHERE email = ?", [$email]);
        if (!$exists) {
            DB::insert('newsletter', ['email' => $email]);
        }
        json_response(['ok' => true, 'message' => 'عضویت شما در خبرنامه با موفقیت انجام شد.']);
    }
}
