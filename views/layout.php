<?php
// views/layout.php

set_security_headers();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = $page_title ?? SITE_NAME;
$page_description = $page_description ?? SITE_DESCRIPTION;
$mainContent = $mainContent ?? '';
$navHome = $content['layout.nav_home'] ?? 'Home';
$navGallery = $content['layout.nav_gallery'] ?? 'Gallery';
$navBooking = $content['layout.nav_booking'] ?? 'Booking';
$navAbout = $content['layout.nav_about'] ?? 'About';
$navDashboard = $content['layout.nav_dashboard'] ?? 'Dashboard';
$navLogin = $content['layout.nav_login'] ?? 'Login';
$navLogout = $content['layout.nav_logout'] ?? 'Logout';
$footerText = $content['layout.footer_text'] ?? 'Mirohood — based on Earth.';

// SEO variables
$og_title = $og_title ?? $page_title;
$og_description = $og_description ?? $page_description;
$og_image = $og_image ?? (SITE_URL . '/assets/images/og-default.jpg');
$og_type = $og_type ?? 'website';
$og_url = $og_url ?? (SITE_URL . '/' . ltrim($_SERVER['REQUEST_URI'] ?? '', '/'));
$canonical_url = $canonical_url ?? $og_url;
$robots = $robots ?? 'index, follow';
$schema_json = $schema_json ?? null;
$twitter_card = $twitter_card ?? 'summary_large_image';
?>
<?php
// Ensure og_url and canonical_url are clean URLs (no query strings for canonical unless needed)
$canonical_url = preg_replace('/\?.*$/', '', $canonical_url);
$canonical_url = preg_replace('/#.*$/', '', $canonical_url);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($page_title); ?> | Mirohood</title>
    <meta name="description" content="<?php echo h($page_description); ?>">
    <meta name="keywords" content="استودیو عکاسی, عکاسی پرتره, عکاسی برند, فیلمبرداری برند, Mirohood, رزرو عکاسی, عکاس حرفه‌ای, استودیو عکس اهواز">
    <meta name="author" content="Mirohood">
    <meta name="robots" content="<?php echo h($robots); ?>">
    <link rel="canonical" href="<?php echo h($canonical_url); ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo h($og_title); ?>">
    <meta property="og:description" content="<?php echo h($og_description); ?>">
    <meta property="og:type" content="<?php echo h($og_type); ?>">
    <meta property="og:url" content="<?php echo h($og_url); ?>">
    <meta property="og:image" content="<?php echo h($og_image); ?>">
    <meta property="og:site_name" content="Mirohood">
    <meta property="og:locale" content="fa_IR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="<?php echo h($twitter_card); ?>">
    <meta name="twitter:title" content="<?php echo h($og_title); ?>">
    <meta name="twitter:description" content="<?php echo h($og_description); ?>">
    <meta name="twitter:image" content="<?php echo h($og_image); ?>">

    <?php if (!empty($schema_json)): ?>
    <script type="application/ld+json">
