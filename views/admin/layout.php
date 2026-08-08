<?php
// views/admin/layout.php - قالب ادمین
set_security_headers();
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($page_title)) $page_title = SITE_NAME;
$content = $content ?? '';
$current_page = $current_page ?? '';

$navItems = [
    ['key' => 'admin', 'url' => 'admin', 'icon' => 'fa-chart-pie', 'label' => 'داشبورد'],
    ['key' => 'admin-bookings', 'url' => 'admin/bookings', 'icon' => 'fa-calendar-check', 'label' => 'رزروها'],
    ['key' => 'admin-gallery', 'url' => 'admin/gallery', 'icon' => 'fa-images', 'label' => 'گالری'],
    ['key' => 'admin-client-galleries', 'url' => 'admin/client-galleries', 'icon' => 'fa-user-lock', 'label' => 'گالری مشتریان'],
    ['key' => 'admin-content', 'url' => 'admin/content', 'icon' => 'fa-pen-to-square', 'label' => 'محتوا'],
    ['key' => 'admin-media', 'url' => 'admin/media', 'icon' => 'fa-film', 'label' => 'رسانه'],
    ['key' => 'admin-hours', 'url' => 'admin/hours', 'icon' => 'fa-clock', 'label' => 'ساعت کاری']
];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($page_title); ?> | پنل ادمین Mirohood</title>
    <meta name="theme-color" content="#0a0908">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="<?php echo url('admin/manifest.json'); ?>">
    <link rel="apple-touch-icon" href="<?php echo asset('images/admin-icon-192.png'); ?>">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" as="style" onload="this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&display=swap" as="style" onload="this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&display=swap" rel="stylesheet"></noscript>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
    
    <style>
        /* ============================================================
           ROOT VARIABLES
           ============================================================ */
        :root {
            --color-noir: #0a0908;
            --color-ivory: #f4f1ea;
            --color-champagne: #c8a862;
            --color-champagne-dim: #a8893a;
            --color-muted: #8a8580;
            --color-panel: #12100e;
            --color-card: #1a1715;
            --color-glass-border: rgba(200,168,98,0.08);
            --sidebar-w: 260px;
            --success: #4ade80;
            --warning: #fbbf24;
            --danger: #f87171;
            --info: #60a5fa;
        }
        
        /* ============================================================
           RESET
           ============================================================ */
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: var(--color-noir);
            color: var(--color-ivory);
            font-family: 'Vazirmatn', sans-serif;
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        
        /* ============================================================
           SIDEBAR
           ============================================================ */
        .admin-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: var(--sidebar-w);
            background: var(--color-panel);
            border-left: 1px solid var(--color-glass-border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s ease;
        }
        .admin-sidebar .logo-box {
            padding: 1.5rem 1.2rem;
            border-bottom: 1px solid var(--color-glass-border);
        }
        .admin-sidebar .logo-box .logo {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 1.6rem;
            font-weight: 300;
            color: var(--color-ivory);
        }
        .admin-sidebar .logo-box .logo span { color: var(--color-champagne); }
        .admin-sidebar .logo-box .tag {
            font-size: 0.55rem;
            letter-spacing: 0.2em;
            color: var(--color-muted);
            text-transform: uppercase;
            margin-top: 0.1rem;
        }
        
        .admin-nav {
            flex: 1;
            padding: 0.8rem 0.6rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            overflow-y: auto;
        }
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.7rem 1rem;
            border-radius: 0.6rem;
            color: var(--color-muted);
            font-size: 0.85rem;
            font-weight: 400;
            transition: all 0.25s ease;
            position: relative;
        }
        .admin-nav a i {
            width: 18px;
            text-align: center;
            font-size: 0.9rem;
        }
        .admin-nav a:hover {
            background: rgba(255,255,255,0.03);
            color: var(--color-ivory);
        }
        .admin-nav a.active {
            background: linear-gradient(90deg, rgba(200,168,98,0.12), rgba(200,168,98,0.02));
            color: var(--color-champagne);
        }
        .admin-nav a.active::before {
            content: '';
            position: absolute;
            right: 0;
            top: 15%;
            bottom: 15%;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--color-champagne);
        }
        
        .admin-sidebar .bottom-box {
            padding: 0.8rem 0.6rem 1.2rem;
            border-top: 1px solid var(--color-glass-border);
        }
        .admin-sidebar .bottom-box a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.7rem 1rem;
            border-radius: 0.6rem;
            color: var(--color-muted);
            font-size: 0.82rem;
            transition: all 0.25s ease;
        }
        .admin-sidebar .bottom-box a:hover {
            color: var(--danger);
            background: rgba(239,68,68,0.06);
        }
        .admin-sidebar .bottom-box a.view-site:hover {
            color: var(--color-champagne);
            background: rgba(200,168,98,0.06);
        }
        
        /* ============================================================
           TOPBAR (Mobile)
           ============================================================ */
        .admin-topbar {
            display: none;
            position: sticky;
            top: 0;
            z-index: 90;
            background: rgba(10,9,8,0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--color-glass-border);
            padding: 0.8rem 1rem;
            align-items: center;
            justify-content: space-between;
        }
        .admin-topbar .logo {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 1.3rem;
            font-weight: 300;
        }
        .admin-topbar .logo span { color: var(--color-champagne); }
        .admin-topbar button {
            background: none;
            border: 1px solid var(--color-glass-border);
            border-radius: 0.5rem;
            color: var(--color-ivory);
            font-size: 1rem;
            padding: 0.4rem 0.7rem;
            cursor: pointer;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 99;
        }
        
        /* ============================================================
           MAIN CONTENT
           ============================================================ */
        .admin-main {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
            padding: 2rem 2.5rem 4rem;
        }
        
        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 900px) {
            .admin-sidebar { transform: translateX(100%); }
            .admin-sidebar.open { transform: translateX(0); box-shadow: -20px 0 60px rgba(0,0,0,0.5); }
            .admin-topbar { display: flex; }
            .sidebar-overlay.open { display: block; }
            .admin-main { margin-right: 0; padding: 1.5rem 1rem 5.5rem; }
        }

        /* ============================================================
           BOTTOM NAVIGATION (Dynamic Island style)
           ============================================================ */
        .admin-bottom-nav {
            display: none;
            position: fixed;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: auto;
            min-width: 340px;
            max-width: 92%;
            height: 58px;
            padding: 0 8px;
            background: rgba(18, 16, 14, 0.95);
            border: 1px solid var(--color-glass-border);
            border-radius: 29px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.03);
            z-index: 95;
            padding-bottom: env(safe-area-inset-bottom, 0);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            overflow: hidden;
        }
        @media (max-width: 900px) {
            .admin-bottom-nav { display: flex; }
        }
        .admin-bottom-nav .nav-indicator {
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
        .admin-bottom-nav .bottom-nav-list {
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
        .admin-bottom-nav .bottom-nav-item {
            flex: 1;
            height: 100%;
            min-width: 60px;
        }
        .admin-bottom-nav .bottom-nav-link {
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
        .admin-bottom-nav .bottom-nav-link i {
            font-size: 1.2rem;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.25s ease;
        }
        .admin-bottom-nav .bottom-nav-link.active {
            color: var(--color-noir);
        }
        .admin-bottom-nav .bottom-nav-link.active i {
            transform: scale(1.1);
        }
        .admin-bottom-nav .bottom-nav-link:active {
            opacity: 0.7;
        }

        /* ============================================================
           COMMON COMPONENTS
           ============================================================ */
        .admin-page-title {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 2.2rem;
            font-weight: 300;
            color: var(--color-ivory);
            margin-bottom: 0.2rem;
        }
        .admin-page-subtitle {
            color: var(--color-muted);
            font-size: 0.85rem;
            margin-bottom: 2rem;
        }
        
        .admin-card {
            background: var(--color-card);
            border: 1px solid var(--color-glass-border);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .admin-card-title {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 1.3rem;
            font-weight: 300;
            color: var(--color-ivory);
            margin-bottom: 0.5rem;
        }
        
        .admin-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1.6rem;
            background: linear-gradient(135deg, var(--color-champagne), var(--color-champagne-dim));
            border: none;
            border-radius: 9999px;
            color: var(--color-noir);
            font-family: 'Vazirmatn', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(200,168,98,0.2);
        }
        .admin-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .admin-btn-danger {
            background: transparent;
            border: 1px solid rgba(239,68,68,0.3);
            color: var(--danger);
        }
        .admin-btn-danger:hover { color: #fff; background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.5); }

        .admin-btn-outline {
            background: transparent;
            border: 1px solid var(--color-glass-border);
            color: var(--color-ivory);
        }
        .admin-btn-outline:hover {
            border-color: var(--color-champagne);
            color: var(--color-champagne);
            background: rgba(200,168,98,0.04);
        }
        
        .admin-input, .admin-select, .admin-textarea {
            width: 100%;
            padding: 0.6rem 1rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--color-glass-border);
            border-radius: 0.6rem;
            color: var(--color-ivory);
            font-size: 0.88rem;
            font-family: 'Vazirmatn', sans-serif;
            transition: all 0.25s ease;
        }
        .admin-input:focus, .admin-select:focus, .admin-textarea:focus {
            outline: none;
            border-color: var(--color-champagne);
            box-shadow: 0 0 0 3px rgba(200,168,98,0.06);
        }
        .admin-label {
            display: block;
            font-size: 0.75rem;
            color: var(--color-muted);
            margin-bottom: 0.3rem;
        }
        
        /* ===== Stats Grid ===== */
        .admin-stat-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 1100px) { .admin-stat-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 640px) { .admin-stat-grid { grid-template-columns: repeat(2, 1fr); } }
        
        .admin-stat-card {
            background: var(--color-card);
            border: 1px solid var(--color-glass-border);
            border-radius: 1rem;
            padding: 1.2rem 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .admin-stat-card:hover {
            border-color: rgba(200,168,98,0.15);
            transform: translateY(-3px);
        }
        .admin-stat-card .num {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 2.2rem;
            font-weight: 300;
        }
        .admin-stat-card .label {
            font-size: 0.65rem;
            color: var(--color-muted);
            margin-top: 0.1rem;
            letter-spacing: 0.05em;
        }
        .admin-stat-card .icon-bg {
            font-size: 1.8rem;
            opacity: 0.06;
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
        }
        .admin-stat-card { position: relative; overflow: hidden; }
        
        /* ===== Quick Links ===== */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 1100px) { .quick-links { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 640px) { .quick-links { grid-template-columns: repeat(2, 1fr); } }
        
        .quick-link-card {
            background: var(--color-card);
            border: 1px solid var(--color-glass-border);
            border-radius: 1rem;
            padding: 1.2rem 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .quick-link-card:hover {
            border-color: var(--color-champagne);
            transform: translateY(-3px);
        }
        .quick-link-card i {
            font-size: 1.5rem;
            color: var(--color-champagne);
        }
        .quick-link-card p {
            font-size: 0.8rem;
            margin-top: 0.3rem;
            color: var(--color-ivory);
        }
        
        /* ===== Table ===== */
        .admin-table-wrap { overflow-x: auto; }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            min-width: 600px;
        }
        .admin-table th {
            text-align: right;
            padding: 0.7rem 0.6rem;
            color: var(--color-muted);
            font-weight: 400;
            font-size: 0.7rem;
            border-bottom: 1px solid var(--color-glass-border);
            letter-spacing: 0.05em;
        }
        .admin-table td {
            padding: 0.7rem 0.6rem;
            border-bottom: 1px solid rgba(200,168,98,0.03);
        }
        
        /* ===== Badges ===== */
        .admin-badge {
            display: inline-block;
            padding: 0.2rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 600;
        }
        .admin-badge.pending { background: rgba(234,179,8,0.15); color: var(--warning); }
        .admin-badge.confirmed { background: rgba(34,197,94,0.15); color: var(--success); }
        .admin-badge.cancelled { background: rgba(239,68,68,0.15); color: var(--danger); }
        .admin-badge.completed { background: rgba(59,130,246,0.15); color: var(--info); }
        
        /* ===== Filters ===== */
        .filter-pill {
            display: inline-block;
            padding: 0.3rem 1.2rem;
            border-radius: 9999px;
            border: 1px solid var(--color-glass-border);
            color: var(--color-muted);
            font-size: 0.75rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .filter-pill.active {
            border-color: var(--color-champagne);
            color: var(--color-champagne);
            background: rgba(200,168,98,0.04);
        }
        .filter-pill:hover {
            border-color: var(--color-champagne);
            color: var(--color-ivory);
        }
        
        /* ===== Gallery Item ===== */
        .gallery-item {
            position: relative;
            border-radius: 0.8rem;
            overflow: hidden;
            aspect-ratio: 3/4;
            background: var(--color-panel);
            border: 1px solid var(--color-glass-border);
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .gallery-item .overlay {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10,9,8,0.85);
            backdrop-filter: blur(6px);
            padding: 0.5rem 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .gallery-item .overlay span { font-size: 0.65rem; color: var(--color-muted); }
        .gallery-item .overlay button {
            background: none;
            border: none;
            color: var(--danger);
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .gallery-item .overlay button:hover { color: #ff4444; transform: scale(1.1); }
        
        /* ===== iOS PWA Install Hint ===== */
        .ios-pwa-hint {
            background: rgba(200,168,98,0.1);
            border: 1px solid rgba(200,168,98,0.2);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #f4f1ea;
            font-size: 0.8rem;
            line-height: 1.5;
        }
        .ios-pwa-hint i { color: #c8a862; }
        .ios-pwa-close {
            background: none;
            border: none;
            color: #8a8580;
            cursor: pointer;
            margin-right: auto;
            font-size: 0.85rem;
            padding: 0.25rem;
            transition: color 0.2s ease;
        }
        .ios-pwa-close:hover { color: #f4f1ea; }

        /* ===== Toast ===== */
        .admin-toast-wrap {
            position: fixed;
            bottom: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
        }
        #toast-message {
            background: var(--color-panel);
            color: var(--color-ivory);
            border: 1px solid var(--color-glass-border);
            border-radius: 0.75rem;
            padding: 0.7rem 1.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            max-width: 24rem;
            text-align: center;
            font-size: 0.85rem;
        }
        .hidden { display: none !important; }

        /* ============================================================
           GALLERY ADMIN EXTENSIONS
           ============================================================ */
        .admin-btn-small { padding: 0.4rem 0.8rem; font-size: 0.75rem; }
        .admin-btn-danger:hover { color: #fff; background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.3); }

        .people-admin-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.2rem;
        }
        @media (max-width: 1100px) { .people-admin-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .people-admin-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; } }
        @media (max-width: 480px) { .people-admin-grid { grid-template-columns: 1fr; } }

        .people-admin-card {
            background: var(--color-card);
            border: 1px solid var(--color-glass-border);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .people-admin-card:hover {
            border-color: rgba(200,168,98,0.2);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
        }
        .people-admin-media {
            position: relative;
            aspect-ratio: 1;
            background: #151210;
            overflow: hidden;
        }
        .people-admin-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .people-admin-initial {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--color-muted);
        }
        .people-admin-badge {
            position: absolute;
            top: 0.6rem;
            right: 0.6rem;
            font-size: 0.6rem;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-weight: 600;
        }
        .people-admin-badge.featured { background: rgba(200,168,98,0.15); color: var(--color-champagne); }
        .people-admin-badge.inactive { background: rgba(239,68,68,0.15); color: var(--danger); }
        .people-admin-info { padding: 1rem; }
        .people-admin-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-ivory);
            margin-bottom: 0.2rem;
        }
        .people-admin-count {
            font-size: 0.75rem;
            color: var(--color-muted);
            margin-bottom: 0.8rem;
        }
        .people-admin-actions {
            display: flex;
            gap: 0.4rem;
        }

        /* Person form */
        .person-form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        @media (max-width: 900px) { .person-form-grid { grid-template-columns: 1fr; } }
        .form-group { margin-bottom: 1.2rem; }
        .form-hint {
            font-size: 0.7rem;
            color: var(--color-muted);
            margin-top: 0.3rem;
        }
        .preview-thumb {
            margin-top: 0.8rem;
            width: 100px;
            height: 100px;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid var(--color-glass-border);
        }
        .preview-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .check-row {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--color-ivory);
            font-size: 0.85rem;
            cursor: pointer;
        }
        .checkbox-label input { width: auto; }

        /* Upload zone */
        .upload-zone {
            position: relative;
            border: 2px dashed var(--color-glass-border);
            border-radius: 1rem;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
        }
        .upload-zone:hover, .upload-zone.dragover {
            border-color: var(--color-champagne);
            background: rgba(200,168,98,0.04);
        }
        .upload-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }
        .upload-zone-content i {
            font-size: 2.5rem;
            color: var(--color-champagne);
            margin-bottom: 0.8rem;
            display: block;
        }
        .upload-zone-content p {
            color: var(--color-ivory);
            font-size: 0.95rem;
            margin-bottom: 0.3rem;
        }
        .upload-zone-content span {
            color: var(--color-muted);
            font-size: 0.75rem;
        }
        .upload-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255,255,255,0.05);
        }
        .progress-bar {
            width: 100%;
            height: 100%;
        }
        .progress-fill {
            width: 0;
            height: 100%;
            background: var(--color-champagne);
            transition: width 0.3s ease;
        }

        /* Photos admin grid */
        .photos-admin-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
        }
        @media (max-width: 1200px) { .photos-admin-grid { grid-template-columns: repeat(4, 1fr); } }
        @media (max-width: 900px) { .photos-admin-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 600px) { .photos-admin-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; } }

        .photo-admin-item {
            position: relative;
            border-radius: 0.75rem;
            overflow: hidden;
            aspect-ratio: 1;
            background: var(--color-panel);
            border: 1px solid var(--color-glass-border);
            cursor: grab;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .photo-admin-item.dragging {
            opacity: 0.6;
            transform: scale(1.03);
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        .photo-admin-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .photo-admin-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .photo-admin-item:hover .photo-admin-overlay { opacity: 1; }
        .photo-admin-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.1);
            color: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .photo-admin-btn:hover { background: rgba(255,255,255,0.2); transform: scale(1.1); }
        .photo-admin-btn.danger:hover { background: rgba(239,68,68,0.25); border-color: rgba(239,68,68,0.4); }
        .photo-admin-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0.4rem 0.6rem;
            font-size: 0.7rem;
            color: rgba(255,255,255,0.8);
            background: linear-gradient(180deg, transparent, rgba(0,0,0,0.7));
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Modal */
        .modal {
            position: fixed;
            inset: 0;
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.75);
            backdrop-filter: blur(8px);
        }
        .modal-content {
            position: relative;
            z-index: 1;
            background: var(--color-card);
            border: 1px solid var(--color-glass-border);
            border-radius: 1rem;
            padding: 1.5rem;
            width: 90%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

    <!-- ===== Topbar (Mobile) ===== -->
    <div class="admin-topbar">
        <span class="logo">Miro<span>hood</span></span>
        <button id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ===== Sidebar ===== -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="logo-box">
            <div class="logo">Miro<span>hood</span></div>
            <div class="tag">Admin Panel</div>
        </div>
        <nav class="admin-nav">
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo url($item['url']); ?>" class="<?php echo $current_page === $item['key'] ? 'active' : ''; ?>">
                    <i class="fas <?php echo $item['icon']; ?>"></i>
                    <span><?php echo $item['label']; ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="bottom-box">
            <a href="<?php echo url(''); ?>" class="view-site" target="_blank">
                <i class="fas fa-arrow-up-left-from-circle"></i>
                <span>مشاهده سایت</span>
            </a>
            <a href="<?php echo url('admin/logout'); ?>">
                <i class="fas fa-right-from-bracket"></i>
                <span>خروج</span>
            </a>
        </div>
    </aside>

    <!-- ===== Main Content ===== -->
    <main class="admin-main">
        <div id="ios-pwa-hint" class="ios-pwa-hint" style="display:none;">
            <i class="fas fa-share" style="transform:rotate(180deg);font-size:0.9rem;"></i>
            <span>برای نصب اپ روی آیفون/آیپد، دکمهٔ Share در سافاری را بزنید و «Add to Home Screen» را انتخاب کنید.</span>
            <button id="ios-pwa-close" class="ios-pwa-close" aria-label="بستن"><i class="fas fa-times"></i></button>
        </div>
        <?php echo $content; ?>
    </main>

    <!-- ===== Bottom Navigation (Dynamic Island style) ===== -->
    <nav class="admin-bottom-nav" id="adminBottomNav">
        <div class="nav-indicator" id="adminNavIndicator"></div>
        <ul class="bottom-nav-list">
            <li class="bottom-nav-item">
                <a href="<?php echo url('admin'); ?>" class="bottom-nav-link <?php echo $current_page === 'admin' ? 'active' : ''; ?>" data-href="<?php echo url('admin'); ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>داشبورد</span>
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="<?php echo url('admin/bookings'); ?>" class="bottom-nav-link <?php echo $current_page === 'admin-bookings' ? 'active' : ''; ?>" data-href="<?php echo url('admin/bookings'); ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>رزروها</span>
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="<?php echo url('admin/gallery'); ?>" class="bottom-nav-link <?php echo $current_page === 'admin-gallery' ? 'active' : ''; ?>" data-href="<?php echo url('admin/gallery'); ?>">
                    <i class="fas fa-images"></i>
                    <span>گالری</span>
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="<?php echo url('admin/client-galleries'); ?>" class="bottom-nav-link <?php echo $current_page === 'admin-client-galleries' ? 'active' : ''; ?>" data-href="<?php echo url('admin/client-galleries'); ?>">
                    <i class="fas fa-user-lock"></i>
                    <span>مشتریان</span>
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="javascript:void(0)" class="bottom-nav-link" id="bottomNavMenu" data-menu="1">
                    <i class="fas fa-bars"></i>
                    <span>منو</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- ===== Toast ===== -->
    <div id="toaster" class="admin-toast-wrap hidden">
        <div id="toast-message"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== Sidebar Toggle =====
        var sidebar = document.getElementById('adminSidebar');
        var overlay = document.getElementById('sidebarOverlay');
        var toggle = document.getElementById('sidebarToggle');
        
        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        }
        
        if (toggle) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('open');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // ===== Bottom Navigation Menu Button =====
        var bottomNavMenu = document.getElementById('bottomNavMenu');
        if (bottomNavMenu) {
            bottomNavMenu.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.toggle('open');
                overlay.classList.toggle('open');
            });
        }

        // ===== Dynamic Island Bottom Nav Indicator =====
        function setupAdminDynamicNav(navId, indicatorId) {
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

        setupAdminDynamicNav('adminBottomNav', 'adminNavIndicator');

        // ===== Toast =====
        var toaster = document.getElementById('toaster');
        var toastMsg = document.getElementById('toast-message');
        var toastTimeout = null;
        
        window.showToast = function(message, type) {
            if (!toaster || !toastMsg) return;
            
            var borderColor = 'rgba(200,168,98,0.3)';
            var bg = '#1a1715';
            if (type === 'success') {
                borderColor = '#22c55e';
                bg = 'rgba(22,163,74,0.9)';
            } else if (type === 'error') {
                borderColor = '#ef4444';
                bg = 'rgba(220,38,38,0.9)';
            }
            
            toastMsg.textContent = message;
            toastMsg.style.borderColor = borderColor;
            toastMsg.style.backgroundColor = bg;
            toaster.classList.remove('hidden');
            
            if (toastTimeout) clearTimeout(toastTimeout);
            toastTimeout = setTimeout(function() {
                toaster.classList.add('hidden');
            }, 4000);
        };

        // Register admin PWA service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('<?php echo url('admin/service-worker.js'); ?>')
                .then(function(reg) { console.log('Admin SW registered', reg.scope); })
                .catch(function(err) { console.log('Admin SW registration failed', err); });
        }

        // ===== iOS PWA Install Hint =====
        function isIos() {
            return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        }
        function isStandalone() {
            return ('standalone' in window.navigator) && window.navigator.standalone;
        }
        var iosHint = document.getElementById('ios-pwa-hint');
        var iosPwaClose = document.getElementById('ios-pwa-close');
        if (iosHint && isIos() && !isStandalone()) {
            iosHint.style.display = 'flex';
        }
        if (iosPwaClose && iosHint) {
            iosPwaClose.addEventListener('click', function() {
                iosHint.style.display = 'none';
            });
        }
    });
    </script>
</body>
</html>