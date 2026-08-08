<?php
// views/admin/gallery.php - People management
$people = $people ?? [];
$csrf_token = generate_csrf_token();
?>

<h1 class="admin-page-title">مدیریت گالری</h1>
<p class="admin-page-subtitle">افراد و آلبوم‌های تصاویر</p>

<div style="display:flex;justify-content:flex-end;margin-bottom:1.5rem;">
    <a href="<?php echo url('admin/gallery/create'); ?>" class="admin-btn">
        <i class="fas fa-plus"></i>
        افزودن شخص جدید
    </a>
</div>

<?php if (!empty($people)): ?>
    <div class="people-admin-grid">
        <?php foreach ($people as $p): ?>
            <div class="people-admin-card" id="person-<?php echo (int) $p['id']; ?>">
                <div class="people-admin-media">
                    <?php if (!empty($p['avatar'])): ?>
                        <img src="<?php echo h($p['avatar']); ?>" alt="<?php echo h($p['name']); ?>">
                    <?php else: ?>
                        <div class="people-admin-initial"><?php echo h(mb_substr($p['name'], 0, 1, 'UTF-8')); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($p['featured'])): ?>
                        <span class="people-admin-badge featured">ویژه</span>
                    <?php endif; ?>
                    <?php if (empty($p['status'])): ?>
                        <span class="people-admin-badge inactive">غیرفعال</span>
                    <?php endif; ?>
                </div>
                <div class="people-admin-info">
                    <h3 class="people-admin-name"><?php echo h($p['name']); ?></h3>
                    <p class="people-admin-count"><?php echo (int) ($p['photo_count'] ?? 0); ?> تصویر</p>
                    <div class="people-admin-actions">
                        <a href="<?php echo url('admin/gallery/photos?slug=' . h($p['slug'])); ?>" class="admin-btn admin-btn-small" title="مدیریت تصاویر">
                            <i class="fas fa-images"></i>
                            تصاویر
                        </a>
                        <a href="<?php echo url('admin/gallery/edit?id=' . (int) $p['id']); ?>" class="admin-btn admin-btn-small admin-btn-outline" title="ویرایش">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button type="button" class="admin-btn admin-btn-small admin-btn-outline admin-btn-danger"
                                onclick="deletePerson(<?php echo (int) $p['id']; ?>)" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="admin-card" style="text-align:center;padding:3rem;">
        <p style="color:var(--color-muted);">هنوز شخصی ایجاد نشده است.</p>
        <a href="<?php echo url('admin/gallery/create'); ?>" class="admin-btn" style="margin-top:1rem;">
            <i class="fas fa-plus"></i>
            افزودن اولین شخص
        </a>
    </div>
<?php endif; ?>

<script>
function deletePerson(id) {
    if (!confirm('آیا از حذف این شخص و تمام تصاویر آن اطمینان دارید؟')) return;

    var fd = new FormData();
    fd.append('id', id);
    fd.append('csrf_token', '<?php echo $csrf_token; ?>');

    fetch('<?php echo url('admin/api/delete-person'); ?>', {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('✅ شخص حذف شد', 'success');
            var el = document.getElementById('person-' + id);
            if (el) el.remove();
        } else {
            showToast('❌ ' + data.message, 'error');
        }
    })
    .catch(function() {
        showToast('❌ خطای ارتباط', 'error');
    });
}
</script>
