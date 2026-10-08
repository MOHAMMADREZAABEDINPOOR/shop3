<?php
/**
 * Generates transparent, modern 3D SVG product & tech artworks for NextShop banners.
 * No hardcoded text - pure visual graphics suitable for any language (EN / FA).
 */

$dir = __DIR__ . '/../public/uploads/banners';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// 1. Hero 1: Flagship Tech (Smartphone + Laptop + Glow)
$hero1 = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 540 400" width="540" height="400" fill="none">
  <defs>
    <linearGradient id="h1_phone_body" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#475569"/>
      <stop offset="50%" stop-color="#1e293b"/>
      <stop offset="100%" stop-color="#0f172a"/>
    </linearGradient>
    <linearGradient id="h1_phone_screen" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#8b5cf6"/>
      <stop offset="35%" stop-color="#6366f1"/>
      <stop offset="70%" stop-color="#ec4899"/>
      <stop offset="100%" stop-color="#3b82f6"/>
    </linearGradient>
    <linearGradient id="h1_laptop_body" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#64748b"/>
      <stop offset="100%" stop-color="#334155"/>
    </linearGradient>
    <linearGradient id="h1_laptop_screen" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#1e1b4b"/>
      <stop offset="50%" stop-color="#312e81"/>
      <stop offset="100%" stop-color="#4f46e5"/>
    </linearGradient>
    <linearGradient id="h1_glass" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#ffffff" stop-opacity="0.35"/>
      <stop offset="40%" stop-color="#ffffff" stop-opacity="0.05"/>
      <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
    </linearGradient>
    <radialGradient id="h1_orb_glow" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#a855f7" stop-opacity="0.6"/>
      <stop offset="100%" stop-color="#a855f7" stop-opacity="0"/>
    </radialGradient>
    <radialGradient id="h1_cyan_glow" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.5"/>
      <stop offset="100%" stop-color="#38bdf8" stop-opacity="0"/>
    </radialGradient>
    <filter id="h1_shadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="18" stdDeviation="22" flood-color="#000000" flood-opacity="0.55"/>
    </filter>
  </defs>

  <!-- Ambient Glow Behind -->
  <circle cx="280" cy="200" r="180" fill="url(#h1_orb_glow)"/>
  <circle cx="420" cy="140" r="120" fill="url(#h1_cyan_glow)"/>

  <!-- Floating Background Elements -->
  <g opacity="0.6">
    <circle cx="90" cy="90" r="28" fill="none" stroke="#c084fc" stroke-width="2" stroke-dasharray="6 6"/>
    <circle cx="460" cy="80" r="18" fill="none" stroke="#38bdf8" stroke-width="2"/>
    <circle cx="480" cy="300" r="34" fill="none" stroke="#f472b6" stroke-width="2.5" stroke-dasharray="8 8"/>
  </g>

  <!-- LAPTOP (Left/Back) -->
  <g transform="translate(60, 110)" filter="url(#h1_shadow)">
    <!-- Display Shell -->
    <rect x="0" y="0" width="300" height="190" rx="14" fill="url(#h1_laptop_body)" stroke="#94a3b8" stroke-width="2"/>
    <!-- Display Bezel & Screen -->
    <rect x="10" y="10" width="280" height="170" rx="6" fill="#090d16"/>
    <rect x="14" y="14" width="272" height="162" rx="4" fill="url(#h1_laptop_screen)"/>
    <!-- Laptop screen dynamic waves -->
    <path d="M 14 120 Q 80 80, 150 110 T 286 90 L 286 176 L 14 176 Z" fill="#6366f1" opacity="0.45"/>
    <path d="M 14 140 Q 90 100, 170 135 T 286 120 L 286 176 L 14 176 Z" fill="#ec4899" opacity="0.35"/>
    <!-- Screen Glass Reflection -->
    <polygon points="14,14 160,14 60,176 14,176" fill="url(#h1_glass)"/>
    <!-- Laptop Base / Keyboard Deck -->
    <path d="M -30 190 L 330 190 L 305 235 L -5 235 Z" fill="#1e293b" stroke="#64748b" stroke-width="1.5"/>
    <!-- Trackpad -->
    <rect x="110" y="200" width="80" height="25" rx="4" fill="#0f172a" stroke="#475569" stroke-width="1"/>
    <!-- Hinge Notch -->
    <rect x="120" y="188" width="60" height="5" rx="2" fill="#0f172a"/>
  </g>

  <!-- FLAGSHIP SMARTPHONE (Right/Foreground) -->
  <g transform="translate(260, 45)" filter="url(#h1_shadow)">
    <!-- Outer Titanium Frame -->
    <rect x="0" y="0" width="180" height="340" rx="36" fill="url(#h1_phone_body)" stroke="#94a3b8" stroke-width="3"/>
    <!-- Inner Screen Bezel -->
    <rect x="6" y="6" width="168" height="328" rx="32" fill="#05050a"/>
    <!-- Vivid OLED Display -->
    <rect x="9" y="9" width="162" height="322" rx="29" fill="url(#h1_phone_screen)"/>
    <!-- Screen Abstract Art -->
    <circle cx="90" cy="170" r="65" fill="#f43f5e" opacity="0.6"/>
    <circle cx="120" cy="140" r="50" fill="#a855f7" opacity="0.7"/>
    <circle cx="60" cy="200" r="45" fill="#38bdf8" opacity="0.65"/>
    <!-- Dynamic Island Pill -->
    <rect x="62" y="18" width="56" height="16" rx="8" fill="#000000"/>
    <circle cx="104" cy="26" r="4" fill="#1e1b4b"/>
    <!-- Glass Reflection Streak -->
    <polygon points="9,9 110,9 25,331 9,331" fill="url(#h1_glass)"/>
    <!-- Speaker & Button Highlights -->
    <rect x="-4" y="65" width="4" height="24" rx="2" fill="#64748b"/>
    <rect x="-4" y="100" width="4" height="40" rx="2" fill="#64748b"/>
    <rect x="-4" y="148" width="4" height="40" rx="2" fill="#64748b"/>
    <rect x="180" y="90" width="4" height="52" rx="2" fill="#64748b"/>
  </g>

  <!-- FLOATING WIRELESS EARBUDS CASE (Front Accent) -->
  <g transform="translate(195, 270)" filter="url(#h1_shadow)">
    <rect x="0" y="0" width="90" height="70" rx="26" fill="url(#h1_phone_body)" stroke="#cbd5e1" stroke-width="2"/>
    <path d="M 0 28 L 90 28" stroke="#0f172a" stroke-width="2"/>
    <circle cx="45" cy="46" r="3" fill="#22c55e"/>
    <ellipse cx="45" cy="14" rx="28" ry="6" fill="url(#h1_glass)"/>
  </g>

  <!-- Sparkles -->
  <g fill="#ffffff" opacity="0.85">
    <polygon points="460,190 463,200 473,203 463,206 460,216 457,206 447,203 457,200"/>
    <polygon points="210,50 212,57 219,59 212,61 210,68 208,61 201,59 208,57"/>
    <polygon points="80,240 82,246 88,248 82,250 80,256 78,250 72,248 78,246"/>
  </g>
