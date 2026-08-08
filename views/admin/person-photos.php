<?php
// views/admin/person-photos.php - Manage photos for a person
$person = $person ?? [];
$images = $images ?? [];
$csrf_token = generate_csrf_token();
?>

<h1 class="admin-page-title">مدیریت تصاویر <?php echo h($person['name'] ?? ''); ?></h1>
<p class="admin-page-subtitle">آپلود، ویرایش و مرتب‌سازی تصاویر</p>

<div style="display:flex;gap:0.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="<?php echo url('admin/gallery'); ?>" class="admin-btn admin-btn-outline">
        <i class="fas fa-arrow-right"></i>
        بازگشت به گالری
    </a>
    <a href="<?php echo url('gallery/' . h($person['slug'])); ?>" target="_blank" class="admin-btn admin-btn-outline">
        <i class="fas fa-eye"></i>
        مشاهده در سایت
    </a>
</div>

<!-- Upload zone -->
<div class="admin-card upload-zone" id="uploadZone">
    <input type="file" id="fileInput" name="images" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="upload-input">
    <div class="upload-zone-content">
        <i class="fas fa-cloud-arrow-up"></i>
        <p>فایل‌ها را اینجا رها کنید یا کلیک کنید</p>
        <span>JPEG, PNG, WebP, GIF — حداکثر هر فایل ۱۰ مگابایت</span>
    </div>
    <div class="upload-progress" id="uploadProgress"></div>
</div>

<!-- Photo grid -->
<?php if (!empty($images)): ?>
    <div class="admin-card">
        <p class="admin-page-subtitle" style="margin-bottom:1rem;">برای مرتب‌سازی تصاویر را جابجا کنید. روی تصویر کلیک کنید تا اطلاعات ویرایش شود.</p>
        <div class="photos-admin-grid" id="photosGrid">
            <?php foreach ($images as $img): ?>
                <div class="photo-admin-item" data-id="<?php echo (int) $img['id']; ?>">
                    <img src="<?php echo h($img['thumbnail'] ?? $img['image']); ?>" alt="<?php echo h($img['alt'] ?? ''); ?>">
                    <div class="photo-admin-overlay">
                        <button type="button" class="photo-admin-btn" onclick="editPhoto(<?php echo (int) $img['id']; ?>)" title="ویرایش">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button type="button" class="photo-admin-btn danger" onclick="deletePhoto(<?php echo (int) $img['id']; ?>)" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="photo-admin-caption" id="caption-<?php echo (int) $img['id']; ?>">
                        <?php echo h($img['caption'] ?? ''); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="saveOrderBtn" class="admin-btn" style="margin-top:1.5rem;display:none;">
            <i class="fas fa-check"></i>
            ذخیره ترتیب
        </button>
    </div>
<?php else: ?>
    <div class="admin-card" style="text-align:center;padding:3rem;" id="emptyPhotos">
        <p style="color:var(--color-muted);">هنوز تصویری آپلود نشده است.</p>
    </div>
<?php endif; ?>

