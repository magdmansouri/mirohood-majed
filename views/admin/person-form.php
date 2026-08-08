<?php
// views/admin/person-form.php
$mode = $mode ?? 'create';
$person = $person ?? null;
$data = $data ?? [];
$errors = $errors ?? [];
$csrf_token = generate_csrf_token();
$isEdit = $mode === 'edit';

$fields = [
    'name' => $data['name'] ?? '',
    'slug' => $data['slug'] ?? '',
    'bio' => $data['bio'] ?? '',
    'instagram' => $data['instagram'] ?? '',
    'website' => $data['website'] ?? '',
    'featured' => (bool) ($data['featured'] ?? false),
    'status' => (bool) ($data['status'] ?? true),
    'sort_order' => (int) ($data['sort_order'] ?? 0)
];
?>

<h1 class="admin-page-title"><?php echo $isEdit ? 'ویرایش ' . h($person['name'] ?? '') : 'افزودن شخص جدید'; ?></h1>
<p class="admin-page-subtitle">اطلاعات شخص و نمایه گالری</p>

<?php if (!empty($errors)): ?>
    <div class="admin-card" style="border-color:var(--danger);margin-bottom:1.5rem;">
        <p style="color:var(--danger);font-size:0.85rem;margin-bottom:0.5rem;"><i class="fas fa-exclamation-circle"></i> لطفاً خطاها را برطرف کنید:</p>
        <ul style="color:var(--danger);font-size:0.8rem;padding-right:1.2rem;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo h($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-card">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

    <div class="person-form-grid">
        <div class="person-form-col">
            <div class="form-group">
                <label class="admin-label" for="name">نام شخص</label>
                <input type="text" id="name" name="name" class="admin-input" value="<?php echo h($fields['name']); ?>" required>
            </div>

            <div class="form-group">
                <label class="admin-label" for="slug">اسلاگ (URL)</label>
                <input type="text" id="slug" name="slug" class="admin-input" value="<?php echo h($fields['slug']); ?>" required>
                <p class="form-hint">مثال: gallery/person-name</p>
            </div>

            <div class="form-group">
                <label class="admin-label" for="bio">بیوگرافی</label>
                <textarea id="bio" name="bio" class="admin-textarea" rows="5"><?php echo h($fields['bio']); ?></textarea>
            </div>
        </div>

        <div class="person-form-col">
            <div class="form-group">
                <label class="admin-label" for="avatar">آواتار</label>
                <input type="file" id="avatar" name="avatar" class="admin-input" accept="image/*">
                <?php if (!empty($person['avatar'])): ?>
                    <div class="preview-thumb">
                        <img src="<?php echo h($person['avatar']); ?>" alt="Current avatar">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="admin-label" for="cover">کاور</label>
                <input type="file" id="cover" name="cover" class="admin-input" accept="image/*">
                <?php if (!empty($person['cover'])): ?>
                    <div class="preview-thumb">
                        <img src="<?php echo h($person['cover']); ?>" alt="Current cover">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="person-form-col">
            <div class="form-group">
                <label class="admin-label" for="instagram">اینستاگرام</label>
                <input type="text" id="instagram" name="instagram" class="admin-input" value="<?php echo h($fields['instagram']); ?>" placeholder="@username">
            </div>

            <div class="form-group">
                <label class="admin-label" for="website">وب‌سایت</label>
                <input type="url" id="website" name="website" class="admin-input" value="<?php echo h($fields['website']); ?>" placeholder="https://example.com">
            </div>

            <div class="form-group">
                <label class="admin-label" for="sort_order">ترتیب نمایش</label>
                <input type="number" id="sort_order" name="sort_order" class="admin-input" value="<?php echo (int) $fields['sort_order']; ?>">
            </div>

            <div class="form-group check-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="featured" <?php echo $fields['featured'] ? 'checked' : ''; ?>>
                    <span>ویژه</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="status" <?php echo $fields['status'] ? 'checked' : ''; ?>>
                    <span>فعال</span>
                </label>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
        <button type="submit" class="admin-btn">
            <i class="fas fa-save"></i>
            <?php echo $isEdit ? 'ذخیره تغییرات' : 'ایجاد شخص'; ?>
        </button>
        <a href="<?php echo url('admin/gallery'); ?>" class="admin-btn admin-btn-outline">انصراف</a>
    </div>
</form>

<script>
(function() {
    var nameInput = document.getElementById('name');
    var slugInput = document.getElementById('slug');
    if (nameInput && slugInput) {
        nameInput.addEventListener('blur', function() {
            if (!slugInput.value.trim()) {
                slugInput.value = nameInput.value.trim().toLowerCase()
                    .replace(/[^\p{L}\p{N}\s-]/gu, '')
                    .replace(/[\s_]+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });
    }
})();
</script>