</svg>
SVG;
file_put_contents($dir . '/hero-slider-1.svg', $hero1);


// 2. Hero 2: Next-Gen Gaming (Console + Pro Controller + Neon)
$hero2 = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 540 400" width="540" height="400" fill="none">
  <defs>
    <linearGradient id="h2_console_body" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#f8fafc"/>
      <stop offset="60%" stop-color="#cbd5e1"/>
      <stop offset="100%" stop-color="#94a3b8"/>
    </linearGradient>
    <linearGradient id="h2_console_core" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#0284c7"/>
      <stop offset="50%" stop-color="#0f172a"/>
      <stop offset="100%" stop-color="#0284c7"/>
    </linearGradient>
    <linearGradient id="h2_pad_body" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#334155"/>
      <stop offset="40%" stop-color="#1e293b"/>
      <stop offset="100%" stop-color="#090d16"/>
    </linearGradient>
    <linearGradient id="h2_neon_blue" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0%" stop-color="#38bdf8"/>
      <stop offset="100%" stop-color="#818cf8"/>
    </linearGradient>
    <radialGradient id="h2_blue_glow" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.6"/>
      <stop offset="100%" stop-color="#38bdf8" stop-opacity="0"/>
    </radialGradient>
    <filter id="h2_shadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="18" stdDeviation="24" flood-color="#000000" flood-opacity="0.6"/>
    </filter>
  </defs>

  <!-- Ambient Glow -->
  <circle cx="270" cy="200" r="180" fill="url(#h2_blue_glow)"/>

  <!-- NEXT-GEN CONSOLE (Back) -->
  <g transform="translate(140, 40)" filter="url(#h2_shadow)">
    <!-- Tower Side Panel 1 -->
    <path d="M 40 20 Q 70 12, 100 0 L 120 310 Q 70 320, 20 310 Z" fill="url(#h2_console_body)" stroke="#ffffff" stroke-width="1.5"/>
    <!-- Black Inner Core & LED Strip -->
    <path d="M 60 10 L 85 5 L 105 315 L 45 315 Z" fill="url(#h2_console_core)"/>
    <path d="M 63 12 L 67 11 L 82 313 L 78 313 Z" fill="url(#h2_neon_blue)"/>
    <!-- Tower Side Panel 2 -->
    <path d="M 80 5 Q 110 0, 140 10 L 160 305 Q 110 325, 60 315 Z" fill="url(#h2_console_body)" opacity="0.9"/>
    <!-- Disc Slot & Power Button -->
    <rect x="72" y="240" width="4" height="50" rx="2" fill="#000000"/>
    <circle cx="74" cy="220" r="3" fill="#38bdf8"/>
  </g>

  <!-- PRO ESPORTS GAMEPAD (Front Center) -->
  <g transform="translate(130, 140)" filter="url(#h2_shadow)">
    <!-- Main Body Chassis -->
    <path d="M 50 100 C 30 60, 60 20, 110 15 C 135 12, 160 25, 175 40 C 190 25, 215 12, 240 15 C 290 20, 320 60, 300 100 C 285 140, 270 200, 235 210 C 205 220, 190 170, 175 160 C 160 170, 145 220, 115 210 C 80 200, 65 140, 50 100 Z" fill="url(#h2_pad_body)" stroke="#475569" stroke-width="2.5"/>
    
    <!-- Central Touchpad / LED Lightbar -->
    <path d="M 130 35 L 220 35 L 210 85 L 140 85 Z" rx="6" fill="#0f172a" stroke="#64748b" stroke-width="1"/>
    <path d="M 140 38 L 210 38" stroke="url(#h2_neon_blue)" stroke-width="3" stroke-linecap="round"/>

    <!-- Left D-Pad -->
    <g transform="translate(95, 75)">
      <rect x="14" y="0" width="14" height="42" rx="4" fill="#1e293b" stroke="#64748b" stroke-width="1"/>
      <rect x="0" y="14" width="42" height="14" rx="4" fill="#1e293b" stroke="#64748b" stroke-width="1"/>
    </g>

    <!-- Right Action Buttons (ABXY) -->
    <g transform="translate(230, 75)">
      <circle cx="21" cy="7" r="6" fill="#1e293b" stroke="#f43f5e" stroke-width="2"/>
      <circle cx="35" cy="21" r="6" fill="#1e293b" stroke="#38bdf8" stroke-width="2"/>
      <circle cx="21" cy="35" r="6" fill="#1e293b" stroke="#22c55e" stroke-width="2"/>
      <circle cx="7" cy="21" r="6" fill="#1e293b" stroke="#eab308" stroke-width="2"/>
    </g>

    <!-- Dual Analog Thumbsticks -->
    <!-- Left Stick -->
    <g transform="translate(125, 115)">
      <circle cx="22" cy="22" r="22" fill="#090d16" stroke="#475569" stroke-width="2"/>
      <circle cx="22" cy="22" r="16" fill="#1e293b"/>
      <circle cx="22" cy="22" r="7" fill="none" stroke="#38bdf8" stroke-width="2"/>
    </g>
    <!-- Right Stick -->
    <g transform="translate(180, 115)">
      <circle cx="22" cy="22" r="22" fill="#090d16" stroke="#475569" stroke-width="2"/>
      <circle cx="22" cy="22" r="16" fill="#1e293b"/>
      <circle cx="22" cy="22" r="7" fill="none" stroke="#818cf8" stroke-width="2"/>
    </g>

    <!-- Brand / Home Button -->
    <circle cx="175" cy="105" r="6" fill="#38bdf8"/>
  </g>

  <!-- Glowing Gaming Accents -->
  <g fill="#38bdf8" opacity="0.8">
    <polygon points="80,120 83,126 89,128 83,130 80,136 77,130 71,128 77,126"/>
    <polygon points="460,180 463,188 472,190 463,192 460,200 457,192 448,190 457,188"/>
    <polygon points="380,60 382,65 388,67 382,69 380,74 378,69 372,67 378,65"/>
  </g>