<!-- Photo edit modal -->
<div class="modal" id="photoModal" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <h3 class="admin-card-title">ویرایش تصویر</h3>
        <form id="photoEditForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="image_id" id="editPhotoId">
            <div class="form-group">
                <label class="admin-label">کپشن</label>
                <input type="text" name="caption" class="admin-input" id="editCaption">
            </div>
            <div class="form-group">
                <label class="admin-label">متن جایگزین (Alt)</label>
                <input type="text" name="alt" class="admin-input" id="editAlt">
            </div>
            <div class="form-group">
                <label class="admin-label">عنوان SEO</label>
                <input type="text" name="seo_title" class="admin-input" id="editSeoTitle">
            </div>
            <div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" class="admin-btn admin-btn-outline" onclick="closeModal()">انصراف</button>
                <button type="submit" class="admin-btn">ذخیره</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var personId = <?php echo (int) ($person['id'] ?? 0); ?>;
    var csrfToken = '<?php echo $csrf_token; ?>';
    var uploadUrl = '<?php echo url('admin/api/upload-gallery'); ?>';
    var deleteUrl = '<?php echo url('admin/api/delete-gallery'); ?>';
    var metaUrl = '<?php echo url('admin/api/update-photo-meta'); ?>';
    var reorderUrl = '<?php echo url('admin/api/reorder-photos'); ?>';

    var grid = document.getElementById('photosGrid');
    var saveOrderBtn = document.getElementById('saveOrderBtn');
    var modal = document.getElementById('photoModal');
    var draggedItem = null;

    // Drag & drop upload
    var uploadZone = document.getElementById('uploadZone');
    var fileInput = document.getElementById('fileInput');
    var uploadProgress = document.getElementById('uploadProgress');

    if (uploadZone) {
        uploadZone.addEventListener('click', function() { fileInput.click(); });
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });
        uploadZone.addEventListener('dragleave', function() {
            uploadZone.classList.remove('dragover');
        });
        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                uploadFiles(e.dataTransfer.files);
            }
        });
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length) uploadFiles(fileInput.files);
        });
    }

    function uploadFiles(files) {
        var fd = new FormData();
        fd.append('person_id', personId);
        fd.append('csrf_token', csrfToken);
        for (var i = 0; i < files.length; i++) {
            fd.append('images[]', files[i]);
        }

        uploadProgress.innerHTML = '<div class="progress-bar"><div class="progress-fill"></div></div>';
        var fill = uploadProgress.querySelector('.progress-fill');
        fill.style.width = '40%';

        fetch(uploadUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                fill.style.width = '100%';
                setTimeout(function() { uploadProgress.innerHTML = ''; }, 400);
                if (data.success) {
                    showToast(data.message, 'success');
                    location.reload();
                } else {
                    showToast(data.message || 'خطا در آپلود', 'error');
                }
            })
            .catch(function() {
                uploadProgress.innerHTML = '';
                showToast('❌ خطای ارتباط در آپلود', 'error');
            });
    }

    // Reorder
    if (grid) {
        grid.querySelectorAll('.photo-admin-item').forEach(function(item) {
            item.draggable = true;
            item.addEventListener('dragstart', function(e) {
                draggedItem = item;
                item.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });
            item.addEventListener('dragend', function() {
                item.classList.remove('dragging');
                draggedItem = null;
                saveOrderBtn.style.display = 'inline-flex';
            });
            item.addEventListener('dragover', function(e) {
                e.preventDefault();
                if (draggedItem === item) return;
                var rect = item.getBoundingClientRect();
                var midpoint = rect.left + rect.width / 2;
                if (e.clientX < midpoint) {
                    grid.insertBefore(draggedItem, item);
                } else {
                    grid.insertBefore(draggedItem, item.nextSibling);
                }
            });
        });
    }

    if (saveOrderBtn) {
        saveOrderBtn.addEventListener('click', function() {
            var ids = Array.from(grid.querySelectorAll('.photo-admin-item')).map(function(el) {
                return el.getAttribute('data-id');
            });
            var fd = new FormData();
            fd.append('person_id', personId);
            fd.append('csrf_token', csrfToken);
            ids.forEach(function(id) { fd.append('order[]', id); });

            fetch(reorderUrl, { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    showToast(data.message, data.success ? 'success' : 'error');
                    if (data.success) saveOrderBtn.style.display = 'none';
                })
                .catch(function() { showToast('❌ خطای ارتباط', 'error'); });
        });
    }

    // Delete
    window.deletePhoto = function(id) {
        if (!confirm('این تصویر حذف شود؟')) return;
        var fd = new FormData();
        fd.append('image_id', id);
        fd.append('csrf_token', csrfToken);
        fetch(deleteUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                showToast(data.message, data.success ? 'success' : 'error');
                if (data.success) location.reload();
            })
            .catch(function() { showToast('❌ خطای ارتباط', 'error'); });
    };

    // Edit modal
    window.editPhoto = function(id) {
        var item = grid.querySelector('.photo-admin-item[data-id="' + id + '"]');
        var caption = item.querySelector('.photo-admin-caption').textContent.trim();
        document.getElementById('editPhotoId').value = id;
        document.getElementById('editCaption').value = caption;
        document.getElementById('editAlt').value = item.querySelector('img').getAttribute('alt') || '';
        document.getElementById('editSeoTitle').value = '';
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
    };

    window.closeModal = function() {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
    };

    if (modal) {
        modal.querySelector('.modal-backdrop').addEventListener('click', closeModal);
    }

    document.getElementById('photoEditForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        fetch(metaUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                showToast(data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    var id = document.getElementById('editPhotoId').value;
                    var caption = document.getElementById('editCaption').value;
                    document.getElementById('caption-' + id).textContent = caption;
                    closeModal();
                }
            })
            .catch(function() { showToast('❌ خطای ارتباط', 'error'); });
    });
})();
</script>
