<?php
// views/admin/content.php - مدیریت محتوا
$contentModel = new SiteContent();
$contents = $contentModel->getAll();
$csrf_token = generate_csrf_token();

$sections = [
    'home' => [
        'title' => '🏠 صفحه اصلی',
        'fields' => [
            'home.eyebrow' => 'خط بالای هیرو',
            'home.title_line1' => 'عنوان هیرو — خط اول',
            'home.title_line2' => 'عنوان هیرو — خط دوم (طلایی)',
            'home.subtitle' => 'زیرنویس هیرو',
            'home.cta_button' => 'متن دکمه هیرو (رزرو)',
            'home.packages_eyebrow' => 'عنوان بخش پکیج‌ها',
            'home.packages_title' => 'تیتر اصلی پکیج‌ها',
            'home.gallery_title' => 'عنوان بخش گالری',
            'home.gallery_heading' => 'تیتر بخش گالری (Featured Projects)',
            'home.gallery_view_all' => 'متن لینک View All',
            'home.about_eyebrow' => 'خط بالای بخش About',
            'home.about_title_line1' => 'تیتر About — خط اول',
            'home.about_title_line2' => 'تیتر About — خط دوم (طلایی)',
            'home.about_text' => 'متن بخش About',
            'home.about_button' => 'متن دکمه Learn More',
            'home.cta_title_line1' => 'تیتر CTA — خط اول',
            'home.cta_title_line2' => 'تیتر CTA — خط دوم (طلایی)',
            'home.cta_text' => 'متن CTA',
            'home.cta_button2' => 'متن دکمه CTA',
            'home.package1_eyebrow' => 'پکیج ۱: خط بالا',
            'home.package1_title' => 'پکیج ۱: عنوان',
            'home.package1_duration' => 'پکیج ۱: مدت زمان',
            'home.package1_desc' => 'پکیج ۱: توضیحات (چند خط)',
            'home.package2_eyebrow' => 'پکیج ۲: خط بالا',
            'home.package2_title' => 'پکیج ۲: عنوان',
            'home.package2_duration' => 'پکیج ۲: مدت زمان',
            'home.package2_desc' => 'پکیج ۲: توضیحات (چند خط)',
            'home.package3_eyebrow' => 'پکیج ۳: خط بالا',
            'home.package3_title' => 'پکیج ۳: عنوان',
            'home.package3_duration' => 'پکیج ۳: مدت زمان',
            'home.package3_desc' => 'پکیج ۳: توضیحات (چند خط)',
            'home.gallery_empty' => 'متن جایگزین وقتی گالری خالی است',
        ],
    ],
    'booking' => [
        'title' => '📅 صفحه رزرو',
        'fields' => [
            'booking.title' => 'عنوان صفحه رزرو',
            'booking.subtitle' => 'زیرنویس صفحه رزرو',
            'booking.badge' => 'برچسب بالای عنوان (Book a Session)',
            'booking.heading_line1' => 'تیتر — خط اول (Book Your)',
            'booking.heading_line2' => 'تیتر — خط دوم (Moment)',
            'booking.package1_name' => 'نام پکیج 1 (Portrait)',
            'booking.package1_badge' => 'بج پکیج 1 (Premium)',
            'booking.package2_name' => 'نام پکیج 2 (Brand)',
            'booking.package2_badge' => 'بج پکیج 2 (Pro)',
            'booking.package3_name' => 'نام پکیج 3 (Photo & Video)',
            'booking.package3_badge' => 'بج پکیج 3 (Editorial)',
            'booking.form_name' => 'برچسب نام و نام خانوادگی',
            'booking.form_phone' => 'برچسب شماره موبایل',
            'booking.form_package' => 'برچسب انتخاب پکیج',
            'booking.form_date' => 'برچسب انتخاب تاریخ',
            'booking.form_time' => 'برچسب انتخاب ساعت',
            'booking.form_note' => 'برچسب توضیح اضافه',
            'booking.form_submit' => 'متن دکمه ارسال',
            'booking.success_title' => 'تیتر صفحه موفقیت (Booking Confirmed!)',
            'booking.success_message' => 'پیام موفقیت',
            'booking.success_back' => 'متن دکمه بازگشت به صفحه اصلی',
            'booking.error_message' => 'پیام خطای کلی',
            'booking.select_placeholder' => 'گزینه پیش‌فرض انتخاب پکیج',
            'booking.option_portrait' => 'گزینه پکیج ۱ در select',
            'booking.option_brand' => 'گزینه پکیج ۲ در select',
            'booking.option_editorial' => 'گزینه پکیج ۳ در select',
        ],
    ],
    'gallery' => [
        'title' => '🖼️ صفحه گالری',
        'fields' => [
            'gallery.eyebrow' => 'خط بالای عنوان',
            'gallery.title' => 'تیتر اصلی صفحه گالری',
            'gallery.subtitle' => 'زیرنویس صفحه گالری',
            'gallery.empty_text' => 'متن وقتی گالری خالی است',
            'gallery.photo_count_label' => 'برچسب تعداد عکس در صفحه گالری (عکس)',
            'gallery.person_photo_count_label' => 'برچسب تعداد عکس در صفحه شخص (عکس)',
            'gallery.person_back_label' => 'متن دکمه بازگشت به گالری',
            'gallery.person_empty_text' => 'متن وقتی عکسی برای شخص وجود ندارد',
            'gallery.person_website_label' => 'برچسب لینک وب‌سایت شخص',
        ],
    ],
    'about' => [
        'title' => '👤 صفحه درباره ما',
        'fields' => [
            'about.eyebrow' => 'خط بالای عنوان',
            'about.title' => 'تیتر اصلی درباره ما',
            'about.intro' => 'توضیح کوتاه زیر تیتر',
            'about.quote' => 'جمله‌ی نقل‌قول بالای صفحه',
            'about.body' => 'متن اصلی درباره ما (چند خط)',
            'about.owner_name' => 'نام صاحب استودیو / پروفایل',
            'about.owner_role' => 'سمت / حرفه (مثلاً Photographer)',
            'about.owner_bio' => 'بیوگرافی کوتاه صاحب استودیو',
            'about.owner_avatar' => 'آدرس تصویر پروفایل (URL)',
            'about.instagram' => 'آیدی اینستاگرام (بدون @)',
            'about.owner_social_youtube' => 'لینک YouTube',
            'about.owner_social_vimeo' => 'لینک Vimeo',
            'about.owner_social_twitter' => 'لینک Twitter / X',
            'about.email' => 'ایمیل تماس',
            'about.website' => 'لینک وب‌سایت شخصی',
            'about.address_label' => 'برچسب آدرس',
            'about.address' => 'متن آدرس',
            'about.contact_label' => 'برچسب تماس',
            'about.contact' => 'متن تماس (HTML مجاز - چند خط)',
            'about.button' => 'متن دکمه رزرو',
            'about.background' => 'آدرس تصویر پس‌زمینه (URL)',
        ],
    ],
    'layout' => [
        'title' => '🧭 منو و فوتر (تمام صفحات)',
        'fields' => [
            'layout.nav_home' => 'منو: Home',
            'layout.nav_gallery' => 'منو: Gallery',
            'layout.nav_booking' => 'منو: Booking',
            'layout.nav_about' => 'منو: About',
            'layout.nav_dashboard' => 'منو: Dashboard',
            'layout.nav_login' => 'منو: Login',
            'layout.nav_logout' => 'منو: Logout',
            'layout.footer_text' => 'متن فوتر (سال خودکار نمایش داده می‌شود)',
        ],
    ],
];
?>
<h1 class="admin-page-title">مدیریت محتوا</h1>
<p class="admin-page-subtitle">هر کدام را عوض کنی و «ذخیره» را بزنی، همان لحظه روی سایت اعمال می‌شود.</p>