</svg>
SVG;
file_put_contents($dir . '/hero-slider-2.svg', $hero2);


// 3. Hero 3: Pro Visual (4K Mirrorless Camera & Aerial Drone)
$hero3 = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 540 400" width="540" height="400" fill="none">
  <defs>
    <linearGradient id="h3_cam_body" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#334155"/>
      <stop offset="50%" stop-color="#1e293b"/>
      <stop offset="100%" stop-color="#090d16"/>
    </linearGradient>
    <linearGradient id="h3_lens_glass" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#0284c7"/>
      <stop offset="40%" stop-color="#0369a1"/>
      <stop offset="70%" stop-color="#0f172a"/>
      <stop offset="100%" stop-color="#0284c7"/>
    </linearGradient>
    <linearGradient id="h3_glass_refl" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.6"/>
      <stop offset="100%" stop-color="#38bdf8" stop-opacity="0"/>
    </linearGradient>
    <radialGradient id="h3_cyan_orb" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#06b6d4" stop-opacity="0.5"/>
      <stop offset="100%" stop-color="#06b6d4" stop-opacity="0"/>
    </radialGradient>
    <filter id="h3_shadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="18" stdDeviation="22" flood-color="#000000" flood-opacity="0.6"/>
    </filter>
  </defs>

  <!-- Ambient Glow -->
  <circle cx="270" cy="200" r="180" fill="url(#h3_cyan_orb)"/>

  <!-- AERIAL DRONE (Top Right Floating) -->
  <g transform="translate(240, 40)" filter="url(#h3_shadow)">
    <!-- Drone Central Body -->
    <ellipse cx="140" cy="70" rx="42" ry="24" fill="url(#h3_cam_body)" stroke="#64748b" stroke-width="1.5"/>
    <ellipse cx="140" cy="65" rx="28" ry="14" fill="#38bdf8" opacity="0.4"/>
    <!-- Drone 4 Rotor Arms -->
    <path d="M 110 65 L 45 40" stroke="#475569" stroke-width="5" stroke-linecap="round"/>
    <path d="M 170 65 L 235 40" stroke="#475569" stroke-width="5" stroke-linecap="round"/>
    <path d="M 120 78 L 65 105" stroke="#475569" stroke-width="5" stroke-linecap="round"/>
    <path d="M 160 78 L 215 105" stroke="#475569" stroke-width="5" stroke-linecap="round"/>
    <!-- Propellers (Spin Blur) -->
    <ellipse cx="45" cy="40" rx="36" ry="7" fill="#94a3b8" opacity="0.4" stroke="#ffffff" stroke-width="1"/>
    <ellipse cx="235" cy="40" rx="36" ry="7" fill="#94a3b8" opacity="0.4" stroke="#ffffff" stroke-width="1"/>
    <ellipse cx="65" cy="105" rx="32" ry="6" fill="#94a3b8" opacity="0.4" stroke="#ffffff" stroke-width="1"/>
    <ellipse cx="215" cy="105" rx="32" ry="6" fill="#94a3b8" opacity="0.4" stroke="#ffffff" stroke-width="1"/>
    <!-- Drone Gimbal Camera -->
    <circle cx="140" cy="94" r="12" fill="#0f172a" stroke="#38bdf8" stroke-width="2"/>
    <circle cx="140" cy="94" r="5" fill="#38bdf8"/>
  </g>

  <!-- MIRRORLESS CAMERA (Foreground Center) -->
  <g transform="translate(80, 120)" filter="url(#h3_shadow)">
    <!-- Camera Body -->
    <rect x="0" y="50" width="260" height="170" rx="20" fill="url(#h3_cam_body)" stroke="#475569" stroke-width="2.5"/>
    <!-- Hand Grip on Left -->
    <path d="M 0 65 Q 25 100, 25 150 Q 25 190, 0 210 Z" fill="#0f172a"/>
    <!-- Viewfinder Pentaprism Top -->
    <polygon points="90,50 115,20 165,20 190,50" fill="url(#h3_cam_body)" stroke="#475569" stroke-width="2"/>
    <rect x="125" y="16" width="30" height="6" rx="2" fill="#000000"/>
    <!-- Top Dials & Shutter -->
    <rect x="35" y="40" width="22" height="12" rx="3" fill="#64748b"/>
    <circle cx="46" cy="40" r="9" fill="#94a3b8"/>
    <rect x="205" y="42" width="26" height="10" rx="3" fill="#64748b"/>
    <!-- Red Accent Ring -->
    <circle cx="140" cy="140" r="82" fill="none" stroke="#ef4444" stroke-width="3"/>
    <!-- Outer Lens Barrel -->
    <circle cx="140" cy="140" r="78" fill="#1e293b" stroke="#64748b" stroke-width="4"/>
    <!-- Lens Focus Rings -->
    <circle cx="140" cy="140" r="68" fill="#0f172a" stroke="#475569" stroke-width="3"/>
    <!-- Front Glass Element -->
    <circle cx="140" cy="140" r="54" fill="url(#h3_lens_glass)"/>
    <!-- Anti-Reflective Optical Coating Rings -->
    <circle cx="140" cy="140" r="42" fill="none" stroke="#38bdf8" stroke-width="2.5" opacity="0.8"/>
    <circle cx="140" cy="140" r="26" fill="#0284c7" opacity="0.7"/>
    <circle cx="140" cy="140" r="14" fill="#0369a1"/>
    <!-- Glass Reflection Highlight -->
    <path d="M 105 110 A 50 50 0 0 1 175 110 Z" fill="url(#h3_glass_refl)"/>
  </g>

  <!-- Sparkles -->
  <g fill="#38bdf8" opacity="0.9">
    <polygon points="60,90 62,97 69,99 62,101 60,108 58,101 51,99 58,97"/>
    <polygon points="480,240 482,247 489,249 482,251 480,258 478,251 471,249 478,247"/>
  </g>
