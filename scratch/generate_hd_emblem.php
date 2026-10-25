<?php
$astronautFile = __DIR__ . '/../public/assets/portal/astronaut.png';
$baseDir = dirname(__DIR__);

if (!file_exists($astronautFile)) {
    die("Astronaut not found\n");
}

$astro = imagecreatefrompng($astronautFile);
imagealphablending($astro, true);
imagesavealpha($astro, true);

$astroW = imagesx($astro);
$astroH = imagesy($astro);
echo "Astronaut dimensions: {$astroW}x{$astroH}\n";

// Function to generate high quality crisp circular emblem
function createEmblemImage($size, $astro) {
    $img = imagecreatetruecolor($size, $size);
    imagealphablending($img, false);
    imagesavealpha($img, true);

    $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagefilledrectangle($img, 0, 0, $size, $size, $transparent);

    imagealphablending($img, true);

    $center = $size / 2;
    $radius = $size / 2;

    // Palette Colors
    // Navy: #00334B -> RGB(0, 51, 75)
    // Gold: #FDBB22 -> RGB(253, 187, 34)
    // Dark Gold/Accent: #D99B16
    $goldColor = imagecolorallocate($img, 253, 187, 34);
    $navyColor = imagecolorallocate($img, 0, 51, 75);

    // 1. Draw outer gold ring (diameter = size)
    imagefilledellipse($img, (int)$center, (int)$center, $size, $size, $goldColor);

    // 2. Draw inner navy circle (diameter = size - (border thickness * 2))
    // Border thickness approx 6% of size
    $borderThickness = max(2, (int)round($size * 0.055));
    $innerDiameter = $size - ($borderThickness * 2);
    imagefilledellipse($img, (int)$center, (int)$center, $innerDiameter, $innerDiameter, $navyColor);

    // 3. Draw astronaut centered inside
    // Scale astronaut so it fits comfortably with good padding inside navy circle
    $astroW = imagesx($astro);
    $astroH = imagesy($astro);

    $maxInnerW = (int)round($innerDiameter * 0.72);
    $maxInnerH = (int)round($innerDiameter * 0.72);

    $scale = min($maxInnerW / $astroW, $maxInnerH / $astroH);
    $targetW = (int)round($astroW * $scale);
    $targetH = (int)round($astroH * $scale);

    $targetX = (int)round($center - ($targetW / 2));
    $targetY = (int)round($center - ($targetH / 2) + ($size * 0.01)); // Slight vertical optical adjustment

    imagecopyresampled($img, $astro, $targetX, $targetY, 0, 0, $targetW, $targetH, $astroW, $astroH);

    return $img;
}

// Generate for each size
$targets = [
    // Web / PWA
    'public/assets/portal/header-emblem.png' => 512,
    'public/assets/portal/icon-192.png' => 192,
    'public/assets/portal/icon-512.png' => 512,
    'public/assets/portal/icon-maskable.png' => 512,
    'public/assets/portal/apple-touch-icon.png' => 180,
    'mobile-app-shell/icon.png' => 512,
    'android/app/src/main/assets/public/icon.png' => 512,
    'ios/App/App/public/icon.png' => 512,

    // iOS App Icon
    'ios/App/App/Assets.xcassets/AppIcon.appiconset/AppIcon-512@2x.png' => 1024,

    // Android Mipmaps
    'android/app/src/main/res/mipmap-mdpi/ic_launcher.png' => 48,
    'android/app/src/main/res/mipmap-mdpi/ic_launcher_round.png' => 48,
    'android/app/src/main/res/mipmap-mdpi/ic_launcher_foreground.png' => 108,

    'android/app/src/main/res/mipmap-hdpi/ic_launcher.png' => 72,
    'android/app/src/main/res/mipmap-hdpi/ic_launcher_round.png' => 72,
    'android/app/src/main/res/mipmap-hdpi/ic_launcher_foreground.png' => 162,

    'android/app/src/main/res/mipmap-xhdpi/ic_launcher.png' => 96,
    'android/app/src/main/res/mipmap-xhdpi/ic_launcher_round.png' => 96,
    'android/app/src/main/res/mipmap-xhdpi/ic_launcher_foreground.png' => 216,

    'android/app/src/main/res/mipmap-xxhdpi/ic_launcher.png' => 144,
    'android/app/src/main/res/mipmap-xxhdpi/ic_launcher_round.png' => 144,
    'android/app/src/main/res/mipmap-xxhdpi/ic_launcher_foreground.png' => 324,

    'android/app/src/main/res/mipmap-xxxhdpi/ic_launcher.png' => 192,
    'android/app/src/main/res/mipmap-xxxhdpi/ic_launcher_round.png' => 192,
    'android/app/src/main/res/mipmap-xxxhdpi/ic_launcher_foreground.png' => 432,
];

foreach ($targets as $relPath => $size) {
    $outPath = $baseDir . '/' . $relPath;
    $dir = dirname($outPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $emblem = createEmblemImage($size, $astro);
    imagepng($emblem, $outPath, 9);
    imagedestroy($emblem);
    echo "Generated HD Emblem: $relPath ({$size}x{$size})\n";
}

imagedestroy($astro);
echo "SUCCESS: All icons replaced with High-Definition Circular Emblem!\n";
