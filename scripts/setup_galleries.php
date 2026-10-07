<?php
require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database as DB;

$uploadDir = config('upload_dir');
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Map known products to high quality JPGs
$jpgMap = [
    'iphone-15-pro'               => 'iphone-15-pro.jpg',
    'galaxy-s24-ultra'            => 'galaxy-s24-ultra.jpg',
    'xiaomi-14-pro'               => 'xiaomi-14-5g.jpg',
    'ipad-air-11-m2'              => 'ipad-air-11-m2.jpg',
    'macbook-pro-14-m3'           => 'macbook-air-m3.jpg',
    'lenovo-legion-pro-5'         => 'lenovo-ideapad-slim5.jpg',
    'asus-zenbook-14-oled'        => 'asus-rog-strix-g16.jpg',
    'sony-wh1000xm5'              => 'sony-wh1000xm5.jpg',
    'airpods-pro-2'               => 'airpods-pro-2.jpg',
    'jbl-boombox-3'               => 'jbl-flip-6.jpg',
    'apple-watch-ultra-2'         => 'apple-watch-s9.jpg',
    'galaxy-watch6-classic'       => 'galaxy-watch6-classic.jpg',
    'garmin-fenix-7x-pro'         => 'garmin-fenix-7.jpg',
    'dyson-v15-detect'            => 'dyson-v15-detect.jpg',
    'dyson-v15-detect-absolute'   => 'dyson-v15-detect.jpg',
    'philips-airfryer-xxl'        => 'philips-airfryer-xl.jpg',
    'dreame-bot-l20-ultra'        => 'xiaomi-robot-s10.jpg',
    'ps5-slim-1tb'                => 'ps5-slim.jpg',
    'xbox-series-x'               => 'xbox-series-x.jpg',
    'dualsense-edge-pro'          => 'dualsense-controller.jpg',
    'nike-air-zoom-pegasus-40'    => 'nike-pegasus-40.jpg',
    'nike-air-jordan-1-low'       => 'nike-pegasus-40.jpg',
    'north-face-triclimate-jacket'=> 'northface-jacket.jpg',
    'the-north-face-1996-nuptse'  => 'northface-jacket.jpg',
    'dior-sauvage-elixir'         => 'dior-sauvage-100.jpg',
    'dyson-supersonic-hair-dryer' => 'dyson-supersonic.jpg',
    'giant-talon-1-29'            => 'giant-talon-3.jpg',
    'naturehike-cloud-up-2'       => 'camping-tent-4.jpg',
    'lego-technic-porsche-gt3'    => 'lego-technic-car.jpg',
    'rc-crawler-traxxas-trx4'     => 'rc-car-4x4.jpg',
    'traxxas-trx-4-scale-crawler' => 'rc-car-4x4.jpg',
    'atomic-habits-hardcover'     => 'compound-effect-book.jpg',
    'lamy-2000-fountain-pen'      => 'faber-pen-set.jpg',
    'bosch-gsb-18v-90c'           => 'bosch-drill-18v.jpg',
    'dewalt-mechanic-142'         => 'wrench-set-108.jpg',
    'marshall-stanmore-iii'       => 'sony-srs-xp500.jpg',
    'nespresso-vertuo-pop'        => 'nespresso-vertuo-next.jpg',
    'nintendo-switch-oled'        => 'nintendo-switch-oled.jpg',
    'bose-quietcomfort-ultra'     => 'airpods-pro-2.jpg',
];

