<?php
// views/admin/client-gallery-photos.php - مدیریت تصاویر گالری مشتری
$gallery = $gallery ?? [];
$images = $images ?? [];
$csrf_token = generate_csrf_token();
?>
<style>
    .cgp-wrap { max-width:1200px; margin:0 auto; padding:2rem 1.5rem; }
    .cgp-header {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap:1rem; margin-bottom: 2rem;
    }
    .cgp-header h1 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2.2rem; font-weight: 300; color: #f4f1ea; margin: 0;
    }
    .cgp-header p { color: #8a8580; font-size: 0.85rem; margin-top: 0.3rem; }
    .cgp-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .cgp-actions a, .cgp-actions button {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.6rem 1.4rem; border-radius: 9999px;
        font-size: 0.8rem; font-weight: 600; text-decoration: none;
        cursor: pointer; transition: all 0.3s ease;
    }
    .cgp-btn-primary { background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908; border: none; }
    .cgp-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,168,98,0.25); }
    .cgp-btn-outline { border: 1px solid rgba(200,168,98,0.2); color: #f4f1ea; background: transparent; }
    .cgp-btn-outline:hover { border-color: #c8a862; color: #c8a862; }

    .cgp-share-box {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;
    }
    .cgp-share-title { color: #f4f1ea; font-size: 1rem; margin-bottom: 0.25rem; }
    .cgp-share-sub { color: #8a8580; font-size: 0.75rem; margin-bottom: 1rem; }
    .cgp-share-grid {
        display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;
    }
    @media (max-width: 900px) { .cgp-share-grid { grid-template-columns: 1fr; } }
    .cgp-share-grid input {
        width: 100%; padding: 0.6rem 1rem; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(200,168,98,0.12); border-radius: 0.6rem;
        color: #f4f1ea; font-size: 0.85rem; font-family: 'Vazirmatn', sans-serif;
    }
    .cgp-share-grid input:focus { outline: none; border-color: #c8a862; }
    .cgp-share-grid label { display: block; font-size: 0.75rem; color: #8a8580; margin-bottom: 0.3rem; }
    .cgp-share-url {
        margin-top: 1rem; padding: 0.75rem 1rem;
        background: rgba(0,0,0,0.2); border-radius: 0.5rem;
        direction: ltr; text-align: left; overflow-x: auto;
    }
    .cgp-share-url code { color: #c8a862; font-size: 0.75rem; }
    .cgp-share-btns { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .cgp-share-btns button, .cgp-share-btns a {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.45rem 1rem; border-radius: 9999px; font-size: 0.75rem;
        cursor: pointer; text-decoration: none; transition: all 0.3s ease;
    }
    .cgp-stat-pills { display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .cgp-stat-pill {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.5rem 1rem; background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.08); border-radius: 9999px;
        color: #f4f1ea; font-size: 0.8rem;
    }
    .cgp-stat-pill i { color: #c8a862; }

    .cgp-upload-zone {
        background: rgba(255,255,255,0.03); border: 2px dashed rgba(200,168,98,0.15);
        border-radius: 1rem; padding: 2rem; text-align: center; margin-bottom: 2rem;
    }
    .cgp-upload-zone i { font-size: 2rem; color: #c8a862; margin-bottom: 0.8rem; }
    .cgp-upload-zone p { color: #f4f1ea; font-size: 1rem; margin-bottom: 0.5rem; }
    .cgp-upload-zone span { color: #8a8580; font-size: 0.8rem; }
    .cgp-upload-zone button {
        margin-top: 1rem; padding: 0.7rem 1.8rem;
        background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908;
        border: none; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; cursor: pointer;
    }

    .cgp-images-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem;
    }
    .cgp-image-card {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem; overflow: hidden; position: relative; transition: all 0.3s ease;
    }
    .cgp-image-card:hover { border-color: rgba(200,168,98,0.18); transform: translateY(-3px); }
    .cgp-image-card .favorite-badge {
        position: absolute; top: 0.6rem; left: 0.6rem; z-index: 2;
        width: 28px; height: 28px; border-radius: 50%;
        background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);
        display: flex; align-items: center; justify-content: center;
        color: #f87171; font-size: 0.8rem;
    }
    .cgp-image-card .media-wrap {
        aspect-ratio: 1/1; background: #0a0908; overflow: hidden;
    }
    .cgp-image-card img { width: 100%; height: 100%; object-fit: cover; }
    .cgp-image-card .meta { padding: 1rem; }
    .cgp-image-card .meta input {
        width: 100%; padding: 0.5rem; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(200,168,98,0.08); border-radius: 0.5rem;
        color: #f4f1ea; font-size: 0.8rem; font-family: 'Vazirmatn', sans-serif;
        margin-bottom: 0.75rem;
    }
    .cgp-image-card .meta .actions { display: flex; gap: 0.5rem; }
    .cgp-image-card .meta .actions button {
        flex: 1; padding: 0.4rem; border-radius: 0.5rem; font-size: 0.75rem;
        cursor: pointer; transition: all 0.3s ease;
    }
    .cgp-save-btn { background: rgba(200,168,98,0.08); border: 1px solid rgba(200,168,98,0.15); color: #c8a862; }
    .cgp-delete-btn { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.15); color: #f87171; }
    .cgp-empty {
        text-align: center; padding: 3rem 1rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem;
    }
    .cgp-empty p { color: #8a8580; }
</style>

<div class="cgp-wrap">
    <div class="cgp-header">
        <div>
            <h1><?php echo h($gallery['title']); ?></h1>
            <p><?php echo h($gallery['user_name'] . ' (' . $gallery['user_phone'] . ')'); ?></p>
        </div>
        <div class="cgp-actions">
            <a href="<?php echo url('admin/client-galleries/download?id=' . $gallery['id']); ?>" class="cgp-btn-primary">
                <i class="fas fa-download"></i> دانلود ZIP
            </a>
            <a href="<?php echo url('admin/client-galleries'); ?>" class="cgp-btn-outline">
                <i class="fas fa-arrow-left"></i> بازگشت
            </a>
        </div>
    </div>

    <div class="cgp-stat-pills">
        <div class="cgp-stat-pill"><i class="fas fa-eye"></i> <?php echo number_format($gallery['view_count'] ?? 0); ?> بازدید</div>
        <div class="cgp-stat-pill"><i class="fas fa-images"></i> <?php echo count($images); ?> تصویر</div>
        <?php if (!empty($gallery['share_token'])): ?>
            <div class="cgp-stat-pill"><i class="fas fa-link"></i> لینک فعال</div>
        <?php endif; ?>
        <?php if (!empty($gallery['share_password'])): ?>
            <div class="cgp-stat-pill"><i class="fas fa-lock"></i> محافظت با رمز</div>
        <?php endif; ?>
        <?php if (!empty($gallery['share_expires_at'])): ?>
            <div class="cgp-stat-pill"><i class="fas fa-hourglass-half"></i> انقضا: <?php echo date('Y/m/d', strtotime($gallery['share_expires_at'])); ?></div>
        <?php endif; ?>
    </div>

    <!-- Share Link Box -->
    <div class="cgp-share-box">
        <div class="cgp-share-title"><i class="fas fa-share-nodes" style="color:#c8a862;margin-left:0.5rem;"></i> لینک اشتراک‌گذاری</div>
        <div class="cgp-share-sub">مشتری می‌تواند بدون لاگین این گالری را با این لینک ببیند.</div>

        <div class="cgp-share-grid">
            <div>
                <label>رمز عبور گالری (اختیاری)</label>
                <input type="password" id="sharePassword" placeholder="خالی = بدون رمز">
            </div>
            <div>
                <label>تاریخ انقضا (اختیاری)</label>
                <input type="datetime-local" id="shareExpires" value="<?php echo !empty($gallery['share_expires_at']) ? date('Y-m-d\TH:i', strtotime($gallery['share_expires_at'])) : ''; ?>">
            </div>
            <div style="display:flex;align-items:flex-end;gap:0.5rem;">
                <button type="button" class="share-btn cgp-btn-primary" data-action="<?php echo !empty($gallery['share_token']) ? 'regenerate' : 'enable'; ?>" style="padding:0.6rem 1.2rem;">
                    <i class="fas fa-link"></i> <?php echo !empty($gallery['share_token']) ? 'نوسازی لینک' : 'فعال‌سازی لینک'; ?>
                </button>
            </div>
        </div>

        <div class="cgp-share-btns" id="share-actions">
            <?php if (!empty($gallery['share_token'])): ?>
                <a href="<?php echo url('gallery/share/' . $gallery['share_token']); ?>" target="_blank" class="cgp-btn-outline">
                    <i class="fas fa-external-link-alt"></i> مشاهده
                </a>
                <button type="button" class="share-copy-btn cgp-btn-outline" data-url="<?php echo url('gallery/share/' . $gallery['share_token']); ?>">
                    <i class="fas fa-copy"></i> کپی
                </button>
                <button type="button" class="share-btn cgp-btn-outline" data-action="update">
                    <i class="fas fa-save"></i> ذخیره تنظیمات
                </button>
                <button type="button" class="share-btn cgp-btn-outline" data-action="disable" style="border-color:rgba(239,68,68,0.2);color:#f87171;">
                    <i class="fas fa-unlink"></i> غیرفعال
                </button>
            <?php endif; ?>
        </div>

        <?php if (!empty($gallery['share_token'])): ?>
            <div class="cgp-share-url">
                <code id="share-url"><?php echo url('gallery/share/' . $gallery['share_token']); ?></code>
            </div>
        <?php endif; ?>
    </div>

    <!-- Upload Area -->
    <div class="cgp-upload-zone">
        <i class="fas fa-cloud-upload-alt"></i>
        <p>آپلود تصاویر</p>
        <span>فایل‌های JPG، PNG یا WebP را انتخاب کنید</span>
        <form id="upload-form" enctype="multipart/form-data">
            <input type="hidden" name="gallery_id" value="<?php echo $gallery['id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
            <input type="file" name="images[]" id="images" multiple accept="image/*" style="display:none;">
            <button type="button" id="select-files">
                <i class="fas fa-images"></i> انتخاب فایل‌ها
            </button>
        </form>
    </div>

    <!-- Images Grid -->
    <?php if (!empty($images)): ?>
        <div class="cgp-images-grid">
            <?php foreach ($images as $img): ?>
                <div class="cgp-image-card" data-id="<?php echo $img['id']; ?>">
                    <?php if ($img['is_favorite']): ?>
                        <div class="favorite-badge"><i class="fas fa-heart"></i></div>
                    <?php endif; ?>
                    <div class="media-wrap">
                        <img src="<?php echo h($img['thumbnail'] ?: $img['image']); ?>" alt="">
                    </div>
                    <div class="meta">
                        <input type="text" class="caption-input" data-id="<?php echo $img['id']; ?>" value="<?php echo h($img['caption'] ?? ''); ?>" placeholder="کپشن...">
                        <div class="actions">
                            <button type="button" class="save-caption-btn cgp-save-btn" data-id="<?php echo $img['id']; ?>">
                                <i class="fas fa-save"></i> ذخیره
                            </button>
                            <button type="button" class="delete-image-btn cgp-delete-btn" data-id="<?php echo $img['id']; ?>">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="cgp-empty">
            <p>هنوز تصویری در این گالری آپلود نشده است.</p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var csrfToken = '<?php echo h($csrf_token); ?>';
    var galleryId = '<?php echo $gallery['id']; ?>';
    var apiBase = '<?php echo url('admin/api/'); ?>';

    // Upload
    var selectBtn = document.getElementById('select-files');
    var fileInput = document.getElementById('images');
    if (selectBtn && fileInput) {
        selectBtn.addEventListener('click', function() { fileInput.click(); });
        fileInput.addEventListener('change', function() {
            if (this.files.length === 0) return;
            // FormData را دستی می‌سازیم تا فایل‌ها تکراری ارسال نشوند
            var formData = new FormData();
            formData.append('gallery_id', galleryId);
            formData.append('csrf_token', csrfToken);
            for (var i = 0; i < this.files.length; i++) {
                formData.append('images[]', this.files[i]);
            }
            selectBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> در حال آپلود...';
            selectBtn.disabled = true;
            fetch(apiBase + 'client-gallery-upload', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'تصاویر آپلود شدند', 'success');
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    showToast(data.message || 'خطا در آپلود', 'error');
                    selectBtn.innerHTML = '<i class="fas fa-images"></i> انتخاب فایل‌ها';
                    selectBtn.disabled = false;
                }
            })
            .catch(() => {
                showToast('خطا در ارتباط', 'error');
                selectBtn.innerHTML = '<i class="fas fa-images"></i> انتخاب فایل‌ها';
                selectBtn.disabled = false;
            });
        });
    }

    // Save caption
    document.querySelectorAll('.save-caption-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var caption = document.querySelector('.caption-input[data-id="' + id + '"]').value;
            var formData = new FormData();
            formData.append('image_id', id);
            formData.append('caption', caption);
            formData.append('csrf_token', csrfToken);
            fetch(apiBase + 'client-gallery-update-meta', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                showToast(data.message || 'ذخیره شد', data.success ? 'success' : 'error');
            })
            .catch(() => showToast('خطا در ارتباط', 'error'));
        });
    });

    // Delete image
    document.querySelectorAll('.delete-image-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('آیا از حذف این تصویر مطمئن هستید؟')) return;
            var id = this.dataset.id;
            var formData = new FormData();
            formData.append('image_id', id);
            formData.append('csrf_token', csrfToken);
            fetch(apiBase + 'client-gallery-delete-image', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('تصویر حذف شد', 'success');
                    document.querySelector('.cgp-image-card[data-id="' + id + '"]').remove();
                } else {
                    showToast(data.message || 'خطا در حذف', 'error');
                }
            })
            .catch(() => showToast('خطا در ارتباط', 'error'));
        });
    });

    // Share link management
    var shareActions = document.getElementById('share-actions');
    var sharePassword = document.getElementById('sharePassword');
    var shareExpires = document.getElementById('shareExpires');

    document.querySelectorAll('.share-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var action = this.dataset.action;
            var originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            var formData = new FormData();
            formData.append('gallery_id', galleryId);
            formData.append('share_action', action);
            formData.append('csrf_token', csrfToken);
            if (sharePassword) formData.append('share_password', sharePassword.value);
            if (shareExpires) formData.append('share_expires_at', shareExpires.value);

            fetch(apiBase + 'client-gallery-share', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message + (action === 'enable' || action === 'regenerate' ? ' — ایمیل به مشتری ارسال شد' : ''), 'success');
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    showToast(data.message || 'خطا', 'error');
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            })
            .catch(() => {
                showToast('خطا در ارتباط', 'error');
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    });

    document.querySelectorAll('.share-copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var url = this.dataset.url;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast('لینک کپی شد', 'success');
                }).catch(function() { fallbackCopy(url); });
            } else {
                fallbackCopy(url);
            }
        });
    });

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        showToast('لینک کپی شد', 'success');
    }
});
</script>