<?php echo $schema_json; ?>
    </script>
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@300;400;500;600&display=swap" as="style" onload="this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@300;400;500;600&display=swap" rel="stylesheet"></noscript>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
    <link rel="manifest" href="<?php echo url('manifest.json'); ?>">
    <link rel="apple-touch-icon" href="<?php echo asset('images/admin-icon-192.png'); ?>">
    <meta name="theme-color" content="#0a0908">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Mirohood">
    <script src="<?php echo asset('js/security.js'); ?>" defer></script>
    <script src="<?php echo asset('js/pwa-install.js'); ?>" defer></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?php echo url('service-worker.js'); ?>').catch(function() {});
            });
        }
    </script>
    <?php if (($current_page ?? '') === 'gallery'): ?>
        <link rel="stylesheet" href="<?php echo asset('css/gallery.css'); ?>">
        <!-- نسخهٔ query برای عبور از کش مرورگر پس از اصلاح نمایشگر تمام‌صفحهٔ گالری -->
        <script src="<?php echo asset('js/gallery.js?v=20260712-3'); ?>" defer></script>
    <?php endif; ?>
    <style>
        :root {
            --color-noir: #0a0908;
            --color-ivory: #f4f1ea;
            --color-champagne: #c8a862;
            --color-muted: #8a8580;
            --color-glass: rgba(255,255,255,0.03);
            --color-glass-border: rgba(200,168,98,0.12);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        html, body {
            overflow-x: hidden;
        }
        body {
            background: var(--color-noir);
            color: var(--color-ivory);
            font-family: 'Vazirmatn', sans-serif;
            line-height: 1.8;
            min-height: 100vh;
            padding-top: 70px;
            padding-bottom: <?php echo (($current_page ?? '') === 'dashboard' && is_user_logged_in()) ? '84px' : '0'; ?>;
        }
        img, video, svg, iframe {
            max-width: 100%;
        }
        a { color: var(--color-ivory); text-decoration: none; transition: all 0.3s ease; }
        a:hover { color: var(--color-champagne); }
        .container { max-width:1200px; margin:0 auto; padding:0 1.5rem; }
        .text-center { text-align:center; }
        .text-muted { color: var(--color-muted); }
        .text-gold { color: var(--color-champagne); }
        .hidden { display:none !important; }
        .flex { display:flex; }
        .flex-col { flex-direction:column; }
        .items-center { align-items:center; }
        .justify-between { justify-content:space-between; }
        .gap-4 { gap:1rem; }
        .gap-6 { gap:1.5rem; }
        .grid { display:grid; }
        .grid-cols-2 { grid-template-columns:repeat(2,1fr); }
        .grid-cols-3 { grid-template-columns:repeat(3,1fr); }
        .grid-cols-4 { grid-template-columns:repeat(4,1fr); }
        .w-full { width:100%; }
        .mb-4 { margin-bottom:1rem; }
        .mb-6 { margin-bottom:1.5rem; }
        .mb-8 { margin-bottom:2rem; }
        .mb-14 { margin-bottom:3.5rem; }
        .py-12 { padding-top:3rem; padding-bottom:3rem; }
        .py-28 { padding-top:6rem; padding-bottom:6rem; }
        .font-serif { font-family:'Cormorant Garamond',Georgia,serif; }
        .font-vazir { font-family:'Vazirmatn',sans-serif; }

        .navbar {
            position:fixed; top:0; left:0; right:0; z-index:1000;
            padding:0.8rem 0;
            background:rgba(10,9,8,0.95);
            backdrop-filter:blur(16px);
            border-bottom:1px solid rgba(200,168,98,0.08);
        }
        .navbar .container { display:flex; justify-content:space-between; align-items:center; }
        .navbar .logo { font-family:'Cormorant Garamond',Georgia,serif; font-size:1.6rem; font-weight:300; }
        .navbar .logo span { color:var(--color-champagne); }
        .nav-links { display:flex; list-style:none; gap:2rem; align-items:center; }
        .nav-links a { font-size:0.85rem; color:var(--color-muted); }
        .nav-links a:hover { color:var(--color-ivory); }
        .nav-links a.active { color:var(--color-champagne); }
        .nav-links .btn-nav { background:var(--color-champagne); color:var(--color-noir); padding:0.4rem 1.2rem; border-radius:9999px; font-weight:600; }
        .nav-links .btn-nav:hover { background:#b8944a; color:var(--color-noir); }
        .menu-toggle { display:none; background:none; border:none; color:var(--color-ivory); font-size:1.5rem; cursor:pointer; }
        #mobile-menu { display:none; position:fixed; inset:0; z-index:999; background:rgba(10,9,8,0.98); padding-top:5rem; }
        #mobile-menu.active { display:block; }
        #mobile-menu ul { list-style:none; display:flex; flex-direction:column; gap:1.5rem; text-align:center; }
        #mobile-menu a { font-family:'Cormorant Garamond',Georgia,serif; font-size:2rem; font-weight:300; }
        @media (max-width:768px) { .nav-links { display:none; } .menu-toggle { display:block; } }

        .btn-primary { display:inline-block; padding:0.8rem 2.5rem; border:1px solid var(--color-champagne); border-radius:9999px; font-size:0.8rem; font-weight:500; transition:all 0.4s ease; }
        .btn-primary:hover { background:var(--color-champagne); color:var(--color-noir); transform:scale(1.02); }

        .glass-card { background:rgba(255,255,255,0.03); backdrop-filter:blur(12px); border:1px solid rgba(200,168,98,0.1); border-radius:1rem; padding:1.75rem; transition:all 0.4s ease; }
        .glass-card:hover { background:rgba(255,255,255,0.06); border-color:rgba(200,168,98,0.25); transform:translateY(-4px); }

        footer { background:var(--color-noir); border-top:1px solid rgba(200,168,98,0.08); padding:2.5rem 0; margin-top:2rem; }
        footer .social a { color:var(--color-muted); margin:0 0.5rem; font-size:1.2rem; }
        footer .social a:hover { color:var(--color-champagne); }

        .reveal { opacity:0; transform:translateY(30px); transition:all 0.8s cubic-bezier(0.25,0.46,0.45,0.94); }
        .reveal.visible { opacity:1; transform:translateY(0); }

        /* ===== Bottom Navigation (Dynamic Island style) for logged-in users ===== */
        .user-bottom-nav {
            display: flex;
            position: fixed;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: auto;
            min-width: 320px;
            max-width: 92%;
            height: 58px;
            padding: 0 8px;
            background: rgba(26, 23, 21, 0.95);
            border: 1px solid rgba(200,168,98,0.15);
            border-radius: 29px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.03);
            z-index: 1001;
            padding-bottom: env(safe-area-inset-bottom, 0);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            overflow: hidden;
        }
        .user-bottom-nav .nav-indicator {
            position: absolute;
            top: 7px;
            bottom: 7px;
            height: auto;
            background: var(--color-champagne);
            border-radius: 22px;
            box-shadow: 0 4px 15px rgba(200,168,98,0.25);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 0;
            opacity: 0.95;
        }
        .user-bottom-nav .bottom-nav-list {
            display: flex;
            align-items: center;
            justify-content: space-around;
            width: 100%;
            height: 100%;
            list-style: none;
            margin: 0;
            padding: 0;
            position: relative;
            z-index: 1;
        }
        .user-bottom-nav .bottom-nav-item {
            flex: 1;
            height: 100%;
            min-width: 60px;
        }
        .user-bottom-nav .bottom-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            position: relative;
            color: var(--color-muted);
            font-size: 0.6rem;
            font-weight: 500;
            gap: 0.15rem;
            transition: color 0.25s ease;
            position: relative;
            text-decoration: none;
            -webkit-tap-highlight-color: transparent;
            cursor: pointer;
        }
        .user-bottom-nav .bottom-nav-link i {
            font-size: 1.2rem;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.25s ease;
        }
        .user-bottom-nav .bottom-nav-link.active {
            color: var(--color-noir);
        }
        .user-bottom-nav .bottom-nav-link.active i {
            transform: scale(1.1);
        }
        .user-bottom-nav .bottom-nav-link:active {
            opacity: 0.7;
        }
        .user-bottom-nav .nav-badge {
            position: absolute; top: 6px; right: 50%; transform: translateX(14px);
            min-width: 16px; height: 16px; padding: 0 4px; border-radius: 8px;
            background: #ef4444; color: #fff; font-size: 0.55rem; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
        }

        /* ===== Public Bottom Navigation (Dynamic Island style) ===== */
        .public-bottom-nav {
            display: flex;
            position: fixed;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: auto;
            min-width: 360px;
            max-width: 92%;
            height: 58px;
            padding: 0 8px;
            background: rgba(26, 23, 21, 0.95);
            border: 1px solid rgba(200,168,98,0.15);
            border-radius: 29px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.03);
            z-index: 1000;
            padding-bottom: env(safe-area-inset-bottom, 0);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            overflow: hidden;
        }
        .public-bottom-nav .nav-indicator {
            position: absolute;
            top: 7px;
            bottom: 7px;
            height: auto;
            background: var(--color-champagne);
            border-radius: 22px;
            box-shadow: 0 4px 15px rgba(200,168,98,0.25);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 0;
            opacity: 0.95;
        }
        .public-bottom-nav .bottom-nav-list {
            display: flex;
            align-items: center;
            justify-content: space-around;
            width: 100%;
            height: 100%;
            list-style: none;
            margin: 0;
            padding: 0;
            position: relative;
            z-index: 1;
        }
        .public-bottom-nav .bottom-nav-item {
            flex: 1;
            height: 100%;
            min-width: 56px;
        }
        .public-bottom-nav .bottom-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--color-muted);
            font-size: 0.6rem;
            font-weight: 500;
            gap: 0.15rem;
            transition: color 0.25s ease;
            position: relative;
            text-decoration: none;
            -webkit-tap-highlight-color: transparent;
            cursor: pointer;
        }
        .public-bottom-nav .bottom-nav-link i {
            font-size: 1.2rem;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.25s ease;
        }
        .public-bottom-nav .bottom-nav-link.active {
            color: var(--color-noir);
        }
        .public-bottom-nav .bottom-nav-link.active i {
            transform: scale(1.1);
        }
        .public-bottom-nav .bottom-nav-link:active {
            opacity: 0.7;
        }
        #toaster {
            bottom: 92px !important;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo url(''); ?>" class="logo">Miro<span>hood</span></a>
            <ul class="nav-links">
                <li><a href="<?php echo url(''); ?>" class="<?php echo ($current_page ?? '') === 'home' ? 'active' : ''; ?>"><?php echo h($navHome); ?></a></li>
                <li><a href="<?php echo url('gallery'); ?>" class="<?php echo ($current_page ?? '') === 'gallery' ? 'active' : ''; ?>"><?php echo h($navGallery); ?></a></li>
                <li><a href="<?php echo url('booking'); ?>" class="<?php echo ($current_page ?? '') === 'booking' ? 'active' : ''; ?>"><?php echo h($navBooking); ?></a></li>
                <li><a href="<?php echo url('about'); ?>" class="<?php echo ($current_page ?? '') === 'about' ? 'active' : ''; ?>"><?php echo h($navAbout); ?></a></li>
                <?php if (is_user_logged_in()): ?>
                    <li><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                    <li><a href="<?php echo url('logout'); ?>">Logout</a></li>
                <?php elseif (is_logged_in()): ?>
                    <li><a href="<?php echo url('admin'); ?>"><?php echo h($navDashboard); ?></a></li>
                    <li><a href="<?php echo url('admin/logout'); ?>"><?php echo h($navLogout); ?></a></li>
                <?php else: ?>
                    <li><a href="<?php echo url('login'); ?>" class="btn-nav">Login</a></li>
                    <li><a href="<?php echo url('register'); ?>">Register</a></li>
                <?php endif; ?>
            </ul>
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
        </div>
    </nav>
    <div id="mobile-menu">
        <ul>
            <li><a href="<?php echo url(''); ?>"><?php echo h($navHome); ?></a></li>
            <li><a href="<?php echo url('gallery'); ?>"><?php echo h($navGallery); ?></a></li>
            <li><a href="<?php echo url('booking'); ?>"><?php echo h($navBooking); ?></a></li>
            <li><a href="<?php echo url('about'); ?>"><?php echo h($navAbout); ?></a></li>
            <?php if (is_user_logged_in()): ?>
                <li><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                <li><a href="<?php echo url('logout'); ?>">Logout</a></li>
            <?php elseif (is_logged_in()): ?>
                <li><a href="<?php echo url('admin'); ?>"><?php echo h($navDashboard); ?></a></li>
                <li><a href="<?php echo url('admin/logout'); ?>"><?php echo h($navLogout); ?></a></li>
            <?php else: ?>
                <li><a href="<?php echo url('login'); ?>">Login</a></li>
                <li><a href="<?php echo url('register'); ?>">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <main><?php echo $mainContent; ?></main>
    <footer>
        <div class="container">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div><p style="color:#8a8580;font-size:0.85rem;">&copy; <?php echo date('Y'); ?> <?php echo h($footerText); ?></p></div>
                <div class="social">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-vimeo-v"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <?php if (is_user_logged_in() && ($current_page ?? '') === 'dashboard'): ?>
    <?php
        $userNavUnread = 0;
        if (function_exists('is_user_logged_in') && is_user_logged_in()) {
            try { $userNavUnread = (new Notification())->countUnread($_SESSION['user_id']); } catch (Exception $e) { $userNavUnread = 0; }
        }
    ?>
    <!-- ===== Bottom Navigation (Dynamic Island style) ===== -->
    <nav class="user-bottom-nav" id="userBottomNav">
        <div class="nav-indicator" id="userNavIndicator"></div>
        <ul class="bottom-nav-list">
            <li class="bottom-nav-item">
                <a href="<?php echo url('dashboard'); ?>" class="bottom-nav-link <?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>" data-href="<?php echo url('dashboard'); ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>داشبورد</span>
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="<?php echo url('my-gallery'); ?>" class="bottom-nav-link <?php echo ($current_page ?? '') === 'my-gallery' ? 'active' : ''; ?>" data-href="<?php echo url('my-gallery'); ?>">
                    <i class="fas fa-images"></i>
                    <span>گالری من</span>
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="<?php echo url('booking'); ?>" class="bottom-nav-link <?php echo ($current_page ?? '') === 'booking' ? 'active' : ''; ?>" data-href="<?php echo url('booking'); ?>">
                    <i class="fas fa-calendar-plus"></i>
                    <span>رزرو</span>
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="<?php echo url('notifications'); ?>" class="bottom-nav-link <?php echo ($current_page ?? '') === 'notifications' ? 'active' : ''; ?>" data-href="<?php echo url('notifications'); ?>">
                    <i class="fas fa-bell"></i>
                    <span>اعلانات</span>
                    <?php if ($userNavUnread > 0): ?><span class="nav-badge"><?php echo $userNavUnread; ?></span><?php endif; ?>
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="<?php echo url('profile'); ?>" class="bottom-nav-link <?php echo ($current_page ?? '') === 'profile' ? 'active' : ''; ?>" data-href="<?php echo url('profile'); ?>">
                    <i class="fas fa-user-cog"></i>
                    <span>پروفایل</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

    <div id="toaster" class="hidden" style="position:fixed;bottom:1rem;left:50%;transform:translateX(-50%);z-index:9999;">
        <div id="toast-message" style="background:#151413;color:#f4f1ea;border:1px solid rgba(200,168,98,0.3);border-radius:0.75rem;padding:0.75rem 1.5rem;box-shadow:0 10px 40px rgba(0,0,0,0.5);max-width:24rem;text-align:center;"></div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.getElementById('menuToggle');
        var menu = document.getElementById('mobile-menu');
        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                menu.classList.toggle('active');
                var icon = this.querySelector('i');
                if (icon) { icon.classList.toggle('fa-bars'); icon.classList.toggle('fa-times'); }
            });
            menu.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    menu.classList.remove('active');
                    var icon = toggle.querySelector('i');
                    if (icon) { icon.classList.remove('fa-times'); icon.classList.add('fa-bars'); }
                });
            });
        }
        var toaster = document.getElementById('toaster');
        var toastMsg = document.getElementById('toast-message');
        window.showToast = function(message, type) {
            if (!toaster || !toastMsg) return;
            var bg = '#151413', border = 'rgba(200,168,98,0.3)';
            if (type === 'success') { bg = 'rgba(22,163,74,0.9)'; border = '#22c55e'; }
            else if (type === 'error') { bg = 'rgba(220,38,38,0.9)'; border = '#ef4444'; }
            toastMsg.textContent = message;
            toastMsg.style.backgroundColor = bg;
            toastMsg.style.borderColor = border;
            toaster.classList.remove('hidden');
            clearTimeout(window.toastTimeout);
            window.toastTimeout = setTimeout(function() { toaster.classList.add('hidden'); }, 4000);
        };
        var reveals = document.querySelectorAll('.reveal');
        function checkReveal() {
            var h = window.innerHeight;
            reveals.forEach(function(el) {
                var rect = el.getBoundingClientRect();
                if (rect.top < h - 150) { el.classList.add('visible'); }
            });
        }
        checkReveal();
        window.addEventListener('scroll', checkReveal);
        window.addEventListener('resize', checkReveal);

        // ===== Dynamic Island Bottom Nav Indicator =====
        function setupDynamicNav(navId, indicatorId) {
            var nav = document.getElementById(navId);
            var indicator = document.getElementById(indicatorId);
            if (!nav || !indicator) return;

            function moveIndicatorTo(link) {
                var itemWidth = link.offsetWidth;
                var itemLeft = link.offsetLeft;
                indicator.style.width = Math.max(itemWidth - 12, 44) + 'px';
                indicator.style.left = (itemLeft + 6) + 'px';
            }

            var activeLink = nav.querySelector('.bottom-nav-link.active');
            if (activeLink) {
                moveIndicatorTo(activeLink);
            }

            nav.querySelectorAll('.bottom-nav-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    if (link.getAttribute('data-menu') === '1') {
                        return;
                    }
                    var href = link.getAttribute('data-href');
                    if (href) {
                        e.preventDefault();
                        nav.querySelectorAll('.bottom-nav-link').forEach(function(l) { l.classList.remove('active'); });
                        link.classList.add('active');
                        moveIndicatorTo(link);
                        setTimeout(function() {
                            window.location.href = href;
                        }, 180);
                    }
                });
            });

            window.addEventListener('resize', function() {
                var currentActive = nav.querySelector('.bottom-nav-link.active');
                if (currentActive) moveIndicatorTo(currentActive);
            });
        }

        setupDynamicNav('userBottomNav', 'userNavIndicator');
        setupDynamicNav('publicBottomNav', 'publicNavIndicator');

        // ===== Public Bottom Nav Menu Button =====
        var publicBottomNavMenu = document.getElementById('publicBottomNavMenu');
        if (publicBottomNavMenu && menu) {
            publicBottomNavMenu.addEventListener('click', function(e) {
                e.preventDefault();
                menu.classList.toggle('active');
                var icon = toggle ? toggle.querySelector('i') : null;
                if (icon) { icon.classList.toggle('fa-bars'); icon.classList.toggle('fa-times'); }
            });
        }

    });
    </script>
</body>
</html>