</svg>
SVG;
file_put_contents($dir . '/hero-slider-3.svg', $hero3);


// 4. Grid 1: Mobile & Tablets (grid-mobile.svg)
$grid1 = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 180" width="240" height="180" fill="none">
  <defs>
    <linearGradient id="gm_scr" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#38bdf8"/>
      <stop offset="100%" stop-color="#6366f1"/>
    </linearGradient>
    <filter id="gm_sh">
      <feDropShadow dx="0" dy="8" stdDeviation="10" flood-color="#000" flood-opacity="0.5"/>
    </filter>
  </defs>
  <!-- Tablet (Back) -->
  <g transform="translate(30, 20)" filter="url(#gm_sh)">
    <rect x="0" y="0" width="120" height="140" rx="14" fill="#1e293b" stroke="#64748b" stroke-width="2"/>
    <rect x="6" y="6" width="108" height="128" rx="10" fill="url(#gm_scr)"/>
    <circle cx="60" cy="12" r="2.5" fill="#000"/>
  </g>
  <!-- Phone (Front) -->
  <g transform="translate(105, 35)" filter="url(#gm_sh)">
    <rect x="0" y="0" width="75" height="135" rx="16" fill="#0f172a" stroke="#94a3b8" stroke-width="2"/>
    <rect x="4" y="4" width="67" height="127" rx="13" fill="url(#gm_scr)"/>
    <rect x="25" y="8" width="25" height="6" rx="3" fill="#000"/>
  </g>
