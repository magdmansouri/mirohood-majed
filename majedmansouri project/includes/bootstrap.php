<?php
require_once __DIR__ . '/../config.php';
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');
if (session_status() === PHP_SESSION_NONE) { session_name('majed_hub'); session_start(); }

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    if (strpos(MH_DB_NAME, 'PASTE_') === 0) throw new RuntimeException('ابتدا Installer را اجرا کن.');
    $pdo = new PDO('mysql:host=' . MH_DB_HOST . ';dbname=' . MH_DB_NAME . ';charset=' . MH_DB_CHARSET, MH_DB_USER, MH_DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    return $pdo;
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url($path = ''): string { return rtrim(MH_BASE_URL, '/') . '/' . ltrim($path, '/'); }
function redirect($path): never { header('Location: ' . url($path)); exit; }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) throw new RuntimeException('نشست منقضی شده؛ صفحه را Refresh کن.'); }
function is_admin(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void { if (!is_admin()) redirect('admin/login'); }
function money($price): string { return number_format((int)$price) . ' تومان'; }
function cart(): array { return $_SESSION['cart'] ?? []; }
function cart_count(): int { return array_sum(cart()); }
function add_cart($id, $qty = 1): void { $c=cart(); $id=(int)$id; $c[$id]=min(99,($c[$id]??0)+$qty); $_SESSION['cart']=$c; }
function remove_cart($id): void { $c=cart(); unset($c[(int)$id]); $_SESSION['cart']=$c; }
function page_header($title, $mode='portfolio'): void {
    $cartCount=cart_count();
    $branding = [
        'portfolio' => ['MM', 'Majed Mansouri', [['#about','مهارت‌ها'],['#work','نمونه‌کار'],['#contact','تماس']], ''],
        'shop' => ['MA', 'Majed Atelier', [[url('shop'),'خانه'],[url('shop').'#collection','کالکشن'],[url('shop/cart'),'سبد خرید']], url('shop/admin')],
        'cafe' => ['NC', 'Noir Café', [[url('cafe'),'منو'],[url('cafe').'#about','درباره کافه'],[url('cafe').'#contact','تماس']], url('cafe/admin')],
        'admin' => ['MH', 'Majed Hub', [[url('admin'),'داشبورد'],[url('shop/admin'),'فروشگاه'],[url('cafe/admin'),'کافه']], url('admin')],
        'studio' => ['NA', 'Nexa Atelier', [['#services','خدمات'],['#projects','پروژه‌ها'],['#contact','تماس']], url('studio/admin')],
        'estate' => ['NE', 'Northline Estates', [['#listings','فایل‌ها'],['#services','خدمات'],['#contact','تماس']], url('estate/admin')],
        'clinic' => ['AC', 'Aura Clinic', [['#services','خدمات'],['#team','تیم'],['#contact','رزرو']], url('clinic/admin')],
    ];
    [$mark,$brand,$links,$adminUrl] = $branding[$mode] ?? $branding['portfolio'];
    ?><!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#0a0908"><title><?=e($title)?> | <?=e($brand)?></title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,500;1,600&family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&family=Vazirmatn:wght@300;400;500;600;700&display=swap"><link rel="stylesheet" href="<?=url('assets/css/app.css?v=2')?>"></head><body class="<?=$mode?>"><header class="topbar"><div class="shell topbar-in"><a class="brand" href="<?= $mode==='portfolio' ? url() : url(trim($mode==='shop'?'shop':($mode==='cafe'?'cafe':$mode),'/')) ?>"><span class="brand-mark"><?=e($mark)?></span><span><?=e($brand)?></span></a><nav><?php foreach($links as [$href,$label]): ?><a href="<?=e($href)?>"><?=e($label)?></a><?php endforeach; ?><?php if($mode==='shop'): ?><a class="cart-link" href="<?=url('shop/cart')?>">سبد خرید <b><?=$cartCount?></b></a><?php endif; ?><?php if($adminUrl): ?><a href="<?=e($adminUrl)?>">مدیریت</a><?php endif; ?></nav></div></header><main>
    <?php
}
function page_footer(): void { ?></main><footer class="footer"><div class="shell">© <?=date('Y')?> MAJED MANSOURI · CUSTOM PHP COMMERCE PLATFORM</div></footer><script src="<?=url('assets/js/app.js?v=1')?>"></script></body></html><?php }
