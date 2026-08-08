<?php
// includes/functions.php

function render($view, $data = []) {
    extract($data);
    $viewFile = VIEWS_PATH . '/' . $view . '.php';
    if (!file_exists($viewFile)) {
        throw new Exception("View not found: {$view}");
    }
    ob_start();
    include $viewFile;
    $content = ob_get_clean();

    if (strpos($view, 'admin/') === 0) {
        $layoutFile = VIEWS_PATH . '/admin/layout.php';
        if (file_exists($layoutFile)) {
            ob_start();
            include $layoutFile;
            return ob_get_clean();
        }
    }
    return $content;
}

function render_partial($partial, $data = []) {
    extract($data);
    $file = PARTIALS_PATH . '/' . $partial . '.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '';
}

function asset($path) {
    return SITE_URL . '/assets/' . ltrim($path, '/');
}

function url($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}

function redirect($path = '') {
    header('Location: ' . url($path));
    exit;
}

function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function is_user_logged_in() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

function current_user() {
    if (!is_user_logged_in()) {
        return null;
    }
    return (new User())->getById($_SESSION['user_id']);
}

function get_client_ip() {
    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Generate responsive image markup with WebP fallback and srcset
 * @param array $image Row from gallery_images with webp_* and sized variants
 * @param string $size One of: thumbnail, medium, large, full (default image)
 * @param string $alt Alt text
 * @param string $class CSS classes
 * @param bool $lazy Whether to use lazy loading
 */
function responsive_image($image, $size = 'large', $alt = '', $class = '', $lazy = true) {
    if (empty($image)) {
        return '';
    }

    $sizeMap = [
        'thumbnail' => ['jpg' => 'thumbnail', 'webp' => 'webp_thumbnail', 'w' => 600],
        'medium' => ['jpg' => 'medium', 'webp' => 'webp_medium', 'w' => 1200],
        'large' => ['jpg' => 'large', 'webp' => 'webp_large', 'w' => 1920],
        'full' => ['jpg' => 'image', 'webp' => 'webp_image', 'w' => 2400]
    ];

    $sizeKey = $sizeMap[$size] ?? $sizeMap['large'];
    $fallbackImage = $image['image'] ?? ($image['src'] ?? '');
    $jpgUrl = !empty($image[$sizeKey['jpg']]) ? $image[$sizeKey['jpg']] : $fallbackImage;
    $webpUrl = !empty($image[$sizeKey['webp']]) ? $image[$sizeKey['webp']] : '';
    if (!$jpgUrl) {
        return '';
    }

    $baseName = preg_replace('/\.[^.]+$/', '', $jpgUrl);
    $srcset = [];
    if (!empty($image['webp_thumbnail'])) {
        $srcset[] = $image['webp_thumbnail'] . ' 600w';
    }
    if (!empty($image['webp_medium'])) {
        $srcset[] = $image['webp_medium'] . ' 1200w';
    }
    if (!empty($image['webp_large'])) {
        $srcset[] = $image['webp_large'] . ' 1920w';
    }

    $sizesAttr = '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
    $loading = $lazy ? 'loading="lazy" decoding="async"' : 'loading="eager" decoding="sync"';
    $classAttr = $class ? ' class="' . htmlspecialchars($class) . '"' : '';
    $altAttr = ' alt="' . htmlspecialchars($alt) . '"';

    $html = '<picture>';
    if ($webpUrl) {
        $html .= '<source srcset="' . htmlspecialchars($webpUrl) . '" type="image/webp">';
    }
    if (!empty($srcset)) {
        $html .= '<img src="' . htmlspecialchars($jpgUrl) . '" srcset="' . htmlspecialchars(implode(', ', $srcset)) . '" sizes="' . $sizesAttr . '"' . $classAttr . $altAttr . ' ' . $loading . '>';
    } else {
        $html .= '<img src="' . htmlspecialchars($jpgUrl) . '"' . $classAttr . $altAttr . ' ' . $loading . '>';
    }
    $html .= '</picture>';
    return $html;
}

function timeAgo($datetime) {
    if (empty($datetime)) return '';
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    if ($diff < 60) return 'همین الان';
    if ($diff < 3600) return floor($diff / 60) . ' دقیقه پیش';
    if ($diff < 86400) return floor($diff / 3600) . ' ساعت پیش';
    if ($diff < 604800) return floor($diff / 86400) . ' روز پیش';
    return date('Y/m/d', $time);
}

?>
