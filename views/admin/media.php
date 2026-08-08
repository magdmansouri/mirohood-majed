<?php
// views/admin/media.php - مدیریت رسانه
$contentModel = new SiteContent();
$heroVideo = $contentModel->get('home.hero_video') ?? '';
$heroPoster = $contentModel->get('home.hero_poster') ?? '';
$csrf_token = generate_csrf_token();

$sections = [
    [
        'type' => 'hero_video',
        'icon' => 'fa-video',
        'title' => '🎬 ویدیوی صفحه اصلی',
        'desc' => 'ویدیوی هیرو صفحه اصلی - فرمت MP4 (حداکثر ۱۰ مگابایت)',
        'accept' => 'video/mp4,video/webm,video/ogg',
        'current' => $heroVideo,
        'is_video' => true
    ],
    [
        'type' => 'hero_poster',
        'icon' => 'fa-image',
        'title' => '🖼️ پوستر صفحه اصلی',
        'desc' => 'تصویر جایگزین قبل از پخش ویدیو (حداکثر ۵ مگابایت)',
        'accept' => 'image/jpeg,image/png,image/webp,image/gif',
        'current' => $heroPoster,
        'is_video' => false
    ],
    [
        'type' => 'booking_background',
        'icon' => 'fa-mountain-sun',
        'title' => '🌄 بک‌گراند صفحه رزرو',
        'desc' => 'تصویر پس‌زمینه صفحه رزرو نوبت (حداکثر ۵ مگابایت)',
        'accept' => 'image/jpeg,image/png,image/webp,image/gif',
        'current' => $contentModel->get('booking.background') ?? '',
        'is_video' => false
    ],
    [
        'type' => 'gallery_background',
        'icon' => 'fa-images',
        'title' => '🖼️ بک‌گراند صفحه گالری',
        'desc' => 'تصویر پس‌زمینه صفحه گالری (حداکثر ۵ مگابایت)',
        'accept' => 'image/jpeg,image/png,image/webp,image/gif',
        'current' => $contentModel->get('gallery.background') ?? '',
        'is_video' => false
    ],
    [
        'type' => 'about_background',
        'icon' => 'fa-circle-info',
        'title' => '🌄 بک‌گراند صفحه درباره ما',
        'desc' => 'تصویر پس‌زمینه صفحه درباره ما (حداکثر ۵ مگابایت)',
        'accept' => 'image/jpeg,image/png,image/webp,image/gif',
        'current' => $contentModel->get('about.background') ?? '',
        'is_video' => false
    ]
];
?>
<h1 class="admin-page-title">مدیریت رسانه</h1>
<p class="admin-page-subtitle">ویدیو و تصاویر پس‌زمینه صفحات را مدیریت کنید</p>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.25rem;">
    <?php foreach ($sections as $s): ?>
        <div class="admin-card" style="margin-bottom:0;">
            <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.1rem;font-weight:300;color:var(--color-champagne);margin-bottom:0.3rem;">
                <i class="fas <?php echo $s['icon']; ?>" style="color:var(--color-champagne);margin-left:0.4rem;"></i>
                <?php echo $s['title']; ?>
            </h3>
            <p style="color:var(--color-muted);font-size:0.75rem;margin-bottom:0.8rem;"><?php echo $s['desc']; ?></p>
            
            <?php if ($s['current']): ?>
                <div class="media-preview" data-type="<?php echo $s['type']; ?>" style="position:relative;margin-bottom:0.8rem;">
                    <?php if ($s['is_video']): ?>
                        <video controls style="width:100%;border-radius:0.6rem;border:1px solid var(--color-glass-border);max-height:180px;">
                            <source src="<?php echo h($s['current']); ?>">
                        </video>
                    <?php else: ?>
                        <img src="<?php echo h($s['current']); ?>" style="width:100%;border-radius:0.6rem;border:1px solid var(--color-glass-border);object-fit:cover;max-height:160px;">
                    <?php endif; ?>
                    <button type="button" class="admin-btn admin-btn-danger admin-btn-small delete-media" data-type="<?php echo $s['type']; ?>" style="position:absolute;top:0.5rem;right:0.5rem;z-index:5;">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </div>
            <?php else: ?>
                <div class="media-placeholder" style="width:100%;height:100px;border-radius:0.6rem;margin-bottom:0.8rem;border:1px dashed rgba(200,168,98,0.12);display:flex;align-items:center;justify-content:center;color:var(--color-muted);font-size:0.75rem;">
                    <i class="fas fa-cloud-upload-alt" style="font-size:1.2rem;margin-left:0.4rem;"></i>
                    فایلی آپلود نشده
                </div>
            <?php endif; ?>
            
            <form class="media-upload-form" method="POST" enctype="multipart/form-data" action="<?php echo url('admin/api/upload-media'); ?>" data-is-video="<?php echo $s['is_video'] ? '1' : '0'; ?>">
                <input type="hidden" name="type" value="<?php echo $s['type']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
                    <input type="file" name="file" accept="<?php echo $s['accept']; ?>" required style="flex:1;min-width:120px;padding:0.4rem;background:rgba(255,255,255,0.03);border:1px solid var(--color-glass-border);border-radius:0.5rem;color:var(--color-ivory);font-size:0.8rem;">
                    <button type="submit" class="admin-btn" style="padding:0.5rem 1.2rem;font-size:0.75rem;">
                        <i class="fas fa-upload"></i>
                        <span class="btn-label">آپلود</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfToken = '<?php echo $csrf_token; ?>';

    document.querySelectorAll('.media-upload-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var btn = form.querySelector('button[type="submit"]');
            var btnLabel = btn.querySelector('.btn-label');
            var fileInput = form.querySelector('input[type="file"]');
            var isVideo = form.dataset.isVideo === '1';

            if (!fileInput.files || !fileInput.files[0]) {
                showToast('❌ لطفاً یک فایل انتخاب کنید', 'error');
                return;
            }

            btn.disabled = true;
            var originalLabel = btnLabel.textContent;
            btnLabel.textContent = 'در حال آپلود...';

            var fd = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (response) {
                if (!response.ok) throw new Error('خطای سرور: ' + response.status);
                return response.json();
            })
            .then(function (data) {
                if (data.success) {
                    showToast('✅ ' + data.message, 'success');
                    location.reload();
                } else {
                    showToast('❌ ' + (data.message || 'خطا در آپلود'), 'error');
                }
            })
            .catch(function (err) {
                showToast('❌ خطا در ارتباط با سرور: ' + err.message, 'error');
            })
            .finally(function () {
                btn.disabled = false;
                btnLabel.textContent = originalLabel;
            });
        });
    });

    document.querySelectorAll('.delete-media').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var type = btn.dataset.type;
            if (!confirm('آیا مطمئنید می‌خواهید این رسانه را حذف کنید؟')) return;

            var fd = new FormData();
            fd.append('type', type);
            fd.append('csrf_token', csrfToken);

            btn.disabled = true;
            var originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch('<?php echo url('admin/api/delete-media'); ?>', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (response) {
                if (!response.ok) throw new Error('خطای سرور: ' + response.status);
                return response.json();
            })
            .then(function (data) {
                if (data.success) {
                    showToast('✅ ' + data.message, 'success');
                    location.reload();
                } else {
                    showToast('❌ ' + (data.message || 'خطا در حذف'), 'error');
                }
            })
            .catch(function (err) {
                showToast('❌ خطا در ارتباط با سرور: ' + err.message, 'error');
            })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    });
});
</script>