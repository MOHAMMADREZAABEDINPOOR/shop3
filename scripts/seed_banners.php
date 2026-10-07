<?php
require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database as DB;

$bannerDir = __DIR__ . '/../public/uploads/banners';
if (!is_dir($bannerDir)) {
    mkdir($bannerDir, 0777, true);
}

function makeBannerGraphicSvg(string $title, string $subtitle, string $badge, string $theme, string $iconType): string
{
    $themes = [
        'purple' => ['#2e1065', '#581c87', '#9333ea', '#c084fc'],
        'blue'   => ['#0c2340', '#0369a1', '#0284c7', '#38bdf8'],
        'dark'   => ['#090d16', '#18181b', '#3b82f6', '#60a5fa'],
        'emerald'=> ['#064e3b', '#047857', '#10b981', '#34d399'],
        'amber'  => ['#78350f', '#b45309', '#f59e0b', '#fbbf24'],
        'rose'   => ['#881337', '#be123c', '#f43f5e', '#fb7185'],
    ];

    $c = $themes[$theme] ?? $themes['purple'];
    $safeTitle = htmlspecialchars($title, ENT_XML1, 'UTF-8');
    $safeSubtitle = htmlspecialchars($subtitle, ENT_XML1, 'UTF-8');
    $safeBadge = htmlspecialchars($badge, ENT_XML1, 'UTF-8');

    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 480" width="1200" height="480">
  <defs>
    <linearGradient id="bg_{$theme}" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$c[0]}"/>
      <stop offset="60%" stop-color="{$c[1]}"/>
      <stop offset="100%" stop-color="{$c[0]}"/>
    </linearGradient>
    <linearGradient id="accent_{$theme}" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$c[2]}"/>
      <stop offset="100%" stop-color="{$c[3]}"/>
    </linearGradient>
    <radialGradient id="glow_{$theme}" cx="85%" cy="40%" r="55%">
      <stop offset="0%" stop-color="{$c[3]}" stop-opacity="0.45"/>
      <stop offset="100%" stop-color="{$c[1]}" stop-opacity="0"/>
    </radialGradient>
    <radialGradient id="glowLeft_{$theme}" cx="15%" cy="60%" r="50%">
      <stop offset="0%" stop-color="{$c[2]}" stop-opacity="0.3"/>
      <stop offset="100%" stop-color="{$c[0]}" stop-opacity="0"/>
    </radialGradient>
    <filter id="cardSh_{$theme}" x="-10%" y="-10%" width="120%" height="120%">
      <feDropShadow dx="0" dy="16" stdDeviation="24" flood-color="#000" flood-opacity="0.45"/>
    </filter>
  </defs>

  <!-- Background -->
  <rect width="1200" height="480" rx="28" fill="url(#bg_{$theme})"/>
  <rect width="1200" height="480" rx="28" fill="url(#glow_{$theme})"/>
  <rect width="1200" height="480" rx="28" fill="url(#glowLeft_{$theme})"/>

  <!-- Geometric Abstract Design Elements -->
  <g opacity="0.18">
    <circle cx="1020" cy="240" r="190" fill="none" stroke="#fff" stroke-width="2" stroke-dasharray="10 12"/>
    <circle cx="1020" cy="240" r="130" fill="none" stroke="#fff" stroke-width="3"/>
    <circle cx="1020" cy="240" r="70" fill="url(#accent_{$theme})" opacity="0.6"/>
  </g>

  <!-- Badge Tag -->
  <g transform="translate(64, 56)">
    <rect width="210" height="38" rx="19" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.2)"/>
    <circle cx="20" cy="19" r="6" fill="{$c[3]}"/>
    <text x="36" y="24" font-size="13" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="700">{$safeBadge}</text>
  </g>

  <!-- Main Typography -->
  <text x="64" y="160" font-size="38" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="900">{$safeTitle}</text>
  
  <text x="64" y="210" font-size="19" fill="#e2e8f0" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="400" opacity="0.92">{$safeSubtitle}</text>

  <!-- Visual Card Accent on Right -->
  <g transform="translate(860, 100)" filter="url(#cardSh_{$theme})">
    <rect width="260" height="280" rx="24" fill="rgba(255,255,255,0.07)" stroke="rgba(255,255,255,0.15)"/>
    <circle cx="130" cy="110" r="54" fill="url(#accent_{$theme})"/>
    <circle cx="130" cy="110" r="64" fill="none" stroke="#ffffff" stroke-width="2" opacity="0.3"/>
    
    <text x="130" y="210" font-size="18" text-anchor="middle" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="800">NEXTSHOP</text>
    <text x="130" y="235" font-size="13" text-anchor="middle" fill="{$c[3]}" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="700">PREMIUM SELECTION</text>
  </g>

  <!-- Bottom Details Bar -->
  <g transform="translate(64, 410)">
    <circle cx="8" cy="8" r="4" fill="{$c[3]}"/>
    <text x="24" y="12" font-size="13" fill="#cbd5e1" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="600">ضمانت اصالت ۱۰۰٪ کالای اورجینال</text>
    
    <circle cx="280" cy="8" r="4" fill="{$c[3]}"/>
    <text x="296" y="12" font-size="13" fill="#cbd5e1" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="600">ارسال سریع سراسری</text>

    <circle cx="480" cy="8" r="4" fill="{$c[3]}"/>
    <text x="496" y="12" font-size="13" fill="#cbd5e1" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="600">۷ روز ضمانت بازگشت کالا</text>
  </g>
