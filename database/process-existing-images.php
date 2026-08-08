<?php
// database/process-existing-images.php
// Run once via browser after deployment to generate WebP and responsive variants for existing images.
// Visit: https://mirohood.ir/database/process-existing-images.php
// Delete this file after successful run.

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/ImageHelper.php';
require_once __DIR__ . '/../includes/CacheHelper.php';

// Autoload models
spl_autoload_register(function ($className) {
    $file = __DIR__ . '/../models/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

set_time_limit(0);

$personModel = new Person();
$photoModel = new GalleryPhoto();

$images = $photoModel->getAll();
$processed = 0;
$skipped = 0;
$failed = 0;

echo "🔧 Processing existing gallery images...\n\n";

foreach ($images as $img) {
    $imagePath = !empty($img['image']) ? $_SERVER['DOCUMENT_ROOT'] . $img['image'] : null;

    if (!$imagePath || !file_exists($imagePath)) {
        echo "⚠️ File not found: " . ($img['image'] ?? 'N/A') . " — skipped\n";
        $skipped++;
        continue;
    }

    // Skip if already has WebP variants
    if (!empty($img['webp_image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img['webp_image'])) {
        echo "✅ Already processed: " . $img['image'] . "\n";
        $skipped++;
        continue;
    }

    try {
        $dir = dirname($imagePath);
        $filename = basename($imagePath);

        $paths = ImageHelper::process($imagePath, $dir, $filename);

        $updateData = [
            'thumbnail' => ImageHelper::toUrl($paths['thumbnail']),
            'medium' => ImageHelper::toUrl($paths['medium']),
            'large' => ImageHelper::toUrl($paths['large']),
            'webp_image' => ImageHelper::toUrl($paths['webp_image']),
            'webp_thumbnail' => ImageHelper::toUrl($paths['webp_thumbnail']),
            'webp_medium' => ImageHelper::toUrl($paths['webp_medium']),
            'webp_large' => ImageHelper::toUrl($paths['webp_large'])
        ];

        $photoModel->update($img['id'], $updateData);
        echo "✅ Processed: " . $img['image'] . "\n";
        $processed++;
    } catch (Exception $e) {
        echo "❌ Failed: " . $img['image'] . " — " . $e->getMessage() . "\n";
        $failed++;
    }
}

// Clear all caches so new variants are served immediately
try {
    CacheHelper::clear();
    echo "✅ Cache cleared\n";
} catch (Exception $e) {
    echo "\n⚠️ Cache clear warning: " . $e->getMessage() . "\n";
}

echo "\n";
echo "✅ Processed: $processed\n";
echo "⏭️ Skipped: $skipped\n";
echo "❌ Failed: $failed\n";
echo "\n⚠️ Delete this file after successful run.\n";
