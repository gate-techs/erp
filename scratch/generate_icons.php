<?php
$sourcePath = dirname(__DIR__) . '/file/Frame 39.png';
$baseDir = dirname(__DIR__);
if (!file_exists($sourcePath)) {
    die("File not found: $sourcePath\n");
}

$info = getimagesize($sourcePath);
echo "Source info: " . $info[0] . "x" . $info[1] . " type: " . $info['mime'] . "\n";

$src = imagecreatefrompng($sourcePath);
if (!$src) {
    die("Could not create image from PNG\n");
}

// Preserve transparency helper
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

// 1. iOS App Icon (1024x1024)
resizeAndSave($src, 1024, 1024, $baseDir . '/ios/App/App/Assets.xcassets/AppIcon.appiconset/AppIcon-512@2x.png');

// 2. Android Icons
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

// 3. Web & PWA Portal Icons
$portalIcons = [
    'public/assets/portal/icon-192.png' => 192,
    'public/assets/portal/icon-512.png' => 512,
    'public/assets/portal/icon-maskable.png' => 512,
    'public/assets/portal/apple-touch-icon.png' => 180,
    'mobile-app-shell/icon.png' => 512,
];

foreach ($portalIcons as $relPath => $size) {
    resizeAndSave($src, $size, $size, $baseDir . '/' . $relPath);
}

imagedestroy($src);
echo "ALL ICONS GENERATED SUCCESSFULLY!\n";