</svg>
SVG;
file_put_contents($dir . '/grid-mobile.svg', $grid1);


// 5. Grid 2: Gaming Hardware (grid-gaming.svg)
$grid2 = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 180" width="240" height="180" fill="none">
  <defs>
    <linearGradient id="gg_neon" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#a855f7"/>
      <stop offset="100%" stop-color="#38bdf8"/>
    </linearGradient>
    <filter id="gg_sh">
      <feDropShadow dx="0" dy="8" stdDeviation="10" flood-color="#000" flood-opacity="0.5"/>
    </filter>
  </defs>
  <!-- Controller Graphic -->
  <g transform="translate(30, 25)" filter="url(#gg_sh)">
    <path d="M 25 60 C 15 35, 35 15, 65 12 C 80 10, 95 18, 105 28 C 115 18, 130 10, 145 12 C 175 15, 195 35, 185 60 C 175 85, 165 120, 145 125 C 125 130, 115 100, 105 95 C 95 100, 85 130, 65 125 C 45 120, 35 85, 25 60 Z" fill="#0f172a" stroke="#475569" stroke-width="2"/>
    <path d="M 80 25 L 130 25" stroke="url(#gg_neon)" stroke-width="3" stroke-linecap="round"/>
    <!-- Joysticks -->
    <circle cx="75" cy="75" r="14" fill="#1e293b" stroke="#a855f7" stroke-width="2"/>
    <circle cx="135" cy="75" r="14" fill="#1e293b" stroke="#38bdf8" stroke-width="2"/>
    <!-- D-pad -->
    <rect x="50" y="44" width="7" height="18" rx="2" fill="#64748b"/>
    <rect x="44.5" y="49.5" width="18" height="7" rx="2" fill="#64748b"/>
    <!-- Buttons -->
    <circle cx="155" cy="45" r="4" fill="#f43f5e"/>
    <circle cx="165" cy="53" r="4" fill="#22c55e"/>
  </g>
