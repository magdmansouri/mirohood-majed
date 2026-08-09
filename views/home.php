<?php
// views/home.php - صفحه اصلی مینیمال و حرفه‌ای
$content = $content ?? [];
$packages = $packages ?? [];
$gallery = $gallery ?? [];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Mirohood — Photography & Filming</title>
    
    <!-- ===== Fonts ===== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap" as="style" onload="this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
    
    <!-- ===== Font Awesome ===== -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
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
    
    <style>
        /* ============================================================
           RESET & BASE
           ============================================================ */
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color:transparent; }
        html { scroll-behavior:smooth; -webkit-text-size-adjust:100%; }
        body { 
            font-family:'Inter', sans-serif; 
            background:#0a0908; 
            color:#f4f1ea; 
            min-height:100vh; 
            min-height:100dvh;
            overflow-x:hidden;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
        }
        
        /* ===== Scrollbar ===== */
        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:#0a0908; }
        ::-webkit-scrollbar-thumb { background:#c8a862; border-radius:3px; }
        ::-webkit-scrollbar-thumb:hover { background:#a8893a; }
        
        /* ===== Selection ===== */
        ::selection { background:#c8a862; color:#0a0908; }
        
        /* ===== Keyboard focus (accessibility) ===== */
        a:focus-visible, button:focus-visible {
            outline:2px solid #c8a862;
            outline-offset:3px;
            border-radius:4px;
        }
        
        /* ===== Respect reduced-motion preference ===== */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration:0.01ms !important;
                animation-iteration-count:1 !important;
                transition-duration:0.01ms !important;
                scroll-behavior:auto !important;
            }
        }
        
        /* ===== Disable "stuck hover" effects on touch devices ===== */
        @media (hover: none) {
            .pkg-card:hover,
            .about-card:hover,
            .back-to-top:hover,
            .btn-primary:hover,
            .btn-outline:hover {
                transform:none;
                box-shadow:none;
            }
            .gal-item:hover img { transform:none; }
        }
        
        /* ============================================================
           BUTTONS
           ============================================================ */
        .btn-primary {
            position:relative;
            overflow:hidden;
            display:inline-block;
            padding:0.9rem 2.8rem;
            background:linear-gradient(135deg,#c8a862,#a8893a);
            border:none;
            border-radius:9999px;
            color:#0a0908;
            font-family:'Inter',sans-serif;
            font-size:0.8rem;
            font-weight:700;
            letter-spacing:0.05em;
            text-decoration:none;
            cursor:pointer;
            transition:all 0.4s cubic-bezier(0.25,0.46,0.45,0.94);
            box-shadow:0 4px 30px rgba(200,168,98,0.1);
        }
        .btn-primary::after {
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(135deg,rgba(255,255,255,0.15),transparent);
            transform:translateX(-100%);
            transition:transform 0.6s ease;
        }
        .btn-primary:hover {
            transform:translateY(-2px) scale(1.01);
            box-shadow:0 8px 40px rgba(200,168,98,0.2);
        }
        .btn-primary:hover::after { transform:translateX(0); }
        .btn-primary:active { transform:scale(0.98); }
        
        .btn-outline {
            display:inline-block;
            padding:0.9rem 2.8rem;
            background:rgba(255,255,255,0.02);
            border:1px solid rgba(200,168,98,0.12);
            border-radius:9999px;
            color:#f4f1ea;
            font-family:'Inter',sans-serif;
            font-size:0.8rem;
            font-weight:600;
            letter-spacing:0.05em;
            text-decoration:none;
            cursor:pointer;
            transition:all 0.4s ease;
        }
        .btn-outline:hover {
            background:rgba(200,168,98,0.06);
            border-color:rgba(200,168,98,0.2);
            transform:translateY(-2px);
        }
        
        /* ============================================================
           PACKAGE CARD
           ============================================================ */
        .pkg-card {
            background:rgba(255,255,255,0.02);
            border:1px solid rgba(200,168,98,0.06);
            border-radius:1rem;
            padding:2rem 1.8rem;
            text-align:center;
            transition:all 0.5s cubic-bezier(0.25,0.46,0.45,0.94);
            position:relative;
            overflow:hidden;
        }
        .pkg-card::before {
            content:'';
            position:absolute;
            inset:0;
            background:radial-gradient(ellipse at center,rgba(200,168,98,0.03),transparent 70%);
            opacity:0;
            transition:opacity 0.5s ease;
        }
        .pkg-card:hover {
            transform:translateY(-6px);
            border-color:rgba(200,168,98,0.15);
            background:rgba(255,255,255,0.04);
            box-shadow:0 20px 60px rgba(0,0,0,0.3);
        }
        .pkg-card:hover::before { opacity:1; }
        .pkg-card .pkg-icon {
            font-size:2.2rem;
            margin-bottom:0.8rem;
            display:block;
            transition:transform 0.4s ease;
        }
        .pkg-card:hover .pkg-icon { transform:scale(1.1); }
        .pkg-card .pkg-btn {
            display:inline-block;
            padding:0.5rem 1.8rem;
            border:1px solid rgba(200,168,98,0.12);
            border-radius:9999px;
            color:#f4f1ea;
            font-size:0.75rem;
            font-weight:500;
            transition:all 0.4s ease;
            text-decoration:none;
        }
        .pkg-card:hover .pkg-btn {
            background:rgba(200,168,98,0.08);
            border-color:rgba(200,168,98,0.2);
            color:#c8a862;
        }
        .pkg-card:hover .pkg-btn i { transform:translateX(4px); }
        .pkg-card .pkg-btn i { transition:transform 0.3s ease; }
        
        /* ============================================================
           TEXT LINK WITH ARROW (View All / Learn More)
           ============================================================ */
        .link-arrow {
            display:inline-flex;
            align-items:center;
            gap:0.35rem;
        }
        .link-arrow i { transition:transform 0.3s ease; }
        @media (hover: hover) {
            .link-arrow:hover { color:#c8a862 !important; }
            .link-arrow:hover i { transform:translateX(4px); }
        }
        
        /* ============================================================
           GALLERY ITEM
           ============================================================ */
        .gal-item {
            position:relative;
            overflow:hidden;
            border-radius:0.8rem;
            aspect-ratio:3/4;
            background:#151413;
        }
        .gal-item picture { display:block; width:100%; height:100%; }
        .gal-item img {
            width:100%;
            height:100%;
            object-fit:cover;
            transition:transform 0.7s cubic-bezier(0.25,0.46,0.45,0.94);
        }
        .gal-item:hover img { transform:scale(1.08); }
        .gal-item .gal-overlay {
            position:absolute;
            inset:0;
            background:linear-gradient(180deg,transparent 40%,rgba(10,9,8,0.6));
            opacity:0;
            transition:opacity 0.5s ease;
        }
        .gal-item:hover .gal-overlay { opacity:1; }
        
        /* ============================================================
           ABOUT CARD
           ============================================================ */
        .about-card {
            border-radius:0.8rem;
            aspect-ratio:1;
            background:rgba(255,255,255,0.02);
            border:1px solid rgba(200,168,98,0.04);
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            padding:1.5rem;
            text-align:center;
            transition:all 0.4s ease;
        }
        .about-card:hover {
            transform:translateY(-4px);
            border-color:rgba(200,168,98,0.08);
            background:rgba(255,255,255,0.04);
        }
        
        /* ============================================================
           BACK TO TOP
           ============================================================ */
        .back-to-top {
            position:fixed;
            bottom:2rem;
            right:2rem;
            z-index:999;
            width:3.2rem;
            height:3.2rem;
            border-radius:50%;
            background:rgba(200,168,98,0.06);
            border:1px solid rgba(200,168,98,0.08);
            color:#c8a862;
            cursor:pointer;
            backdrop-filter:blur(12px);
            transition:all 0.4s cubic-bezier(0.25,0.46,0.45,0.94);
            opacity:0;
            transform:translateY(20px);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.2rem;
        }
        .back-to-top.visible {
            opacity:1;
            transform:translateY(0);
        }
        .back-to-top:hover {
            background:rgba(200,168,98,0.12);
            border-color:rgba(200,168,98,0.2);
            transform:translateY(-4px);
            box-shadow:0 8px 30px rgba(200,168,98,0.1);
        }
        
        /* ============================================================
           GRIDS (packages & gallery)
           ============================================================ */
        .packages-grid {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
            gap:1.5rem;
        }
        .gallery-grid {
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
            gap:1rem;
        }
        
        /* ============================================================
           HERO SECTION
           ============================================================ */
        .hero-section {
            position:relative;
            min-height:100vh;
            min-height:100dvh;
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            background:#0a0908;
            padding:2rem;
        }
        .hero-inner { position:relative; z-index:2; text-align:center; max-width:800px; padding:2rem; }
        
        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 768px) {
            .hero-title { font-size:clamp(2.2rem,8vw,3.5rem) !important; }
            .about-grid { grid-template-columns:1fr !important; gap:2rem !important; }
            .about-grid .about-cards { grid-template-columns:1fr 1fr !important; }
            .btn-primary, .btn-outline { padding:0.7rem 1.8rem; font-size:0.7rem; }
            .back-to-top { width:2.8rem; height:2.8rem; font-size:1rem; bottom:1.2rem; right:1.2rem; }
            .packages-grid { gap:1.2rem; }
        }
        @media (max-width: 600px) {
            .hero-section { padding:1.2rem; }
            .hero-inner { padding:1rem 0.5rem; }
            .gallery-grid { grid-template-columns:repeat(2,1fr); gap:0.6rem; }
        }
        @media (max-width: 480px) {
            .about-grid .about-cards { grid-template-columns:1fr !important; }
            .pkg-card { padding:1.5rem 1rem; }
            .packages-grid { grid-template-columns:1fr; }
        }
        @media (max-width: 360px) {
            .gallery-grid { gap:0.4rem; }
        }
        
        /* Keep the back-to-top button clear of the home-indicator area on notch phones */
        .back-to-top { bottom:calc(2rem + env(safe-area-inset-bottom)); }
        @media (max-width: 768px) {
            .back-to-top { bottom:calc(1.2rem + env(safe-area-inset-bottom)); }
        }
        
        /* Portrait hero video: cover on mobile, contain (stay vertical) on desktop */
        @media (min-width: 769px) {
            #heroVideo {
                width: auto !important;
                height: 100% !important;
                max-width: 100% !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                object-fit: contain !important;
            }
        }
    </style>
</head>
<body>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="hero-section">
    
    <!-- Video Background -->
    <div style="position:absolute;inset:0;z-index:0;overflow:hidden;">
        <?php $heroVideoSrc = $content['home.hero_video'] ?? 'https://mirohood.ir/assets/images/hero.mp4'; ?>
        <?php $heroWebmSrc = $content['home.hero_webm'] ?? '/assets/images/hero.webm'; ?>
        <?php $heroPoster = $content['home.hero_poster'] ?? '/assets/images/og-default.jpg'; ?>
        <video id="heroVideo" autoplay muted loop playsinline preload="metadata" poster="<?php echo h($heroPoster); ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;opacity:0.3;">
            <?php if (!empty($heroWebmSrc)): ?><source src="<?php echo h($heroWebmSrc); ?>" type="video/webm"><?php endif; ?>
            <source src="<?php echo h($heroVideoSrc); ?>" type="video/mp4">
        </video>
        <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(10,9,8,0.2) 0%,rgba(10,9,8,0.6) 50%,rgba(10,9,8,0.95) 100%);"></div>
    </div>
    
    <!-- Hero Content -->
    <div class="hero-inner">
        
        <div>
            <span style="font-size:0.7rem;letter-spacing:0.2em;text-transform:uppercase;color:#c8a862;font-weight:400;border:1px solid rgba(200,168,98,0.1);padding:0.3rem 1.2rem;border-radius:9999px;display:inline-block;">
                <?php echo h($content['home.eyebrow'] ?? 'Based on Earth'); ?>
            </span>
        </div>
        
        <h1 class="hero-title" style="font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(2.8rem,9vw,5.5rem);font-weight:300;line-height:1.05;color:#f4f1ea;margin:1.2rem 0;letter-spacing:-0.02em;">
            <?php echo h($content['home.title_line1'] ?? 'Every Frame'); ?><br>
            <span style="color:#c8a862;"><?php echo h($content['home.title_line2'] ?? 'A Timeless Moment'); ?></span>
        </h1>
        
        <p style="font-size:1.05rem;color:#8a8580;max-width:500px;margin:0 auto 2.5rem;line-height:1.8;font-weight:300;">
            <?php echo h($content['home.subtitle'] ?? 'Portrait photography, brand filming & cinematic content.'); ?>
        </p>
        
        <div>
            <a href="<?php echo url('booking'); ?>" class="btn-primary">
                <i class="fas fa-calendar-check" style="margin-left:0.5rem;"></i>
                <?php echo h($content['home.cta_button'] ?? 'Book a Session'); ?>
            </a>
        </div>
        
    </div>
