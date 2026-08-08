<?php
require_once __DIR__ . '/includes/bootstrap.php';
$route = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($base && $base !== '.' && str_starts_with($route, $base . '/')) $route = trim(substr($route, strlen($base)), '/');

try {
    if ($route === '') { readfile(__DIR__ . '/views/majed-portfolio.html'); exit; }
    elseif ($route === 'shop') require __DIR__ . '/views/shop.php';
    elseif ($route === 'shop/cart') require __DIR__ . '/views/cart.php';
    elseif ($route === 'shop/checkout') require __DIR__ . '/views/checkout.php';
    elseif ($route === 'shop/add') require __DIR__ . '/views/shop-add.php';
    elseif (preg_match('#^shop/product/(\d+)$#', $route, $m)) { $_GET['id']=(int)$m[1]; require __DIR__ . '/views/product.php'; }
    elseif ($route === 'cafe') require __DIR__ . '/views/cafe.php';
    elseif ($route === 'cafe/barcode') require __DIR__ . '/views/barcode.php';
    elseif (in_array($route, ['studio','estate','clinic'], true)) { $siteType = $route; require __DIR__ . '/views/client-site.php'; }
    elseif (preg_match('#^(studio|estate|clinic)/admin$#', $route, $m)) { require_admin(); $siteType = $m[1]; require __DIR__ . '/views/client-dashboard.php'; }
    elseif ($route === 'shop/admin') { require_admin(); require __DIR__ . '/views/admin/products.php'; }
    elseif ($route === 'cafe/admin') { require_admin(); require __DIR__ . '/views/admin/cafe.php'; }
    elseif ($route === 'admin/login') require __DIR__ . '/views/admin/login.php';
    elseif ($route === 'admin/logout') { $_SESSION=[]; session_destroy(); redirect('admin/login'); }
    elseif ($route === 'admin') { require_admin(); require __DIR__ . '/views/admin/dashboard.php'; }
    elseif ($route === 'admin/products') { require_admin(); require __DIR__ . '/views/admin/products.php'; }
    elseif ($route === 'admin/cafe') { require_admin(); require __DIR__ . '/views/admin/cafe.php'; }
    elseif ($route === 'admin/orders') { require_admin(); require __DIR__ . '/views/admin/orders.php'; }
    else { http_response_code(404); page_header('404'); echo '<section class="shop-wrap"><div class="shell"><div class="empty">صفحه پیدا نشد.</div></div></section>'; page_footer(); }
} catch (Throwable $e) {
    error_log('Majed Hub: ' . $e->getMessage());
    http_response_code(500); page_header('خطا'); echo '<section class="shop-wrap"><div class="shell"><div class="empty">خطای سرور. ابتدا Installer را اجرا و تنظیمات دیتابیس را بررسی کن.</div></div></section>'; page_footer();
}
