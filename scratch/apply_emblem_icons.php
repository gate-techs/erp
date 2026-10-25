<?php
$sourcePath = __DIR__ . '/../public/assets/portal/header-emblem.png';
$baseDir = dirname(__DIR__);

if (!file_exists($sourcePath)) {
    die("File not found: $sourcePath\n");
}

$src = imagecreatefrompng($sourcePath);
if (!$src) {
    die("Could not load source PNG\n");
}

$srcW = imagesx($src);
$srcH = imagesy($src);
echo "Source Emblem Dimensions: {$srcW}x{$srcH}\n";

function resizeAndSave($src, $w, $h, $destPath, $fillBg = null) {
    $dir = dirname($destPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $dst = imagecreatetruecolor($w, $h);

    if ($fillBg !== null) {
        $color = imagecolorallocate($dst, $fillBg[0], $fillBg[1], $fillBg[2]);
        imagefilledrectangle($dst, 0, 0, $w, $h, $color);
    } else {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $w, $h, $transparent);
    }

    $srcW = imagesx($src);
    $srcH = imagesy($src);

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $srcW, $srcH);
    imagepng($dst, $destPath, 9);
    imagedestroy($dst);
    echo "Saved: $destPath ({$w}x{$h})\n";
}

// 1. iOS Native App Icon (1024x1024)
resizeAndSave($src, 1024, 1024, $baseDir . '/ios/App/App/Assets.xcassets/AppIcon.appiconset/AppIcon-512@2x.png');

// 2. Android Native Icons
$androidIcons = [
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

foreach ($androidIcons as $relPath => $size) {
    resizeAndSave($src, $size, $size, $baseDir . '/' . $relPath);
}

// 3. Web & PWA Portal Icons (192, 512, maskable, apple-touch-icon)
$portalIcons = [
    'public/assets/portal/icon-192.png' => 192,
    'public/assets/portal/icon-512.png' => 512,
    'public/assets/portal/icon-maskable.png' => 512,
    'public/assets/portal/apple-touch-icon.png' => 180,
    'mobile-app-shell/icon.png' => 512,
    'android/app/src/main/assets/public/icon.png' => 512,
    'ios/App/App/public/icon.png' => 512,
];

foreach ($portalIcons as $relPath => $size) {
    resizeAndSave($src, $size, $size, $baseDir . '/' . $relPath);
}

imagedestroy($src);
echo "All App & PWA icons successfully updated to Circular Emblem!\n";