</svg>
SVG;
file_put_contents($dir . '/grid-gaming.svg', $grid2);


// 6. Grid 3: Cameras & Drones (grid-camera.svg)
$grid3 = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 180" width="240" height="180" fill="none">
  <defs>
    <radialGradient id="gc_glass" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#34d399"/>
      <stop offset="60%" stop-color="#047857"/>
      <stop offset="100%" stop-color="#064e3b"/>
    </radialGradient>
    <filter id="gc_sh">
      <feDropShadow dx="0" dy="8" stdDeviation="10" flood-color="#000" flood-opacity="0.5"/>
    </filter>
  </defs>
  <!-- Camera Graphic -->
  <g transform="translate(35, 25)" filter="url(#gc_sh)">
    <rect x="0" y="30" width="170" height="110" rx="14" fill="#1e293b" stroke="#475569" stroke-width="2"/>
    <polygon points="60,30 75,12 110,12 125,30" fill="#1e293b"/>
    <circle cx="95" cy="85" r="45" fill="#0f172a" stroke="#64748b" stroke-width="3"/>
    <circle cx="95" cy="85" r="34" fill="url(#gc_glass)"/>
    <circle cx="95" cy="85" r="20" fill="none" stroke="#6ee7b7" stroke-width="2" opacity="0.7"/>
    <circle cx="25" cy="45" r="6" fill="#ef4444"/>
  </g>
</svg>
SVG;
file_put_contents($dir . '/grid-camera.svg', $grid3);