</section>

<!-- ============================================================
     PACKAGES SECTION
     ============================================================ -->
<section style="max-width:1200px;margin:0 auto;padding:5rem 1.5rem;">
    
    <div style="text-align:center;margin-bottom:3.5rem;">
        <span style="font-size:0.7rem;letter-spacing:0.2em;text-transform:uppercase;color:#c8a862;"><?php echo h($content['home.packages_eyebrow'] ?? 'Packages'); ?></span>
        <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(2rem,4vw,3.2rem);font-weight:300;color:#f4f1ea;margin-top:0.3rem;">
            <?php echo h($content['home.packages_title'] ?? 'Choose Your Experience'); ?>
        </h2>
        <div style="width:3rem;height:2px;background:#c8a862;margin:0.8rem auto 0;"></div>
    </div>
    
    <div class="packages-grid">
        <?php foreach ($packages as $index => $pkg): ?>
            <div class="pkg-card">
                <span class="pkg-icon">
                    <?php 
                        $icons = ['portrait' => '✦', 'brand' => '◆', 'editorial' => '◈']; 
                        echo $icons[$pkg['id']] ?? '✧';
                    ?>
                </span>
                
                <span style="font-size:0.6rem;letter-spacing:0.15em;text-transform:uppercase;color:#c8a862;"><?php echo h($pkg['eyebrow']); ?></span>
                
                <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.4rem;font-weight:400;color:#f4f1ea;margin:0.4rem 0 0.2rem;">
                    <?php echo h($pkg['titleEn']); ?>
                </h3>
                
                <p style="font-size:0.8rem;color:#8a8580;margin-bottom:0.8rem;"><?php echo h($pkg['durationEn']); ?></p>
                
                <p style="color:#8a8580;font-size:0.85rem;line-height:1.7;margin-bottom:1.5rem;">
                    <?php echo h($pkg['descriptionEn']); ?>
                </p>
                
                <a href="<?php echo url('booking?package=' . $pkg['id']); ?>" class="pkg-btn">
                    Book Now <i class="fas fa-arrow-right" style="font-size:0.6rem;margin-right:0.3rem;"></i>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ============================================================
     GALLERY SECTION
     ============================================================ -->