</svg>
SVG;
}

$bannersData = [
    // 1. Hero Sliders (home_hero)
    [
        'title'          => 'جشنواره فروش ویژه پرچمداران دیجیتال',
        'title_en'       => 'Flagship Tech Mega Festival',
        'subtitle'       => 'جدیدترین گوشی‌های هوشمند، لپ‌تاپ‌های مهندسی و لوازم جانبی با ضمانت اصالت ۱۰۰٪',
        'subtitle_en'    => 'Latest smartphones, laptops and pro accessories with official warranty',
        'badge'          => 'فروش ویژه فصل — تا ۳۰٪ تخفیف',
        'badge_en'       => 'Special Season Sale — Up to 30% Off',
        'image'          => 'hero-slider-1.svg',
        'theme'          => 'purple',
        'link'           => '/shop?discount=1',
        'button_text'    => 'مشاهده تخفیف‌ها',
        'button_text_en' => 'View Deals',
        'position'       => 'home_hero',
        'color'          => 'gradient-purple',
        'sort_order'     => 1,
    ],
    [
        'title'          => 'کنسول‌های نسل ۹ و تجهیزات گیمینگ حرفه‌ای',
        'title_en'       => 'Next-Gen Consoles & Pro Gaming Gear',
        'subtitle'       => 'پلی‌استیشن ۵، ایکس‌باکس سریز، کنترلرهای سفارشی و هدست‌های سه‌بعدی با نرخ فریم بالا',
        'subtitle_en'    => 'PS5 Slim, Xbox Series X, pro controllers and spatial 3D audio gear',
        'badge'          => 'پیشنهاد طلایی گیمرها',
        'badge_en'       => 'Gamers Golden Choice',
        'image'          => 'hero-slider-2.svg',
        'theme'          => 'dark',
        'link'           => '/category/gaming',
        'button_text'    => 'ورود به دنیای گیمینگ',
        'button_text_en' => 'Explore Gaming',
        'position'       => 'home_hero',
        'color'          => 'gradient-dark',
        'sort_order'     => 2,
    ],
    [
        'title'          => 'دوربین‌های عکاسی ۴K و هلی‌شات‌های هوایی DJI',
        'title_en'       => '4K Mirrorless Cameras & DJI Drones',
        'subtitle'       => 'تجهیزات تخصصی تصویربرداری، لنزهای سینمایی و استابیلایزرهای هوشمند با کیفیت بی‌نظیر',
        'subtitle_en'    => 'Pro cinema lenses, mirrorless gear and intelligent gimbal stabilizers',
        'badge'          => 'جدیدترین مدل‌های ۲۰۲۶',
        'badge_en'       => 'Latest 2026 Models',
        'image'          => 'hero-slider-3.svg',
        'theme'          => 'blue',
        'link'           => '/category/camera',
        'button_text'    => 'مشاهده تجهیزات تصویربرداری',
        'button_text_en' => 'Shop Cameras',
        'position'       => 'home_hero',
        'color'          => 'gradient-blue',
        'sort_order'     => 3,
    ],

    // 2. Featured 3-Grid (home_promo_grid)
    [
        'title'          => 'موبایل و تبلت با گارانتی شرکتی',
        'title_en'       => 'Smartphones & Flagship Tablets',
        'subtitle'       => 'پرچمداران اپل، سامسونگ و شیائومی با تحویل فوری',
        'subtitle_en'    => 'Official Apple, Samsung & Xiaomi flagships',
        'badge'          => 'جدیدترین‌ها',
        'badge_en'       => 'Latest Flagships',
        'image'          => 'grid-mobile.svg',
        'theme'          => 'blue',
        'link'           => '/category/mobile',
        'button_text'    => 'مشاهده و خرید',
        'button_text_en' => 'Shop Now',
        'position'       => 'home_promo_grid',
        'color'          => 'gradient-blue',
        'sort_order'     => 1,
    ],
    [
        'title'          => 'کنسول و تجهیزات گیمینگ حرفه‌ای',
        'title_en'       => 'Consoles & Pro Gaming Hardware',
        'subtitle'       => 'بالاترین فریم‌ریت و سرعت پاسخ‌دهی برای مسابقات',
        'subtitle_en'    => 'Ultra FPS & competitive esports gear',
        'badge'          => 'دنیای بازی',
        'badge_en'       => 'Gaming Arena',
        'image'          => 'grid-gaming.svg',
        'theme'          => 'dark',
        'link'           => '/category/gaming',
        'button_text'    => 'مشاهده و خرید',
        'button_text_en' => 'Shop Now',
        'position'       => 'home_promo_grid',
        'color'          => 'gradient-dark',
        'sort_order'     => 2,
    ],
    [
        'title'          => 'دوربین و هلی‌شات برندهای معتبر',
        'title_en'       => 'Cameras & Drones Leading Brands',
        'subtitle'       => 'سنسورهای فول‌فریم و ضبط 4K/60fps بدون لرزش',
        'subtitle_en'    => 'Full-frame sensors & 4K cinematic capture',
        'badge'          => 'عکاسی حرفه‌ای',
        'badge_en'       => 'Pro Visual',
        'image'          => 'grid-camera.svg',
        'theme'          => 'emerald',
        'link'           => '/category/camera',
        'button_text'    => 'مشاهده و خرید',
        'button_text_en' => 'Shop Now',
        'position'       => 'home_promo_grid',
        'color'          => 'gradient-emerald',
        'sort_order'     => 3,
    ],

    // 3. Wide Middle Banner (home_middle)
    [
        'title'          => 'با کد تخفیف WELCOME10 ده درصد تخفیف بگیرید',
        'title_en'       => 'Get 10% Off with code WELCOME10',
        'subtitle'       => 'ارسال رایگان برای خریدهای بالای ۱.۵ میلیون تومان به سراسر کشور + کد MEGA20 برای خریدهای بالای ۵ میلیون',
        'subtitle_en'    => 'Free nationwide shipping for orders over 1.5M + use code MEGA20 for orders above 5M',
        'badge'          => 'کد تخفیف اولین خرید',
        'badge_en'       => 'Welcome Coupon Code',
        'image'          => 'middle-coupon.svg',
        'theme'          => 'purple',
        'link'           => '/shop',
        'button_text'    => 'همین حالا خرید کنید',
        'button_text_en' => 'Shop Now',
        'position'       => 'home_middle',
        'color'          => 'gradient-purple',
        'sort_order'     => 1,
    ],

    // 4. Dual Promos (home_dual)
    [
        'title'          => 'اولترابوک‌ها و لپ‌تاپ‌های مهندسی',
        'title_en'       => 'High-End Laptops & Ultrabooks',
        'subtitle'       => 'مک‌بوک‌های سری M3 و لپ‌تاپ‌های قدرتمند OLED نسل جدید با پردازنده‌های پرسرعت',
        'subtitle_en'    => 'Apple M3 silicon, OLED ultrabooks and RTX gaming powerhouses',
        'badge'          => 'ابزار حرفه‌ای کار',
        'badge_en'       => 'Pro Workstations',
        'image'          => 'dual-laptop.svg',
        'theme'          => 'blue',
        'link'           => '/category/laptop',
        'button_text'    => 'مشاهده لپ‌تاپ‌ها',
        'button_text_en' => 'Explore Laptops',
        'position'       => 'home_dual',
        'color'          => 'gradient-blue',
        'sort_order'     => 1,
    ],
    [
        'title'          => 'ساعت هوشمند و پایش ۲۴ ساعته سلامت',
        'title_en'       => 'Smartwatches & Fitness Trackers',
        'subtitle'       => 'سنسورهای پیشرفته ضربان قلب، اکسیژن خون و GPS دقیق دوفرکانسه برای ورزشکاران',
        'subtitle_en'    => 'Advanced heart rate sensors, dual GPS and titanium durability',
        'badge'          => 'ورزش و سلامت',
        'badge_en'       => 'Health & Fitness',
        'image'          => 'dual-wearable.svg',
        'theme'          => 'amber',
        'link'           => '/category/wearable',
        'button_text'    => 'مشاهده ساعت‌ها',
        'button_text_en' => 'Explore Wearables',
        'position'       => 'home_dual',
        'color'          => 'gradient-amber',
        'sort_order'     => 2,
    ],

    // 5. Bottom Panorama Mega Poster (home_bottom)
    [
        'title'          => 'خرید هوشمندانه با تضمین اصالت و ضمانت ۷ روزه بازگشت وجه',
        'title_en'       => 'Smart Shopping with 100% Authentic Guarantee & 7-Day Returns',
        'subtitle'       => 'بیش از ۷۰ محصول برگزیده در ۱۶ دسته‌بندی تخصصی با بهترین قیمت و پشتیبانی ۲۴ ساعته در تمام روزهای هفته',
        'subtitle_en'    => 'Over 70 premium authentic products across 16 specialized categories with fast delivery',
        'badge'          => 'چرا نکست‌شاپ؟',
        'badge_en'       => 'Why NextShop?',
        'image'          => 'bottom-mega.svg',
        'theme'          => 'rose',
        'link'           => '/shop',
        'button_text'    => 'شروع خرید در نکست‌شاپ',
        'button_text_en' => 'Start Shopping',
        'position'       => 'home_bottom',
        'color'          => 'gradient-rose',
        'sort_order'     => 1,
    ],

    // 6. Shop Top Header Poster (shop_top)
    [
        'title'          => 'فروشگاه تخصصی نکست‌شاپ؛ تنوع کالاهای دیجیتال و لایف‌استایل',
        'title_en'       => 'NextShop Catalog: Complete Digital & Lifestyle Tech',
        'subtitle'       => 'از برترین پرچمداران موبایل تا سخت‌افزار گیمینگ و تجهیزات خانه هوشمند با فیلترهای پیشرفته',
        'subtitle_en'    => 'From flagship phones to PC components and smart appliances',
        'badge'          => 'فروشگاه رسمی',
        'badge_en'       => 'Official Store',
        'image'          => 'shop-top.svg',
        'theme'          => 'purple',
        'link'           => '/shop?discount=1',
        'button_text'    => 'مشاهده تخفیف‌های ویژه',
        'button_text_en' => 'Explore Deals',
        'position'       => 'shop_top',
        'color'          => 'gradient-purple',
        'sort_order'     => 1,
    ],
];

// Clean existing banners or sync
DB::query("DELETE FROM banners");
try {
    DB::query("DELETE FROM sqlite_sequence WHERE name = 'banners'");
} catch (\Throwable $e) {}

$stmt = DB::pdo()->prepare("INSERT INTO banners (title, title_en, subtitle, subtitle_en, badge, badge_en, image, link, button_text, button_text_en, position, color, sort_order, is_active)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");

foreach ($bannersData as $b) {
    // Generate the SVG file
    $svgContent = makeBannerGraphicSvg($b['title'], $b['subtitle'], $b['badge'], $b['theme'], $b['position']);
    file_put_contents($bannerDir . '/' . $b['image'], $svgContent);

    $stmt->execute([
        $b['title'],
        $b['title_en'],
        $b['subtitle'],
        $b['subtitle_en'],
        $b['badge'],
        $b['badge_en'],
        $b['image'],
        $b['link'],
        $b['button_text'],
        $b['button_text_en'],
        $b['position'],
        $b['color'],
        $b['sort_order'],
    ]);
}

echo "Successfully created and seeded " . count($bannersData) . " rich banners across all site positions!\n";
