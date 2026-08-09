<?php
// index.php - نقطه ورود اصلی

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/ImageHelper.php';
require_once __DIR__ . '/includes/CacheHelper.php';
require_once __DIR__ . '/includes/EmailHelper.php';
require_once __DIR__ . '/config/database.php';


spl_autoload_register(function ($className) {
    $file = MODELS_PATH . '/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});


// ============================================================
// اصلاح مسیر درخواست
// ============================================================

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');

// حذف index.php از ابتدای مسیر
$path = preg_replace('#^index\.php/?#', '', $path);

// حذف مسیر پایه در صورت وجود
$basePath = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
if (!empty($basePath) && $basePath !== '.' && strpos($path, $basePath . '/') === 0) {
    $path = trim(substr($path, strlen($basePath)), '/');
}


// ============================================================
// API Routes
// ============================================================

if (strpos($path, 'admin/api/') === 0) {
    $apiPath = trim(substr($path, strlen('admin/api/')), '/');

    // مسیرهای عمومی بدون لاگین
    $publicApiRoutes = [
        'booking-submit',
        'available-times'
    ];

    if (in_array($apiPath, $publicApiRoutes, true)) {
        require_once CONTROLLERS_PATH . '/BookingController.php';
        $ctrl = new BookingController();
        switch ($apiPath) {
            case 'booking-submit':
                $ctrl->submit();
                break;
            case 'available-times':
                $ctrl->getAvailableTimes();
                break;
        }
        exit;
    }

    // API های ادمین
    require_once CONTROLLERS_PATH . '/AdminController.php';
    $ctrl = new AdminController();

    switch ($apiPath) {
        case 'update-booking-status':
            $ctrl->updateBookingStatus();
            break;
        case 'delete-booking':
            $ctrl->deleteBooking();
            break;
        case 'handle-booking-request':
            $ctrl->handleBookingRequest();
            break;
        case 'upload-gallery':
            $ctrl->uploadGalleryImage();
            break;
        case 'delete-gallery':
            $ctrl->deleteGalleryImage();
            break;
        case 'delete-person':
            $ctrl->personDelete();
            break;
        case 'update-photo-meta':
            $ctrl->updatePhotoMeta();
            break;
        case 'reorder-photos':
            $ctrl->reorderPhotos();
            break;
        case 'update-content':
            $ctrl->updateContent();
            break;
        case 'upload-media':
            $ctrl->uploadMedia();
            break;
        case 'delete-media':
            $ctrl->deleteMedia();
            break;
        case 'update-exceptions':
            $ctrl->updateExceptions();
            break;
        case 'client-gallery-delete':
            $ctrl->clientGalleryDelete();
            break;
        case 'client-gallery-upload':
            $ctrl->uploadClientGalleryImage();
            break;
        case 'client-gallery-delete-image':
            $ctrl->deleteClientGalleryImage();
            break;
        case 'client-gallery-update-meta':
            $ctrl->updateClientGalleryPhotoMeta();
            break;
        case 'client-gallery-toggle-favorite':
            $ctrl->toggleClientGalleryFavorite();
            break;
        case 'client-gallery-share':
            $ctrl->manageClientGalleryShare();
            break;
        default:
            http_response_code(404);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'API not found: ' . $apiPath
            ]);
            break;
    }
    exit;
}


// ============================================================
// Page Routes
// ============================================================

