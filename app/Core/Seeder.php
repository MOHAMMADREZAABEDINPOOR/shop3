<?php
namespace App\Core;

use PDO;

/**
 * داده‌های نمونه جامع فروشگاه نکست‌شاپ (۱۶ دسته‌بندی تخصصی، بیش از ۷۰ محصول دوزبانه واقعی)
 * با پشتیبانی کامل از دیتابیس SQLite و سندهای NoSQL/MongoDB
 */
class Seeder
{
    public static function run(PDO $pdo): void
    {
        $config = require __DIR__ . '/../config.php';
        $uploadDir = $config['upload_dir'];
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        // اطمینان از وجود ستون‌های دو زبانه در جدول‌ها
        Schema::ensureBilingualColumns($pdo);

        // پاک‌سازی جدول‌های وابسته جهت ایجاد داده‌های یکپارچه و تازه
        $pdo->exec("PRAGMA foreign_keys = OFF");
        $pdo->exec("DELETE FROM order_items");
        $pdo->exec("DELETE FROM orders");
        $pdo->exec("DELETE FROM reviews");
        $pdo->exec("DELETE FROM wishlists");
        $pdo->exec("DELETE FROM coupons");
        $pdo->exec("DELETE FROM products");
        $pdo->exec("DELETE FROM categories");
        try {
            $pdo->exec("DELETE FROM sqlite_sequence WHERE name IN ('categories', 'products', 'reviews', 'orders', 'order_items', 'coupons', 'wishlists')");
        } catch (\Throwable $e) {}
        $pdo->exec("PRAGMA foreign_keys = ON");


        // پاک‌سازی یا حفظ کاربران
        $adminCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        if ($adminCount === 0) {
            $pdo->prepare("INSERT INTO users (name,email,phone,password,role,city,address) VALUES (?,?,?,?,?,?,?)")
                ->execute(['Admin Manager', 'admin@nextshop.ir', '09120000000', password_hash('admin123', PASSWORD_DEFAULT), 'admin', 'Tehran', 'Valiasr Ave, Next Tower, Fl 3']);
            $pdo->prepare("INSERT INTO users (name,email,phone,password,role,city,address) VALUES (?,?,?,?,?,?,?)")
                ->execute(['Sarah Miller', 'sara@example.com', '09121111111', password_hash('123456', PASSWORD_DEFAULT), 'customer', 'Isfahan', 'Chaharbagh Abbasi St']);
            $pdo->prepare("INSERT INTO users (name,email,phone,password,role,city,address) VALUES (?,?,?,?,?,?,?)")
                ->execute(['Alex Reza', 'ali@example.com', '09122222222', password_hash('123456', PASSWORD_DEFAULT), 'customer', 'Shiraz', 'Zand Blvd']);
            $pdo->prepare("INSERT INTO users (name,email,phone,password,role,city,address) VALUES (?,?,?,?,?,?,?)")
                ->execute(['Maria Karimi', 'maryam@example.com', '09123333333', password_hash('123456', PASSWORD_DEFAULT), 'customer', 'Tehran', 'Saadat Abad']);
        }

        // ---------- ۱۶ دسته‌بندی تخصصی (با نام و توضیحات دو زبانه) ----------
        $categories = [
            ['mobile',      'Smartphones & Tablets',        'موبایل و تبلت',            'mobile',       '#2563eb', 'Smartphones, flagship tablets, smart accessories and fast chargers', 'انواع گوشی‌های هوشمند پرچمدار، تبلت‌های پیشرفته و لوازم جانبی اورجینال'],
            ['laptop',      'Laptops & Ultrabooks',        'لپ‌تاپ و اولترابوک',       'laptop',       '#0891b2', 'High-performance ultrabooks, gaming laptops and monitors', 'لپ‌تاپ‌های مهندسی، گیمینگ و باریک به همراه نمایشگرهای حرفه‌ای'],
            ['audio',       'Audio & Sound Systems',       'تجهیزات صوتی و هدفون',      'audio',        '#e11d48', 'Studio headphones, true wireless earbuds and portable speakers', 'هدفون‌های حرفه‌ای، هندزفری‌های بی‌سیم و اسپیکرهای پرقدرت بلوتوثی'],
            ['wearable',    'Smartwatches & Wearables',    'ساعت و پوشیدنی‌های هوشمند', 'wearable',     '#d97706', 'Smart fitness trackers, GPS running watches and stylish wearables', 'ساعت‌های هوشمند پیشرفته، مچ‌بندهای ورزشی و گجت‌های پایش سلامت'],
            ['home',        'Home & Smart Appliances',     'خانه هوشمند و لوازم خانگی', 'home',         '#059669', 'Smart cleaning robots, air purifiers and modern home equipment', 'جاروبرقی‌های رباتیک، دستگاه‌های تصفیه هوا و تجهیزات مدرن خانگی'],
            ['gaming',      'Gaming Consoles & Gear',      'کنسول و تجهیزات گیمینگ',    'gaming',       '#7c3aed', 'Next-gen consoles, mechanical keyboards, gaming controllers and gear', 'کنسول‌های بازی نسل نهم، کنترلرهای اختصاصی و تجهیزات حرفه‌ای گیمینگ'],
            ['pc-parts',    'PC Hardware & Components',    'قطعات کامپیوتر و سخت‌افزار', 'pc-parts',    '#3b82f6', 'Processors, graphics cards, fast NVMe SSDs and motherboards', 'پردازنده‌ها، کارت‌های گرافیک، حافظه‌های پرسرعت SSD و مادربردها'],
            ['camera',      'Cameras & Drones',            'دوربین و تصویربرداری',     'camera',       '#0284c7', 'Mirrorless 4K cameras, action cams, aerial drones and studio lenses', 'دوربین‌های بدون‌آینه عکاسی، هلی‌شات‌های هوایی و لنزهای تخصصی استودیویی'],
            ['fashion',     'Fashion & Apparel',           'مد و پوشاک',               'fashion',      '#db2777', 'Designer sneakers, premium outerwear, genuine leather goods', 'کفش‌های ورزشی اورجینال، کاپشن‌های تنفسی و اکسسوری چرم طبیعی'],
            ['beauty',      'Beauty & Personal Care',      'زیبایی و سلامت',           'beauty',       '#0d9488', 'Luxury fragrances, advanced skin care, hair styling and personal grooming', 'عطر و ادکلن‌های لوکس، محصولات مراقبت از پوست و تجهیزات زیبایی'],
            ['sport',       'Sports & Outdoor Camping',    'ورزش و کمپینگ',            'sport',        '#ea580c', 'Camping tents, professional mountain bikes, training weights and bags', 'چادرهای ضدآب کمپینگ، دوچرخه‌های کوهستان و لوازم ورزشی حرفه‌ای'],
            ['kids',        'Toys & Hobbies',              'اسباب‌بازی و سرگرمی',      'kids',         '#65a30d', 'Educational building sets, RC 4x4 vehicles and family board games', 'لگوهای آموزشی، ماشین‌های کنترلی آفرود و بازی‌های فکری خانوادگی'],
            ['book',        'Books & Stationery',          'کتاب و نوشت‌افزار',         'book',         '#9333ea', 'Bestselling non-fiction books, luxury fountain pens and notebooks', 'پرفروش‌ترین کتاب‌های توسعه فردی، خودکارهای نفیس و دفترهای خاص'],
            ['tools',       'Tools & Technical Gear',      'ابزارآلات و تجهیزات فنی',   'tools',        '#475569', 'Cordless brushless power drills, mechanic sets and tactical lighting', 'دریل‌های شارژی صنعتی، جعبه‌بکس‌های فولادی و چراغ‌قوه‌های شکاری'],
            ['network',     'Networking & Smart Office',   'تجهیزات شبکه و اداری',     'network',      '#10b981', 'Wi-Fi 6 mesh routers, smart office switches and high-speed hubs', 'مودم و روترهای نسل ششم، سوئیچ‌های گیگابیت و هاب‌های اداری'],
            ['accessories', 'Digital Accessories',         'لوازم جانبی دیجیتال',       'accessories',  '#6366f1', 'Multi-device GaN chargers, durable braided cables and magnetic docks', 'شارژرهای سریع GaN، کابل‌های بادوام تایپ سی و پایه‌های مغناطیسی'],
            ['kitchen',     'Kitchen & Dining',            'آشپزخانه و پخت‌وپز',       'kitchen',      '#ea580c', 'Espresso machines, airfryers, stand mixers and culinary gear', 'دستگاه‌های اسپرسوساز، سرخ‌کن‌های هوشمند، همزن‌ها و لوازم مدرن آشپزخانه'],
        ];


        $catIds = [];
        $catColors = [];
        $stmtCat = $pdo->prepare("INSERT INTO categories (name,name_en,name_fa,slug,icon,color,description,description_en,description_fa,sort_order)
            VALUES (?,?,?,?,?,?,?,?,?,?)");

        $mongoCategories = [];
        foreach ($categories as $i => $c) {
            [$slug, $nameEn, $nameFa, $icon, $color, $descEn, $descFa] = $c;
            $stmtCat->execute([$nameEn, $nameEn, $nameFa, $slug, $icon, $color, $descEn, $descEn, $descFa, $i]);
            $id = (int)$pdo->lastInsertId();
            $catIds[$slug] = $id;
            $catColors[$slug] = $color;

            $mongoCategories[] = [
                '_id' => (string)$id,
                'slug' => $slug,
                'name_en' => $nameEn,
                'name_fa' => $nameFa,
                'icon' => $icon,
                'color' => $color,
                'description_en' => $descEn,
                'description_fa' => $descFa,
                'sort_order' => $i
            ];
        }

        // ---------- بیش از ۷۰ محصول جامع و واقعی با مشخصات فنی و متون دو زبانه ----------
        // ساختار: [slug, nameEn, nameFa, catSlug, brand, priceToman, discountToman, stock, isFeatured, art, shortEn, shortFa, specsEn, specsFa]
        $catalog = [
            // 1. Mobile & Tablet
            ['iphone-15-pro', 'Apple iPhone 15 Pro 256GB Natural Titanium', 'گوشی موبایل اپل iPhone 15 Pro ظرفیت 256 گیگابایت',
                'mobile', 'Apple', 78900000, 74500000, 14, 1, 'phone-pro',
                'Aerospace-grade titanium design, A17 Pro chip, custom Action button, and 48MP main camera.',
                'بدنه تیتانیومی مقاوم، چیپست فوق‌العاده A17 Pro، دکمه اکشن اختصاصی و دوربین پیشرفته ۴۸ مگاپیکسلی.',
                ['Display' => '6.1" Super Retina XDR OLED 120Hz', 'Chipset' => 'Apple A17 Pro (3nm)', 'Storage' => '256GB NVMe', 'RAM' => '8GB', 'Camera' => '48MP + 12MP + 12MP', 'Weight' => '187g'],
                ['نمایشگر' => '۶.۱ اینچ Super Retina XDR نرخ ۱۲۰ هرتز', 'پردازنده' => 'Apple A17 Pro سه نانومتری', 'حافظه داخلی' => '۲۵۶ گیگابایت', 'رم' => '۸ گیگابایت', 'دوربین' => 'سه‌گانه ۴۸+۱۲+۱۲ مگاپیکسل', 'وزن' => '۱۸۷ گرم']],

            ['galaxy-s24-ultra', 'Samsung Galaxy S24 Ultra 5G 256GB AI Titanium', 'گوشی موبایل سامسونگ Galaxy S24 Ultra نسخه هوش مصنوعی',
                'mobile', 'Samsung', 69800000, 66900000, 9, 1, 'phone-ultra',
                'Built-in S Pen, Titanium frame, integrated Galaxy AI and 200MP quad camera setup.',
                'قلم هوشمند S Pen، بدنه تیتانیومی، امکانات هوش مصنوعی Galaxy AI و دوربین خارق‌العاده ۲۰۰ مگاپیکسلی.',
                ['Display' => '6.8" Dynamic AMOLED 2X 120Hz', 'Chip' => 'Snapdragon 8 Gen 3 for Galaxy', 'RAM' => '12GB', 'Battery' => '5000mAh 45W Fast Charging', 'Camera' => '200MP + 50MP + 12MP + 10MP'],
                ['نمایشگر' => '۶.۸ اینچ Dynamic AMOLED 2X نرخ ۱۲۰ هرتز', 'پردازنده' => 'اسنپدراگون 8 نسل 3', 'رم' => '۱۲ گیگابایت', 'باتری' => '۵۰۰۰ میلی‌آمپر با شارژ ۴۵ وات', 'دوربین' => 'چهارگانه ۲۰۰+۵۰+۱۲+۱۰ مگاپیکسل']],

            ['xiaomi-14-pro', 'Xiaomi 14 Pro 512GB Leica Optical Camera', 'گوشی شیائومی 14 پرو با لنز حرفه‌ای لایکا',
                'mobile', 'Xiaomi', 46500000, 43900000, 18, 0, 'phone-mid',
                'Leica Summilux lenses, Snapdragon 8 Gen 3, 120W HyperCharge, and 2K AMOLED screen.',
                'لنزهای سوپر لایکا، پردازنده نسل سوم اسنپدراگون، شارژ فوق‌سریع ۱۲۰ واتی و نمایشگر خیره‌کننده 2K.',
                ['Display' => '6.73" LTPO AMOLED 120Hz 3000nits', 'Storage' => '512GB UFS 4.0', 'RAM' => '16GB', 'Charging' => '120W Wired + 50W Wireless', 'Protection' => 'IP68'],
                ['نمایشگر' => '۶.۷۳ اینچ LTPO AMOLED روشنایی ۳۰۰۰ نیت', 'حافظه داخلی' => '۵۱۲ گیگابایت', 'رم' => '۱۶ گیگابایت', 'سرعت شارژ' => '۱۲۰ وات سیمی + ۵۰ وات بی‌سیم', 'استاندارد' => 'IP68 ضدآب']],

            ['ipad-air-11-m2', 'Apple iPad Air 11-inch M2 Chip Liquid Retina', 'تبلت اپل آیپد ایر ۱۱ اینچ با تراشه قدرتمند M2',
                'mobile', 'Apple', 43800000, null, 7, 0, 'tablet',
                'Blazing-fast M2 silicon, Liquid Retina anti-reflective display, Apple Pencil Pro support.',
                'سرعت خیره‌کننده پردازنده M2، نمایشگر رتینا با پوشش ضد انعکاس و پشتیبانی از قلم نسل جدید.',
                ['Chip' => 'Apple M2 8-core CPU / 10-core GPU', 'Display' => '11-inch Liquid Retina True Tone', 'Storage' => '128GB', 'Weight' => '462g'],
                ['تراشه' => 'Apple M2 با ۸ هسته پردازشی', 'صفحه نمایش' => '۱۱ اینچ Liquid Retina', 'حافظه' => '۱۲۸ گیگابایت', 'وزن' => '۴۶۲ گرم']],

            ['galaxy-tab-s9-plus', 'Samsung Galaxy Tab S9 Plus 12.4" Dynamic AMOLED', 'تبلت سامسونگ Galaxy Tab S9 پلاس با قلم هوشمند',
                'mobile', 'Samsung', 49500000, 46900000, 5, 0, 'tablet',
                'Water-resistant IP68 flagship tablet with S Pen included and quad AKG tuned speakers.',
                'تبلت پرچمدار ضدآب با استاندارد IP68، قلم هوشمند داخل جعبه و اسپیکرهای چهارگانه AKG.',
                ['Display' => '12.4" Dynamic AMOLED 2X 120Hz', 'RAM' => '12GB', 'Battery' => '10090mAh', 'Included' => 'S Pen Stylus'],
                ['نمایشگر' => '۱۲.۴ اینچ Dynamic AMOLED 2X', 'رم' => '۱۲ گیگابایت', 'باتری' => '۱۰۰۹۰ میلی‌آمپر ساعت', 'اقلام همراه' => 'قلم S Pen اورجینال']],

            // 2. Laptops & Ultrabooks
            ['macbook-pro-14-m3', 'Apple MacBook Pro 14" M3 Pro 18GB/512GB Space Black', 'لپ‌تاپ اپل مک‌بوک پرو ۱۴ اینچ با پردازنده M3 Pro',
                'laptop', 'Apple', 119000000, 114500000, 6, 1, 'laptop-air',
                'Liquid Retina XDR screen, up to 22 hours battery life, hardware-accelerated ray tracing.',
                'نمایشگر خیره‌کننده رتینا XDR، شارژدهی فوق‌العاده ۲۲ ساعته و سیستم خنک‌کننده فعال بدون صدا.',
                ['Chip' => 'Apple M3 Pro 11-core CPU', 'Unified Memory' => '18GB', 'Storage' => '512GB SSD', 'Display' => '14.2" Liquid Retina XDR 120Hz', 'Battery' => 'Up to 22 Hours'],
                ['پردازنده' => 'Apple M3 Pro یازده هسته‌ای', 'حافظه رم' => '۱۸ گیگابایت یکپارچه', 'حافظه داخلی' => '۵۱۲ گیگابایت SSD فوق سریع', 'نمایشگر' => '۱۴.۲ اینچ Liquid Retina XDR', 'شارژدهی' => 'تا ۲۲ ساعت کار مداوم']],

            ['asus-zenbook-14-oled', 'ASUS Zenbook 14 OLED Intel Core Ultra 7', 'اولترابوک ایسوس ذن‌بوک 14 با نمایشگر OLED لمسی',
                'laptop', 'ASUS', 68500000, 64900000, 10, 1, 'laptop-essential',
                'Ultra-light 1.2kg chassis, Intel Core Ultra AI processor, 3K 120Hz OLED screen.',
                'وزن فوق سبک ۱.۲ کیلوگرمی، پردازنده مجهز به هوش مصنوعی اینتل و پنل اولد با دقت رنگ ۱۰۰٪.',
                ['Processor' => 'Intel Core Ultra 7 155H', 'RAM' => '16GB LPDDR5X', 'SSD' => '1TB PCIe 4.0', 'Display' => '14.0" 3K (2880x1800) OLED 120Hz', 'Weight' => '1.2 kg'],
                ['پردازنده' => 'Intel Core Ultra 7 155H با موتور هوش مصنوعی', 'رم' => '۱۶ گیگابایت LPDDR5X', 'حافظه' => '۱ ترابایت SSD M.2', 'صفحه نمایش' => '۱۴ اینچ 3K OLED نرخ ۱۲۰ هرتز', 'وزن' => '۱.۲ کیلوگرم']],

            ['dell-xps-13-plus', 'Dell XPS 13 Plus 9320 4K Touch Ultrabook', 'لپ‌تاپ دل XPS 13 Plus با نمایشگر 4K لمسی',
                'laptop', 'Dell', 89000000, null, 4, 0, 'laptop-air',
                'Zero-lattice keyboard, invisible glass trackpad, CNC machined aluminum body.',
                'طراحی مدرن با کیبورد یکپارچه، ترک‌پد شیشه‌ای مخفی و بدنه ماشین‌کاری شده آلومینیوم.',
                ['Processor' => 'Intel Core i7-1360P', 'RAM' => '32GB LPDDR5', 'Storage' => '1TB NVMe', 'Display' => '13.4" 4K UHD+ Touch 500nits'],
                ['پردازنده' => 'Intel Core i7-1360P نسل ۱۳', 'رم' => '۳۲ گیگابایت LPDDR5', 'حافظه' => '۱ ترابایت NVMe SSD', 'نمایشگر' => '۱۳.۴ اینچ 4K لمسی روشنایی ۵۰۰ نیت']],

            ['lenovo-legion-pro-5', 'Lenovo Legion Pro 5 Gen 8 RTX 4070 Gaming Laptop', 'لپ‌تاپ گیمینگ لنوو لژیون پرو 5 با گرافیک RTX 4070',
                'laptop', 'Lenovo', 84000000, 79900000, 7, 1, 'laptop-gaming',
                'Ryzen 7 7745HX, NVIDIA GeForce RTX 4070 8GB, 240Hz WQXGA esports display.',
                'پردازنده قدرتمند رایزن ۷، گرافیک قدرتمند RTX 4070، نمایشگر فوق‌سریع ۲۴۰ هرتزی گیمینگ.',
                ['CPU' => 'AMD Ryzen 7 7745HX', 'GPU' => 'NVIDIA RTX 4070 8GB 140W', 'RAM' => '32GB DDR5', 'SSD' => '1TB M.2', 'Display' => '16" WQXGA (2560x1600) 240Hz'],
                ['پردازنده' => 'AMD Ryzen 7 7745HX هشت هسته‌ای', 'کارت گرافیک' => 'RTX 4070 با توان ۱۴۰ وات', 'رم' => '۳۲ گیگابایت DDR5', 'حافظه' => '۱ ترابایت NVMe', 'نمایشگر' => '۱۶ اینچ 2K با نرخ نوسازی ۲۴۰ هرتز']],

            // 3. Audio & Sound Systems
            ['sony-wh1000xm5', 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones', 'هدفون بلوتوثی سونی WH-1000XM5 با برترین نویزکنسلینگ',
                'audio', 'Sony', 19800000, 18500000, 15, 1, 'headphones',
                'Industry-leading active noise cancellation with 8 mics, Auto NC Optimizer, and 30-hour battery life.',
                'برترین فناوری حذف نویز فعال بازار با ۸ میکروفون اختصاصی، وضوح صدای استودیویی و باتری ۳۰ ساعته.',
                ['Type' => 'Over-ear Wireless', 'Battery' => '30 Hours with ANC on', 'Drivers' => '30mm Carbon fiber dome', 'Codecs' => 'LDAC, AAC, SBC', 'Weight' => '250g'],
                ['نوع' => 'هدفون دور گوشی بی‌سیم', 'شارژدهی' => '۳۰ ساعت با نویزکنسلینگ روشن', 'درایور' => '۳۰ میلی‌متری کربن کامپوزیت', 'کدک‌ها' => 'LDAC کیفیت بالا، AAC، SBC', 'وزن' => '۲۵۰ گرم']],

            ['airpods-pro-2', 'Apple AirPods Pro 2nd Gen USB-C MagSafe', 'هدفون بی‌سیم اپل AirPods Pro نسل دوم با پورت تایپ سی',
                'audio', 'Apple', 14900000, 13800000, 22, 1, 'earbuds',
                'Up to 2x more Active Noise Cancellation, Adaptive Audio, Conversation Awareness and USB-C.',
                'دو برابر حذف نویز قوی‌تر، قابلیت تطبیق صدای هوشمند محیطی و کیس شارژ ضدآب مجهز به اسپیکر.',
                ['Chip' => 'Apple H2 headphone chip', 'Battery' => '6 Hours (30 Hours with MagSafe Case)', 'Sweat & Water Resistance' => 'IP54', 'Connection' => 'Bluetooth 5.3'],
                ['تراشه' => 'Apple H2 پردازش صوت اختصاصی', 'شارژدهی' => '۶ ساعت مداوم (۳۰ ساعت با کیس)', 'مقاومت در برابر آب' => 'استاندارد IP54', 'بلوتوث' => 'نسخه ۵.۳ با برد بالا']],

            ['bose-quietcomfort-ultra', 'Bose QuietComfort Ultra Spatial Audio Earbuds', 'هندزفری بی‌سیم بوز QuietComfort Ultra با صدای سه‌بعدی',
                'audio', 'Bose', 16500000, null, 11, 0, 'earbuds',
                'World-class noise cancellation, breakthrough spatialized audio, custom tune technology.',
                'نویزکنسلینگ افسانه‌ای بوز، صدای فضایی اختصاصی بدون وابستگی به منبع و گوشی‌های بسیار ارگونومیک.',
                ['Battery' => '6 Hours playtime', 'Immersion Audio' => 'Yes, hardware spatial', 'Bluetooth' => '5.3 multipoint', 'Codecs' => 'aptX Adaptive, AAC'],
                ['شارژدهی' => '۶ ساعت پخش پیوسته', 'صدای فضایی' => 'سه‌بعدی غوطه‌ورکننده', 'بلوتوث' => '۵.۳ با اتصال همزمان به دو دستگاه', 'کدک' => 'aptX Adaptive']],

            ['jbl-boombox-3', 'JBL Boombox 3 Portable Bluetooth Speaker 180W', 'اسپیکر قابل‌حمل جی‌بی‌ال Boombox 3 با توان ۱۸۰ وات',
                'audio', 'JBL', 24900000, 22800000, 8, 1, 'speaker-box',
                'Massive sound and deepest bass with 3-way speaker system, 24 hours of playtime, IP67 waterproof.',
                'صدای پرقدرت با بیس لرزاننده، سیستم صوتی ۳ سویه، باتری عظیم ۲۴ ساعته و بدنه ضدآب مقاوم.',
                ['Output Power' => '180W RMS (AC mode)', 'Battery' => '24 Hours playback (10000mAh)', 'Waterproof' => 'IP67 Dust & Water', 'Weight' => '6.7 kg'],
                ['توان خروجی' => '۱۸۰ وات RMS با برق / ۱۳۶ وات با باتری', 'شارژدهی' => '۲۴ ساعت پخش مداوم', 'استاندارد مقاومت' => 'IP67 کاملاً ضدآب و گردوغبار', 'وزن' => '۶.۷ کیلوگرم']],

            // 4. Smartwatches & Wearables
            ['apple-watch-ultra-2', 'Apple Watch Ultra 2 GPS + Cellular 49mm Titanium', 'ساعت هوشمند اپل واچ اولترا ۲ با بدنه تیتانیوم ۴۹ میلی‌متری',
                'wearable', 'Apple', 48500000, 45900000, 6, 1, 'wearable',
                'Rugged 49mm titanium case, 3000-nit brightest display, precision dual-frequency GPS.',
                'بدنه فوق‌مقاوم تیتانیومی ۴۹ میلی‌متری، روشنایی ۳۰۰۰ نیت مناسب آفتاب مستقیم و GPS دو فرکانسه دقیق.',
                ['Case' => '49mm Aerospace Titanium', 'Display' => 'Always-On Retina OLED 3000 nits', 'Battery' => 'Up to 36 hours (72h Low Power)', 'Water Resistance' => '100m dive-certified'],
                ['جنس بدنه' => 'تیتانیوم هوافضا ۴۹ میلی‌متری', 'روشنایی نمایشگر' => '۳۰۰۰ نیت خوانا در کویر و کوهستان', 'شارژدهی' => 'تا ۳۶ ساعت عادی (۷۲ ساعت ذخیره نیرو)', 'مقاومت در برابر آب' => 'عمق ۱۰۰ متر غواصی']],

            ['galaxy-watch6-classic', 'Samsung Galaxy Watch 6 Classic 47mm Rotating Bezel', 'ساعت هوشمند سامسونگ گلکسی واچ ۶ کلاسیک با حاشیه چرخان',
                'wearable', 'Samsung', 16200000, 14900000, 12, 0, 'wearable',
                'Iconic rotating physical bezel, Sapphire crystal display, advanced ECG and sleep tracking.',
                'بزل مکانیکی چرخان نمادین، محافظ یاقوت کبود، پایش پیشرفته نوار قلب ECG و کیفیت خواب.',
                ['Size' => '47mm Stainless Steel', 'Display' => '1.5" Super AMOLED Sapphire', 'Sensors' => 'ECG, BioActive, Blood Pressure', 'Battery' => 'Up to 40 hours'],
                ['سایز' => '۴۷ میلی‌متر استیل ضدزنگ', 'نمایشگر' => '۱.۵ اینچ Super AMOLED با شیشه سافایر', 'حسگرها' => 'نوار قلب، فشار خون، اکسیژن و دمای پوست', 'باتری' => 'تا ۴۰ ساعت با یک شارژ']],

            ['garmin-fenix-7x-pro', 'Garmin Fenix 7X Pro Solar Sapphire Multisport Watch', 'ساعت ورزشی حرفه‌ای گارمین Fenix 7X Pro شارژ خورشیدی',
                'wearable', 'Garmin', 56000000, null, 4, 1, 'wearable',
                'Built-in LED flashlight, Power Sapphire solar charging lens, up to 37 days of battery life.',
                'چراغ‌قوه LED پرنور، شارژ با نور خورشید خورشیدی، باتری تا ۳۷ روز و نقشه‌های دقیق توپوگرافی.',
                ['Lens' => 'Power Sapphire Solar Charging', 'Battery' => 'Up to 37 days in smartwatch mode', 'Flashlight' => 'Multi-LED variable strobe', 'Navigation' => 'Topographic color maps'],
                ['شیشه' => 'پاور سافایر مجهز به سلول خورشیدی', 'شارژدهی باتری' => 'تا ۳۷ روز در حالت ساعت هوشمند', 'چراغ‌قوه' => 'LED چندحالته سفید و قرمز', 'مسیریابی' => 'نقشه‌های رنگی آفلاین سراسر دنیا']],

            // 5. Home & Smart Appliances
            ['dyson-v15-detect', 'Dyson V15 Detect Absolute Cordless Vacuum Cleaner', 'جاروبرقی شارژی دایسون V15 Detect با سنسور لیزری گردوغبار',
                'home', 'Dyson', 44500000, 41900000, 8, 1, 'home',
                'Laser illumination reveals microscopic dust, acoustic piezo sensor measures particles in real time.',
                'نور لیزری دقیق برای آشکارسازی ذرات میکروسکوپی خاک، سنسور پیزوالکتریک و مکش بی‌رقیب ۲۴۰ واتی.',
                ['Suction Power' => '240 Air Watts', 'Run Time' => 'Up to 60 minutes', 'Bin Volume' => '0.77 Liters', 'Filtration' => 'HEPA 99.99% down to 0.3 microns'],
                ['توان مکش' => '۲۴۰ ایروات قدرتمند', 'مدت کارکرد' => 'تا ۶۰ دقیقه مداوم', 'ظرفیت مخزن' => '۰.۷۷ لیتر با تخلیه آسان', 'فیلتراسیون' => 'هپای پیشرفته ۹۹.۹۹ درصد']],

            ['philips-airfryer-xxl', 'Philips Airfryer XXL Smart Sensing Connected HD9880', 'سرخ‌کن بدون روغن فیلیپس XXL مدل هوشمند HD9880',
                'home', 'Philips', 18900000, 16900000, 14, 0, 'kitchen',
                'Smart Sensing technology automatically adjusts time and temperature for perfect cooking results.',
                'سنسورهای هوشمند پخت خودکار، ظرفیت فوق‌العاده ۲ کیلوگرمی برای کل خانواده و کاهش ۹۰ درصدی مصرف روغن.',
                ['Capacity' => '8.3 Liters (2kg fries)', 'Power' => '2200W Rapid CombiAir', 'Connectivity' => 'NutriU App Wi-Fi', 'Presets' => '22 cooking programs'],
                ['ظرفیت' => '۸.۳ لیتر (مناسب تا ۷ نفر)', 'توان' => '۲۲۰۰ وات کانوکشن سریع', 'اتصال' => 'وای‌فای و اپلیکیشن اختصاصی موبایل', 'برنامه‌ها' => '۲۲ حالت پخت خودکار و سنسوری']],

            ['dreame-bot-l20-ultra', 'Dreame L20 Ultra Robot Vacuum and Mop with Base Station', 'جاروبرقی رباتیک دریمی L20 Ultra با ایستگاه شستشوی خودکار',
                'home', 'Dreame', 52000000, 48900000, 5, 1, 'home',
                'MopExtend technology reaches edges, 7000Pa suction power, automatic mop washing and hot air drying.',
                'بازوی متحرک شستشوی لبه‌ها و گوشه‌ها، مکش باورنکردنی ۷۰۰۰ پاسکال و خشک‌کن اتوماتیک با باد گرم.',
                ['Suction' => '7000Pa Vormax System', 'Base Station' => 'Self-emptying, auto water refill & hot air dry', 'Navigation' => 'AI Action + 3D Structured Light', 'Battery' => '6400mAh'],
                ['قدرت مکش' => '۷۰۰۰ پاسکال فوق قوی', 'ایستگاه مرکزی' => 'تخلیه خودکار زباله، تعویض آب و شستشوی پد با باد گرم', 'مسیریابی' => 'هوش مصنوعی دوربین‌دار و اسکن سه‌بعدی لیزری', 'باتری' => '۶۴۰۰ میلی‌آمپر ساعت']],

            // 6. Gaming & Consoles
            ['ps5-slim-1tb', 'Sony PlayStation 5 Slim 1TB Disc Edition Console', 'کنسول بازی سونی پلی‌استیشن 5 اسلیم نسخه استاندارد دیسک‌خور',
                'gaming', 'Sony', 34500000, 32900000, 10, 1, 'gaming',
                'Sleek compact design, ultra-high speed 1TB SSD, ray tracing, haptic feedback and Tempest 3D audio.',
                'طراحی باریک‌تر و مدرن‌تر، حافظه پرسرعت ۱ ترابایتی، گرافیک خیره‌کننده ۴K و تریگرهای تطبیق‌پذیر دسته.',
                ['Storage' => '1TB Custom High-Speed SSD (5.5 GB/s)', 'Graphics' => '10.3 TFLOPS AMD RDNA 2', 'Output' => '4K 120Hz / 8K ready / HDR', 'Included' => 'DualSense Wireless Controller'],
                ['حافظه ذخیره‌سازی' => '۱ ترابایت SSD اختصاصی ۵.۵ گیگابایت بر ثانیه', 'پردازنده گرافیکی' => '۱۰.۳ ترافلاپس بر پایه معماری RDNA 2', 'خروجی تصویر' => '4K با نرخ ۱۲۰ فریم و پشتیبانی از 8K', 'اقلام همراه' => 'یک عدد دسته بی‌سیم دوال‌سنس']],

            ['xbox-series-x', 'Microsoft Xbox Series X 1TB 4K 120FPS Gaming Console', 'کنسول مایکروسافت ایکس‌باکس سری ایکس با توان ۱۲ ترافلاپس',
                'gaming', 'Microsoft', 33000000, null, 8, 0, 'gaming',
                'The most powerful Xbox ever with 12 teraflops of graphical processing power and Quick Resume.',
                'قوی‌ترین کنسول تاریخ ایکس‌باکس با ۱۲ ترافلاپس قدرت پردازش گرافیکی و قابلیت تعویض سریع بازی Quick Resume.',
                ['GPU' => '12 TFLOPS AMD RDNA 2', 'RAM' => '16GB GDDR6', 'Storage' => '1TB Custom NVMe SSD', 'Features' => 'Quick Resume, Dolby Vision Gaming'],
                ['قدرت گرافیکی' => '۱۲ ترافلاپس پردازش سریع', 'حافظه رم' => '۱۶ گیگابایت GDDR6 پهنای باند بالا', 'حافظه' => '۱ ترابایت SSD نسل چهارم', 'امکانات' => 'پشتیبانی از صدای دالبی اتموس و بازی‌های ۶۰ و ۱۲۰ فریم']],

            ['dualsense-edge-pro', 'Sony DualSense Edge Wireless Controller for PS5', 'دسته بازی حرفه‌ای سونی DualSense Edge پلی‌استیشن ۵',
                'gaming', 'Sony', 11900000, 10800000, 14, 0, 'gaming',
                'Pro-level customization, changeable stick caps and rear buttons, adjustable trigger stops.',
                'شخصی‌سازی دکمه‌های پشت دسته، آنالوگ‌های قابل تعویض ماژولار، تنظیم عمق تریگرها و کیف حمل مخصوص.',
                ['Compatibility' => 'PS5, PC, Mac, iOS, Android', 'Features' => 'Remappable back paddles, interchangeable stick modules', 'Cable' => 'Braided USB-C with locking housing'],
                ['سازگاری' => 'پلی‌استیشن ۵، کامپیوتر شخصی و موبایل', 'امکانات' => 'پدال‌های فلزی پشت، تنظیم حساسیت استیک‌ها', 'کابل' => 'کابل روکش‌دار ۳ متری با قفل مکانیکی اتصال']],

            ['logitech-g502-hero', 'Logitech G502 HERO High Performance Gaming Mouse', 'ماوس گیمینگ لاجیتک G502 HERO با سنسور ۲۵۶۰۰ DPI',
                'gaming', 'Logitech', 3200000, 2790000, 30, 0, 'gaming',
                'HERO 25K optical sensor, 11 programmable buttons, adjustable weight system.',
                'سنسور فوق‌دقیق HERO با دقت ۲۵۶۰۰ DPI، یازده کلید قابل برنامه‌ریزی و وزنه‌های تنظیم تعادل دست.',
                ['Sensor' => 'HERO 25K Optical', 'Max DPI' => '25,600 DPI', 'Buttons' => '11 programmable with onboard memory', 'Weight Tuning' => 'Five 3.6g weights included'],
                ['سنسور' => 'اپتیکال HERO 25K بدون خطا', 'حداکثر دقت' => '۲۵,۶۰۰ دی‌پی‌آی', 'کلیدها' => '۱۱ کلید ماکرو با حافظه ذخیره‌سازی داخلی', 'وزن' => 'دارای ۵ وزنه ۳.۶ گرمی قابل تغییر']],

            // 7. PC Hardware & Parts
            ['intel-core-i9-14900k', 'Intel Core i9-14900K 24-Core 6.0GHz Desktop Processor', 'پردازنده اینتل Core i9-14900K فرکانس ۶ گیگاهرتز',
                'pc-parts', 'Intel', 34000000, 31900000, 8, 1, 'pc-parts',
                '24 cores (8 Performance + 16 Efficient), up to 6.0 GHz with Thermal Velocity Boost.',
                '۲۴ هسته پردازشی پرقدرت، فرکانس بوست دیوانه‌وار ۶ گیگاهرتز و پشتیبانی کامل از رم‌های DDR5 و مادربردهای سری ۷۰۰.',
                ['Cores/Threads' => '24 Cores (8P + 16E) / 32 Threads', 'Max Frequency' => '6.0 GHz', 'Cache' => '36MB Intel Smart Cache', 'Socket' => 'LGA 1700'],
                ['هسته‌ها / رشته‌ها' => '۲۴ هسته (۸ قدرتی + ۱۶ کم‌مصرف) / ۳۲ رشته', 'فرکانس بوست' => '۶.۰ گیگاهرتز فوق‌سریع', 'حافظه کش' => '۳۶ مگابایت اسمارت‌کش', 'سوکت' => 'LGA 1700 نسل ۱۴']],

            ['asus-rog-rtx-4080-super', 'ASUS ROG Strix GeForce RTX 4080 SUPER 16GB OC', 'کارت گرافیک ایسوس ROG Strix RTX 4080 Super ظرفیت ۱۶ گیگ',
                'pc-parts', 'ASUS', 79000000, 75500000, 4, 1, 'pc-parts',
                'Ada Lovelace architecture, DLSS 3.5 neural rendering, massive 3.5-slot axial-tech cooling.',
                'معماری انقلابی آدا لاولیس، فناوری ارتقای فریم DLSS 3.5 با هوش مصنوعی و خنک‌کننده غول‌پیکر ۳ فن فلزی.',
                ['VRAM' => '16GB GDDR6X 256-bit', 'Boost Clock' => '2670 MHz (OC Mode)', 'Outputs' => '2x HDMI 2.1a, 3x DisplayPort 1.4a', 'Power' => '16-pin 12VHPWR (750W+ Recommended)'],
                ['حافظه ویدیویی' => '۱۶ گیگابایت GDDR6X با باس ۲۵۶ بیت', 'فرکانس بوست' => '۲۶۷۰ مگاهرتز در حالت اورکلاک', 'پورت‌ها' => 'دو خروجی HDMI 2.1a و سه پورت DisplayPort 1.4a', 'توان پیشنهادی' => 'پاور حداقل ۷۵۰ وات استاندارد']],

            ['samsung-990-pro-2tb', 'Samsung 990 PRO 2TB PCIe 4.0 NVMe M.2 SSD Heatsink', 'اس‌اس‌دی سامسونگ 990 Pro ظرفیت ۲ ترابایت با هیت‌سینک',
                'pc-parts', 'Samsung', 12400000, 11200000, 20, 0, 'pc-parts',
                'Unrivaled read/write speeds up to 7450/6900 MB/s, integrated thermal heatsink for PS5 and PC.',
                'سرعت خواندن ۷۴۵۰ مگابایت بر ثانیه، هیت‌سینک اختصاصی خنک‌کننده با سازگاری ۱۰۰٪ با کنسول PS5 و کامپیوتر.',
                ['Capacity' => '2TB (2000GB)', 'Sequential Read' => 'Up to 7,450 MB/s', 'Sequential Write' => 'Up to 6,900 MB/s', 'Interface' => 'PCIe Gen 4.0 x4, NVMe 2.0'],
                ['ظرفیت' => '۲ ترابایت واقعی', 'سرعت خواندن متوالی' => 'تا ۷,۴۵۰ مگابایت در ثانیه', 'سرعت نوشتن متوالی' => 'تا ۶,۹۰۰ مگابایت در ثانیه', 'خنک‌کننده' => 'دارای هیت‌سینک آلومینیومی شرکتی']],

            // 8. Cameras & Drones
            ['sony-a7-iv', 'Sony Alpha 7 IV Full-Frame Mirrorless 33MP Camera', 'دوربین بدون آینه سونی Alpha 7 IV فول‌فریم ۳۳ مگاپیکسل',
                'camera', 'Sony', 115000000, 109000000, 3, 1, 'camera',
                '33MP full-frame Exmor R sensor, 4K 60p 10-bit recording, AI real-time autofocus for humans and birds.',
                'سنسور فول‌فریم ۳۳ مگاپیکسلی، فیلم‌برداری باکیفیت 4K با عمق رنگ ۱۰ بیت و فوکوس خودکار مبتنی بر هوش مصنوعی.',
                ['Sensor' => '33.0 MP Full-Frame Exmor R CMOS', 'Video' => '4K 60p 10-bit 4:2:2 All-Intra', 'Autofocus' => '759 phase-detection points with AI tracking', 'Stabilization' => '5-axis in-body 5.5 stops'],
                ['سنسور' => '۳۳ مگاپیکسل Full-Frame BSI CMOS', 'فیلم‌برداری' => '4K با سرعت ۶۰ فریم و رنگ ۱۰ بیتی 4:2:2', 'سیستم فوکوس' => '۷۵۹ نقطه فوکوس فازی با رهگیری لحظه‌ای چشم', 'لرزشگیر' => '۵ محوره داخل بدنه تا ۵.۵ استاپ']],

            ['dji-mini-4-pro', 'DJI Mini 4 Pro Drone with DJI RC 2 Smart Controller', 'هلی‌شات هوایی دی‌جی‌آی Mini 4 Pro با ریموت مانیتوردار',
                'camera', 'DJI', 47500000, 44900000, 7, 1, 'camera',
                'Under 249g ultralight drone, omnidirectional obstacle sensing, 4K/60fps HDR true vertical video.',
                'وزن فوق‌سبک زیر ۲۴۹ گرم، حسگرهای ۳۶۰ درجه جلوگیری از برخورد با موانع و فیلم‌برداری عمودی مناسب سوشال مدیا.',
                ['Weight' => '249g Ultra-lightweight', 'Video' => '4K/60fps HDR & 4K/100fps Slow Motion', 'Transmission' => 'DJI O4 up to 20 km FHD', 'Flight Time' => 'Up to 34 minutes per battery'],
                ['وزن' => '۲۴۹ گرم (بدون نیاز به پلاک در اغلب قوانین)', 'کیفیت تصویر' => '4K با سرعت ۶۰ فریم HDR و فیلم‌برداری عمودی واقعی', 'برد ارسال تصویر' => 'تا ۲۰ کیلومتر بدون قطعی O4', 'مدت پرواز' => '۳۴ دقیقه با هر باتری هوشمند']],

            // 9. Fashion & Apparel
            ['nike-air-zoom-pegasus-40', 'Nike Air Zoom Pegasus 40 Running Shoes Men', 'کفش رانینگ مردانه نایک مدل Air Zoom Pegasus 40',
                'fashion', 'Nike', 7900000, 6900000, 25, 0, 'fashion',
                'Engineered mesh upper for breathability, dual Zoom Air units and React foam midsole.',
                'رویه مش تنفسی دو لایه مهندسی‌شده، دو کپسول هوای زوم ایر در پاشنه و پنجه و فوم سبک واکنش‌گرا.',
                ['Upper' => 'Single-layer engineered mesh', 'Cushioning' => 'Nike React foam with dual Zoom Air units', 'Outsole' => 'Waffle-inspired rubber for traction', 'Weight' => '288g (Size 42)'],
                ['رویه' => 'مش تنفسی دولایه مقاوم', 'کفی میانی' => 'فوم نایک React همراه با دو کپسول Zoom Air', 'زیره' => 'لاستیک گریپ ضدلغزش وافل', 'وزن' => '۲۸۸ گرم بسیار سبک']],

            ['north-face-triclimate-jacket', 'The North Face Men Evolve II Triclimate Waterproof Jacket', 'کاپشن دوپوش ضدآب نورث فیس مدل تری‌کلایمت',
                'fashion', 'The North Face', 14500000, 12900000, 12, 0, 'fashion',
                'DryVent 2L waterproof outer shell paired with a warm removable fleece inner jacket.',
                'رویه ضدآب و بادگیر با فناوری دو لایه DryVent به همراه پوش دوم پلار پشمی گرم جداشونده.',
                ['Fabric' => 'DryVent 2-Layer 100% Nylon waterproof', 'Inner Layer' => '100% Recycled polyester micro-fleece', 'Hood' => 'Adjustable storable hood', 'Breathability' => 'High air-permeable'],
                ['جنس لایه اول' => 'پارچه نایلونی کاملاً ضدآب DryVent', 'لایه دوم داخلی' => 'کت پلار پشمی گرم جدا شونده', 'کلاه' => 'قابل تنظیم و جمع‌شونده در یقه', 'کاربری' => 'شهری، کوهنوردی و اسکی']],

            // 10. Beauty & Personal Care
            ['dior-sauvage-elixir', 'Dior Sauvage Elixir Men Eau de Parfum 60ml', 'عطر ادکلن مردانه دیور ساواج الکسیر حجم ۶۰ میلی‌لیتر',
                'beauty', 'Dior', 16500000, 15200000, 16, 1, 'beauty',
                'An intoxicatingly concentrated fragrance with notes of spicy cardamon, lavender, and rich amber woods.',
                'شاهکار عطرسازی فرانسوی با غلظت الکسیر، رایحه گرم و تند هل، اسطوخودوس دست‌چین و چوب‌های عنبری ماندگار.',
                ['Volume' => '60 ml / 2.0 fl.oz', 'Concentration' => 'Parfum Elixir', 'Top Notes' => 'Cinnamon, Nutmeg, Cardamom, Grapefruit', 'Longevity' => '18+ Hours monstrous sillage'],
                ['حجم' => '۶۰ میلی‌لیتر اورجینال فرانسوی', 'غلظت' => 'اکستریت / الکسیر پرفیوم', 'نت‌های ابتدایی' => 'هل، دارچین، جوز هندی و گریپ‌فروت', 'ماندگاری' => 'بیش از ۲۴ ساعت با پخش بوی فوق‌العاده']],

            ['dyson-supersonic-hair-dryer', 'Dyson Supersonic Nural Intelligent Hair Dryer', 'سشوار هوشمند دایسون Supersonic Nural با سنسور محافظت مو',
                'beauty', 'Dyson', 28500000, 25900000, 8, 1, 'beauty',
                'Scalp protect mode automatically reduces heat close to the head to protect natural scalp shine.',
                'سنسور مادون‌قرمز محافظت از پوست سر، موتور دیجیتال بدون برس V9 با جریان هوای متمرکز و ۵ سری مغناطیسی.',
                ['Motor' => 'Dyson digital motor V9 (110,000 rpm)', 'Heat Settings' => '4 precise heat modes', 'Attachments' => '5 magnetic styling nozzles included', 'Cable' => '2.8m salon length'],
                ['موتور' => 'دیجیتال V9 دایسون با دور ۱۱۰ هزار در دقیقه', 'حالت‌های حرارتی' => '۴ سطح دما همراه با باد سرد تثبیت‌کننده', 'سری‌ها' => '۵ عدد نازل مغناطیسی برای انواع حالت مو', 'طول سیم' => '۲.۸ متر استاندارد سالنی']],

            // 11. Sports & Outdoors
            ['giant-talon-1-29', 'Giant Talon 1 29er Mountain Bike 2024 Shimano Deore', 'دوچرخه کوهستان جاینت مدل Talon 1 سایز طوقه ۲۹ اینچ',
                'sport', 'Giant', 39000000, 36500000, 6, 1, 'sport',
                'ALUXX-grade butted aluminum frame, air suspension fork with lockout, Shimano Deore 1x10 drivetrain.',
                'بدنه سبک آلومینیوم اختصاصی ALUXX، دوشاخ فنربادی قفل‌شو روی فرمان و سیستم دنده ۱۰ سرعته شیمانو دئور.',
                ['Frame' => 'ALUXX-Grade Aluminum', 'Fork' => 'SXC32-2 RLR air spring, 100mm travel', 'Drivetrain' => 'Shimano Deore M5120 1x10-speed', 'Brakes' => 'Tektro HDC M275 hydraulic disc'],
                ['جنس فریم' => 'آلومینیوم اختصاصی ALUXX جاینت', 'دوشاخ' => 'بادی روغنی با ۱۰۰ میلی‌متر بازی و ریموت قفل‌شو', 'سیستم دنده' => 'شیمانو دئور ۱۰ سرعته تک طبق', 'ترمزها' => 'دیسک هیدرولیک تکترو']],

            ['naturehike-cloud-up-2', 'Naturehike Cloud-Up 2 Ultralight 20D Backpacking Tent', 'چادر کمپینگ دو نفره نیچرهایک مدل Cloud Up 2 پارچه 20D',
                'sport', 'Naturehike', 7800000, 6900000, 15, 0, 'sport',
                'Ultralight 1.4kg free-standing tent made with 20D silicone-coated nylon, 4000mm waterproof.',
                'وزن فوق‌العاده سبک ۱.۴ کیلوگرمی، پارچه نایلونی روکش سیلیکون با مقاومت ۴۰۰۰ میلی‌متر در برابر بارش شدید.',
                ['Capacity' => '2 Person', 'Weight' => '1.4 kg (Without pegs)', 'Rainfly' => '20D Silicone coated nylon 4000mm', 'Poles' => '7001 Aviation Aluminum alloy'],
                ['ظرفیت' => '۲ نفره استاندارد کوهنوردی', 'وزن' => '۱.۴ کیلوگرم مناسب کوله‌کشی طولانی', 'مقاومت ضدآب' => '۴۰۰۰ میلی‌متر پوشش سیلیکونی', 'تیرک‌ها' => 'آلومینیوم هوانوردی سری ۷۰۰۱']],

            // 12. Toys & Hobbies
            ['lego-technic-porsche-gt3', 'LEGO Technic Porsche 911 GT3 RS 1:8 Scale 2704 Pcs', 'لگو تکنیک پورشه 911 GT3 RS با ۲۷۰۴ قطعه ساختنی',
                'kids', 'LEGO', 22500000, 19900000, 6, 1, 'kids',
                'Authentic orange bodywork, working 4-speed gearbox, flat 6 engine with moving pistons.',
                'ماکت حرفه‌ای مقیاس ۱:۸، گیربکس ۴ سرعته مکانیکی با قابلیت تعویض دنده، موتور ۶ سیلندر با پیستون متحرک.',
                ['Pieces' => '2,704 building elements', 'Scale' => '1:8 Authentic replica', 'Dimensions' => '57cm long, 25cm wide', 'Age Rating' => '16+ Collectors Edition'],
                ['تعداد قطعات' => '۲,۷۰۴ قطعه مکانیکی دقیق', 'مقیاس' => '۱ به ۸ با جزئیات کامل پورشه', 'ابعاد' => 'طول ۵۷ سانتی‌متر و عرض ۲۵ سانتی‌متر', 'رده سنی' => 'بالای ۱۶ سال و کلکسیونرها']],

            ['rc-crawler-traxxas-trx4', 'Traxxas TRX-4 4WD Scale Trail Rock Crawler RC Truck', 'ماشین کنترلی صخره‌نورد ترکسس TRX-4 دیفرانسیل قفل‌دار',
                'kids', 'Traxxas', 29000000, null, 5, 0, 'kids',
                'Portal axles for huge ground clearance, remote locking front and rear differentials, waterproof electronics.',
                'محورهای پرتال مرتفع برای عبور از سخت‌ترین صخره‌ها، قفل دیفرانسیل از روی کنترلر و قطعات ضدآب.',
                ['Drive' => 'Shaft-driven 4WD', 'Axles' => 'Portal axles with remote locking diffs', 'Transmission' => 'High/Low range two-speed', 'Electronics' => 'Fully waterproof'],
                ['سیستم محرکه' => 'چهار چرخ متحرک ۴WD شفت‌دار', 'دیفرانسیل' => 'دو دیفرانسیل با قابلیت قفل از راه دور', 'گیربکس' => 'دو سرعته قدرتی و سرعتی', 'مقاومت' => 'الکترونیک کاملاً ضدآب در برف و گل']],

            // 13. Books & Stationery
            ['atomic-habits-hardcover', 'Atomic Habits by James Clear Collector Hardcover Edition', 'کتاب عادت‌های اتمی نوشته جیمز کلیر جلد نفیس گالینگور',
                'book', 'Penguin', 550000, 480000, 40, 0, 'book',
                'An easy and proven way to build good habits and break bad ones. Over 15 million copies sold globally.',
                'پرفروش‌ترین راهنمای عملی جهان برای ساخت عادت‌های سازنده پایدار و شکستن عادت‌های نامطلوب.',
                ['Author' => 'James Clear', 'Pages' => '320 pages', 'Binding' => 'Hardcover collector edition', 'Language' => 'English / Translated Persian'],
                ['نویسنده' => 'جیمز کلیر', 'تعداد صفحات' => '۳۲۰ صفحه با کاغذ باکیفیت', 'نوع جلد' => 'گالینگور نفیس با سلفون مات', 'قطع' => 'رقعی استاندارد']],

            ['lamy-2000-fountain-pen', 'Lamy 2000 Makrolon 14K Gold Nib Fountain Pen', 'خودنویس لوکس لامی ۲۰۰۰ آلمان با نوک طلای ۱۴ عیار',
                'book', 'Lamy', 11500000, 9900000, 10, 1, 'pens',
                'Iconic Bauhaus design since 1966, piston filling system, platinum-coated 14 carat gold nib.',
                'طراحی جاودانه باهاوس ساخت آلمان، بدنه ماکرولون با الیاف فایبرگلاس، مخزن پیستونی و نوک طلای ۱۴ عیار.',
                ['Nib Material' => '14K Gold with Platinum plating', 'Filling Mechanism' => 'Piston filling system', 'Body' => 'Fiberglass-reinforced Makrolon', 'Country of Origin' => 'Germany'],
                ['جنس نوک' => 'طلای ۱۴ عیار روکش پلاتین دست‌ساز', 'سیستم شارژ جوهر' => 'پیستونی یکپارچه بدون نیاز به کارتریج', 'جنس بدنه' => 'ماکرولون فایبرگلاس نشکن مات', 'کشور سازنده' => 'آلمان (هایدلبرگ)']],

            // 14. Tools & Technical Gear
            ['bosch-gsb-18v-90c', 'Bosch Professional GSB 18V-90 C Brushless Impact Drill', 'دریل پیچ‌گوشتی چکشی شارژی بوش براشلس مدل GSB 18V-90 C',
                'tools', 'Bosch', 16800000, 15400000, 9, 1, 'drill',
                '90Nm heavy-duty torque, brushless motor, KickBack Control, Bluetooth connectivity module.',
                'گشتاور سنگین ۹۰ نیوتن‌متر، موتور براشلس بدون زغال، سنسور محافظ مچ دست KickBack و اتصال بلوتوث.',
                ['Voltage' => '18V Li-Ion', 'Max Torque' => '90 Nm (Hard joint)', 'Chuck' => '13mm all-metal Röhm chuck', 'Weight' => '1.45 kg without battery'],
                ['ولتاژ باتری' => '۱۸ ولت لیتیوم یون بوش پرو', 'حداکثر گشتاور' => '۹۰ نیوتن‌متر قدرتمند', 'سه‌نظام' => '۱۳ میلی‌متری تمام فلزی رهم آلمان', 'سیستم ایمنی' => 'سیستم ترمز هوشمند KickBack Control']],

            ['dewalt-mechanic-142', 'DEWALT 142-Piece Mechanics Tool Set Chrome Vanadium', 'جعبه ابزار مکانیکی دیوالت ۱۴۲ پارچه با روکش کروم براق',
                'tools', 'DEWALT', 13900000, 12500000, 12, 0, 'toolset',
                '72-tooth ratchets with 5-degree arc swing, DirectTorque technology to prevent fastener rounding.',
                'آچارهای جغجغه‌ای ۷۲ دندانه سریع، بکس‌های فولادی نشکن با فناوری جلوگیری از رد کردن پیچ و کیف ضدضربه.',
                ['Piece Count' => '142 Pieces', 'Material' => 'Chrome Vanadium Steel with polish finish', 'Ratchets' => '1/4" and 3/8" 72-tooth', 'Case' => 'Durable blow-mold storage case'],
                ['تعداد اقلام' => '۱۴۲ پارچه آچار، بکس و متعلقات', 'جنس آلیاژ' => 'فولاد کروم وانادیوم سخت‌کاری شده', 'جغجغه‌ها' => 'دو عدد درایو ۱/۴ و ۳/۸ با زاویه گردش ۵ درجه', 'کیف' => 'جعبه BMC مقاوم در برابر ضربه']],

            // 15. Networking & Smart Office
            ['asus-rog-rapture-gt-axe16000', 'ASUS ROG Rapture GT-AXE16000 Quad-Band Wi-Fi 6E Gaming Router', 'روتر گیمینگ ایسوس ROG Rapture چهاربانده با سرعت ۱۶۰۰۰ مگابیت',
                'network', 'ASUS', 38000000, 35500000, 5, 1, 'network',
                'World first quad-band Wi-Fi 6E router, dual 10G ports, quad 1G ports and 2.5G WAN port.',
                'اولین روتر چهاربانده Wi-Fi 6E جهان، دو پورت شبکه پرسرعت ۱۰ گیگابیت، پینگ اختصاصی برای گیمرها.',
                ['Wi-Fi Standard' => 'Wi-Fi 6E (802.11axe) Quad-Band 16Gbps', 'Ports' => '2x 10Gbps, 1x 2.5Gbps WAN, 4x 1Gbps LAN', 'Processor' => '2.0 GHz quad-core 64-bit CPU', 'Antennas' => '8 external antennas'],
                ['استاندارد شبکه' => 'چهاربانده Wi-Fi 6E با پهنای باند ۱۶۰۰۰ مگابیت', 'پورت‌های پرسرعت' => 'دو پورت ۱۰ گیگابیتی و یک پورت ۲.۵ گیگ', 'پردازنده' => 'چهار هسته‌ای ۲ گیگاهرتزی با ۲ گیگ رم', 'آنتن‌ها' => '۸ آنتن خارجی قدرتمند']],

            ['tp-link-deco-x50-3pack', 'TP-Link Deco X50 AX3000 Whole Home Mesh Wi-Fi 6 (3-Pack)', 'پک ۳ عددی سیستم مش وای‌فای تی‌پی‌لینک Deco X50 پوشش کل خانه',
                'network', 'TP-Link', 16500000, 14900000, 11, 0, 'network',
                'Seamless roaming across up to 6,500 sq ft, AI-driven mesh, connects over 150 smart devices.',
                'پوشش یکدست وای‌فای در کل ساختمان تا ۶۰۰ مترمربع، رومینگ هوشمند بدون قطعی و اتصال بیش از ۱۵۰ دستگاه همزمان.',
                ['Speed' => 'AX3000 (2402 Mbps on 5GHz + 574 Mbps on 2.4GHz)', 'Coverage' => 'Up to 600 m² (6,500 sq ft)', 'Ports per unit' => '3x Gigabit Ethernet ports', 'AI Mesh' => 'Automated routing'],
                ['سرعت وای‌فای' => '۳۰۰۰ مگابیت بر ثانیه دو بانده Wi-Fi 6', 'پوشش‌دهی' => 'تا ۶۰۰ متر مربع بدون نقطه کور', 'پورت‌های هر دستگاه' => '۳ پورت شبکه گیگابیتی', 'سیستم هوشمند' => 'مسیریابی خودکار مبتنی بر هوش مصنوعی']],

            // 16. Digital Accessories
            ['anker-prime-20000-200w', 'Anker Prime 20,000mAh Power Bank 200W Output with Smart Display', 'پاوربانک فست‌شارژ انکر Prime ظرفیت ۲۰۰۰۰ با توان ۲۰۰ وات',
                'accessories', 'Anker', 9800000, 8900000, 18, 1, 'accessories',
                '200W total output with two USB-C ports, compact can-size design, digital smart color screen.',
                'توان خروجی خارق‌العاده ۲۰۰ وات برای شارژ همزمان دو لپ‌تاپ مک‌بوک پرو، نمایشگر رنگی هوشمند درصد و توان.',
                ['Capacity' => '20,000 mAh (72Wh)', 'Max Single Port Output' => '100W Power Delivery 3.0', 'Total Output' => '200W simultaneous multi-device', 'Display' => 'TFT LCD live battery stats'],
                ['ظرفیت باتری' => '۲۰,۰۰۰ میلی‌آمپر ساعت (۷۲ وات ساعت)', 'خروجی تک پورت' => '۱۰۰ وات PD برای شارژ لپ‌تاپ', 'مجموع توان خروجی' => '۲۰۰ وات برای سه دستگاه همزمان', 'نمایشگر' => 'صفحه نمایش دیجیتال TFT رنگی وضعیت شارژ']],

            ['ugreen-nexode-300w-gan', 'UGREEN Nexode 300W 5-Port GaN Fast Desktop Charger', 'شارژر رومیزی ۳۰۰ وات یوگرین Nexode مدل GaN مجهز به ۵ پورت',
                'accessories', 'UGREEN', 11200000, 10200000, 14, 0, 'accessories',
                '300W maximum output, PD 3.1 single-port 140W fast charging, GaN III technology for cool efficiency.',
                'توان دیوانه‌وار ۳۰۰ وات، پشتیبانی از شارژ سریع ۱۴۰ واتی PD 3.1 برای مک‌بوک پرو ۱۶ و خنک‌کنندگی عالی گان نسل ۳.',
                ['Total Output' => '300W Max', 'Ports' => '4x USB-C + 1x USB-A', 'Single Port Max' => '140W PD 3.1 Fast Charge', 'Technology' => 'GaNFast & Thermal Guard System'],
                ['توان خروجی کل' => '۳۰۰ وات فوق سریع', 'پورت‌ها' => '۴ پورت Type-C و ۱ پورت USB-A', 'حداکثر توان تک پورت' => '۱۴۰ وات با فناوری PD 3.1', 'فناوری' => 'تراشه خنک GaNFast نسل سه']],

            // Additional 26 products to reach 70 comprehensive items across all 16 categories:
            ['marshall-stanmore-iii', 'Marshall Stanmore III Bluetooth Home Speaker', 'اسپیکر خانگی مارشال استنمور ۳ با صدای فراگیر استریو',
                'audio', 'Marshall', 24500000, 22900000, 8, 1, 'audio',
                'Iconic vintage design, reimagined wider stereo soundstage, dynamic loudness and Bluetooth 5.2.',
                'طراحی نوستالژیک چرمی مارشال، صدای استریوی عمیق و پرقدرت، ورودی آنالوگ RCA و بلوتوث نسخه ۵.۲.',
                ['Power Output' => '80W Class D Amplification', 'Connectivity' => 'Bluetooth 5.2, 3.5mm AUX, RCA', 'Frequency Range' => '45–20,000 Hz', 'Weight' => '4.25 kg'],
                ['توان خروجی' => '۸۰ وات آمپلی‌فایر کلاس D', 'اتصالات' => 'بلوتوث ۵.۲، جک ۳.۵ و ورودی RCA', 'پاسخ فرکانسی' => '۴۵ تا ۲۰,۰۰۰ هرتز', 'وزن' => '۴.۲۵ کیلوگرم']],

            ['sony-wf1000xm5', 'Sony WF-1000XM5 Truly Wireless Noise Canceling Earbuds', 'هندزفری بی‌سیم سونی WF-1000XM5 با برترین پردازنده صوتی',
                'audio', 'Sony', 13800000, 12900000, 19, 0, 'audio',
                'Ultra-compact earbuds with Integrated Processor V2, bone-conduction voice sensors, and LDAC audio.',
                'سبک‌ترین و خوش‌فرم‌ترین ایرپاد سونی با سنسورهای استخوانی انتقال صدا و پشتیبانی از صدای های‌رزولوشن.',
                ['Battery' => '8 Hours (24h with case)', 'Codecs' => 'LDAC, LC3, AAC, SBC', 'Water Resistance' => 'IPX4', 'Weight' => '5.9g per earbud'],
                ['شارژدهی' => '۸ ساعت مداوم (۲۴ ساعت همراه کیس)', 'کدک‌های صوتی' => 'LDAC کیفیت استودیویی و AAC', 'مقاومت در برابر رطوبت' => 'استاندارد IPX4', 'وزن هر گوشی' => '۵.۹ گرم سبک']],

            ['suunto-race-titanium', 'Suunto Race Titanium Multisport GPS Watch with AMOLED', 'ساعت ورزشی تخصصی سونتو Race تیتانیوم با نمایشگر امولد',
                'wearable', 'Suunto', 29500000, 27900000, 7, 1, 'wearable',
                'AMOLED touchscreen, titanium bezel, sapphire crystal, HRV recovery measurement, free offline outdoor maps.',
                'نمایشگر لمسی امولد خیره‌کننده، بزل تیتانیومی مقاوم، پایش پیشرفته تغییرات ضربان قلب و نقشه‌های توپوگرافی آفلاین رایگان.',
                ['Display' => '1.43" AMOLED 466x466 high resolution', 'Bezel' => 'Titanium Grade 5', 'Battery' => 'Up to 26 days in daily mode, 40h performance GPS', 'Water Resistance' => '100 meters'],
                ['صفحه نمایش' => '۱.۴۳ اینچ امولد با وضوح فوق‌العاده', 'جنس بزل' => 'تیتانیوم گرید ۵ بسیار سبک و نشکن', 'شارژدهی باتری' => 'تا ۲۶ روز استفاده روزمره و ۴۰ ساعت جی‌پی‌اس مداوم', 'مقاومت در برابر آب' => 'تا عمق ۱۰۰ متر']],


            ['xiaomi-watch-2-pro', 'Xiaomi Watch 2 Pro Wear OS Snapdragon W5+ Gen 1', 'ساعت هوشمند شیائومی Watch 2 Pro با سیستم عامل گوگل',
                'wearable', 'Xiaomi', 11500000, 10200000, 25, 0, 'wearable',
                'Wear OS by Google, 4nm flagship processor, body composition analysis, stainless steel case.',
                'دسترسی کامل به فروشگاه گوگل پلی و اپلیکیشن‌ها، سنسور سنجش ترکیبات بدنی و بدنه استیل لوکس.',
                ['OS' => 'Wear OS by Google', 'Processor' => 'Qualcomm Snapdragon W5+ Gen 1 (4nm)', 'Display' => '1.43" AMOLED 466x466 600nits', 'Battery' => 'Up to 65 Hours'],
                ['سیستم‌عامل' => 'Wear OS گوگل با پشتیبانی فارسی', 'پردازنده' => 'اسنپدراگون W5+ نسل ۱ با معماری ۴ نانومتر', 'نمایشگر' => '۱.۴۳ اینچ اولد با روشنایی ۶۰۰ نیت', 'باتری' => 'تا ۶۵ ساعت کار مداوم']],

            ['dyson-v15-detect-absolute', 'Dyson V15 Detect Absolute Cordless Stick Vacuum', 'جاروبرقی بی‌سیم دایسون V15 Detect با سنسور لیزری هوشمند',
                'home', 'Dyson', 49900000, 47500000, 7, 1, 'home',
                'Laser illumination reveals invisible dust, piezo sensor measures microscopic particles, 240AW suction.',
                'نور لیزر سبز برای نشان دادن ریزترین ذرات غبار، سنسور سنجش اندازه ذرات و مکش خارق‌العاده ۲۴۰ وات هوا.',
                ['Suction Power' => '240 Air Watts (Boost mode)', 'Run Time' => 'Up to 60 minutes fade-free', 'Filtration' => 'Advanced whole-machine HEPA', 'Bin Volume' => '0.77 Liters'],
                ['قدرت مکش' => '۲۴۰ ایروات مافوق صوت', 'مدت زمان کارکرد' => 'تا ۶۰ دقیقه بدون افت توان مکش', 'فیلتراسیون' => 'فیلتر هپای چندلایه ضدحساسیت', 'حجم مخزن' => '۰.۷۷ لیتر با تخلیه بهداشتی']],

            ['nespresso-vertuo-pop', 'Nespresso Vertuo Pop Coffee and Espresso Machine Spicy Red', 'دستگاه قهوه‌ساز و اسپرسوساز نسپرسو مدل ورتو پاپ قرمز',
                'kitchen', 'Nespresso', 9800000, 8900000, 18, 0, 'kitchen',
                'Centrifusion extraction technology, reads individual capsule barcodes, brews 4 cup sizes at the touch of a button.',
                'فناوری چرخش سانتریفیوژ برای عصاره‌گیری کرمای غلیظ قهوه، خواندن خودکار بارکد کپسول و تهیه ۴ سایز فنجان با یک کلیک.',
                ['Technology' => 'Centrifusion extraction (4,000 rpm)', 'Cup Sizes' => 'Mug, Gran Lungo, Double Espresso, Espresso', 'Water Tank' => '0.6 Liters compact', 'Heat-up Time' => '30 Seconds'],
                ['فناوری عصاره‌گیری' => 'چرخش ۴۰۰۰ دور بر دقیقه سانتریفیوژ', 'اندازه فنجان‌ها' => '۴ سایز از اسپرسو تکی تا ماگ ۲۳۰ میل', 'مخزن آب' => '۰.۶ لیتر بهینه و کم‌جا', 'زمان آماده‌سازی' => 'تنها ۳۰ ثانیه از زمان روشن شدن']],

            ['nintendo-switch-oled', 'Nintendo Switch OLED Model Mario Red Edition 64GB', 'کنسول بازی نینتندو سوییچ نسخه اولد با حافظه ۶۴ گیگ',
                'gaming', 'Nintendo', 19500000, 18200000, 12, 1, 'gaming',
                'Vibrant 7-inch OLED screen, wide adjustable stand, wired LAN dock, enhanced crystal-clear audio.',
                'صفحه نمایش ۷ اینچی خیره‌کننده OLED با رنگ‌های زنده، پایه فلزی مستحکم و اسپیکرهای استریوی بهبودیافته.',
                ['Display' => '7.0-inch OLED multi-touch screen', 'Internal Storage' => '64GB (expandable via microSD)', 'Battery Life' => 'Approx. 4.5 to 9 hours', 'Output' => 'Up to 1080p via HDMI in TV mode'],
                ['صفحه نمایش' => '۷.۰ اینچ اولد لمسی خازنی', 'حافظه داخلی' => '۶۴ گیگابایت با پشتیبانی رم میکرو', 'شارژدهی باتری' => 'بین ۴.۵ تا ۹ ساعت پیوسته', 'خروجی تصویر' => 'کیفیت Full HD روی تلویزیون']],

            ['logitech-g-pro-x-superlight-2', 'Logitech G PRO X SUPERLIGHT 2 Lightspeed Gaming Mouse', 'ماوس گیمینگ لاجیتک G Pro X Superlight 2 با وزن ۶۰ گرم',
                'gaming', 'Logitech G', 8900000, 8200000, 30, 0, 'gaming',
                'HERO 2 32K sensor, LIGHTFORCE hybrid optical-mechanical switches, 95 hours continuous battery life.',
                'سنسور ۳۲۰۰۰ دی‌پی‌آی نسل جدید HERO 2، سوییچ‌های هیبریدی نوری و شارژدهی ۹۵ ساعته برای مسابقات ورزش‌های الکترونیک.',
                ['Weight' => '60 grams ultra-light', 'Sensor' => 'HERO 2 (100–32,000 DPI)', 'Polling Rate' => 'Up to 4000 Hz with Lightspeed', 'Battery' => '95 Hours active play'],
                ['وزن خالص' => 'فقط ۶۰ گرم سبک و بدون سوراخ', 'سنسور' => 'سنسور هیرو ۲ با دقت ۳۲۰۰۰ DPI', 'نرخ نمونه‌برداری' => 'تا ۴۰۰۰ هرتز مافوق سریع', 'شارژدهی' => '۹۵ ساعت کار با هر بار شارژ']],

            ['amd-ryzen-9-7950x3d', 'AMD Ryzen 9 7950X3D 16-Core 3D V-Cache Flagship Processor', 'پردازنده قدرتمند ای‌ام‌دی Ryzen 9 7950X3D کش سه‌بعدی',
                'pc-parts', 'AMD', 34500000, 32900000, 8, 1, 'pc-parts',
                '16 cores, 32 threads, 144MB AMD 3D V-Cache technology for world-class gaming and high-performance multithreading.',
                '۱۶ هسته و ۳۲ رشته موازی، مجهز به کش غول‌آسای سه‌بعدی ۱۴۴ مگابایتی برای دستیابی به بالاترین نرخ فریم در بازی‌های فوق‌سنگین.',
                ['Cores / Threads' => '16 Cores / 32 Threads', 'Max Boost Clock' => 'Up to 5.7 GHz', 'Total Cache' => '144MB (L2+L3 3D V-Cache)', 'Socket' => 'AM5 PCIe 5.0 ready'],
                ['هسته‌ها و رشته‌ها' => '۱۶ هسته و ۳۲ رشته پردازشی', 'حداکثر فرکانس بوست' => 'تا ۵.۷ گیگاهرتز', 'حافظه کش' => '۱۴۴ مگابایت کش سه‌بعدی 3D V-Cache', 'سوکت مادربرد' => 'AMD AM5 با استاندارد PCIe 5.0']],


            ['corsair-dominator-titanium-64gb', 'Corsair Dominator Titanium RGB 64GB (2x32GB) DDR5 6000MHz', 'رم کامپیوتر کورسیر Dominator Titanium ظرفیت ۶۴ گیگابایت DDR5',
                'pc-parts', 'Corsair', 18500000, 17200000, 11, 0, 'pc-parts',
                'Patented DHX cooling technology, 11 addressable RGB LEDs per module, premium forged aluminum top bar.',
                'خنک‌کنندگی انحصاری DHX، تاچ‌بارهای تعویض‌پذیر با آلومینیوم مات و بالاترین پایداری در اورکلاک.',
                ['Capacity' => '64GB (2 x 32GB)', 'Speed' => 'DDR5-6000 MHz CL30', 'Heat Spreader' => 'Forged Aluminum with DHX', 'Profiles' => 'Intel XMP 3.0 & AMD EXPO'],
                ['ظرفیت کل' => '۶۴ گیگابایت (دو ماژول ۳۲ گیگ)', 'فرکانس کاری' => '۶۰۰۰ مگاهرتز با تاخیر پایین CL30', 'خنک‌کننده' => 'هیت‌سینک آلومینیومی فورج شده', 'پروفایل اورکلاک' => 'پشتیبانی از XMP 3.0 و EXPO']],

            ['gopro-hero-12-black', 'GoPro HERO12 Black 5.3K Waterproof Action Camera', 'دوربین اکشن گوپرو HERO 12 Black با فیلمبرداری 5.3K ضدآب',
                'camera', 'GoPro', 23500000, 21900000, 15, 1, 'camera',
                'Emmy-winning HyperSmooth 6.0 stabilization, HDR 5.3K and 4K video, 2x longer runtime with Enduro battery.',
                'لرزشگیر بی‌نظیر هایپراسموت ۶ برنده جایزه امی، فیلمبرداری بدون لرزش زیر آب تا عمق ۱۰ متر بدون نیاز به قاب.',
                ['Video' => '5.3K 60fps, 4K 120fps HDR', 'Stabilization' => 'HyperSmooth 6.0 with 360 Horizon Lock', 'Waterproof' => '10m (33ft) without housing', 'Photo' => '27 Megapixels'],
                ['کیفیت ویدیو' => '5.3K شصت فریم و 4K صدوبیست فریم', 'لرزشگیر' => 'HyperSmooth 6.0 با قفل افق ۳۶۰ درجه', 'ضدآب' => 'تا عمق ۱۰ متر زیر آب', 'عکاسی' => '۲۷ مگاپیکسل با وضوح بالا']],

            ['canon-eos-r6-mark-ii', 'Canon EOS R6 Mark II Mirrorless Camera with 24-105mm Lens', 'دوربین عکاسی بدون‌آینه کانن EOS R6 Mark II همراه کیت لنز',
                'camera', 'Canon', 118000000, 112000000, 4, 1, 'camera',
                '24.2MP full-frame CMOS sensor, 40 fps electronic shutter, 4K 60p 10-bit oversampled internal video.',
                'سنسور فول‌فریم ۲۴ مگاپیکسلی با فوکوس هوشمند مبتنی بر هوش مصنوعی تشخیص چشم انسان، حیوان و پرنده.',
                ['Sensor' => '24.2MP Full-Frame CMOS', 'Burst Shooting' => 'Up to 40 fps electronic', 'Video Recording' => '6K oversampled 4K 60p 10-bit', 'In-Body Stabilization' => 'Up to 8 stops with IBIS'],
                ['سنسور' => 'فول‌فریم ۲۴.۲ مگاپیکسلی سیموس', 'سرعت شاتر' => 'تا ۴۰ فریم بر ثانیه پیاپی', 'فیلمبرداری' => '4K شصت فریم ۱۰ بیتی حرفه‌ای', 'لرزشگیر داخلی' => 'لرزشگیر ۵ محوره تا ۸ استاپ']],

            ['nike-air-jordan-1-low', 'Nike Air Jordan 1 Low OG Chicago White/Gym Red/Black', 'کتانی نایک ایر جردن ۱ لو مدل نوستالژیک شیکاگو',
                'fashion', 'Nike', 9500000, 8700000, 14, 1, 'fashion',
                'Iconic basketball silhouette inspired by the 1985 classic, genuine leather upper with Air-Sole unit.',
                'یکی از نمادین‌ترین کفش‌های ورزشی تاریخ با ترکیب رنگ افسانه‌ای شیکاگو و چرم طبیعی دست‌دوز.',
                ['Upper Material' => 'Genuine top-grain leather', 'Cushioning' => 'Encapsulated Nike Air-Sole unit', 'Outsole' => 'Solid rubber with pivot circle', 'Style' => 'Low-top retro lifestyle'],
                ['جنس رویه' => 'چرم طبیعی با منافذ تنفسی', 'کفی داخلی' => 'کپسول هوای کپسوله ایر نایک', 'زیره کفش' => 'لاستیک ضدسایش با چسبندگی بالا', 'مدل' => 'ساق کوتاه اورجینال شیکاگو']],

            ['the-north-face-1996-nuptse', 'The North Face 1996 Retro Nuptse 700-Down Jacket Black', 'کاپشن زمستانه نورث‌فیس مدل ۱۹۹۶ نوپتسه پر غاز مشکی',
                'fashion', 'The North Face', 17500000, 15900000, 8, 1, 'fashion',
                'Original shiny ripstop fabric, 700-fill goose down insulation, packable hood folds into collar.',
                'پارچه ضد پارگی ریپ‌استاپ، عایق پر غاز ۷۰۰ با استاندارد RDS و کلاه تاشو مخفی در یقه.',
                ['Insulation' => '700 fill goose down (RDS certified)', 'Shell Fabric' => '100% recycled nylon ripstop with DWR', 'Pockets' => 'Secure-zip hand pockets, stowable in pocket', 'Weight' => '775 grams'],
                ['نوع عایق' => 'پر طبیعی غاز درجه ۷۰۰ فوق‌گرم', 'پارچه رویه' => 'نایلون ریپ‌استاپ ضدآب و ضدباد', 'جیب‌ها' => 'جیب‌های زیپ‌دار با قابلیت جمع‌شدن کاپشن', 'وزن' => '۷۷۵ گرم بسیار سبک']],

            ['creed-aventus-edp', 'Creed Aventus Eau de Parfum 100ml Luxury Fragrance', 'عطر ادکلن سلطنتی کرید اونتوس مردانه حجم ۱۰۰ میلی‌لیتر',
                'beauty', 'Creed', 22500000, 20900000, 9, 1, 'beauty',
                'Sensual, audacious and contemporary scent with notes of pineapple, bergamot, birch and oakmoss.',
                'پرفروش‌ترین و پرطرفدارترین عطر نیش جهان با پخش بوی فوق‌العاده و خط بوی فراموش‌نشدنی آناناس و دود توس.',
                ['Volume' => '100ml / 3.3 fl oz', 'Concentration' => 'Eau de Parfum (Millesime)', 'Top Notes' => 'Bergamot, Blackcurrant, Apple, Pineapple', 'Base Notes' => 'Birch, Oakmoss, Ambergris, Vanilla'],
                ['حجم' => '۱۰۰ میلی‌لیتر اسپری شیشه‌ای دست‌ساز', 'غلظت' => 'ادوپرفیوم نیش میلسیم با ماندگاری بالا', 'نت‌های ابتدایی' => 'ترنج، انگور سیاه، سیب تازه و آناناس', 'نت‌های پایانی' => 'چوب توس، خزه بلوط، عنبر سائل و وانیل']],

            ['braun-series-9-pro', 'Braun Series 9 Pro Electric Foil Shaver with SmartCare Center', 'ماشین اصلاح و ریش‌تراش براون آلمان سری ۹ پرو با پایه شستشو',
                'beauty', 'Braun', 18900000, 17500000, 12, 1, 'beauty',
                'ProLift trimmer cuts 1, 3, or 7-day beards, 40,000 sonic cuts per minute, 5-in-1 SmartCare center and 100% waterproof.',
                'مجهز به تیغه جدید پرولیفت برای اصلاح موهای خوابیده صورت، ۴۰ هزار برش لرزشی در دقیقه و پایه اتوماتیک ضدعفونی و شارژ.',
                ['Shaving Elements' => '5 synchronized shaving elements with ProLift trimmer', 'Sonic Technology' => '10,000 sonic micro-vibrations', 'Cleaning' => '5-in-1 SmartCare center included', 'Origin' => 'Made in Germany'],
                ['تیغه‌ها' => '۵ المان اصلاح هماهنگ با تیغه تیتانیوم پرولیفت', 'موتور' => 'موتور خطی سونیک با ۴۰ هزار حرکت در دقیقه', 'پایه نگهداری' => 'پایه شستشو و شارژ ۵ کاره اتوماتیک', 'کشور سازنده' => '۱۰۰٪ ساخت آلمان']],

            ['trek-marlin-7-gen-3', 'Trek Marlin 7 Gen 3 Cross-Country Trail Mountain Bike', 'دوچرخه کوهستان حرفه‌ای ترک Marlin 7 نسل سوم',
                'sport', 'Trek', 44000000, 41500000, 5, 1, 'sport',
                'Alpha Silver Aluminum frame with internal cable routing, RockShox Judy fork, Shimano Deore 1x10.',
                'بدنه آلومینیومی آلفا سیلور با سیم‌کشی داخلی، کمک‌فنر روغنی راک‌شاکس جودی با قفل هیدرولیک و ترمزهای دیسکی.',
                ['Frame' => 'Alpha Silver Aluminum with internal routing', 'Fork' => 'RockShox Judy Silver, 100mm travel', 'Drivetrain' => 'Shimano Deore M5120 1x10 speed', 'Brakes' => 'Tektro HD-M275 hydraulic disc'],
                ['تنه دوچرخه' => 'آلومینیوم هیدروفرم آلفا سیلور ترک', 'کمک‌فنر' => 'راک‌شاکس جودی سیلور با بازی ۱۰۰ میلی‌متر', 'سیستم دنده' => 'شیمانو دئور ۱۰ سرعته تکی', 'ترمزها' => 'دیسک هیدرولیک تکترو']],

            ['coleman-instant-cabin-tent-6p', 'Coleman WeatherMaster 6-Person Instant Setup Cabin Tent', 'چادر مسافرتی و کمپینگ ۶ نفره ضدآب کلمن آمریکا',
                'sport', 'Coleman', 18900000, 16900000, 7, 0, 'sport',
                'Sets up in 60 seconds with pre-attached poles, WeatherTec system with patented welded floors.',
                'راه‌اندازی فوری در ۶۰ ثانیه به لطف پایه‌های تلسکوپی متصل، کف جوش‌خورده ضد نفوذ آب باران و سقف بلند.',
                ['Capacity' => '6 Persons (fits 2 queen airbeds)', 'Setup Time' => '60 Seconds instant', 'Dimensions' => '10 x 9 ft with 6 ft center height', 'WeatherTec' => 'Tested up to 35+ mph winds'],
                ['ظرفیت' => '۶ نفر به همراه وسایل کامل کمپینگ', 'زمان برپایی' => 'کمتر از ۱ دقیقه به سادگی باز کردن چتر', 'ابعاد' => '۳ در ۲.۷ متر با ارتفاع ایستادن ۱۸۰ سانت', 'مقاومت جوی' => 'تست شده در بادهای شدید و باران سیل‌آسا']],

            ['lego-star-wars-millennium-falcon', 'LEGO Star Wars Millennium Falcon Ultimate Collector 75192', 'لگو سفینه شاهکار فالکون هزاره جنگ ستارگان سری کلکسیونر',
                'kids', 'LEGO', 42000000, 39500000, 6, 1, 'kids',
                '7,541 pieces, intricate exterior detailing, interchangeable sensor dishes, 7 minifigures included.',
                'یکی از بزرگ‌ترین و باشکوه‌ترین مجموعه‌های تاریخ لگو با بیش از ۷۵۰۰ قطعه و مینی‌فیگورهای هان سولو و لیا.',
                ['Piece Count' => '7,541 Pieces', 'Minifigures' => '7 iconic Star Wars characters', 'Dimensions' => '84cm long x 60cm wide x 21cm high', 'Age Recommendation' => '16+ Adult Collectors'],
                ['تعداد قطعات' => '۷,۵۴۱ قطعه استاندارد دانمارکی', 'مینی‌فیگورها' => '۷ شخصیت اصلی فیلم سینمایی جنگ ستارگان', 'ابعاد مدل کامل' => 'طول ۸۴ سانتیمتر در عرض ۶۰ سانتیمتر', 'رده سنی' => 'نوجوانان و بزرگسالان علاقه‌مند به کلکسیون']],

            ['traxxas-trx-4-scale-crawler', 'Traxxas TRX-4 1/10 Scale Rock Crawler 4WD Remote Car', 'ماشین کنترلی صخره‌نورد حرفه‌ای ترکسس TRX-4 آفرود ۴ چرخ',
                'kids', 'Traxxas', 28500000, 26900000, 8, 0, 'kids',
                'Portal axles for huge ground clearance, remote-locking differentials, waterproof electronics, high/low range transmission.',
                'اکسل‌های پورتال برای عبور از سخت‌ترین موانع صخره‌ای، قفل دیفرانسیل از روی رادیوکنترل و موتور گشتاور بالا.',
                ['Scale' => '1/10 4WD Scale and Trail Crawler', 'Speed & Transmission' => '2-speed high/low remote shifting', 'Differentials' => 'Remote-locking T-Lock front and rear', 'Waterproof' => 'Fully waterproof electronics'],
                ['مقیاس' => '۱ به ۱۰ حرفه‌ای با بدنه طرح رنج‌روور', 'گیربکس' => 'دو سرعته سنگین و سبک قابل تعویض از راه دور', 'دیفرانسیل' => 'قفل برقی هوشمند روی هر دو اکسل', 'عایق‌بندی' => 'ضدآب کامل برای عبور از رودخانه و گل']],

            ['designing-data-intensive-apps', 'Designing Data-Intensive Applications Hardcover by Martin Kleppmann', 'کتاب طراحی سیستم‌های داده‌محور مقیاس‌پذیر مارتین کلمپن',
                'book', 'O\'Reilly', 2200000, 1950000, 35, 1, 'book',
                'Comprehensive architectural guide exploring storage engines, distributed consensus, stream processing and replication.',
                'مرجع بدون رقیب مهندسی نرم‌افزار برای یادگیری معماری پایگاه‌های داده، سیستم‌های توزیع‌شده و پردازش داده کلان.',
                ['Publisher' => 'O\'Reilly Media', 'Author' => 'Martin Kleppmann', 'Pages' => '616 Pages', 'Language' => 'English / Illustrated'],
                ['انتشارات' => 'اورایلی مدیا (چاپ اصل با جلد سخت)', 'نویسنده' => 'پروفسور مارتین کلمپن محقق دانشگاه کمبریج', 'تعداد صفحات' => '۶۱۶ صفحه قطع رحلی', 'موضوع' => 'معماری سیستم‌های توزیع‌شده و پایگاه داده']],

            ['clean-code-robert-martin', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'کتاب مرجع کد تمیز نوشته رابرت سی مارتین انتشارات پیرسون',
                'book', 'Pearson', 1850000, 1650000, 40, 1, 'book',
                'The timeless programming handbook by Uncle Bob Martin on meaningful names, solid functions, refactoring, and code hygiene.',
                'یکی از مهم‌ترین کتاب‌های کلاسیک تاریخ برنامه‌نویسی برای نوشتن کدهای تمیز، خوانا، قابل نگهداری و حرفه‌ای.',
                ['Author' => 'Robert C. Martin (Uncle Bob)', 'Publisher' => 'Prentice Hall / Pearson', 'Pages' => '464 Pages', 'Topic' => 'Clean Code & Agile Craftsmanship'],
                ['نویسنده' => 'رابرت سی مارتین (عمو باب)', 'ناشر' => 'پیرسون با کاغذ مرغوب تحریر', 'تعداد صفحات' => '۴۶۴ صفحه آموزشی با مثال‌های واقعی', 'موضوع' => 'اصول طراحی و مهندسی نرم‌افزار حرفه‌ای']],



            ['delonghi-dedica-deluxe', 'De\'Longhi Dedica Deluxe Pump Espresso Machine EC685M', 'دستگاه اسپرسوساز دلونگی مدل دلیکا دولوکس بدنه تمام استیل',
                'kitchen', 'De\'Longhi', 11800000, 10700000, 15, 1, 'kitchen',
                '15-bar Italian high pressure pump, thermoblock heating system fast 40s warm up, adjustable frother.',
                'پمپ حرفه‌ای ۱۵ باری ساخت ایتالیا، آماده‌سازی قهوه زیر ۴۰ ثانیه و نازل بخار برای تهیه لاته و کاپوچینوی فوم‌دار.',
                ['Pump Pressure' => '15 Bar Italian Pressure', 'Heating System' => 'Rapid Thermoblock technology', 'Dimensions' => 'Only 15cm slim design', 'Water Tank' => '1.1 Liters removable'],
                ['فشار پمپ' => '۱۵ بار واقعی ایتالیایی', 'سیستم حرارتی' => 'ترموبلاک سریع با قابلیت گرمایش فنجان', 'عرض بدنه' => 'تنها ۱۵ سانتی‌متر فوق باریک', 'مخزن آب' => '۱.۱ لیتر جداشونده با فیلتر رسوب‌گیر']],

            ['kitchenaid-artisan-stand-mixer', 'KitchenAid Artisan Series 4.8L Tilt-Head Stand Mixer Empire Red', 'همزن و میکسر قنادی کیچن‌اید مدل آرتیسان کاسه استیل قرمز',
                'kitchen', 'KitchenAid', 39500000, 36900000, 6, 1, 'kitchen',
                'Direct drive planetary action, 10 speed settings, full metal construction, includes wire whip and dough hook.',
                'بدنه تمام فلزی سنگین و بدون لرزش، حرکت دورانی سیاره‌ای برای مخلوط کردن کامل خمیر و تهیه کیک و نان فانتزی.',
                ['Bowl Capacity' => '4.8 Liters polished stainless steel with handle', 'Motor' => '300W direct drive robust motor', 'Speeds' => '10 distinct mixing speeds', 'Origin' => 'Assembled in Greenville, Ohio, USA'],
                ['ظرفیت کاسه' => '۴.۸ لیتر استیل براق دسته دار', 'موتور' => '۳۰۰ وات با کوپلینگ مستقیم بدون تسمه', 'سرعت‌ها' => '۱۰ سرعت مختلف از همزدن آهسته تا پرسرعت', 'کشور سازنده' => 'ساخت گرینویل، اوهایو آمریکا']],

            ['ubiquiti-unifi-dream-machine-pro', 'Ubiquiti UniFi Dream Machine Pro Enterprise 10G Gateway Router', 'روتر و گیت‌وی سازمانی یوبیکیوتی مدل UniFi Dream Machine Pro',
                'network', 'Ubiquiti', 34500000, 32000000, 8, 1, 'network',
                'All-in-one 1U rackmount console, integrated security gateway, 10G SFP+ LAN/WAN ports, 8-port GbE switch.',
                'کنسول مدیریت یکپارچه شبکه، پورت‌های ۱۰ گیگابیت فیبر نوری SFP+، فایروال تشخیص نفوذ IPS/IDS بدون افت سرعت.',
                ['Processor' => 'Quad-core ARM Cortex-A57 at 1.7 GHz', 'System Memory' => '4 GB DDR4', 'Network Ports' => '8x GbE RJ45 + 2x 10G SFP+', 'Security' => '3.5+ Gbps IPS/IDS threat protection'],
                ['پردازنده' => 'چهار هسته‌ای Cortex-A57 فرکانس ۱.۷ گیگاهرتز', 'رم سیستم' => '۴ گیگابایت DDR4 پایدار', 'پورت‌های شبکه' => '۸ پورت گیگابیتی و ۲ پورت فیبر ۱۰ گیگابیت', 'امنیت' => 'فایروال پیشرفته لایه ۷ و مدیریت ترافیک']],

            ['logitech-mx-master-3s', 'Logitech MX Master 3S Ergonomic Wireless Performance Mouse', 'ماوس ارگونومیک بی‌سیم لاجیتک MX Master 3S کلیک سایلنت',
                'accessories', 'Logitech', 6900000, 6200000, 28, 1, 'accessories',
                'Quiet clicks with 90% less noise, 8000 DPI track-on-glass optical sensor, MagSpeed electromagnetic scroll wheel.',
                'بی‌صدا‌ترین ماوس رده‌بالای لاجیتک، سنسور ۸۰۰۰ دی‌پی‌آی با قابلیت کار روی شیشه و شارژدهی ۷۰ روزه.',
                ['Sensor' => 'Darkfield high precision (200–8000 DPI)', 'Scrolling' => 'MagSpeed Electromagnetic scrolling (1000 lines/sec)', 'Battery' => 'Up to 70 days on full charge', 'Connectivity' => 'Bluetooth Low Energy & Logi Bolt USB'],
                ['سنسور حرکتی' => 'تکنولوژی Darkfield با دقت ۸۰۰۰ DPI', 'اسکرول' => 'چرخ اسکرول فلزی الکترومغناطیسی با سرعت ۱۰۰۰ خط در ثانیه', 'شارژدهی باتری' => 'تا ۷۰ روز با هر بار شارژ و پورت تایپ سی', 'اتصال' => 'بلوتوث هوشمند ۳ کاناله و دانگل Logi Bolt']],
        ];

        // درج در جدول محصولات SQLite

        $stmtProd = $pdo->prepare("INSERT INTO products (category_id,name,name_en,name_fa,slug,short_description,short_description_en,short_description_fa,description,description_en,description_fa,price,discount_price,stock,image,specs,specs_en,specs_fa,brand,is_featured,views,sold,rating_avg,rating_count)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

        $productIds = [];
        $mongoProducts = [];

        foreach ($catalog as $idx => $item) {
            [$slug, $nameEn, $nameFa, $catSlug, $brand, $price, $discount, $stock, $featured, $art, $shortEn, $shortFa, $specsEn, $specsFa] = $item;

            $firstCatId = !empty($catIds) ? reset($catIds) : 1;
            $catId = $catIds[$catSlug] ?? $firstCatId;
            $catColor = $catColors[$catSlug] ?? '#6366f1';


            // تصویر: وکتور SVG اختصاصی بسیار تمیز و مدرن
            $imgName = $slug . '.svg';
            file_put_contents($uploadDir . '/' . $imgName, self::generateSvg($art, $catColor, $brand, $nameEn, $idx));

            // متون چندبندی توضیحات غنی در هر دو زبان
            $descEn = self::createLongDescriptionEn($nameEn, $shortEn, $brand);
            $descFa = self::createLongDescriptionFa($nameFa, $shortFa, $brand);

            $specsEnJson = json_encode($specsEn, JSON_UNESCAPED_UNICODE);
            $specsFaJson = json_encode($specsFa, JSON_UNESCAPED_UNICODE);

            $rating = round(mt_rand(44, 50) / 10, 1);
            $ratingCount = mt_rand(12, 195);
            $views = mt_rand(300, 12000);
            $sold = mt_rand(8, 480);

            $stmtProd->execute([
                $catId,
                $nameEn, // default name is English
                $nameEn,
                $nameFa,
                $slug,
                $shortEn, // default short
                $shortEn,
                $shortFa,
                $descEn, // default long
                $descEn,
                $descFa,
                $price,
                $discount,
                $stock,
                $imgName,
                $specsEnJson,
                $specsEnJson,
                $specsFaJson,
                $brand,
                $featured,
                $views,
                $sold,
                $rating,
                $ratingCount
            ]);

            $pId = (int)$pdo->lastInsertId();
            $productIds[] = $pId;

            // ساخت سند NoSQL برای کالکشن MongoDB
            $mongoProducts[] = [
                '_id' => (string)$pId,
                'category_id' => (string)$catId,
                'category_slug' => $catSlug,
                'name' => $nameEn,
                'name_en' => $nameEn,
                'name_fa' => $nameFa,
                'slug' => $slug,
                'short_description_en' => $shortEn,
                'short_description_fa' => $shortFa,
                'description_en' => $descEn,
                'description_fa' => $descFa,
                'price' => $price,
                'discount_price' => $discount,
                'stock' => $stock,
                'image' => $imgName,
                'specs_en' => $specsEn,
                'specs_fa' => $specsFa,
                'brand' => $brand,
                'is_featured' => (bool)$featured,
                'views' => $views,
                'sold' => $sold,
                'rating_avg' => $rating,
                'rating_count' => $ratingCount,
                'created_at' => date('c')
            ];
        }

        // ---------- نظرات واقعی دو زبانه کاربران ----------
        $bilingualReviews = [
            [5, 'Outstanding Quality & Speedy Delivery', 'کیفیت عالی و ارسال بسیار سریع', 'Arrived in 2 days, pristine factory sealed packaging. The performance is completely authentic. Truly satisfied!', 'بسته‌بندی پلمپ و شرکتی بود، کالا دو روزه رسید و عملکردش فوق‌العاده‌ست. خیلی راضی هستم.'],
            [5, 'Best Value for the Specs', 'ارزش خرید فوق‌العاده بالا', 'Compared many alternatives before purchasing. This exceeds all expectations and the price is better than competitors.', 'قبل از خرید خیلی مقایسه کردم. فراتر از انتظارم بود و قیمتش از همه جا مناسب‌تر بود.'],
            [4, 'Great Experience with Minor Notes', 'خوب با نکات جزئی', 'Build quality is top tier. Exactly matches the technical specifications listed. Highly recommended.', 'کیفیت ساخت درجه یکه و مشخصات فنیش مو نمی‌زنه با سایت سازنده. پیشنهادش می‌کنم.'],
            [5, 'Official Warranty & Great Support', 'گارانتی معتبر و پشتیبانی عالی', 'Official corporate warranty included with formal invoice. Store support answered my questions with utmost patience.', 'کارت گارانتی معتبر شرکتی با فاکتور رسمی همراهش بود و تیم پشتیبانی با حوصله راهنماییم کردند.'],
            [4, 'Recommended Purchase', 'خرید مطمئن و پیشنهادی', 'Everything worked right out of the box. Delivery was safe with plenty of protective air cushions.', 'از لحظه باز کردن جعبه همه چی روان کار می‌کرد و بسته‌بندی پستی هم حباب‌دار و ضدضربه بود.'],
        ];

        $stmtRev = $pdo->prepare("INSERT INTO reviews (product_id,user_id,rating,title,comment,comment_en,comment_fa,is_approved) VALUES (?,?,?,?,?,?,?,1)");
        $mongoReviews = [];

        foreach ($productIds as $idx => $pid) {
            $rev = $bilingualReviews[$idx % count($bilingualReviews)];
            $uid = 2 + ($idx % 3);

            $stmtRev->execute([$pid, $uid, $rev[0], $rev[1], $rev[3], $rev[3], $rev[4]]);
            $mongoReviews[] = [
                '_id' => (string)($idx + 1),
                'product_id' => (string)$pid,
                'user_id' => (string)$uid,
                'rating' => $rev[0],
                'title' => $rev[1],
                'comment_en' => $rev[3],
                'comment_fa' => $rev[4],
                'is_approved' => 1,
                'created_at' => date('c')
            ];

            // نظر دوم برای محصولات منتخب
            if ($idx % 2 === 0) {
                $rev2 = $bilingualReviews[($idx + 2) % count($bilingualReviews)];
                $uid2 = ($uid === 2) ? 3 : 2;
                $stmtRev->execute([$pid, $uid2, $rev2[0], $rev2[1], $rev2[3], $rev2[3], $rev2[4]]);
                $mongoReviews[] = [
                    '_id' => (string)(count($productIds) + $idx + 1),
                    'product_id' => (string)$pid,
                    'user_id' => (string)$uid2,
                    'rating' => $rev2[0],
                    'title' => $rev2[1],
                    'comment_en' => $rev2[3],
                    'comment_fa' => $rev2[4],
                    'is_approved' => 1,
                    'created_at' => date('c')
                ];
            }
        }

        // ---------- کدهای تخفیف ----------
        $pdo->prepare("INSERT INTO coupons (code,type,value,min_total,max_uses,expires_at,is_active) VALUES (?,?,?,?,?,?,1)")
            ->execute(['WELCOME10', 'percent', 10, 500000, 1000, date('Y-m-d', strtotime('+1 year'))]);
        $pdo->prepare("INSERT INTO coupons (code,type,value,min_total,max_uses,expires_at,is_active) VALUES (?,?,?,?,?,?,1)")
            ->execute(['OFF200', 'fixed', 200000, 2000000, 500, date('Y-m-d', strtotime('+6 months'))]);
        $pdo->prepare("INSERT INTO coupons (code,type,value,min_total,max_uses,expires_at,is_active) VALUES (?,?,?,?,?,?,1)")
            ->execute(['MEGA20', 'percent', 20, 5000000, 200, date('Y-m-d', strtotime('+3 months'))]);

        $mongoCoupons = [
            ['_id' => '1', 'code' => 'WELCOME10', 'type' => 'percent', 'value' => 10, 'min_total' => 500000, 'max_uses' => 1000, 'is_active' => true],
            ['_id' => '2', 'code' => 'OFF200', 'type' => 'fixed', 'value' => 200000, 'min_total' => 2000000, 'max_uses' => 500, 'is_active' => true],
            ['_id' => '3', 'code' => 'MEGA20', 'type' => 'percent', 'value' => 20, 'min_total' => 5000000, 'max_uses' => 200, 'is_active' => true],
        ];

        // ---------- ذخیره خودکار در لایه دیتابیس NoSQL / MongoDB ----------
        MongoDatabase::saveCollection('categories', $mongoCategories);
        MongoDatabase::saveCollection('products', $mongoProducts);
        MongoDatabase::saveCollection('reviews', $mongoReviews);
        MongoDatabase::saveCollection('coupons', $mongoCoupons);

        // همچنین ایجاد فایل خروجی کامل MongoDB Export
        $exportData = [
            'database' => 'nextshop',
            'exported_at' => date('c'),
            'collections' => [
                'categories' => $mongoCategories,
                'products' => $mongoProducts,
                'reviews' => $mongoReviews,
                'coupons' => $mongoCoupons
            ]
        ];
        file_put_contents(__DIR__ . '/../../database/mongodb_export.json', json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        MongoDatabase::generateMongoDbJsScript();
    }


    /**
     * تولید توضیحات تفصیلی انگلیسی چندبندی برای هر محصول
     */
    private static function createLongDescriptionEn(string $name, string $short, string $brand): string
    {
        return $name . " represents the peak of modern engineering and design from " . $brand . ".\n\n"
            . $short . " Built with durable, premium materials, it delivers unparalleled reliability, state-of-the-art performance, and an intuitive user experience engineered for demanding daily workflows.\n\n"
            . "Whether you are a creative professional, tech enthusiast, or casual user, this product combines seamless connectivity, energy efficiency, and high ergonomics. Backed by full official manufacturer warranty, genuine authenticity inspection, and 7-day unconditional replacement guarantee.";
    }

    /**
     * تولید توضیحات تفصیلی فارسی چندبندی برای هر محصول
     */
    private static function createLongDescriptionFa(string $name, string $short, string $brand): string
    {
        return $name . " یکی از برترین و محبوب‌ترین گزینه‌ها در کلاس خود از برند معتبر " . $brand . " است.\n\n"
            . $short . " این محصول با استفاده از متریال درجه یک و پیشرفته‌ترین استانداردهای کیفی جهانی تولید شده تا عملکردی پایدار، کم‌مصرف و پرقدرت را برای استفاده‌های حرفه‌ای و روزمره تضمین نماید.\n\n"
            . "طراحی ارگونومیک، دوام فوق‌العاده قطعات و کیفیت ساخت کم‌نظیر، این مدل را به انتخابی بی‌رقیب تبدیل کرده است. کالا به همراه گارانتی اصالت معتبر، فاکتور رسمی و مهلت تست ۷ روزه بازگشت وجه ارسال می‌گردد.";
    }

    /**
     * تولید تصویر وکتوری SVG فوق‌العاده مدرن با پالت اختصاصی هر دسته‌بندی
     */
    private static function generateSvg(string $art, string $color, string $brand, string $title, int $idx): string
    {
        $palettes = [
            ['#f8fafc', '#e2e8f0'], ['#f1f5f9', '#cbd5e1'], ['#fefce8', '#fde68a'],
            ['#eff6ff', '#bfdbfe'], ['#fdf2f8', '#fbcfe8'], ['#f0fdf4', '#bbf7d0'],
            ['#fff7ed', '#fed7aa'], ['#f5f3ff', '#ddd6fe'], ['#ecfdf5', '#a7f3d0'],
        ];
        $bg = $palettes[$idx % count($palettes)];
        $safeBrand = htmlspecialchars($brand, ENT_QUOTES, 'UTF-8');
        $safeTitle = htmlspecialchars(mb_substr($title, 0, 30), ENT_QUOTES, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600">
  <defs>
    <linearGradient id="bg{$idx}" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$bg[0]}"/>
      <stop offset="100%" stop-color="{$bg[1]}"/>
    </linearGradient>
    <linearGradient id="acc{$idx}" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$color}"/>
      <stop offset="100%" stop-color="#1e1b4b"/>
    </linearGradient>
  </defs>
  <rect width="600" height="600" rx="36" fill="url(#bg{$idx})"/>
  <circle cx="500" cy="100" r="90" fill="{$color}" opacity="0.12"/>
  <circle cx="90" cy="510" r="110" fill="{$color}" opacity="0.09"/>
  <rect x="36" y="36" width="130" height="34" rx="17" fill="#0f172a" opacity="0.88"/>
  <text x="101" y="59" font-size="14" text-anchor="middle" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="700" letter-spacing="1">AUTHENTIC</text>
  <g transform="translate(300, 260)">
    <circle cx="0" cy="0" r="110" fill="url(#acc{$idx})" opacity="0.95"/>
    <circle cx="0" cy="0" r="125" fill="none" stroke="{$color}" stroke-width="3" stroke-dasharray="8 8" opacity="0.4"/>
    <text x="0" y="16" font-size="44" text-anchor="middle" fill="#ffffff" font-family="-apple-system, sans-serif" font-weight="800">{$safeBrand}</text>
  </g>
  <rect x="50" y="475" width="500" height="85" rx="20" fill="#0f172a" opacity="0.92"/>
  <rect x="50" y="475" width="10" height="85" rx="5" fill="{$color}"/>
  <text x="300" y="512" font-size="20" text-anchor="middle" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="700">{$safeBrand}</text>
  <text x="300" y="538" font-size="14" text-anchor="middle" fill="#94a3b8" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">{$safeTitle}</text>
</svg>
SVG;
    }
}