<section style="max-width:1200px;margin:0 auto;padding:3rem 1.5rem 5rem;">
    
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <span style="font-size:0.7rem;letter-spacing:0.2em;text-transform:uppercase;color:#c8a862;"><?php echo h($content['home.gallery_title'] ?? 'Recent Work'); ?></span>
            <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(1.8rem,3vw,2.8rem);font-weight:300;color:#f4f1ea;margin-top:0.2rem;"><?php echo h($content['home.gallery_heading'] ?? 'Featured Projects'); ?></h2>
        </div>
        <a href="<?php echo url('gallery'); ?>" class="link-arrow" style="color:#8a8580;font-size:0.85rem;text-decoration:none;">
            <?php echo h($content['home.gallery_view_all'] ?? 'View All'); ?> <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
        </a>
    </div>
    
    <?php if (!empty($gallery)): ?>
        <div class="gallery-grid">
            <?php foreach ($gallery as $index => $item): ?>
                <div class="gal-item">
                    <?php echo responsive_image($item, 'medium', $item['alt'], '', true); ?>
                    <div class="gal-overlay"></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color:#8a8580;text-align:center;padding:3rem 0;"><?php echo h($content['home.gallery_empty'] ?? 'No images available yet.'); ?></p>
    <?php endif; ?>
    