<div class="admin-card">
    <form method="POST" action="<?php echo url('admin/api/update-content'); ?>" id="content-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <?php foreach ($sections as $sectionKey => $section): ?>
            <div style="border-bottom:1px solid rgba(200,168,98,0.04);padding:1.25rem 0;">
                <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.15rem;font-weight:300;color:var(--color-champagne);margin-bottom:1rem;">
                    <?php echo $section['title']; ?>
                </h3>

                <?php if ($sectionKey === 'about'): ?>
                <div style="margin-bottom:1.25rem;padding:1rem;background:rgba(200,168,98,0.03);border:1px solid rgba(200,168,98,0.08);border-radius:0.75rem;">
                    <label class="admin-label" style="margin-bottom:0.5rem;">📷 آپلود عکس پروفایل صاحب استودیو</label>
                    <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
                        <?php $avatarUrl = $contents['about.owner_avatar'] ?? ''; ?>
                        <?php if (!empty($avatarUrl)): ?>
                            <img src="<?php echo h($avatarUrl); ?>" alt="Current avatar" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid rgba(200,168,98,0.3);">
                        <?php endif; ?>
                        <input type="file" id="about-avatar-input" accept="image/*" class="admin-input" style="padding:0.4rem;flex:1;min-width:200px;">
                        <button type="button" onclick="uploadAboutAvatar()" class="admin-btn" style="padding:0.5rem 1.2rem;font-size:0.7rem;white-space:nowrap;">
                            <i class="fas fa-upload"></i> آپلود عکس
                        </button>
                    </div>
                    <p style="font-size:0.7rem;color:var(--color-muted);margin-top:0.5rem;">
                        پس از آپلود، آدرس تصویر در فیلد «آدرس تصویر پروفایل» ذخیره می‌شود.
                    </p>
                </div>
                <?php endif; ?>

                <?php foreach ($section['fields'] as $key => $label): ?>
                    <?php $isTextarea = (strpos($key, 'body') !== false || strpos($key, 'contact') !== false || strpos($key, 'desc') !== false || strpos($key, 'text') !== false); ?>
                    <div style="margin-bottom:0.8rem;display:flex;gap:0.6rem;align-items:flex-end;flex-wrap:wrap;">
                        <div style="flex:1;min-width:200px;">
                            <label class="admin-label"><?php echo $label; ?></label>
                            <?php if ($isTextarea): ?>
                                <textarea name="content[<?php echo $key; ?>]" class="admin-input" style="font-size:0.9rem;min-height:80px;resize:vertical;" rows="3"><?php echo htmlspecialchars($contents[$key] ?? ''); ?></textarea>
                            <?php else: ?>
                                <input type="text" name="content[<?php echo $key; ?>]" value="<?php echo htmlspecialchars($contents[$key] ?? ''); ?>" class="admin-input" style="font-size:0.9rem;">
                            <?php endif; ?>
                        </div>
                        <button type="button" onclick="saveContent('<?php echo $key; ?>')" class="admin-btn" style="padding:0.5rem 1.2rem;font-size:0.7rem;white-space:nowrap;">
                            <i class="fas fa-save"></i> ذخیره
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        
        <button type="button" onclick="saveAllContent()" class="admin-btn" style="margin-top:1.5rem;">
            <i class="fas fa-save"></i> ذخیره همه
        </button>
    </form>