try {
    // Output buffering to reduce TTFB and improve perceived performance
    if (!ob_get_level()) {
        ob_start();
    }

    // Site-wide PWA manifest & service worker
    if ($path === 'manifest.json') {
        header('Content-Type: application/json; charset=utf-8');
        $manifestFile = __DIR__ . '/assets/site-manifest.json';
        if (file_exists($manifestFile)) {
            readfile($manifestFile);
        } else {
            echo '{}';
        }
        exit;
    }

    if ($path === 'service-worker.js') {
        header('Content-Type: application/javascript; charset=utf-8');
        header('Service-Worker-Allowed: /');
        $swFile = __DIR__ . '/assets/site-service-worker.js';
        if (file_exists($swFile)) {
            readfile($swFile);
        } else {
            echo '';
        }
        exit;
    }

    // Admin PWA manifest & service worker
    if ($path === 'admin/manifest.json') {
        header('Content-Type: application/json; charset=utf-8');
        $manifestFile = __DIR__ . '/assets/admin-manifest.json';
        if (file_exists($manifestFile)) {
            readfile($manifestFile);
        } else {
            echo '{}';
        }
        exit;
    }

    if ($path === 'admin/service-worker.js') {
        header('Content-Type: application/javascript; charset=utf-8');
        $swFile = __DIR__ . '/assets/admin-service-worker.js';
        if (file_exists($swFile)) {
            readfile($swFile);
        } else {
            echo '';
        }
        exit;
    }

    // robots.txt
    if ($path === 'robots.txt') {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /database/\n";
        echo "Sitemap: " . SITE_URL . "/sitemap.xml\n";
        exit;
    }

    // sitemap.xml
    if ($path === 'sitemap.xml') {
        header('Content-Type: application/xml; charset=utf-8');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';

        $portfolioUrl = 'https://majedmansouri.ir';
        $staticUrls = [
            ['loc' => $portfolioUrl . '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => $portfolioUrl . '/shop', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => $portfolioUrl . '/cafe', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $portfolioUrl . '/ticket', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $portfolioUrl . '/company', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $portfolioUrl . '/restaurant', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $portfolioUrl . '/realestate', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $portfolioUrl . '/hotel', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $portfolioUrl . '/medical', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        foreach ($staticUrls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $xml .= "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        // Dynamic gallery person pages
        try {
            require_once MODELS_PATH . '/Person.php';
            $personModel = new Person();
            $people = $personModel->getActive();
            foreach ($people as $p) {
                $url = SITE_URL . '/gallery/' . urlencode($p['slug']);
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
                $xml .= "    <lastmod>" . date('Y-m-d', strtotime($p['updated_at'] ?? $p['created_at'] ?? 'now')) . "</lastmod>\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.9</priority>\n";
                $xml .= "  </url>\n";
            }
        } catch (Exception $e) {
            error_log('Sitemap person fetch error: ' . $e->getMessage());
        }

        $xml .= '</urlset>';
        echo $xml;
        exit;
    }

    // گالری اشتراکی مشتری: /gallery/share/token
    if (preg_match('#^gallery/share/([^/]+)/download$#', $path, $matches)) {
        require_once CONTROLLERS_PATH . '/ClientGalleryController.php';
        (new ClientGalleryController())->downloadShared($matches[1]);
        exit;
    }

    if (preg_match('#^gallery/share/([^/]+)$#', $path, $matches)) {
        require_once CONTROLLERS_PATH . '/ClientGalleryController.php';
        (new ClientGalleryController())->shared($matches[1]);
        exit;
    }

    // گالری شخص: /gallery/slug
    if (preg_match('#^gallery/([^/]+)$#', $path, $matches)) {
        require_once CONTROLLERS_PATH . '/GalleryController.php';
        (new GalleryController())->person($matches[1]);
        exit;
    }

    // Public dashboard showcase — read-only mock data, separate from /admin.
    if ($path === 'dashboard') {
        require_once BASE_PATH . '/includes/DemoRenderer.php';
        (new DemoRenderer())->dashboardDemo();
        exit;
    }

    // Portfolio demos: one shared renderer with route-specific content and metadata.
    // Examples: /shop, /shop/products, /shop/product/coffee-machine, /ticket/event/concert
    $demoSegments = array_values(array_filter(explode('/', $path), function ($segment) { return $segment !== ''; }));
    $demoSlugs = ['shop', 'cafe', 'ticket', 'company', 'restaurant', 'realestate', 'hotel', 'medical'];
    if (!empty($demoSegments) && in_array($demoSegments[0], $demoSlugs, true)) {
        require_once BASE_PATH . '/includes/DemoRenderer.php';
        $subpage = $demoSegments[1] ?? '';
        (new DemoRenderer())->demo($demoSegments[0], $subpage);
        exit;
    }

    switch ($path) {
        case '':
        case 'home':
        case 'index':
            require_once BASE_PATH . '/includes/DemoRenderer.php';
            (new DemoRenderer())->portfolio();
            break;

        case 'booking':
            require_once CONTROLLERS_PATH . '/BookingController.php';
            $ctrl = new BookingController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ctrl->submit();
            } else {
                $ctrl->index();
            }
            break;

        case 'login':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->login();
            break;

        case 'register':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->register();
            break;

        case 'forgot-password':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->forgotPassword();
            break;

        case 'reset-password':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->resetPassword();
            break;

        case 'logout':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->logout();
            break;

        case 'dashboard':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->dashboard();
            break;

        case 'profile':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->profile();
            break;

        case 'profile/update':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->updateProfile();
            break;

        case 'my-gallery':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->myGallery();
            break;

        case 'my-gallery/download':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->downloadMyGalleryZip();
            break;

        case 'my-gallery/toggle-favorite':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->toggleFavorite();
            break;

        case 'my-gallery/toggle-selection':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->toggleFinalSelection();
            break;

        case 'my-gallery/confirm-selection':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->confirmFinalSelection();
            break;

        case 'booking/request':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->requestBookingAction();
            break;

        case 'notifications':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->notifications();
            break;

        case 'notifications/mark-read':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->markNotificationRead();
            break;

        case 'notifications/mark-all-read':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->markAllNotificationsRead();
            break;

        case 'notifications/delete':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->deleteNotification();
            break;

        case 'downloads':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->downloads();
            break;

        case 'gallery':
            require_once CONTROLLERS_PATH . '/GalleryController.php';
            (new GalleryController())->index();
            break;

        case 'about':
            require_once CONTROLLERS_PATH . '/HomeController.php';
            (new HomeController())->about();
            break;

        case 'admin':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->index();
            break;

        case 'admin/bookings':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->bookings();
            break;

        case 'admin/gallery':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->gallery();
            break;

        case 'admin/gallery/create':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->personCreate();
            break;

        case 'admin/gallery/edit':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->personEdit();
            break;

        case 'admin/gallery/photos':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->personPhotos();
            break;

        case 'admin/client-galleries':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->clientGalleries();
            break;

        case 'admin/client-galleries/create':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->clientGalleryCreate();
            break;

        case 'admin/client-galleries/edit':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->clientGalleryEdit();
            break;

        case 'admin/client-galleries/photos':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->clientGalleryPhotos();
            break;

        case 'admin/client-galleries/download':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->downloadClientGalleryZip();
            break;

        case 'admin/content':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->content();
            break;

        case 'admin/media':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->media();
            break;

        case 'admin/hours':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->hours();
            break;

        case 'admin/login':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->login();
            break;

        case 'admin/logout':
            require_once CONTROLLERS_PATH . '/AdminController.php';
            (new AdminController())->logout();
            break;

        default:
            http_response_code(404);
            echo '<!DOCTYPE html>
            <html lang="fa" dir="rtl">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>404 - صفحه پیدا نشد | Mirohood</title>
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;600&family=Cormorant+Garamond:wght@300;400;600&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
                <style>
                    * { margin:0; padding:0; box-sizing:border-box; }
                    body { font-family: \'Vazirmatn\', sans-serif; background: #0a0908; color: #f4f1ea; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; background-image: radial-gradient(ellipse at center, rgba(200,168,98,0.04), transparent 70%); }
                    .error-box { max-width: 420px; }
                    .error-box .code { font-family: \'Cormorant Garamond\', Georgia, serif; font-size: 8rem; font-weight: 300; color: #c8a862; line-height: 1; margin-bottom: 0.5rem; text-shadow: 0 0 60px rgba(200,168,98,0.2); }
                    .error-box h1 { font-family: \'Cormorant Garamond\', Georgia, serif; font-size: 1.8rem; font-weight: 300; margin-bottom: 0.75rem; }
                    .error-box p { color: #8a8580; font-size: 0.95rem; line-height: 1.8; margin-bottom: 1.5rem; }
                    .error-box a { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.7rem 2rem; background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908; text-decoration: none; border-radius: 9999px; font-weight: 600; font-size: 0.85rem; transition: all 0.3s ease; }
                    .error-box a:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,168,98,0.25); }
                </style>
            </head>
            <body>
                <div class="error-box">
                    <div class="code">404</div>
                    <h1>صفحه پیدا نشد</h1>
                    <p>صفحه‌ای که به دنبال آن بودید وجود ندارد یا منتقل شده است.</p>
                    <a href="/"><i class="fas fa-home"></i> بازگشت به صفحه اصلی</a>
                </div>
            </body>
            </html>';
            break;
    }

} catch (Exception $e) {
    error_log('Routing error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    if (ob_get_level()) { ob_end_clean(); }

    http_response_code(500);
    echo '<!DOCTYPE html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>500 - خطای سرور | Mirohood</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;600&family=Cormorant+Garamond:wght@300;400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body { font-family: \'Vazirmatn\', sans-serif; background: #0a0908; color: #f4f1ea; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; background-image: radial-gradient(ellipse at center, rgba(239,68,68,0.04), transparent 70%); }
            .error-box { max-width: 420px; }
            .error-box .code { font-family: \'Cormorant Garamond\', Georgia, serif; font-size: 8rem; font-weight: 300; color: #ef4444; line-height: 1; margin-bottom: 0.5rem; text-shadow: 0 0 60px rgba(239,68,68,0.2); }
            .error-box h1 { font-family: \'Cormorant Garamond\', Georgia, serif; font-size: 1.8rem; font-weight: 300; margin-bottom: 0.75rem; }
            .error-box p { color: #8a8580; font-size: 0.95rem; line-height: 1.8; margin-bottom: 1.5rem; }
            .error-box a { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.7rem 2rem; background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908; text-decoration: none; border-radius: 9999px; font-weight: 600; font-size: 0.85rem; transition: all 0.3s ease; }
            .error-box a:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,168,98,0.25); }
        </style>
    </head>
    <body>
        <div class="error-box">
            <div class="code">500</div>
            <h1>خطای سرور</h1>
            <p>خطایی در پردازش درخواست شما رخ داده است. لطفاً دوباره تلاش کنید.</p>
            <a href="/"><i class="fas fa-home"></i> بازگشت به صفحه اصلی</a>
        </div>
    </body>
    </html>';
    exit;
}

if (ob_get_level()) { ob_end_flush(); }

?>