function makePerspectiveSvg(string $name, string $brand, string $angleType, string $color, array $specs): string
{
    $angleLabels = [
        'angle'  => ['title' => 'Angle Perspective', 'subtitle' => 'Ergonomic 3D View & Build Profile', 'badge' => '360° VIEW'],
        'detail' => ['title' => 'Hardware & Sensor Detail', 'subtitle' => 'Precision Engineering & Optics', 'badge' => 'MACRO DETAIL'],
        'box'    => ['title' => 'Package & Official Kit', 'subtitle' => 'Original Retail Box & Accessories', 'badge' => 'IN THE BOX'],
        'front'  => ['title' => 'Official Studio Shot', 'subtitle' => 'Authentic Edition Full Profile', 'badge' => 'STUDIO MASTER'],
    ];

    $cfg = $angleLabels[$angleType] ?? $angleLabels['angle'];
    $spec1 = !empty($specs) ? array_keys($specs)[0] . ': ' . reset($specs) : '100% Authentic Quality';
    $spec2 = count($specs) > 1 ? array_keys($specs)[1] . ': ' . array_values($specs)[1] : 'Official Warranty';

    $bgGradients = [
        'angle'  => ['#090d16', '#1e1b4b'],
        'detail' => ['#0f172a', '#1e293b'],
        'box'    => ['#0d1117', '#161b22'],
        'front'  => ['#0b0f19', '#1a103c'],
    ];
    $bgGrad = $bgGradients[$angleType] ?? ['#090d16', '#1e1b4b'];
    $accent = $color ?: '#6366f1';

    $safeName = htmlspecialchars(mb_substr($name, 0, 38), ENT_XML1, 'UTF-8');
    $safeBrand = htmlspecialchars($brand ?: 'NextShop', ENT_XML1, 'UTF-8');
    $safeTitle = htmlspecialchars($cfg['title'], ENT_XML1, 'UTF-8');
    $safeSubtitle = htmlspecialchars($cfg['subtitle'], ENT_XML1, 'UTF-8');
    $safeBadge = htmlspecialchars($cfg['badge'], ENT_XML1, 'UTF-8');
    $safeSpec1 = htmlspecialchars(mb_substr($spec1, 0, 40), ENT_XML1, 'UTF-8');
    $safeSpec2 = htmlspecialchars(mb_substr($spec2, 0, 40), ENT_XML1, 'UTF-8');

    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" width="800" height="800">
  <defs>
    <linearGradient id="bg_{$angleType}" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$bgGrad[0]}"/>
      <stop offset="100%" stop-color="{$bgGrad[1]}"/>
    </linearGradient>
    <radialGradient id="cg_{$angleType}" cx="50%" cy="45%" r="50%">
      <stop offset="0%" stop-color="{$accent}" stop-opacity="0.3"/>
      <stop offset="100%" stop-color="{$accent}" stop-opacity="0"/>
    </radialGradient>
    <linearGradient id="cardG_{$angleType}" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="rgba(255,255,255,0.08)"/>
      <stop offset="100%" stop-color="rgba(255,255,255,0.02)"/>
    </linearGradient>
    <filter id="sh_{$angleType}" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="16" stdDeviation="20" flood-color="#000000" flood-opacity="0.6"/>
    </filter>
  </defs>

  <rect width="800" height="800" rx="36" fill="url(#bg_{$angleType})"/>
  <rect width="800" height="800" fill="url(#cg_{$angleType})"/>
  
  <circle cx="400" cy="360" r="240" fill="none" stroke="{$accent}" stroke-width="1.5" stroke-dasharray="6 8" opacity="0.3"/>
  <circle cx="400" cy="360" r="160" fill="none" stroke="{$accent}" stroke-width="1" opacity="0.2"/>

  <!-- Top Badges -->
  <g transform="translate(48, 44)">
    <rect width="140" height="34" rx="17" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.14)"/>
    <text x="70" y="22" font-size="12" text-anchor="middle" fill="#f8fafc" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="700" letter-spacing="1">{$safeBadge}</text>
  </g>
  <g transform="translate(610, 44)">
    <rect width="142" height="34" rx="17" fill="{$accent}" opacity="0.2"/>
    <text x="71" y="22" font-size="12" text-anchor="middle" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="700">{$safeBrand}</text>
  </g>

  <!-- Center Showcase -->
  <g transform="translate(400, 350)" filter="url(#sh_{$angleType})">
    <rect x="-240" y="-140" width="480" height="280" rx="28" fill="url(#cardG_{$angleType})" stroke="rgba(255,255,255,0.12)"/>
    
    <circle cx="0" cy="-20" r="64" fill="{$accent}" opacity="0.85"/>
    <circle cx="0" cy="-20" r="74" fill="none" stroke="#ffffff" stroke-width="2" opacity="0.3"/>
    <text x="0" y="-7" font-size="30" text-anchor="middle" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="900">{$safeBrand}</text>

    <text x="0" y="60" font-size="17" text-anchor="middle" fill="#f1f5f9" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="800">{$safeTitle}</text>
    <text x="0" y="84" font-size="13" text-anchor="middle" fill="#94a3b8" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">{$safeSubtitle}</text>
  </g>

  <!-- Bottom Details Bar -->
  <g transform="translate(48, 620)">
    <rect width="704" height="132" rx="24" fill="rgba(15, 23, 42, 0.9)" stroke="rgba(255,255,255,0.1)"/>
    <rect x="0" y="0" width="8" height="132" rx="4" fill="{$accent}"/>
    
    <text x="32" y="38" font-size="18" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="800">{$safeName}</text>
    
    <g transform="translate(32, 62)">
      <circle cx="5" cy="5" r="4" fill="{$accent}"/>
      <text x="18" y="9" font-size="13" fill="#cbd5e1" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">{$safeSpec1}</text>
    </g>
    
    <g transform="translate(32, 90)">
      <circle cx="5" cy="5" r="4" fill="{$accent}" opacity="0.7"/>
      <text x="18" y="9" font-size="13" fill="#94a3b8" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">{$safeSpec2}</text>
    </g>

    <text x="670" y="82" font-size="12" text-anchor="end" fill="{$accent}" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="700">ORIGINAL GUARANTEED</text>
  </g>
