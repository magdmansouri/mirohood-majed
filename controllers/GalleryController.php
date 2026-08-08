<?php
// controllers/GalleryController.php

class GalleryController {

    private $personModel;
    private $photoModel;

    public function __construct() {
        $this->personModel = new Person();
        $this->photoModel = new GalleryPhoto();
    }

    /**
     * /gallery — Apple Photos "People" grid
     */
    public function index() {
        $people = $this->personModel->getActive();
        $contentModel = new SiteContent();
        $content = $contentModel->getMultiple([
            'gallery.eyebrow', 'gallery.title', 'gallery.subtitle',
            'gallery.empty_text', 'gallery.photo_count_label', 'gallery.background',
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);
        $galleryBg = $content['gallery.background'] ?? '';

        $galleryDescription = 'گالری عکاسی حرفه‌ای Mirohood — آلبوم‌های افراد، پرتره، برند و ادیتوریال. عکس‌های هر فرد با نام و اینستاگرام در گوگل قابل جستجو است.';
        $galleryUrl = SITE_URL . '/gallery';
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ImageGallery',
            'name' => 'گالری عکاسی Mirohood',
            'description' => $galleryDescription,
            'url' => $galleryUrl,
            'hasPart' => []
        ];
        foreach ($people as $p) {
            $schema['hasPart'][] = [
                '@type' => 'CollectionPage',
                'name' => $p['name'],
                'url' => SITE_URL . '/gallery/' . h($p['slug']),
                'description' => !empty($p['bio']) ? $p['bio'] : 'گالری عکس‌های ' . $p['name']
            ];
        }

        $data = [
            'page_title' => 'گالری عکاسی Mirohood',
            'page_description' => $galleryDescription,
            'current_page' => 'gallery',
            'people' => $people,
            'content' => $content,
            'galleryBg' => $galleryBg,
            'og_title' => 'گالری عکاسی Mirohood',
            'og_description' => $galleryDescription,
            'og_type' => 'website',
            'og_url' => $galleryUrl,
            'canonical_url' => $galleryUrl,
            'schema_json' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG)
        ];

        $contentView = render('gallery', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }

    /**
     * /gallery/person-name — Person album page
     */
    public function person($slug) {
        $person = $this->personModel->getBySlug($slug);

        if (!$person) {
            http_response_code(404);
            echo '<h1 style="color:#f4f1ea;text-align:center;padding:5rem;">404 - شخص مورد نظر پیدا نشد</h1>';
            return;
        }

        $images = $this->photoModel->getByPerson($person['id']);
        $contentModel = new SiteContent();
        $content = $contentModel->getMultiple([
            'gallery.photo_count_label', 'gallery.person_photo_count_label',
            'gallery.person_back_label', 'gallery.person_empty_text', 'gallery.person_website_label',
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);

        $personName = $person['name'] ?? '';
        $personBio = !empty($person['bio']) ? $person['bio'] : 'گالری عکس‌های حرفه‌ای ' . $personName . ' در استودیو Mirohood';
        $personInstagram = !empty($person['instagram']) ? ltrim($person['instagram'], '@') : '';
        $personWebsite = !empty($person['website']) ? $person['website'] : '';
        $avatarUrl = !empty($person['avatar']) ? SITE_URL . $person['avatar'] : null;
        $pageUrl = SITE_URL . '/gallery/' . h($slug);

        // Build keyword-rich description for search engines
        $seoDescription = $personBio;
        if (!empty($personInstagram)) {
            $seoDescription .= ' | اینستاگرام: @' . $personInstagram;
        }
        $seoDescription = mb_substr($seoDescription, 0, 160, 'UTF-8');

        // SameAs links for social profiles (helps Google connect person to profiles)
        $sameAs = [];
        if (!empty($personInstagram)) {
            $sameAs[] = 'https://instagram.com/' . $personInstagram;
        }
        if (!empty($personWebsite)) {
            $sameAs[] = $personWebsite;
        }

        // ImageObject schema for gallery photos
        $imageObjects = [];
        foreach ($images as $img) {
            $imageUrl = !empty($img['image']) ? SITE_URL . $img['image'] : null;
            if ($imageUrl) {
                $imageObjects[] = [
                    '@type' => 'ImageObject',
                    'contentUrl' => $imageUrl,
                    'description' => !empty($img['caption']) ? $img['caption'] : $personName,
                    'name' => !empty($img['alt']) ? $img['alt'] : $personName
                ];
            }
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'name' => $personName,
            'description' => $seoDescription,
            'url' => $pageUrl,
            'image' => $avatarUrl,
            'mainEntity' => [
                '@type' => 'Person',
                'name' => $personName,
                'description' => $personBio,
                'image' => $avatarUrl,
                'url' => $pageUrl,
                'sameAs' => array_values(array_filter($sameAs))
            ],
            'isPartOf' => [
                '@type' => 'ImageGallery',
                'name' => 'گالری Mirohood',
                'url' => SITE_URL . '/gallery'
            ]
        ];

        if (!empty($imageObjects)) {
            $schema['mainEntity']['subjectOf'] = $imageObjects;
        }

        $data = [
            'page_title' => $personName . ' — گالری عکاسی Mirohood',
            'page_description' => $seoDescription,
            'current_page' => 'gallery',
            'person' => $person,
            'images' => $images,
            'content' => $content,
            'og_title' => $personName . ' — گالری عکاسی Mirohood',
            'og_description' => $seoDescription,
            'og_type' => 'profile',
            'og_image' => $avatarUrl,
            'og_url' => $pageUrl,
            'canonical_url' => $pageUrl,
            'twitter_card' => 'summary_large_image',
            'schema_json' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG)
        ];

        $contentView = render('person', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }
}
