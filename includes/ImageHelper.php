<?php
// includes/ImageHelper.php

class ImageHelper {

    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif'
    ];

    private const MAX_WIDTH = 2400;
    private const MAX_HEIGHT = 2400;
    private const JPEG_QUALITY = 85;
    private const WEBP_QUALITY = 90;
    private const THUMB_WIDTH = 600;
    private const THUMB_HEIGHT = 600;
    private const MEDIUM_WIDTH = 1200;
    private const MEDIUM_HEIGHT = 1200;
    private const LARGE_WIDTH = 1920;
    private const LARGE_HEIGHT = 1920;

    /**
     * Validate uploaded image file
     */
    public static function validate(array $file): bool {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        $info = getimagesize($file['tmp_name']);
        if (!$info) {
            return false;
        }
        $mime = image_type_to_mime_type($info[2]);
        return isset(self::ALLOWED_TYPES[$mime]);
    }

    /**
     * Get safe extension from uploaded file
     */
    public static function getExtension(array $file): ?string {
        $info = getimagesize($file['tmp_name']);
        if (!$info) {
            return null;
        }
        $mime = image_type_to_mime_type($info[2]);
        return self::ALLOWED_TYPES[$mime] ?? null;
    }

    /**
     * Generate random filename
     */
    public static function generateFilename(string $extension): string {
        return bin2hex(random_bytes(12)) . '_' . time() . '.' . $extension;
    }

    /**
     * Process image: resize main image, create multiple sizes, create WebP variants, return paths
     */
    public static function process(string $sourcePath, string $targetDir, string $filename): array {
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!file_exists($sourcePath)) {
            throw new Exception('Source file not found');
        }

        $imagePath = $targetDir . '/' . $filename;
        copy($sourcePath, $imagePath);

        // Resize main image (max 2400x2400, high quality)
        // resize() may change the extension to .jpg for photos
        self::resize($imagePath, $imagePath, self::MAX_WIDTH, self::MAX_HEIGHT, self::JPEG_QUALITY);
        // Refresh the actual path after resize
        $imagePath = self::resolveResizedPath($imagePath);
        $filename = basename($imagePath);
        $baseName = pathinfo($filename, PATHINFO_FILENAME);

        // Create sized JPEG variants
        $thumbPath = $targetDir . '/thumb_' . $filename;
        $mediumPath = $targetDir . '/' . $baseName . '_medium.jpg';
        $largePath = $targetDir . '/' . $baseName . '_large.jpg';

        self::resize($imagePath, $thumbPath, self::THUMB_WIDTH, self::THUMB_HEIGHT, 80, true);
        self::resize($imagePath, $mediumPath, self::MEDIUM_WIDTH, self::MEDIUM_HEIGHT, self::JPEG_QUALITY);
        self::resize($imagePath, $largePath, self::LARGE_WIDTH, self::LARGE_HEIGHT, self::JPEG_QUALITY);

        // Create WebP variants (smaller, same quality)
        $webpPath = $targetDir . '/' . $baseName . '.webp';
        $webpThumbPath = $targetDir . '/thumb_' . $baseName . '.webp';
        $webpMediumPath = $targetDir . '/' . $baseName . '_medium.webp';
        $webpLargePath = $targetDir . '/' . $baseName . '_large.webp';

        self::convertToWebp($imagePath, $webpPath, self::WEBP_QUALITY);
        self::convertToWebp($thumbPath, $webpThumbPath, self::WEBP_QUALITY);
        self::convertToWebp($mediumPath, $webpMediumPath, self::WEBP_QUALITY);
        self::convertToWebp($largePath, $webpLargePath, self::WEBP_QUALITY);

        return [
            'image' => $imagePath,
            'thumbnail' => $thumbPath,
            'medium' => $mediumPath,
            'large' => $largePath,
            'webp_image' => $webpPath,
            'webp_thumbnail' => $webpThumbPath,
            'webp_medium' => $webpMediumPath,
            'webp_large' => $webpLargePath
        ];
    }

    /**
     * Resize image with GD
     */
    public static function resize($source, $dest, $maxWidth, $maxHeight, $quality = 82, $crop = false) {
        $info = getimagesize($source);
        if (!$info) {
            throw new Exception('Cannot read image dimensions');
        }

        $srcWidth = $info[0];
        $srcHeight = $info[1];
        $type = $info[2];

        // Calculate new dimensions
        if ($crop) {
            $ratio = max($maxWidth / $srcWidth, $maxHeight / $srcHeight);
            $newWidth = (int) round($srcWidth * $ratio);
            $newHeight = (int) round($srcHeight * $ratio);
            $cropX = (int) round(($newWidth - $maxWidth) / 2);
            $cropY = (int) round(($newHeight - $maxHeight) / 2);
        } else {
            $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight, 1);
            $newWidth = (int) round($srcWidth * $ratio);
            $newHeight = (int) round($srcHeight * $ratio);
        }

        $srcImage = self::createImage($source, $type);
        if (!$srcImage) {
            throw new Exception('Cannot create image resource');
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP || $type === IMAGETYPE_GIF) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
            imagefill($dstImage, 0, 0, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
        imagedestroy($srcImage);

        if ($crop) {
            $cropped = imagecreatetruecolor($maxWidth, $maxHeight);
            if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP || $type === IMAGETYPE_GIF) {
                imagealphablending($cropped, false);
                imagesavealpha($cropped, true);
                $transparent = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
                imagefill($cropped, 0, 0, $transparent);
            }
            imagecopy($cropped, $dstImage, 0, 0, $cropX, $cropY, $maxWidth, $maxHeight);
            imagedestroy($dstImage);
            $dstImage = $cropped;
        }

        // Always save as JPEG for photos, except transparent PNG/GIF
        $outputType = ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) ? $type : IMAGETYPE_JPEG;
        $destExt = strtolower(pathinfo($dest, PATHINFO_EXTENSION));
        if ($outputType === IMAGETYPE_JPEG && $destExt !== 'jpg' && $destExt !== 'jpeg') {
            $dest = preg_replace('/\.[^.]+$/', '.jpg', $dest);
        }

        switch ($outputType) {
            case IMAGETYPE_JPEG:
                imagejpeg($dstImage, $dest, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($dstImage, $dest, 6);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($dstImage, $dest, $quality);
                break;
            case IMAGETYPE_GIF:
                imagegif($dstImage, $dest);
                break;
        }

        imagedestroy($dstImage);
    }

    /**
     * Convert an image to WebP format
     */
    public static function convertToWebp(string $source, string $dest, int $quality = 88): void {
        if (!function_exists('imagewebp')) {
            // WebP not supported, skip
            return;
        }
        $info = getimagesize($source);
        if (!$info) {
            throw new Exception('Cannot read image dimensions for WebP conversion');
        }
        $srcImage = self::createImage($source, $info[2]);
        if (!$srcImage) {
            throw new Exception('Cannot create image resource for WebP conversion');
        }
        $width = imagesx($srcImage);
        $height = imagesy($srcImage);
        $dstImage = imagecreatetruecolor($width, $height);
        if ($info[2] === IMAGETYPE_PNG || $info[2] === IMAGETYPE_GIF) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
            imagefill($dstImage, 0, 0, $transparent);
        }
        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $width, $height, $width, $height);
        imagedestroy($srcImage);
        imagewebp($dstImage, $dest, $quality);
        imagedestroy($dstImage);
    }

    private static function createImage(string $path, int $type) {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            default:
                return null;
        }
    }

    /**
     * Delete image files including all variants
     */
    public static function deleteFiles($imagePath, $thumbPath = null, $mediumPath = null, $largePath = null, $webpPaths = []) {
        $paths = array_filter([$imagePath, $thumbPath, $mediumPath, $largePath]);
        if (is_array($webpPaths)) {
            $paths = array_merge($paths, $webpPaths);
        }
        foreach ($paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Convert filesystem path to web URL
     */
    public static function toUrl(string $filePath): string {
        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
        if (strpos($filePath, $docRoot) === 0) {
            return substr($filePath, strlen($docRoot));
        }
        return $filePath;
    }

    /**
     * After resize(), the file extension may have changed to .jpg.
     * Return the actual path that exists.
     */
    private static function resolveResizedPath(string $originalPath): string {
        $jpgPath = preg_replace('/\.[^.]+$/', '.jpg', $originalPath);
        if ($jpgPath !== $originalPath && file_exists($jpgPath)) {
            return $jpgPath;
        }
        return $originalPath;
    }
}
