<?php
// views/admin/client-gallery-form.php
$gallery = $gallery ?? null;
$data = $data ?? [];
$errors = $errors ?? [];
$mode = $mode ?? 'create';
$users = $data['users'] ?? [];
$csrf_token = generate_csrf_token();
?>
<style>
    .cgf-wrap { max-width:700px; margin:0 auto; padding:2rem 1.5rem; }
    .cgf-wrap h1 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2.2rem; font-weight: 300; color: #f4f1ea; margin-bottom: 1.5rem;
    }
    .cgf-errors {
        background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.15);
        color: #f87171; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;
    }
    .cgf-form {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem; padding: 2rem;
    }
    .cgf-group { margin-bottom: 1.25rem; }
    .cgf-group label { display: block; font-size: 0.8rem; color: #8a8580; margin-bottom: 0.4rem; }
    .cgf-group input,
    .cgf-group select,
    .cgf-group textarea {
        width: 100%; padding: 0.7rem 1rem; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(200,168,98,0.08); border-radius: 0.6rem;
        color: #f4f1ea; font-size: 0.9rem; font-family: 'Vazirmatn', sans-serif;
        transition: all 0.25s ease;
    }
    .cgf-group input:focus,
    .cgf-group select:focus,
    .cgf-group textarea:focus {
        outline: none; border-color: #c8a862;
        box-shadow: 0 0 0 3px rgba(200,168,98,0.06);
    }
    .cgf-user-search {
        margin-bottom: 0.5rem;
    }
    .cgf-user-search input {
        width: 100%; padding: 0.6rem 1rem; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(200,168,98,0.12); border-radius: 9999px;
        color: #f4f1ea; font-size: 0.85rem; font-family: 'Vazirmatn', sans-serif;
    }
    .cgf-user-search input:focus { outline: none; border-color: #c8a862; }
    .cgf-actions { display: flex; gap: 0.75rem; margin-top: 0.5rem; }
    .cgf-actions a {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.7rem 1.6rem; border: 1px solid rgba(200,168,98,0.2);
        border-radius: 9999px; color: #f4f1ea; font-size: 0.8rem; font-weight: 600;
        text-decoration: none; transition: all 0.3s ease;
    }
    .cgf-actions button {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.7rem 1.6rem; background: linear-gradient(135deg, #c8a862, #a8893a);
        color: #0a0908; border: none; border-radius: 9999px;
        font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;
    }
</style>

<div class="cgf-wrap">
    <h1><?php echo $mode === 'create' ? 'گالری جدید مشتری' : 'ویرایش گالری مشتری'; ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="cgf-errors">
            <?php foreach ($errors as $error): ?>
                <div style="margin-bottom:0.3rem;"><i class="fas fa-exclamation-circle" style="margin-left:0.4rem;"></i> <?php echo h($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="cgf-form">
        <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">

        <?php if ($mode === 'create'): ?>
            <div class="cgf-group">
                <label>کاربر</label>
                <div class="cgf-user-search">
                    <input type="text" id="userSearch" placeholder="جستجو نام یا موبایل...">
                </div>
                <select name="user_id" id="userSelect" required>
                    <option value="">انتخاب کاربر</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>" data-search="<?php echo h(mb_strtolower($u['name'] . ' ' . $u['phone'])); ?>" <?php echo ($data['user_id'] ?? '') == $u['id'] ? 'selected' : ''; ?>>
                            <?php echo h($u['name'] . ' (' . $u['phone'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="cgf-group">
                <label>شناسه رزرو (اختیاری)</label>
                <input type="number" name="booking_id" value="<?php echo h($data['booking_id'] ?? ''); ?>" placeholder="مثال: 12">
            </div>
        <?php endif; ?>

        <div class="cgf-group">
            <label>عنوان گالری</label>
            <input type="text" name="title" required value="<?php echo h($data['title'] ?? ''); ?>" placeholder="مثال: عکس‌های پرتره اسفند ۱۴۰۳">
        </div>

        <div class="cgf-group">
            <label>توضیحات</label>
            <textarea name="description" rows="4" placeholder="توضیحات گالری..."><?php echo h($data['description'] ?? ''); ?></textarea>
        </div>

        <div class="cgf-group">
            <label>حداکثر انتخاب نهایی مشتری (۰ = غیرفعال)</label>
            <input type="number" name="max_selections" min="0" value="<?php echo h($data['max_selections'] ?? 0); ?>" placeholder="مثال: 10">
            <div style="font-size:0.7rem;color:#8a8580;margin-top:0.3rem;">اگر ۰ باشد، دکمه انتخاب نهایی در داشبورد کاربر نمایش داده نمی‌شود.</div>
        </div>

        <div class="cgf-group">
            <label style="display:inline-flex;align-items:center;gap:0.5rem;color:#f4f1ea;font-size:0.9rem;cursor:pointer;">
                <input type="checkbox" name="status" value="1" <?php echo ($data['status'] ?? 1) ? 'checked' : ''; ?> style="accent-color:#c8a862;">
                <span>گالری فعال باشد</span>
            </label>
        </div>

        <div class="cgf-actions">
            <a href="<?php echo url('admin/client-galleries'); ?>">بازگشت</a>
            <button type="submit">
                <i class="fas fa-save"></i> ذخیره
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('userSearch');
    var select = document.getElementById('userSelect');
    if (searchInput && select) {
        searchInput.addEventListener('input', function() {
            var term = this.value.trim().toLowerCase();
            var options = select.querySelectorAll('option');
            options.forEach(function(opt) {
                if (opt.value === '') return;
                var text = opt.dataset.search || '';
                opt.style.display = (!term || text.indexOf(term) !== -1) ? '' : 'none';
            });
        });
    }
});
</script>