</section>

<!-- ============================================================
     ABOUT TEASER
     ============================================================ -->
<section style="max-width:1200px;margin:0 auto;padding:4rem 1.5rem 5rem;border-top:1px solid rgba(200,168,98,0.04);">
    <div class="about-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:center;">
        <div>
            <span style="font-size:0.7rem;letter-spacing:0.2em;text-transform:uppercase;color:#c8a862;"><?php echo h($content['home.about_eyebrow'] ?? 'About'); ?></span>
            <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(2rem,3.5vw,3rem);font-weight:300;color:#f4f1ea;margin-top:0.3rem;line-height:1.1;">
                <?php echo h($content['home.about_title_line1'] ?? 'Based on Earth.'); ?><br>
                <span style="color:#c8a862;"><?php echo h($content['home.about_title_line2'] ?? 'Rooted in Light.'); ?></span>
            </h2>
            <p style="color:#8a8580;font-size:0.95rem;line-height:1.9;margin-top:1.2rem;">
                <?php echo h($content['home.about_text'] ?? 'Mirohood has been working out of Ahvaz since 2021. Every session is built around light and framing that gets close to who you actually are, instead of repeating the same familiar templates.'); ?>
            </p>
            <a href="<?php echo url('about'); ?>" class="btn-outline link-arrow" style="margin-top:1.5rem;">
                <?php echo h($content['home.about_button'] ?? 'Learn More'); ?> <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
            </a>
        </div>
        
        <div class="about-cards" style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;">
            <div class="about-card">
                <span style="font-size:1.8rem;color:#c8a862;margin-bottom:0.3rem;">✦</span>
                <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.2rem;font-weight:300;color:#f4f1ea;">Authentic</p>
            </div>
            <div class="about-card">
                <span style="font-size:1.8rem;color:#c8a862;margin-bottom:0.3rem;">◆</span>
                <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.2rem;font-weight:300;color:#f4f1ea;">Cinematic</p>
            </div>
            <div class="about-card">
                <span style="font-size:1.8rem;color:#c8a862;margin-bottom:0.3rem;">◈</span>
                <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.2rem;font-weight:300;color:#f4f1ea;">Personal</p>
            </div>
            <div class="about-card">
                <span style="font-size:1.8rem;color:#c8a862;margin-bottom:0.3rem;">✧</span>
                <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.2rem;font-weight:300;color:#f4f1ea;">Premium</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     CTA SECTION
     ============================================================ -->
