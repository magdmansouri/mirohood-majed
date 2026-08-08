<?php
// views/booking.php - Booking Page with Font Awesome Icons
$content = $content ?? [];
$packages = $packages ?? [];
$selected_package = $selected_package ?? '';
$bookingBg = $bookingBg ?? '';
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Book a Session | Mirohood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap" as="style" onload="this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/assets/images/admin-icon-192.png">
    <meta name="theme-color" content="#0a0908">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Mirohood">
    <script src="/assets/js/security.js" defer></script>
    <script src="/assets/js/pwa-install.js" defer></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js').catch(function() {});
            });
        }
    </script>

    <!-- ===== Persian Datepicker Library (local, works on slow connections) ===== -->
    <link rel="stylesheet" href="/assets/css/persian-datepicker.min.css">
    <script src="/assets/js/jquery-3.6.0.min.js"></script>
    <script src="/assets/js/persian-date.min.js" defer></script>
    <script src="/assets/js/persian-datepicker.min.js" defer></script>
    
    <style>
        /* ============================================================
           RESET & BASE
           ============================================================ */
        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            font-family:'Inter', sans-serif; 
            background:#0a0908; 
            color:#f4f1ea; 
            min-height:100vh; 
            overflow-x:hidden;
        }
        
        /* ============================================================
           Booking Page - Main Container
           ============================================================ */
        .booking-page { 
            position:relative; 
            min-height:100vh; 
            display:flex; 
            align-items:center; 
            justify-content:center; 
            padding:4rem 1.2rem; 
            background:#0a0908; 
            overflow:hidden; 
        }
        
        /* ============================================================
           Background - روشن‌تر
           ============================================================ */
        .booking-bg { 
            position:fixed; 
            top:0; 
            left:0; 
            width:100vw; 
            height:100vh; 
            z-index:0; 
            object-fit:cover;
            object-position:center;
            pointer-events:none;
        }
        
        .booking-bg-overlay { 
            position:fixed; 
            top:0; 
            left:0; 
            width:100vw; 
            height:100vh; 
            z-index:1; 
            background:rgba(10,9,8,0.12); 
            backdrop-filter:blur(2px) brightness(0.98);
            -webkit-backdrop-filter:blur(2px) brightness(0.98);
            pointer-events:none;
        }
        
        /* ============================================================
           Main Form Box
           ============================================================ */
        .booking-box { 
            position:relative; 
            z-index:2; 
            max-width:580px; 
            width:100%; 
            background:linear-gradient(145deg, rgba(20,18,16,0.92), rgba(10,9,8,0.96));
            border:1px solid rgba(200,168,98,0.12); 
            border-radius:2rem; 
            padding:2.8rem 2.2rem; 
            backdrop-filter:blur(30px);
            -webkit-backdrop-filter:blur(30px);
            box-shadow: 
                0 30px 80px rgba(0,0,0,0.7),
                inset 0 1px 0 rgba(200,168,98,0.05);
        }
        
        /* ============================================================
           Page Header
           ============================================================ */
        .booking-header { 
            text-align:center; 
            margin-bottom:2.2rem; 
        }
        .booking-header .badge {
            display:inline-block;
            font-size:0.6rem;
            letter-spacing:0.25em;
            text-transform:uppercase;
            color:#c8a862;
            background:rgba(200,168,98,0.08);
            border:1px solid rgba(200,168,98,0.1);
            padding:0.25rem 1rem;
            border-radius:9999px;
            margin-bottom:0.8rem;
            font-weight:600;
        }
        .booking-header h1 { 
            font-family:'Cormorant Garamond',Georgia,serif; 
            font-size:clamp(2.2rem,5vw,3.4rem); 
            font-weight:400; 
            color:#f4f1ea; 
            letter-spacing:-0.02em;
            line-height:1.1;
        }
        .booking-header h1 span { 
            color:#c8a862; 
        }
        .booking-header .subtitle { 
            color:#8a8580; 
            font-size:0.9rem; 
            margin-top:0.3rem;
            font-weight:300;
            letter-spacing:0.05em;
        }
        .booking-header .divider {
            width:3rem;
            height:2px;
            background:linear-gradient(90deg,transparent,#c8a862,transparent);
            margin:0.8rem auto 0;
        }
        
        /* ============================================================
           Packages - Professional Design with Font Awesome
           ============================================================ */
        .packages-grid { 
            display:grid; 
            grid-template-columns:repeat(3,1fr); 
            gap:0.6rem; 
            margin-bottom:2rem; 
        }
        .package-item { 
            background:rgba(255,255,255,0.02); 
            border:1px solid rgba(200,168,98,0.06); 
            border-radius:1rem; 
            padding:0.9rem 0.3rem; 
            text-align:center; 
            transition:all 0.4s cubic-bezier(0.25,0.46,0.45,0.94);
            cursor:default;
            position:relative;
            overflow:hidden;
        }
        .package-item::before {
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(135deg,rgba(200,168,98,0.03),transparent);
            opacity:0;
            transition:opacity 0.4s ease;
        }
        .package-item:hover { 
            border-color:rgba(200,168,98,0.2); 
            background:rgba(200,168,98,0.04);
            transform:translateY(-2px);
        }
        .package-item:hover::before { opacity:1; }
        .package-item .icon-wrap {
            width:3.2rem;
            height:3.2rem;
            border-radius:50%;
            background:rgba(200,168,98,0.06);
            border:1px solid rgba(200,168,98,0.08);
            display:flex;
            align-items:center;
            justify-content:center;
            margin:0 auto 0.4rem;
            transition:all 0.3s ease;
        }
        .package-item:hover .icon-wrap {
            background:rgba(200,168,98,0.12);
            border-color:rgba(200,168,98,0.2);
            transform:scale(1.05);
        }
        .package-item .icon-wrap i {
            font-size:1.4rem;
            color:#c8a862;
            transition:all 0.3s ease;
        }
        .package-item:hover .icon-wrap i {
            color:#dbb872;
            transform:scale(1.05);
        }
        .package-item .name { 
            font-size:0.7rem; 
            color:#f4f1ea; 
            font-weight:500;
            line-height:1.3;
        }
        .package-item .pkg-badge {
            display:inline-block;
            font-size:0.45rem;
            letter-spacing:0.1em;
            text-transform:uppercase;
            color:#c8a862;
            background:rgba(200,168,98,0.06);
            padding:0.1rem 0.6rem;
            border-radius:9999px;
            margin-top:0.2rem;
            font-weight:600;
        }
        
        /* ============================================================
           Form
           ============================================================ */
        .form-group { 
            margin-bottom:1.2rem; 
        }
        .form-group label { 
            display:flex;
            align-items:center;
            gap:0.5rem;
            font-size:0.75rem; 
            color:#8a8580; 
            margin-bottom:0.3rem; 
            font-weight:500;
            letter-spacing:0.05em;
        }
        .form-group label i {
            color:#c8a862;
            font-size:0.75rem;
            opacity:0.7;
            width:1rem;
            text-align:center;
        }
        .form-group input, 
        .form-group select, 
        .form-group textarea { 
            width:100%; 
            padding:0.8rem 1.1rem; 
            background:rgba(255,255,255,0.04); 
            border:1px solid rgba(200,168,98,0.08); 
            border-radius:0.9rem; 
            color:#f4f1ea; 
            font-size:0.9rem; 
            font-family:'Inter', sans-serif; 
            transition:all 0.35s ease; 
            outline:none;
            box-shadow:inset 0 1px 0 rgba(255,255,255,0.02);
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder { 
            color:rgba(255,255,255,0.12); 
            font-weight:300;
        }
        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus { 
            border-color:#c8a862; 
            box-shadow:0 0 0 3px rgba(200,168,98,0.06), inset 0 1px 0 rgba(200,168,98,0.05);
            background:rgba(255,255,255,0.06);
        }
        .form-group input[readonly] { 
            cursor:pointer; 
        }
        .form-group input[readonly]:hover {
            border-color:rgba(200,168,98,0.2);
            background:rgba(200,168,98,0.04);
        }
        .form-group select {
            appearance:none;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%238a8580' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat:no-repeat;
            background-position:left 1rem center;
            padding-left:2.8rem;
            cursor:pointer;
        }
        .form-group select option {
            background:#1a1715;
            color:#f4f1ea;
        }
        .form-group textarea {
            resize:vertical;
            min-height:80px;
        }
        .error-message { 
            color:#f87171; 
            font-size:0.75rem; 
            margin-top:0.2rem; 
            font-weight:400;
            display:flex;
            align-items:center;
            gap:0.3rem;
        }
        .error-message i { font-size:0.6rem; }
        .hidden { display:none !important; }
        
        /* ============================================================
           Submit Button
           ============================================================ */
        .submit-btn { 
            width:100%; 
            padding:0.9rem; 
            background:linear-gradient(135deg, #c8a862, #a8893a);
            border:none; 
            border-radius:9999px; 
            color:#0a0908; 
            font-family:'Inter', sans-serif; 
            font-size:0.85rem; 
            font-weight:700; 
            cursor:pointer; 
            transition:all 0.4s cubic-bezier(0.25,0.46,0.45,0.94);
            letter-spacing:0.05em;
            position:relative;
            overflow:hidden;
        }
        .submit-btn::before {
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(135deg, #dbb872, #b8944a);
            opacity:0;
            transition:opacity 0.4s ease;
        }
        .submit-btn:hover { 
            transform:scale(1.01); 
            box-shadow:0 4px 30px rgba(200,168,98,0.25);
        }
        .submit-btn:hover::before { opacity:1; }
        .submit-btn span { position:relative; z-index:1; display:flex; align-items:center; justify-content:center; gap:0.6rem; }
        .submit-btn:disabled { opacity:0.5; cursor:not-allowed; transform:none !important; }
        
        /* ============================================================
           Success Message
           ============================================================ */
        .success-message { 
            text-align:center; 
            padding:3rem 1rem; 
        }
        .success-message .icon-wrap {
            width:5rem;
            height:5rem;
            border-radius:50%;
            background:linear-gradient(135deg, rgba(74,222,128,0.15), rgba(74,222,128,0.05));
            border:1px solid rgba(74,222,128,0.2);
            display:flex;
            align-items:center;
            justify-content:center;
            margin:0 auto 1.5rem;
        }
        .success-message .icon-wrap i { 
            font-size:2.5rem; 
            color:#4ade80; 
        }
        .success-message h2 { 
            font-family:'Cormorant Garamond',Georgia,serif; 
            font-size:2rem; 
            font-weight:400; 
            margin-bottom:0.5rem; 
            color:#f4f1ea; 
        }
        .success-message p { 
            color:#8a8580; 
            font-size:0.9rem; 
        }
        .success-message a { 
            display:inline-block; 
            margin-top:1.5rem; 
            padding:0.6rem 2rem; 
            border:1px solid rgba(200,168,98,0.2); 
            border-radius:9999px; 
            color:#f4f1ea; 
            transition:all 0.3s ease; 
            font-size:0.8rem;
            font-weight:500;
        }
        .success-message a:hover { 
            background:#c8a862; 
            color:#0a0908; 
            border-color:#c8a862;
        }
        
        /* ============================================================
           Responsive - Mobile (بک‌گراند روشن‌تر)
           ============================================================ */
        @media (max-width: 768px) {
            .booking-page { padding:3rem 0.8rem; }
            
            .booking-bg {
                object-fit:cover;
                object-position:center;
                height:100vh;
                width:100vw;
            }
            .booking-bg-overlay {
                background:rgba(10,9,8,0.18);
                backdrop-filter:blur(2px) brightness(0.98);
                -webkit-backdrop-filter:blur(2px) brightness(0.98);
            }
            
            .booking-box {
                padding:1.8rem 1.2rem;
                border-radius:1.5rem;
                margin:0.3rem;
            }
            .booking-header h1 { font-size:2rem; }
            
            .packages-grid {
                grid-template-columns:1fr 1fr;
                gap:0.5rem;
            }
            .package-item { padding:0.7rem 0.2rem; }
            .package-item .icon-wrap { width:2.8rem; height:2.8rem; }
            .package-item .icon-wrap i { font-size:1.2rem; }
            .package-item .name { font-size:0.65rem; }
            
            .form-group input, 
            .form-group select, 
            .form-group textarea { 
                padding:0.7rem 0.9rem; 
                font-size:0.85rem; 
            }
            .submit-btn { padding:0.8rem; font-size:0.8rem; }
        }
        
        @media (max-width: 480px) {
            .booking-box { padding:1.2rem 0.8rem; }
            .packages-grid { grid-template-columns:1fr; }
            .booking-header h1 { font-size:1.8rem; }
            .package-item .icon-wrap { width:3.2rem; height:3.2rem; }
            .package-item .icon-wrap i { font-size:1.4rem; }
        }
        
        /* ============================================================
           Persian Datepicker - Premium Styling
           ============================================================ */
        .datepicker-plot-area {
            background: #0a0908 !important;
            border: 1px solid rgba(200,168,98,0.15) !important;
            border-radius: 1.2rem !important;
            box-shadow: 0 30px 80px rgba(0,0,0,0.9) !important;
            padding: 0.8rem 1rem !important;
            font-family: 'Inter', sans-serif !important;
            min-width:280px !important;
        }
        
        .datepicker-plot-area .datepicker-navigator {
            padding: 0.4rem 0 !important;
            border-bottom: 1px solid rgba(200,168,98,0.06) !important;
            margin-bottom: 0.6rem !important;
        }
        .datepicker-plot-area .datepicker-navigator .pwt-btn-switch {
            color: #c8a862 !important;
            font-weight: 500 !important;
            font-size: 0.9rem !important;
            background: transparent !important;
            letter-spacing:0.05em;
        }
        .datepicker-plot-area .datepicker-navigator .pwt-btn-next,
        .datepicker-plot-area .datepicker-navigator .pwt-btn-prev {
            color: #8a8580 !important;
            font-size: 1rem !important;
            background: transparent !important;
            border-radius:0.5rem !important;
            padding:0.2rem 0.6rem !important;
            transition:all 0.3s ease !important;
        }
        .datepicker-plot-area .datepicker-navigator .pwt-btn-next:hover,
        .datepicker-plot-area .datepicker-navigator .pwt-btn-prev:hover {
            color: #c8a862 !important;
            background: rgba(200,168,98,0.06) !important;
        }
        
        .datepicker-plot-area .datepicker-day-view .header .header-row-cell {
            color: #8a8580 !important;
            font-size: 0.65rem !important;
            font-weight: 600 !important;
            letter-spacing:0.05em !important;
            padding-bottom:0.4rem !important;
        }
        
        .datepicker-plot-area .datepicker-day-view .table-days td {
            padding:0.05rem !important;
        }
        .datepicker-plot-area .datepicker-day-view .table-days td span {
            color: #f4f1ea !important;
            font-size: 0.8rem !important;
            border-radius: 0.5rem !important;
            transition: all 0.25s ease !important;
            background: transparent !important;
            padding:0.3rem 0.2rem !important;
            display:block !important;
        }
        .datepicker-plot-area .datepicker-day-view .table-days td span:hover {
            background: rgba(200,168,98,0.08) !important;
            color: #c8a862 !important;
        }
        
        .datepicker-plot-area .datepicker-day-view .table-days td.selected span {
            background: linear-gradient(135deg, #c8a862, #a8893a) !important;
            color: #0a0908 !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 16px rgba(200,168,98,0.2) !important;
        }
        
        .datepicker-plot-area .datepicker-day-view .table-days td.today span {
            background: rgba(200,168,98,0.08) !important;
            color: #c8a862 !important;
            border: 1px solid rgba(200,168,98,0.15) !important;
        }
        
        .datepicker-plot-area .datepicker-day-view .table-days td.disabled span {
            color: #3d3a35 !important;
            cursor:not-allowed !important;
        }
        .datepicker-plot-area .datepicker-day-view .table-days td.disabled span:hover {
            background:transparent !important;
            color:#3d3a35 !important;
        }
        
        .datepicker-plot-area .datepicker-day-view .table-days td span.other-month {
            color: #2d2a25 !important;
        }
        
        .datepicker-plot-area .datepicker-month-view .month-item,
        .datepicker-plot-area .datepicker-year-view .year-item {
            color: #f4f1ea !important;
            border-radius:0.5rem !important;
            transition:all 0.25s ease !important;
            background:transparent !important;
            padding:0.3rem !important;
        }
        .datepicker-plot-area .datepicker-month-view .month-item:hover,
        .datepicker-plot-area .datepicker-year-view .year-item:hover {
            background:rgba(200,168,98,0.08) !important;
            color:#c8a862 !important;
        }
        .datepicker-plot-area .datepicker-month-view .month-item.selected,
        .datepicker-plot-area .datepicker-year-view .year-item.selected {
            background:linear-gradient(135deg, #c8a862, #a8893a) !important;
            color:#0a0908 !important;
            font-weight:700 !important;
        }
        
        .datepicker-plot-area .toolbox {
            border-top:1px solid rgba(200,168,98,0.06) !important;
            padding-top:0.6rem !important;
            margin-top:0.4rem !important;
        }
        .datepicker-plot-area .toolbox .pwt-btn-today {
            color:#c8a862 !important;
            background:transparent !important;
            border:1px solid rgba(200,168,98,0.1) !important;
            border-radius:0.5rem !important;
            padding:0.3rem 1rem !important;
            font-size:0.7rem !important;
            font-weight:500 !important;
            transition:all 0.3s ease !important;
        }
        .datepicker-plot-area .toolbox .pwt-btn-today:hover {
            background:rgba(200,168,98,0.06) !important;
            border-color:rgba(200,168,98,0.2) !important;
        }
        
        .datepicker-plot-area .toolbox .pwt-btn-submit {
            color:#0a0908 !important;
            background:linear-gradient(135deg, #c8a862, #a8893a) !important;
            border:none !important;
            border-radius:0.5rem !important;
            padding:0.3rem 1.2rem !important;
            font-size:0.7rem !important;
            font-weight:600 !important;
            transition:all 0.3s ease !important;
        }
        .datepicker-plot-area .toolbox .pwt-btn-submit:hover {
            transform:scale(1.02) !important;
            box-shadow:0 2px 16px rgba(200,168,98,0.2) !important;
        }
    </style>
</head>
<body>

<div class="booking-page">
    
    <!-- ===== Background ===== -->
    <?php if (!empty($bookingBg)): ?>
        <img src="<?php echo h($bookingBg); ?>" class="booking-bg" alt="Booking Background">
    <?php endif; ?>
    <div class="booking-bg-overlay"></div>
    
    <!-- ===== Main Box ===== -->
    <div class="booking-box">
        
        <!-- ===== User Info Bar ===== -->
        <?php if (!empty($user)): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.5rem;margin-bottom:1.5rem;padding:0.6rem 1rem;background:rgba(200,168,98,0.05);border:1px solid rgba(200,168,98,0.1);border-radius:0.8rem;">
            <div style="font-size:0.8rem;color:#f4f1ea;">
                <i class="fas fa-user-circle" style="color:#c8a862;margin-left:0.4rem;"></i>
                <?php echo h($user['name']); ?> — <?php echo h($user['phone']); ?>
            </div>
            <div style="font-size:0.75rem;">
                <a href="<?php echo url('dashboard'); ?>" style="color:#c8a862;text-decoration:none;margin-left:0.8rem;">داشبورد</a>
                <a href="<?php echo url('logout'); ?>" style="color:#8a8580;text-decoration:none;">خروج</a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- ===== Header ===== -->
        <div class="booking-header">
            <span class="badge"><i class="fas fa-star" style="margin-left:0.3rem;"></i> <?php echo h($content['booking.badge'] ?? 'Book a Session'); ?></span>
            <h1><?php echo h($content['booking.heading_line1'] ?? 'Book Your'); ?> <span><?php echo h($content['booking.heading_line2'] ?? 'Moment'); ?></span></h1>
            <p class="subtitle"><?php echo h($content['booking.subtitle'] ?? 'Capture your story with us'); ?></p>
            <div class="divider"></div>
        </div>
        
        <!-- ===== Packages with Font Awesome Icons ===== -->
        <div class="packages-grid">
            <div class="package-item">
                <div class="icon-wrap"><i class="fas fa-camera-retro"></i></div>
                <span class="name"><?php echo h($content['booking.package1_name'] ?? 'Professional Portrait'); ?></span>
                <span class="pkg-badge"><?php echo h($content['booking.package1_badge'] ?? '✨ Premium'); ?></span>
            </div>
            <div class="package-item">
                <div class="icon-wrap"><i class="fas fa-film"></i></div>
                <span class="name"><?php echo h($content['booking.package2_name'] ?? 'Brand Filming'); ?></span>
                <span class="pkg-badge"><?php echo h($content['booking.package2_badge'] ?? '🎯 Pro'); ?></span>
            </div>
            <div class="package-item">
                <div class="icon-wrap"><i class="fas fa-video"></i></div>
                <span class="name"><?php echo h($content['booking.package3_name'] ?? 'Photo & Video'); ?></span>
                <span class="pkg-badge"><?php echo h($content['booking.package3_badge'] ?? '🎭 Editorial'); ?></span>
            </div>
        </div>
        
        <!-- ===== Form ===== -->
        <div id="booking-form-container">
            <form id="booking-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> <?php echo h($content['booking.form_name'] ?? 'Full Name'); ?></label>
                    <input type="text" name="full_name" required placeholder="<?php echo h($content['booking.form_name'] ?? 'Full Name'); ?>" value="<?php echo h($user['name'] ?? ''); ?>" <?php echo !empty($user) ? 'readonly style="opacity:0.7;cursor:default;"' : ''; ?>>
                    <div class="error-message hidden" data-for="full_name"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> <?php echo h($content['booking.form_phone'] ?? 'Phone Number'); ?></label>
                    <input type="tel" name="phone" required placeholder="<?php echo h($content['booking.form_phone'] ?? 'Phone Number'); ?>" value="<?php echo h($user['phone'] ?? ''); ?>" <?php echo !empty($user) ? 'readonly style="opacity:0.7;cursor:default;"' : ''; ?>>
                    <div class="error-message hidden" data-for="phone"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-layer-group"></i> <?php echo h($content['booking.form_package'] ?? 'Select Package'); ?></label>
                    <select name="package_id" required>
                        <option value=""><?php echo h($content['booking.select_placeholder'] ?? 'Choose a package'); ?></option>
                        <option value="portrait"><?php echo h($content['booking.option_portrait'] ?? '📷 Professional Portrait'); ?></option>
                        <option value="brand"><?php echo h($content['booking.option_brand'] ?? '🎬 Brand Filming'); ?></option>
                        <option value="editorial"><?php echo h($content['booking.option_editorial'] ?? '🎥 Photo & Video'); ?></option>
                    </select>
                    <div class="error-message hidden" data-for="package_id"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> <?php echo h($content['booking.form_date'] ?? 'Select Date'); ?></label>
                    <input type="text" name="jalali_date" id="jalali_date" placeholder="<?php echo h($content['booking.form_date'] ?? 'Select Date'); ?>" required readonly>
                    <input type="hidden" name="gregorian_date" id="gregorian_date">
                    <div class="error-message hidden" data-for="date"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> <?php echo h($content['booking.form_time'] ?? 'Select Time'); ?></label>
                    <select name="time" id="time" required>
                        <option value="">Choose a time</option>
                    </select>
                    <div class="error-message hidden" data-for="time"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-pen"></i> <?php echo h($content['booking.form_note'] ?? 'Additional Notes (Optional)'); ?></label>
                    <textarea name="note" rows="3" placeholder="<?php echo h($content['booking.form_note'] ?? 'Additional Notes (Optional)'); ?>"></textarea>
                </div>
                
                <button type="submit" class="submit-btn" id="submit-btn">
                    <span><i class="fas fa-check-circle"></i> <?php echo h($content['booking.form_submit'] ?? 'Book Now'); ?></span>
                </button>
                
                <div id="form-message" class="hidden" style="text-align:center;margin-top:0.8rem;color:#f87171;font-size:0.85rem;"></div>
            </form>
        </div>
        
        <!-- ===== Success Message ===== -->
        <div id="success-message" class="hidden success-message">
            <div class="icon-wrap"><i class="fas fa-check"></i></div>
            <h2><?php echo h($content['booking.success_title'] ?? 'Booking Confirmed!'); ?></h2>
            <p><?php echo h($content['booking.success_message'] ?? 'We will contact you shortly.'); ?></p>
            <a href="<?php echo url(''); ?>"><?php echo h($content['booking.success_back'] ?? 'Back to Home'); ?></a>
        </div>
        
    </div>
</div>

<script>
$(document).ready(function() {
    'use strict';
    
    // ============================================================
    // Persian Datepicker (with text fallback for slow/no-JS scenarios)
    // ============================================================
    function populateDefaultTimes(select) {
        select.innerHTML = '<option value="">انتخاب ساعت</option>';
        for (var i = 9; i <= 20; i++) {
            var h = String(i).padStart(2, '0');
            select.add(new Option(h + ':00', h + ':00'));
            select.add(new Option(h + ':30', h + ':30'));
        }
    }

    function loadAvailableTimes(gregorianDate) {
        var timeSelect = document.getElementById('time');
        if (!timeSelect) return;
        timeSelect.innerHTML = '<option value="">در حال دریافت...</option>';
        var apiUrl = '/admin/api/available-times?date=' + gregorianDate;
        fetch(apiUrl)
            .then(function(r) {
                return r.text().then(function(text) {
                    if (!r.ok) {
                        console.error('Available times HTTP error', r.status, text);
                        throw new Error('Server error ' + r.status);
                    }
                    try {
                        return JSON.parse(text);
                    } catch (parseError) {
                        console.error('Available times non-JSON response', text);
                        throw new Error('Invalid server response');
                    }
                });
            })
            .then(function(data) {
                timeSelect.innerHTML = '<option value="">انتخاب ساعت</option>';
                if (data.success && data.times && data.times.length > 0) {
                    data.times.forEach(function(time) {
                        var opt = document.createElement('option');
                        opt.value = time;
                        opt.textContent = time;
                        timeSelect.appendChild(opt);
                    });
                } else {
                    populateDefaultTimes(timeSelect);
                }
            })
            .catch(function(error) {
                console.error('Error loading times:', error);
                populateDefaultTimes(timeSelect);
            });
    }

    try {
        if (typeof $ !== 'undefined' && $.fn && $.fn.persianDatepicker) {
            $('#jalali_date').persianDatepicker({
                format: 'YYYY/MM/DD',
                autoClose: true,
                initialValue: true,
                observer: true,
                toolbox: {
                    enabled: true,
                    todayButton: {
                        enabled: true,
                        text: { fa: 'امروز' }
                    }
                },
                onSelect: function(unix) {
                    var date = new persianDate(unix);
                    var gregorian = new Date(date.toDate());
                    var year = gregorian.getFullYear();
                    var month = String(gregorian.getMonth() + 1).padStart(2, '0');
                    var day = String(gregorian.getDate()).padStart(2, '0');
                    var gregorianDate = year + '-' + month + '-' + day;
                    $('#gregorian_date').val(gregorianDate);
                    loadAvailableTimes(gregorianDate);
                }
            });
        } else {
            throw new Error('Persian datepicker not loaded');
        }
    } catch (e) {
        console.warn('Persian datepicker failed to load; enabling text fallback:', e);
        var jalaliInput = document.getElementById('jalali_date');
        var gregorianInput = document.getElementById('gregorian_date');
        if (jalaliInput) {
            jalaliInput.removeAttribute('readonly');
            jalaliInput.setAttribute('type', 'text');
            jalaliInput.setAttribute('placeholder', 'مثال: 1403/04/25');
            jalaliInput.addEventListener('change', function() {
                var parts = this.value.split('/');
                if (parts.length === 3 && typeof persianDate !== 'undefined') {
                    try {
                        var y = parseInt(parts[0], 10);
                        var m = parseInt(parts[1], 10) - 1;
                        var d = parseInt(parts[2], 10);
                        var gd = new persianDate([y, m, d]).toDate();
                        var g = new Date(gd);
                        var gy = g.getFullYear();
                        var gm = String(g.getMonth() + 1).padStart(2, '0');
                        var gday = String(g.getDate()).padStart(2, '0');
                        var gregorianDate = gy + '-' + gm + '-' + gday;
                        if (gregorianInput) { gregorianInput.value = gregorianDate; }
                        loadAvailableTimes(gregorianDate);
                    } catch (convErr) {
                        console.error('Date conversion error:', convErr);
                    }
                }
            });
        }
    }
    
    // ============================================================
    // Set Today's Date as Default
    // ============================================================
    var today = new persianDate();
    $('#jalali_date').val(today.format('YYYY/MM/DD'));
    
    var now = new Date();
    var year = now.getFullYear();
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var day = String(now.getDate()).padStart(2, '0');
    var todayGregorian = year + '-' + month + '-' + day;
    $('#gregorian_date').val(todayGregorian);
    
    // ============================================================
    // Load Today's Available Times
    // ============================================================
    loadAvailableTimes(todayGregorian);
    
    // ============================================================
    // Simple Message Display (booking page is standalone)
    // ============================================================
    function showBookingMessage(message, type) {
        var formMessage = document.getElementById('form-message');
        if (!formMessage) return;
        formMessage.textContent = message;
        formMessage.style.color = type === 'success' ? '#4ade80' : '#f87171';
        formMessage.classList.remove('hidden');
    }

    // ============================================================
    // Form Submission
    // ============================================================
    var form = document.getElementById('booking-form');
    var successMsg = document.getElementById('success-message');
    var formContainer = document.getElementById('booking-form-container');
    var submitBtn = document.getElementById('submit-btn');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var errors = document.querySelectorAll('.error-message');
            errors.forEach(function(el) { 
                el.classList.add('hidden'); 
                el.querySelector('span').textContent = ''; 
            });
            
            var formData = new FormData(this);
            var originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span><i class="fas fa-spinner fa-spin"></i> در حال ارسال...</span>';
            submitBtn.disabled = true;
            
            fetch('/admin/api/booking-submit', { 
                method: 'POST', 
                body: formData 
            })
            .then(function(r) { 
                // Capture response text for debugging non-JSON errors
                return r.text().then(function(text) {
                    if (!r.ok) {
                        console.error('Booking submit HTTP error', r.status, text);
                        throw new Error('Server error ' + r.status + ': ' + text.substring(0, 200));
                    }
                    try {
                        return JSON.parse(text);
                    } catch (parseError) {
                        console.error('Booking submit non-JSON response', text);
                        throw new Error('Invalid server response: ' + text.substring(0, 200));
                    }
                });
            })
            .then(function(data) {
                if (data.success) {
                    if (formContainer && successMsg) {
                        formContainer.style.display = 'none';
                        successMsg.classList.remove('hidden');
                    }
                    showBookingMessage(data.message || 'رزرو با موفقیت ثبت شد!', 'success');
                } else {
                    if (data.errors) {
                        for (var key in data.errors) {
                            var el = document.querySelector('.error-message[data-for="' + key + '"]');
                            if (el) { 
                                el.querySelector('span').textContent = data.errors[key];
                                el.classList.remove('hidden'); 
                            }
                        }
                    }
                    if (data.message) { showBookingMessage(data.message, 'error'); }
                }
            })
            .catch(function(error) { 
                console.error('Submit error:', error);
                showBookingMessage('<?php echo h($content['booking.error_message'] ?? 'خطایی رخ داد. لطفاً دوباره تلاش کنید.'); ?>', 'error'); 
            })
            .finally(function() {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>

</body>
</html>