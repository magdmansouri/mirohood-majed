<?php
// controllers/HomeController.php

class HomeController {
    private $galleryModel;
    private $contentModel;

    public function __construct() {
        $this->galleryModel = new GalleryImage();
        $this->contentModel = new SiteContent();
    }

    public function index() {
        $gallery = $this->galleryModel->getFeatured(4);
        $content = $this->contentModel->getMultiple([
            'home.eyebrow', 'home.title_line1', 'home.title_line2',
            'home.subtitle', 'home.cta_button', 'home.packages_eyebrow',
            'home.packages_title', 'home.gallery_title', 'home.gallery_heading',
            'home.gallery_view_all', 'home.about_eyebrow', 'home.about_title_line1',
            'home.about_title_line2', 'home.about_text', 'home.about_button',
            'home.cta_title_line1', 'home.cta_title_line2', 'home.cta_text',
            'home.cta_button2', 'home.hero_video', 'home.hero_poster',
            'home.package1_eyebrow', 'home.package1_title', 'home.package1_duration', 'home.package1_desc',
            'home.package2_eyebrow', 'home.package2_title', 'home.package2_duration', 'home.package2_desc',
            'home.package3_eyebrow', 'home.package3_title', 'home.package3_duration', 'home.package3_desc',
            'home.gallery_empty',
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);
        $packages = [
            ['id' => 'portrait', 'eyebrow' => $content['home.package1_eyebrow'] ?? 'پرتره', 'titleEn' => $content['home.package1_title'] ?? 'جلسه پرتره حرفه‌ای', 'durationEn' => $content['home.package1_duration'] ?? '۱ تا ۲ ساعت', 'descriptionEn' => $content['home.package1_desc'] ?? 'ثبت زیبایی‌های منحصر‌به‌فرد شما با نورپردازی سینمایی'],
            ['id' => 'brand', 'eyebrow' => $content['home.package2_eyebrow'] ?? 'برندینگ', 'titleEn' => $content['home.package2_title'] ?? 'جلسه فیلم‌برداری برند', 'durationEn' => $content['home.package2_duration'] ?? '۲ تا ۴ ساعت', 'descriptionEn' => $content['home.package2_desc'] ?? 'روایت داستان برند شما با تصاویر سینمایی'],
            ['id' => 'editorial', 'eyebrow' => $content['home.package3_eyebrow'] ?? 'ادیتوریال', 'titleEn' => $content['home.package3_title'] ?? 'جلسه فیلم و عکس', 'durationEn' => $content['home.package3_duration'] ?? '۳ تا ۵ ساعت', 'descriptionEn' => $content['home.package3_desc'] ?? 'ترکیب عکاسی و فیلم‌برداری برای محتوای حرفه‌ای']
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => SITE_NAME,
            'image' => SITE_URL . '/assets/images/og-default.jpg',
            '@id' => SITE_URL,
            'url' => SITE_URL,
            'email' => defined('SITE_EMAIL') ? SITE_EMAIL : 'Parsmiro@gmail.com',
            'priceRange' => '$$',
            'description' => SITE_DESCRIPTION,
            'sameAs' => [
                'https://instagram.com/mirohood'
            ]
        ];

        $data = [
            'page_title' => 'استودیو عکاسی Mirohood | رزرو جلسه عکاسی',
            'page_description' => SITE_DESCRIPTION,
            'current_page' => 'home',
            'gallery' => $gallery,
            'content' => $content,
            'packages' => $packages,
            'og_title' => 'استودیو عکاسی Mirohood',
            'og_description' => SITE_DESCRIPTION,
            'og_image' => SITE_URL . '/assets/images/og-default.jpg',
            'og_type' => 'website',
            'schema_json' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        ];
        $contentView = render('home', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }

    public function about() {
        $content = $this->contentModel->getMultiple([
            'about.eyebrow', 'about.title', 'about.intro', 'about.quote', 'about.body',
            'about.address_label', 'about.address', 'about.contact_label',
            'about.contact', 'about.button', 'about.background',
            'about.owner_name', 'about.owner_role', 'about.owner_bio', 'about.owner_avatar',
            'about.instagram', 'about.owner_social_youtube', 'about.owner_social_vimeo',
            'about.owner_social_twitter', 'about.email', 'about.website',
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);
        $aboutBg = $content['about.background'] ?? '';
        $data = [
            'page_title' => 'درباره ما',
            'page_description' => 'درباره Mirohood بیشتر بدانید',
            'current_page' => 'about',
            'content' => $content,
            'aboutBg' => $aboutBg
        ];
        $contentView = render('about', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }
}
?>