// 7. Middle Coupon: Glowing 3D Gift & Badge (middle-coupon.svg)
$middle = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 180" width="240" height="180" fill="none">
  <defs>
    <linearGradient id="mc_box" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#a855f7"/>
      <stop offset="100%" stop-color="#6366f1"/>
    </linearGradient>
    <linearGradient id="mc_gold" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#fef08a"/>
      <stop offset="50%" stop-color="#eab308"/>
      <stop offset="100%" stop-color="#ca8a04"/>
    </linearGradient>
    <filter id="mc_sh">
      <feDropShadow dx="0" dy="10" stdDeviation="12" flood-color="#000" flood-opacity="0.55"/>
    </filter>
  </defs>
  <!-- 3D Gift Box -->
  <g transform="translate(55, 30)" filter="url(#mc_sh)">
    <!-- Main Box Body -->
    <rect x="15" y="45" width="100" height="85" rx="8" fill="url(#mc_box)" stroke="#c084fc" stroke-width="2"/>
    <rect x="58" y="45" width="16" height="85" fill="url(#mc_gold)"/>
    <rect x="15" y="80" width="100" height="16" fill="url(#mc_gold)"/>
    <!-- Lid -->
    <rect x="10" y="32" width="110" height="18" rx="5" fill="url(#mc_box)" stroke="#c084fc" stroke-width="2"/>
    <rect x="58" y="32" width="16" height="18" fill="url(#mc_gold)"/>
    <!-- Bow Ribbon Top -->
    <path d="M 65 32 C 45 5, 20 20, 60 30 Z" fill="url(#mc_gold)"/>
    <path d="M 65 32 C 85 5, 110 20, 70 30 Z" fill="url(#mc_gold)"/>
    <circle cx="65" cy="30" r="7" fill="url(#mc_gold)"/>
  </g>
  <!-- Floating Sparkles -->
  <polygon points="35,45 37,51 43,53 37,55 35,61 33,55 27,53 33,51" fill="#fde047"/>
  <polygon points="190,65 192,71 198,73 192,75 190,81 188,75 182,73 188,71" fill="#fde047"/>
</svg>
SVG;
file_put_contents($dir . '/middle-coupon.svg', $middle);


// 8. Dual 1: Ultrabooks (dual-laptop.svg)
$dual1 = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 280 200" width="280" height="200" fill="none">
  <defs>
    <linearGradient id="dl_scr" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#38bdf8"/>
      <stop offset="50%" stop-color="#6366f1"/>
      <stop offset="100%" stop-color="#a855f7"/>
    </linearGradient>
    <filter id="dl_sh">
      <feDropShadow dx="0" dy="12" stdDeviation="14" flood-color="#000" flood-opacity="0.55"/>
    </filter>
  </defs>
  <g transform="translate(25, 25)" filter="url(#dl_sh)">
    <!-- Screen -->
    <rect x="25" y="0" width="180" height="115" rx="8" fill="#1e293b" stroke="#94a3b8" stroke-width="2"/>
    <rect x="31" y="6" width="168" height="103" rx="4" fill="url(#dl_scr)"/>
    <!-- Base -->
    <path d="M 5 115 L 225 115 L 205 145 L 25 145 Z" fill="#0f172a" stroke="#64748b" stroke-width="2"/>
    <rect x="95" y="122" width="45" height="15" rx="3" fill="#1e293b"/>
  </g>
</svg>
SVG;
file_put_contents($dir . '/dual-laptop.svg', $dual1);


// 9. Dual 2: Smartwatch (dual-wearable.svg)
$dual2 = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 280 200" width="280" height="200" fill="none">
  <defs>
    <radialGradient id="dw_rings" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#f59e0b"/>
      <stop offset="60%" stop-color="#ef4444"/>
      <stop offset="100%" stop-color="#0f172a"/>
    </radialGradient>
    <filter id="dw_sh">
      <feDropShadow dx="0" dy="12" stdDeviation="14" flood-color="#000" flood-opacity="0.55"/>
    </filter>
  </defs>
  <g transform="translate(85, 20)" filter="url(#dw_sh)">
    <!-- Straps -->
    <rect x="28" y="0" width="54" height="40" rx="8" fill="#334155"/>
    <rect x="28" y="120" width="54" height="40" rx="8" fill="#334155"/>
    <!-- Watch Case -->
    <rect x="10" y="25" width="90" height="110" rx="28" fill="#0f172a" stroke="#cbd5e1" stroke-width="2.5"/>
    <!-- Screen -->
    <rect x="18" y="33" width="74" height="94" rx="20" fill="#05050a"/>
    <!-- Activity Rings -->
    <circle cx="55" cy="80" r="26" fill="none" stroke="#ef4444" stroke-width="4"/>
    <circle cx="55" cy="80" r="19" fill="none" stroke="#22c55e" stroke-width="4"/>
    <circle cx="55" cy="80" r="12" fill="none" stroke="#38bdf8" stroke-width="4"/>
    <!-- Crown Button -->
    <rect x="100" y="55" width="5" height="18" rx="2" fill="#cbd5e1"/>
  </g>