</div>

<script>
function uploadAboutAvatar() {
    var input = document.getElementById('about-avatar-input');
    if (!input || !input.files || !input.files[0]) {
        showToast('❌ لطفاً یک تصویر انتخاب کنید', 'error');
        return;
    }

    var fd = new FormData();
    fd.append('file', input.files[0]);
    fd.append('type', 'about_avatar');
    fd.append('csrf_token', '<?php echo $csrf_token; ?>');

    fetch('<?php echo url("admin/api/upload-media"); ?>', {
        method: 'POST',
        body: fd,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        if (data.success && data.url) {
            var avatarInput = document.querySelector('input[name="content[about.owner_avatar]"]');
            if (avatarInput) {
                avatarInput.value = data.url;
            }
            showToast('✅ ' + data.message, 'success');
            // Refresh to show the new avatar preview
            setTimeout(function() { location.reload(); }, 800);
        } else {
            showToast('❌ ' + (data.message || 'خطا در آپلود'), 'error');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showToast('❌ خطا در ارتباط با سرور: ' + error.message, 'error');
    });
}

function saveContent(key) {
    var input = document.querySelector('input[name="content[' + key + ']"], textarea[name="content[' + key + ']"]');
    if (!input) {
        showToast('❌ خطا: فیلد پیدا نشد', 'error');
        return;
    }
    
    var fd = new FormData();
    fd.append('key', key);
    fd.append('value', input.value);
    fd.append('csrf_token', '<?php echo $csrf_token; ?>');
    
    fetch('<?php echo url("admin/api/update-content"); ?>', {
        method: 'POST',
        body: fd,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            showToast('✅ ' + data.message, 'success');
        } else {
            showToast('❌ ' + (data.message || 'خطا در ذخیره'), 'error');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showToast('❌ خطا در ارتباط با سرور: ' + error.message, 'error');
    });
}

function saveAllContent() {
    var form = document.getElementById('content-form');
    var fd = new FormData(form);
    
    fetch('<?php echo url("admin/api/update-content"); ?>', {
        method: 'POST',
        body: fd,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            showToast('✅ ' + data.message, 'success');
        } else {
            showToast('❌ ' + (data.message || 'خطا در ذخیره'), 'error');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showToast('❌ خطا در ارتباط با سرور: ' + error.message, 'error');
    });
}
</script>