<section style="max-width:1200px;margin:0 auto;padding:2rem 1.5rem 5rem;">
    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(200,168,98,0.05);border-radius:1.5rem;padding:3.5rem 2rem;text-align:center;">
        <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(1.8rem,3vw,2.8rem);font-weight:300;color:#f4f1ea;">
            <?php echo h($content['home.cta_title_line1'] ?? 'Ready to Capture'); ?><br>
            <span style="color:#c8a862;"><?php echo h($content['home.cta_title_line2'] ?? 'Your Moment?'); ?></span>
        </h2>
        <p style="color:#8a8580;font-size:0.95rem;max-width:450px;margin:0.5rem auto 1.8rem;line-height:1.7;">
            <?php echo h($content['home.cta_text'] ?? 'Let\'s create something timeless together.'); ?>
        </p>
        <a href="<?php echo url('booking'); ?>" class="btn-primary">
            <i class="fas fa-calendar-check" style="margin-left:0.5rem;"></i>
            <?php echo h($content['home.cta_button2'] ?? 'Book Now'); ?>
        </a>
    </div>
</section>

<!-- ============================================================
     BACK TO TOP
     ============================================================ -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ===== Hero Video =====
    var video = document.getElementById('heroVideo');
    if (video) {
        video.play().catch(function() {});
    }
    
    // ===== Back to Top =====
    var backBtn = document.getElementById('backToTop');
    window.addEventListener('scroll', function() {
        backBtn.classList.toggle('visible', window.scrollY > 500);
    });
    backBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    // ===== Smooth Scroll =====
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
});
</script>

</body>
</html>