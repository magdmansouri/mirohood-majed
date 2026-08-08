<?php
// views/majed-mansouri.php - Standalone, indexable resume landing page
set_security_headers();
$profileUrl = SITE_URL . '/majedmansouri';
$profileImage = SITE_URL . '/assets/images/majed-mansouri.jpg';
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => 'Majed Mansouri',
    'url' => $profileUrl,
    'image' => $profileImage,
    'email' => 'mailto:magd.mansouri81@gmail.com',
    'jobTitle' => 'Electrical & Electronics Engineer, Web Designer & Developer',
    'description' => 'Majed Mansouri is an Electrical and Electronics Engineer, Web Designer and Web Developer. Creator of the Mirohood photography studio website.',
    'sameAs' => [
        'https://t.me/Manssuri',
        'https://www.instagram.com/themajead/'
    ],
    'knowsAbout' => [
        'Electrical Engineering',
        'Electronics',
        'Web Design',
        'Web Development',
        'PHP',
        'Responsive Web Design',
        'PWA'
    ]
];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Majed Mansouri | مهندس برق الکترونیک و توسعه‌دهنده وب</title>
    <meta name="description" content="رزومه Majed Mansouri — مهندس برق الکترونیک، طراح و توسعه‌دهنده وب. طراح و توسعه‌دهنده پروژه وب‌سایت Mirohood.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo h($profileUrl); ?>">
    <meta property="og:type" content="profile">
    <meta property="og:title" content="Majed Mansouri | Electrical & Electronics Engineer · Web Developer">
    <meta property="og:description" content="مهندس برق الکترونیک، طراح و توسعه‌دهنده وب — خالق وب‌سایت Mirohood.">
    <meta property="og:url" content="<?php echo h($profileUrl); ?>">
    <meta property="og:image" content="<?php echo h($profileImage); ?>">
    <meta property="og:site_name" content="Majed Mansouri">
    <meta property="og:locale" content="fa_IR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Majed Mansouri | Web Designer & Developer">
    <meta name="twitter:description" content="مهندس برق الکترونیک، طراح و توسعه‌دهنده وب.">
    <meta name="twitter:image" content="<?php echo h($profileImage); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&family=Vazirmatn:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&family=Vazirmatn:wght@300;400;500;600;700&display=swap"></noscript>
    <link rel="icon" href="<?php echo asset('images/admin-icon-192.png'); ?>">
    <meta name="theme-color" content="#11110f">
    <script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>
    <style>
        :root {
            --ink: #11110f;
            --ink-soft: #1a1916;
            --paper: #f2eee5;
            --paper-muted: #c1bcb0;
            --gold: #c5a365;
            --gold-light: #e7d0a1;
            --line: rgba(242,238,229,0.15);
            --line-subtle: rgba(242,238,229,0.09);
            --radius-xl: 30px;
            --radius-lg: 20px;
            --mono: 'DM Mono', ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            --latin: 'Manrope', Arial, sans-serif;
            --fa: 'Vazirmatn', Tahoma, sans-serif;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; background: var(--ink); }
        body {
            margin: 0;
            min-width: 320px;
            color: var(--paper);
            background:
                radial-gradient(circle at 6% 6%, rgba(197,163,101,0.14), transparent 24rem),
                radial-gradient(circle at 90% 40%, rgba(197,163,101,0.07), transparent 28rem),
                var(--ink);
            font-family: var(--fa);
            line-height: 1.8;
            overflow-x: hidden;
        }
        .site-atlas {
            position: fixed;
            z-index: 0;
            inset: 0;
            pointer-events: none;
            opacity: .18;
            background-image: linear-gradient(90deg, rgba(17,17,15,.94) 0%, rgba(17,17,15,.24) 48%, rgba(17,17,15,.92) 100%), url('<?php echo asset('images/majed-digital-atlas.jpg'); ?>');
            background-image: linear-gradient(90deg, rgba(17,17,15,.94) 0%, rgba(17,17,15,.24) 48%, rgba(17,17,15,.92) 100%), image-set(url('<?php echo asset('images/majed-digital-atlas.webp'); ?>') type('image/webp'), url('<?php echo asset('images/majed-digital-atlas.jpg'); ?>') type('image/jpeg'));
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center top;
            mask-image: linear-gradient(to bottom, black 0%, rgba(0,0,0,.88) 58%, transparent 100%);
            -webkit-mask-image: linear-gradient(to bottom, black 0%, rgba(0,0,0,.88) 58%, transparent 100%);
        }
        a { color: inherit; }
        img { display: block; max-width: 100%; }
        ::selection { color: var(--ink); background: var(--gold-light); }
        .skip-link { position: fixed; top: -100px; right: 1rem; z-index: 50; background: var(--gold); color: var(--ink); padding: .65rem 1rem; border-radius: 0 0 10px 10px; font-weight: 700; text-decoration: none; }
        .skip-link:focus { top: 0; }
        .shell { position: relative; z-index: 1; width: min(1180px, calc(100% - 3rem)); margin: 0 auto; }
        .rule { height: 1px; background: linear-gradient(90deg, transparent, var(--line) 10%, var(--line) 90%, transparent); }

        .topbar { min-height: 96px; display: flex; align-items: center; justify-content: space-between; gap: 1rem; direction: ltr; }
        .monogram { display: inline-flex; align-items: center; gap: .75rem; text-decoration: none; font-family: var(--latin); font-weight: 800; letter-spacing: -.05em; font-size: 1.1rem; }
        .monogram-mark { display: grid; width: 34px; height: 34px; place-items: center; border: 1px solid rgba(197,163,101,.65); border-radius: 50%; color: var(--gold-light); font-family: Georgia, serif; font-size: 1.1rem; }
        .page-tag { margin: 0; color: var(--paper-muted); font: 400 .69rem/1 var(--mono); letter-spacing: .09em; text-transform: uppercase; }

        .hero { display: grid; grid-template-columns: minmax(270px, .82fr) minmax(0, 1.18fr); align-items: stretch; gap: clamp(2rem, 6vw, 6.5rem); padding: 3.2rem 0 6.5rem; }
        .hero-copy { grid-column: 2; display: flex; flex-direction: column; justify-content: center; padding: 1.5rem 0; }
        .hero-visual {
            grid-column: 1;
            grid-row: 1;
            position: relative;
            min-height: 540px;
            border: 1px solid rgba(197,163,101,.35);
            border-radius: var(--radius-xl);
            overflow: hidden;
            background: #24211e;
            box-shadow: 0 30px 70px rgba(0,0,0,.45), 0 0 0 7px rgba(197,163,101,.035);
            isolation: isolate;
        }
        .hero-visual::before {
            content: '';
            position: absolute;
            z-index: 2;
            inset: 0;
            pointer-events: none;
            opacity: .35;
            mix-blend-mode: screen;
            background-image: linear-gradient(145deg, rgba(197,163,101,.22), transparent 34%), url('<?php echo asset('images/majed-digital-atlas.jpg'); ?>');
            background-image: linear-gradient(145deg, rgba(197,163,101,.22), transparent 34%), image-set(url('<?php echo asset('images/majed-digital-atlas.webp'); ?>') type('image/webp'), url('<?php echo asset('images/majed-digital-atlas.jpg'); ?>') type('image/jpeg'));
            background-size: cover;
            background-position: center;
        }
        .hero-visual::after { content: ''; position: absolute; inset: 0; z-index: 3; background: linear-gradient(180deg, rgba(10,10,9,.03), rgba(10,10,9,.10) 44%, rgba(10,10,9,.82)); pointer-events: none; }
        .hero-visual picture, .hero-visual img { position: relative; z-index: 1; width: 100%; height: 100%; }
        .hero-visual img { object-fit: cover; object-position: 58% center; filter: saturate(.82) contrast(1.04); }
        .photo-caption { position: absolute; z-index: 4; left: 1.15rem; bottom: 1rem; direction: ltr; margin: 0; color: rgba(242,238,229,.72); font: 400 .62rem/1.5 var(--mono); letter-spacing: .08em; }
        .visual-chip { position: absolute; z-index: 4; display: inline-flex; align-items: center; gap: .45rem; padding: .38rem .6rem; border: 1px solid rgba(242,238,229,.22); border-radius: 999px; color: var(--paper); background: rgba(12,12,11,.48); box-shadow: 0 8px 24px rgba(0,0,0,.16); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); font: 400 .55rem/1 var(--mono); letter-spacing: .08em; direction: ltr; }
        .visual-chip::before { content: ''; width: .35rem; height: .35rem; border-radius: 50%; background: var(--gold); box-shadow: 0 0 12px var(--gold); }
        .visual-chip.top { top: 1rem; right: 1rem; }
        .visual-chip.bottom { right: 1rem; bottom: 1rem; color: var(--gold-light); }
        .visual-chip.bottom::before { background: #9ccf86; box-shadow: 0 0 10px #9ccf86; }
        .availability { display: inline-flex; align-items: center; align-self: flex-start; gap: .55rem; padding: .35rem .72rem; border: 1px solid rgba(197,163,101,.45); border-radius: 999px; color: var(--gold-light); background: rgba(197,163,101,.07); font: 500 .67rem/1 var(--mono); letter-spacing: .06em; direction: ltr; text-transform: uppercase; }
        .availability::before { content: ''; width: .46rem; height: .46rem; border-radius: 50%; background: #9ccf86; box-shadow: 0 0 0 4px rgba(156,207,134,.12); }
        .hero-kicker { margin: 1.9rem 0 .75rem; color: var(--gold); font: 500 .72rem/1.5 var(--mono); letter-spacing: .12em; direction: ltr; text-transform: uppercase; }
        h1 { margin: 0; color: var(--paper); font: 700 clamp(3.3rem, 8vw, 7rem)/.91 var(--latin); letter-spacing: -.075em; direction: ltr; }
        .role { max-width: 620px; margin: 1.45rem 0 0; color: var(--gold-light); font: 600 clamp(1rem, 2vw, 1.3rem)/1.7 var(--fa); }
        .intro { max-width: 610px; margin: 1rem 0 0; color: var(--paper-muted); font-size: .95rem; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: .7rem; margin-top: 2rem; }
        .button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; gap: .5rem; padding: .6rem 1.05rem; border: 1px solid var(--line); border-radius: 999px; color: var(--paper); background: rgba(255,255,255,.025); font: 500 .76rem/1 var(--fa); text-decoration: none; transition: transform .2s ease, background .2s ease, border-color .2s ease; }
        .button:hover { transform: translateY(-2px); border-color: rgba(197,163,101,.7); background: rgba(197,163,101,.12); }
        .button.primary { color: var(--ink); border-color: var(--gold); background: var(--gold); font-weight: 700; }
        .button.primary:hover { background: var(--gold-light); }
        .button svg { width: 16px; height: 16px; fill: currentColor; flex: 0 0 auto; }
        .quick-facts { display: flex; flex-wrap: wrap; gap: 1.2rem 1.8rem; margin-top: 2.4rem; padding-top: 1.25rem; border-top: 1px solid var(--line); color: var(--paper-muted); font: 400 .68rem/1.4 var(--mono); direction: ltr; }
        .quick-facts strong { display: block; margin-bottom: .2rem; color: var(--paper); font-weight: 500; }

        section { padding: clamp(4.5rem, 9vw, 8rem) 0; }
        .section-grid { display: grid; grid-template-columns: 180px minmax(0,1fr); gap: 2.5rem; }
        .section-index { color: var(--gold); font: 500 .7rem/1.2 var(--mono); letter-spacing: .13em; direction: ltr; }
        .section-label { display: block; margin-top: .5rem; color: var(--paper-muted); font-size: .76rem; }
        .section-title { max-width: 820px; margin: -.35rem 0 1.3rem; font: 600 clamp(1.75rem, 4vw, 3.1rem)/1.2 var(--fa); letter-spacing: -.035em; }
        .section-lead { max-width: 790px; margin: 0; color: var(--paper-muted); font-size: 1rem; }
        .skills { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 2rem; }
        .skill { padding: .52rem .82rem; border: 1px solid var(--line); border-radius: 999px; color: #ded8cc; background: rgba(255,255,255,.025); font: 400 .73rem/1.2 var(--fa); }
        .skill.emphasis { border-color: rgba(197,163,101,.48); color: var(--gold-light); background: rgba(197,163,101,.08); }

        .project { position: relative; display: grid; grid-template-columns: minmax(0,1fr) minmax(330px,.85fr); overflow: hidden; border: 1px solid rgba(197,163,101,.28); border-radius: var(--radius-xl); background: linear-gradient(135deg, rgba(197,163,101,.08), transparent 34%), #171613; box-shadow: 0 30px 80px rgba(0,0,0,.22); }
        .project::before { content: ''; position: absolute; z-index: 0; top: 0; right: 0; width: 36%; height: 1px; background: linear-gradient(90deg, transparent, var(--gold)); }
        .project-copy { position: relative; z-index: 1; padding: clamp(1.8rem, 4vw, 3.75rem); }
        .project-eyebrow { margin: 0 0 .8rem; color: var(--gold); font: 500 .69rem/1 var(--mono); letter-spacing: .1em; direction: ltr; text-transform: uppercase; }
        .project-name { margin: 0; direction: ltr; font: 700 clamp(2.3rem, 5vw, 4.7rem)/.95 var(--latin); letter-spacing: -.07em; }
        .project-kind { margin: .9rem 0 1.15rem; color: var(--gold-light); font: 500 .78rem/1.5 var(--mono); direction: ltr; }
        .project-copy > p:not(.project-eyebrow):not(.project-kind) { max-width: 570px; margin: 0; color: var(--paper-muted); font-size: .92rem; }
        .project-points { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: .8rem; margin: 1.65rem 0 2rem; padding: 0; list-style: none; }
        .project-points li { display: flex; align-items: center; gap: .5rem; color: #d6d0c4; font-size: .75rem; }
        .project-points li::before { content: '↗'; color: var(--gold); font: 500 1rem/1 var(--mono); direction: ltr; }
        .live-link { display: inline-flex; align-items: center; gap: .7rem; color: var(--gold-light); font: 600 .78rem/1 var(--latin); direction: ltr; text-decoration: none; }
        .live-link::after { content: '↗'; transition: transform .2s ease; }
        .live-link:hover::after { transform: translate(3px,-3px); }
        .project-art { position: relative; min-height: 440px; overflow: hidden; background: #0c0c0b; }
        .project-art::before { content: ''; position: absolute; z-index: 0; inset: 0; background: linear-gradient(110deg, rgba(10,10,9,.16), rgba(10,10,9,.8)), url('<?php echo asset('images/og-default.jpg'); ?>') center/cover no-repeat; filter: saturate(.8); }
        .project-art::after { content: ''; position: absolute; z-index: 1; inset: 0; opacity: .36; mix-blend-mode: screen; background-image: linear-gradient(0deg, rgba(10,10,9,.4), transparent), url('<?php echo asset('images/majed-digital-atlas.jpg'); ?>'); background-image: linear-gradient(0deg, rgba(10,10,9,.4), transparent), image-set(url('<?php echo asset('images/majed-digital-atlas.webp'); ?>') type('image/webp'), url('<?php echo asset('images/majed-digital-atlas.jpg'); ?>') type('image/jpeg')); background-size: cover; background-position: center; pointer-events: none; }
        .browser { position: absolute; inset: auto 1.15rem 1.15rem 1.15rem; z-index: 2; overflow: hidden; border: 1px solid rgba(242,238,229,.18); border-radius: 13px; background: rgba(19,18,16,.88); box-shadow: 0 22px 50px rgba(0,0,0,.45); direction: ltr; }
        .browser-top { display: flex; align-items: center; gap: .32rem; height: 29px; padding: 0 .65rem; border-bottom: 1px solid rgba(242,238,229,.09); }
        .browser-top i { display: block; width: .38rem; height: .38rem; border-radius: 50%; background: rgba(242,238,229,.4); }
        .browser-url { overflow: hidden; margin-left: .45rem; color: rgba(242,238,229,.57); font: 400 .52rem/1 var(--mono); white-space: nowrap; text-overflow: ellipsis; }
        .browser-screen { min-height: 175px; padding: 1.25rem; background: linear-gradient(145deg, #171510, #090908); }
        .browser-brand { color: var(--gold-light); font: 600 1.8rem/1 var(--latin); letter-spacing: -.07em; }
        .browser-subtitle { margin-top: .55rem; color: rgba(242,238,229,.72); font: 400 .54rem/1.6 var(--mono); letter-spacing: .05em; text-transform: uppercase; }
        .browser-line { height: 1px; width: 72%; margin-top: 1.25rem; background: rgba(197,163,101,.58); }
        .browser-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: .35rem; margin-top: 1rem; }
        .browser-grid span { aspect-ratio: 1; border-radius: 3px; background: linear-gradient(135deg, rgba(197,163,101,.6), rgba(242,238,229,.08)); }

        .routes-intro { margin-bottom: 1.35rem; color: var(--paper-muted); font-size: .85rem; }
        .routes { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: .85rem; direction: ltr; }
        .route-card { position: relative; min-height: 183px; overflow: hidden; padding: .95rem; border: 1px solid var(--line); border-radius: 15px; background: #161512; color: var(--paper); text-decoration: none; transition: transform .22s ease, border-color .22s ease; }
        .route-card:hover { transform: translateY(-4px); border-color: rgba(197,163,101,.65); }
        .route-browser { display: flex; gap: .18rem; padding-bottom: .65rem; }
        .route-browser span { width: .25rem; height: .25rem; border-radius: 50%; background: rgba(242,238,229,.45); }
        .route-name { position: absolute; right: .95rem; bottom: .8rem; z-index: 2; color: var(--paper); font: 600 .72rem/1 var(--fa); direction: rtl; }
        .route-name em { display: block; margin-top: .25rem; color: var(--gold); font: 400 .52rem/1 var(--mono); direction: ltr; letter-spacing: .05em; font-style: normal; }
        .route-home::before { content: 'Mirohood'; position: absolute; inset: 2.9rem .8rem auto; color: var(--gold-light); font: 600 1.55rem/1 var(--latin); letter-spacing: -.08em; }
        .route-home::after { content: ''; position: absolute; inset: 0; background: linear-gradient(140deg, transparent 40%, rgba(0,0,0,.74)), url('<?php echo asset('images/og-default.jpg'); ?>') center/cover; opacity: .72; }
        .route-gallery .mini-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: .25rem; margin-top: .2rem; }
        .route-gallery .mini-grid i { aspect-ratio: .78; border-radius: 3px; background: linear-gradient(140deg, #93764d, #2a2520); }
        .route-gallery .mini-grid i:nth-child(2n) { background: linear-gradient(140deg, #394047, #171819); }
        .route-booking .calendar { display: grid; grid-template-columns: repeat(4,1fr); gap: .28rem; margin: .7rem 0; }
        .route-booking .calendar i { height: .85rem; border-radius: 2px; background: rgba(242,238,229,.13); }
        .route-booking .calendar i:nth-child(3), .route-booking .calendar i:nth-child(8), .route-booking .calendar i:nth-child(13) { background: var(--gold); }
        .route-booking .book-line { height: .52rem; width: 83%; margin-top: .65rem; border-radius: 3px; background: rgba(242,238,229,.12); }
        .route-about .mini-avatar { width: 56px; height: 56px; margin: .55rem auto .75rem; border: 1px solid rgba(197,163,101,.6); border-radius: 50%; background: linear-gradient(135deg, #d8bc84, #29241e); }
        .route-about .mini-copy { width: 76%; height: .35rem; margin: .35rem auto; border-radius: 2px; background: rgba(242,238,229,.14); }
        .route-about .mini-copy.short { width: 48%; }

        .contact-panel { display: grid; grid-template-columns: minmax(0,1fr) auto; align-items: center; gap: 2rem; padding: clamp(1.5rem, 4vw, 3.5rem); border: 1px solid rgba(197,163,101,.37); border-radius: var(--radius-xl); background: linear-gradient(115deg, rgba(197,163,101,.15), rgba(197,163,101,.035)); }
        .contact-panel h2 { max-width: 650px; margin: 0; font: 600 clamp(1.55rem, 3.5vw, 2.8rem)/1.3 var(--fa); letter-spacing: -.035em; }
        .contact-panel p { margin: .75rem 0 0; color: var(--paper-muted); font-size: .87rem; }
        .contact-links { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: .65rem; }

        footer { padding: 1.6rem 0 2.5rem; color: rgba(242,238,229,.48); font: 400 .62rem/1.7 var(--mono); direction: ltr; }
        .footer-inner { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .footer-inner a { color: var(--gold); text-decoration: none; }

        @media (max-width: 860px) {
            .hero { grid-template-columns: minmax(230px,.8fr) minmax(0,1.2fr); gap: 2rem; }
            .hero-visual { min-height: 450px; }
            .section-grid { grid-template-columns: 130px minmax(0,1fr); }
            .project { grid-template-columns: 1fr; }
            .project-art { min-height: 350px; }
            .routes { grid-template-columns: repeat(2, minmax(0,1fr)); }
        }
        @media (max-width: 640px) {
            .site-atlas { opacity: .27; background-position: 35% top; }
            .shell { width: min(100% - 2rem, 1180px); }
            .topbar { position: sticky; z-index: 10; top: 0; min-height: 68px; margin: 0 -1rem; padding: 0 1rem; background: rgba(17,17,15,.76); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
            .monogram { font-size: 1rem; }
            .monogram-mark { width: 30px; height: 30px; font-size: 1rem; }
            .page-tag { font-size: .51rem; letter-spacing: .05em; }
            .hero { display: flex; flex-direction: column; padding: 1.15rem 0 3.8rem; gap: 1.55rem; }
            .hero-copy { order: 2; padding: 0; }
            .hero-visual { order: 1; min-height: clamp(350px, 105vw, 470px); border-radius: 24px; box-shadow: 0 24px 52px rgba(0,0,0,.4), 0 0 0 5px rgba(197,163,101,.04); }
            .hero-visual img { object-position: 57% 37%; }
            .visual-chip { padding: .34rem .52rem; font-size: .49rem; }
            .visual-chip.top { top: .8rem; right: .8rem; }
            .visual-chip.bottom { right: .8rem; bottom: .8rem; }
            .photo-caption { left: .85rem; bottom: .8rem; font-size: .53rem; }
            .availability { padding: .36rem .63rem; font-size: .57rem; }
            .hero-kicker { margin: 1.1rem 0 .6rem; font-size: .61rem; letter-spacing: .08em; }
            h1 { font-size: clamp(3.45rem, 16.5vw, 4.8rem); line-height: .9; }
            .role { margin-top: 1rem; font-size: .98rem; line-height: 1.85; }
            .intro { margin-top: .65rem; font-size: .85rem; line-height: 2; }
            .hero-actions { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: .55rem; margin-top: 1.45rem; }
            .hero-actions .button { min-width: 0; min-height: 48px; padding: .55rem .4rem; overflow: hidden; font-size: .61rem; white-space: nowrap; text-overflow: ellipsis; }
            .hero-actions .button.primary { grid-column: 1 / -1; font-size: .73rem; }
            .quick-facts { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 0; margin-top: 1.6rem; padding-top: 1rem; }
            .quick-facts span { min-width: 0; padding: 0 .5rem; border-left: 1px solid var(--line-subtle); font-size: .48rem; overflow-wrap: anywhere; }
            .quick-facts span:first-child { padding-right: 0; }
            .quick-facts span:last-child { padding-left: 0; border-left: 0; }
            .quick-facts strong { margin-bottom: .32rem; font-size: .52rem; }
            .section-grid { display: block; }
            .section-index { display: flex; align-items: baseline; gap: .65rem; margin-bottom: 1rem; font-size: .63rem; }
            .section-label { display: inline; margin: 0; font-size: .62rem; letter-spacing: .07em; }
            section { padding: 3.7rem 0; }
            .section-title { margin: 0 0 1rem; font-size: 1.72rem; line-height: 1.45; }
            .section-lead { font-size: .86rem; line-height: 2.05; }
            .skills { flex-wrap: nowrap; gap: .48rem; margin: 1.35rem -1rem 0; padding: 0 1rem .6rem; overflow-x: auto; overscroll-behavior-inline: contain; scrollbar-width: none; scroll-snap-type: x proximity; }
            .skills::-webkit-scrollbar { display: none; }
            .skill { flex: 0 0 auto; padding: .55rem .7rem; font-size: .62rem; white-space: nowrap; scroll-snap-align: start; }
            .project { border-radius: 22px; }
            .project-art { order: -1; min-height: 270px; }
            .project-copy { padding: 1.45rem; }
            .project-eyebrow { font-size: .59rem; }
            .project-name { font-size: 2.7rem; }
            .project-kind { margin: .6rem 0 1rem; font-size: .63rem; }
            .project-copy > p:not(.project-eyebrow):not(.project-kind) { font-size: .83rem; line-height: 2; }
            .project-points { grid-template-columns: 1fr; gap: .58rem; margin: 1.25rem 0 1.45rem; }
            .project-points li { font-size: .68rem; }
            .browser { inset: auto .75rem .75rem; border-radius: 10px; }
            .browser-screen { min-height: 138px; padding: 1rem; }
            .browser-brand { font-size: 1.42rem; }
            .routes-intro { margin: 1.65rem 0 .9rem; font-size: .76rem; }
            .routes { display: grid; grid-auto-flow: column; grid-auto-columns: minmax(210px, 74vw); grid-template-columns: none; gap: .7rem; margin: 0 -1rem; padding: 0 1rem .85rem; overflow-x: auto; overscroll-behavior-inline: contain; scrollbar-width: none; scroll-snap-type: x mandatory; }
            .routes::-webkit-scrollbar { display: none; }
            .route-card { min-height: 175px; scroll-snap-align: start; }
            .route-card:first-child { margin-right: 0; }
            .contact-panel { grid-template-columns: 1fr; gap: 1.3rem; padding: 1.45rem; border-radius: 22px; }
            .contact-panel h2 { font-size: 1.55rem; line-height: 1.55; }
            .contact-panel p { font-size: .78rem; line-height: 1.95; }
            .contact-links { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); justify-content: stretch; gap: .55rem; }
            .contact-links .button { min-width: 0; padding: .6rem .35rem; overflow: hidden; font-size: .61rem; white-space: nowrap; text-overflow: ellipsis; }
            .contact-links .button.primary { grid-column: 1 / -1; font-size: .67rem; }
            footer { padding: 1.3rem 0 2rem; }
            .footer-inner { align-items: flex-start; flex-direction: column; gap: .35rem; font-size: .55rem; }
        }
        @media (max-width: 375px) {
            .page-tag { display: none; }
            .hero-visual { min-height: 335px; }
            h1 { font-size: 3.25rem; }
            .hero-actions .button { font-size: .55rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition-duration: .01ms !important; animation-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <div class="site-atlas" aria-hidden="true"></div>
    <a href="#content" class="skip-link">رفتن به محتوا</a>
    <div class="shell">
        <header class="topbar">
            <a class="monogram" href="<?php echo h($profileUrl); ?>" aria-label="Majed Mansouri — صفحهٔ اول رزومه">
                <span class="monogram-mark">M</span><span>MM</span>
            </a>
            <p class="page-tag">Selected profile · 2026</p>
        </header>
        <div class="rule"></div>

        <main id="content">
            <section class="hero" aria-labelledby="profile-name">
                <div class="hero-copy">
                    <span class="availability">Open to digital projects</span>
                    <p class="hero-kicker">Electrical Engineer · Digital Builder</p>
                    <h1 id="profile-name">Majed<br>Mansouri</h1>
                    <p class="role">مهندس برق الکترونیک، طراح و توسعه‌دهندهٔ وب</p>
                    <p class="intro">در نقطهٔ تلاقی تفکر مهندسی و طراحی دیجیتال کار می‌کنم؛ از ساخت تجربه‌های وب دقیق و سریع تا تبدیل یک هویت برند به محصولی قابل‌استفاده و ماندگار.</p>
                    <div class="hero-actions">
                        <a class="button primary" href="mailto:magd.mansouri81@gmail.com">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/></svg>
                            ارسال ایمیل
                        </a>
                        <a class="button" href="https://t.me/Manssuri" target="_blank" rel="noopener noreferrer">Telegram · @Manssuri</a>
                        <a class="button" href="https://www.instagram.com/themajead/" target="_blank" rel="noopener noreferrer">Instagram · @themajead</a>
                    </div>
                    <div class="quick-facts" aria-label="اطلاعات کوتاه">
                        <span><strong>Focus</strong>Design × Engineering</span>
                        <span><strong>Specialty</strong>Web Design & Development</span>
                        <span><strong>Featured Work</strong>Mirohood.ir</span>
                    </div>
                </div>
                <div class="hero-visual">
                    <picture>
                        <source srcset="<?php echo asset('images/majed-mansouri.webp'); ?>" type="image/webp">
                        <img src="<?php echo asset('images/majed-mansouri.jpg'); ?>" alt="Majed Mansouri" width="900" height="1200" fetchpriority="high">
                    </picture>
                    <span class="visual-chip top">MM / 01</span>
                    <span class="visual-chip bottom">DIGITAL BUILDER</span>
                    <p class="photo-caption">MAJED MANSOURI / PROFILE</p>
                </div>
            </section>

            <div class="rule"></div>

            <section aria-labelledby="about-title">
                <div class="section-grid">
                    <div class="section-index">01 <span class="section-label">PROFILE</span></div>
                    <div>
                        <h2 class="section-title" id="about-title">ساختن راه‌حل‌هایی که هم دقیق کار می‌کنند و هم درست دیده می‌شوند.</h2>
                        <p class="section-lead">پیش‌زمینهٔ مهندسی برق و الکترونیک، نگاه من به طراحی و توسعهٔ وب را ساختارمند کرده است: توجه به جزئیات، عملکرد قابل‌اعتماد و تجربه‌ای که برای مخاطب ساده و روان باشد.</p>
                        <div class="skills" aria-label="مهارت‌ها">
                            <span class="skill emphasis">Electrical & Electronics Engineering</span>
                            <span class="skill emphasis">Web Design</span>
                            <span class="skill emphasis">Web Development</span>
                            <span class="skill">Responsive Interfaces</span>
                            <span class="skill">PHP & Database-driven Websites</span>
                            <span class="skill">Performance Optimization</span>
                            <span class="skill">PWA Experience</span>
                            <span class="skill">UI / UX Thinking</span>
                        </div>
                    </div>
                </div>
            </section>

            <div class="rule"></div>

            <section aria-labelledby="mirohood-title">
                <div class="section-grid">
                    <div class="section-index">02 <span class="section-label">SELECTED WORK</span></div>
                    <div>
                        <article class="project">
                            <div class="project-copy">
                                <p class="project-eyebrow">Featured digital project</p>
                                <h2 class="project-name" id="mirohood-title">Mirohood</h2>
                                <p class="project-kind">Photography & Filming — mirohood.ir</p>
                                <p>طراحی و توسعهٔ وب‌سایت استودیوی عکاسی Mirohood با تمرکز بر هویت بصری مینیمال، رزرو آنلاین، گالری‌های عکاسی، پنل‌های کاربری و مدیریت محتوا. این پروژه نمونه‌ای از ترکیب طراحی برندمحور با توسعهٔ عملی و بهینه‌سازی تجربهٔ کاربر است.</p>
                                <ul class="project-points">
                                    <li>طراحی واکنش‌گرا و رابط کاربری اختصاصی</li>
                                    <li>سامانهٔ رزرو و تقویم شمسی</li>
                                    <li>گالری عمومی و گالری اختصاصی مشتری</li>
                                    <li>پنل ادمین و داشبورد کاربر</li>
                                    <li>بهینه‌سازی سرعت، WebP و PWA</li>
                                    <li>SEO، sitemap و ساختار صفحات</li>
                                </ul>
                                <a class="live-link" href="https://mirohood.ir" target="_blank" rel="noopener noreferrer">View the live project</a>
                            </div>
                            <div class="project-art" aria-label="نمای گرافیکی پروژه Mirohood">
                                <div class="browser" aria-hidden="true">
                                    <div class="browser-top"><i></i><i></i><i></i><span class="browser-url">mirohood.ir</span></div>
                                    <div class="browser-screen">
                                        <div class="browser-brand">Mirohood</div>
                                        <div class="browser-subtitle">Photography & Filming</div>
                                        <div class="browser-line"></div>
                                        <div class="browser-grid"><span></span><span></span><span></span></div>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <div style="margin-top:2rem;">
                            <p class="routes-intro">بخش‌های اصلی پروژهٔ زنده را از اینجا مشاهده کنید:</p>
                            <div class="routes" aria-label="صفحات پروژه Mirohood">
                                <a class="route-card route-home" href="https://mirohood.ir" target="_blank" rel="noopener noreferrer"><div class="route-browser"><span></span><span></span><span></span></div><span class="route-name">صفحهٔ اصلی<em>/</em></span></a>
                                <a class="route-card route-gallery" href="https://mirohood.ir/gallery" target="_blank" rel="noopener noreferrer"><div class="route-browser"><span></span><span></span><span></span></div><div class="mini-grid"><i></i><i></i><i></i><i></i><i></i><i></i></div><span class="route-name">گالری<em>/gallery</em></span></a>
                                <a class="route-card route-booking" href="https://mirohood.ir/booking" target="_blank" rel="noopener noreferrer"><div class="route-browser"><span></span><span></span><span></span></div><div class="calendar"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div><div class="book-line"></div><span class="route-name">رزرو<em>/booking</em></span></a>
                                <a class="route-card route-about" href="https://mirohood.ir/about" target="_blank" rel="noopener noreferrer"><div class="route-browser"><span></span><span></span><span></span></div><div class="mini-avatar"></div><div class="mini-copy"></div><div class="mini-copy short"></div><span class="route-name">دربارهٔ ما<em>/about</em></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="rule"></div>

            <section aria-labelledby="contact-title">
                <div class="contact-panel">
                    <div>
                        <h2 id="contact-title">برای همکاری در طراحی و توسعهٔ محصول دیجیتال، در ارتباط باشیم.</h2>
                        <p>ایمیل، تلگرام یا اینستاگرام؛ هر کانالی که برای شما ساده‌تر است.</p>
                    </div>
                    <div class="contact-links">
                        <a class="button primary" href="mailto:magd.mansouri81@gmail.com">magd.mansouri81@gmail.com</a>
                        <a class="button" href="https://t.me/Manssuri" target="_blank" rel="noopener noreferrer">@Manssuri</a>
                        <a class="button" href="https://www.instagram.com/themajead/" target="_blank" rel="noopener noreferrer">@themajead</a>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <div class="rule" style="margin-bottom:1.4rem;"></div>
            <div class="footer-inner">
                <span>© <span id="current-year">2026</span> MAJED MANSOURI</span>
                <span>Independent profile on <a href="https://mirohood.ir" target="_blank" rel="noopener noreferrer">mirohood.ir</a></span>
            </div>
        </footer>
    </div>
    <script>document.getElementById('current-year').textContent = new Date().getFullYear();</script>
</body>
</html>