</svg>
SVG;
file_put_contents($dir . '/dual-wearable.svg', $dual2);


// 10. Bottom Mega: Guarantee Shield & Stars (bottom-mega.svg)
$bottom = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 220" width="320" height="220" fill="none">
  <defs>
    <linearGradient id="bm_shield" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#fb7185"/>
      <stop offset="50%" stop-color="#e11d48"/>
      <stop offset="100%" stop-color="#881337"/>
    </linearGradient>
    <linearGradient id="bm_gold" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#fef08a"/>
      <stop offset="100%" stop-color="#eab308"/>
    </linearGradient>
    <filter id="bm_sh">
      <feDropShadow dx="0" dy="12" stdDeviation="15" flood-color="#000" flood-opacity="0.6"/>
    </filter>
  </defs>
  <g transform="translate(85, 25)" filter="url(#bm_sh)">
    <!-- 3D Security Shield -->
    <path d="M 75 10 Q 120 18, 140 35 Q 140 100, 75 145 Q 10 100, 10 35 Q 30 18, 75 10 Z" fill="url(#bm_shield)" stroke="#fda4af" stroke-width="3"/>
    <!-- Inner Shield -->
    <path d="M 75 22 Q 110 28, 125 42 Q 125 92, 75 130 Q 25 92, 25 42 Q 40 28, 75 22 Z" fill="#4c0519"/>
    <!-- Checkmark -->
    <path d="M 50 75 L 68 93 L 105 52" stroke="url(#bm_gold)" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
  </g>
  <!-- Floating Gold Stars -->
  <polygon points="50,60 53,68 62,69 55,75 58,84 50,79 42,84 45,75 38,69 47,68" fill="url(#bm_gold)"/>
  <polygon points="260,80 263,88 272,89 265,95 268,104 260,99 252,104 255,95 248,89 257,88" fill="url(#bm_gold)"/>
  <polygon points="230,150 232,156 238,157 233,162 235,168 230,164 225,168 227,162 222,157 228,156" fill="url(#bm_gold)"/>
</svg>
SVG;
file_put_contents($dir . '/bottom-mega.svg', $bottom);


// 11. Shop Top: Catalog Ecosystem (shop-top.svg)
$shoptop = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 220" width="360" height="220" fill="none">
  <defs>
    <linearGradient id="st_scr" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#818cf8"/>
      <stop offset="100%" stop-color="#c084fc"/>
    </linearGradient>
    <filter id="st_sh">
      <feDropShadow dx="0" dy="10" stdDeviation="12" flood-color="#000" flood-opacity="0.5"/>
    </filter>
  </defs>
  <!-- Laptop in Catalog -->
  <g transform="translate(60, 45)" filter="url(#st_sh)">
    <rect x="0" y="0" width="170" height="110" rx="8" fill="#1e293b" stroke="#64748b" stroke-width="2"/>
    <rect x="6" y="6" width="158" height="98" rx="4" fill="url(#st_scr)"/>
    <path d="M -20 110 L 190 110 L 175 135 L -5 135 Z" fill="#0f172a" stroke="#475569" stroke-width="1.5"/>
  </g>
  <!-- Phone in Front -->
  <g transform="translate(200, 30)" filter="url(#st_sh)">
    <rect x="0" y="0" width="65" height="125" rx="14" fill="#0f172a" stroke="#cbd5e1" stroke-width="2"/>
    <rect x="4" y="4" width="57" height="117" rx="11" fill="url(#st_scr)"/>
  </g>
</svg>
SVG;
file_put_contents($dir . '/shop-top.svg', $shoptop);

echo "All 11 clean 3D visual banner SVGs successfully generated!\n";