</svg>
SVG;
}

$products = DB::fetchAll("SELECT p.*, c.color as cat_color FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id");

echo "Processing " . count($products) . " products...\n";

foreach ($products as $p) {
    $id = (int)$p['id'];
    $slug = $p['slug'];
    $name = $p['name_en'] ?: $p['name'];
    $brand = $p['brand'] ?: 'NextShop';
    $color = $p['cat_color'] ?: '#6366f1';
    $specs = $p['specs'] ? (json_decode($p['specs'], true) ?: []) : [];

    // Main image
    $mainImg = $jpgMap[$slug] ?? null;
    if (!$mainImg) {
        if (file_exists($uploadDir . '/' . $slug . '.jpg')) {
            $mainImg = $slug . '.jpg';
        } else {
            // Existing svg or generate new
            $mainImg = $slug . '.svg';
            if (!file_exists($uploadDir . '/' . $mainImg)) {
                file_put_contents($uploadDir . '/' . $mainImg, makePerspectiveSvg($name, $brand, 'front', $color, $specs));
            }
        }
    }

    // Generate 3 gallery angle images
    $angleImg = $slug . '-angle.svg';
    $detailImg = $slug . '-detail.svg';
    $boxImg = $slug . '-box.svg';

    file_put_contents($uploadDir . '/' . $angleImg, makePerspectiveSvg($name, $brand, 'angle', $color, $specs));
    file_put_contents($uploadDir . '/' . $detailImg, makePerspectiveSvg($name, $brand, 'detail', $color, $specs));
    file_put_contents($uploadDir . '/' . $boxImg, makePerspectiveSvg($name, $brand, 'box', $color, $specs));

    // Gallery array (main image + 3 perspectives)
    $gallery = [$mainImg, $angleImg, $detailImg, $boxImg];

    // If there is an existing SVG alongside a JPG, include it too
    if (str_ends_with($mainImg, '.jpg') && file_exists($uploadDir . '/' . $slug . '.svg')) {
        $gallery = [$mainImg, $slug . '.svg', $angleImg, $detailImg, $boxImg];
    }

    $galleryJson = json_encode($gallery, JSON_UNESCAPED_SLASHES);

    DB::query("UPDATE products SET image = ?, gallery = ? WHERE id = ?", [$mainImg, $galleryJson, $id]);
}

echo "Successfully updated all " . count($products) . " products with rich images and 4-5 photo galleries!\n";
