<?php
// views/auth/profile.php - User Profile & Change Password
$user = $user ?? [];
$success = $success ?? '';
$error = $error ?? '';
?>
<style>
    .profile-wrap { max-width: 700px; margin: 0 auto; padding: 4rem 1.5rem; }
    .profile-header {
        display: flex; align-items: center; gap: 1.25rem;
        margin-bottom: 2rem; padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(200,168,98,0.08);
    }
    .profile-avatar-wrap { position: relative; flex-shrink: 0; }
    .profile-avatar {
        width: 80px; height: 80px; border-radius: 50%;
        background: linear-gradient(135deg, #c8a862, #a8893a);
        display: flex; align-items: center; justify-content: center;
        color: #0a0908; font-size: 2rem; font-family: 'Cormorant Garamond', serif;
        box-shadow: 0 8px 30px rgba(200,168,98,0.15);
        overflow: hidden;
    }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-form { position: absolute; bottom: -2px; left: -2px; }
    .avatar-upload-btn {
        width: 30px; height: 30px; border-radius: 50%;
        background: rgba(10,9,8,0.85); border: 1px solid rgba(200,168,98,0.3);
        color: #c8a862; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s ease; font-size: 0.75rem;
    }
    .avatar-upload-btn:hover { background: #c8a862; color: #0a0908; border-color: #c8a862; }
    .avatar-upload-btn input { display: none; }
    .profile-title h1 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2rem; font-weight: 300; color: #f4f1ea; margin-bottom: 0.3rem;
    }
    .profile-header p { color: #8a8580; font-size: 0.85rem; }
    .profile-card {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;
    }
    .profile-card h2 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.3rem; font-weight: 300; color: #f4f1ea; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 0.6rem;
    }
    .profile-card h2 i { color: #c8a862; font-size: 1.1rem; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-size: 0.8rem; color: #8a8580; margin-bottom: 0.4rem; }
    .form-group input {
        width: 100%; padding: 0.8rem 1rem;
        background: rgba(255,255,255,0.04); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 0.8rem; color: #f4f1ea; font-size: 0.9rem;
        font-family: 'Vazirmatn', sans-serif; transition: all 0.3s ease; outline: none;
    }
    .form-group input:focus { border-color: #c8a862; background: rgba(255,255,255,0.06); }
    .form-group input[readonly] { opacity: 0.6; cursor: not-allowed; }
    .form-group .hint { font-size: 0.75rem; color: #8a8580; margin-top: 0.3rem; }
    .form-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; }
    .form-actions a, .form-actions button {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.7rem 1.6rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600;
        transition: all 0.3s ease; text-decoration: none; border: none; cursor: pointer;
    }
    .btn-save {
        background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,168,98,0.25); }
    .btn-back {
        background: transparent; color: #f4f1ea; border: 1px solid rgba(200,168,98,0.2);
    }
    .btn-back:hover { border-color: #c8a862; color: #c8a862; }
    .alert {
        padding: 0.9rem 1.2rem; border-radius: 0.8rem; margin-bottom: 1.5rem;
        font-size: 0.85rem; display: flex; align-items: center; gap: 0.6rem;
    }
    .alert-success { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); color: #4ade80; }
    .alert-error { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #f87171; }
    .divider { height: 1px; background: rgba(200,168,98,0.08); margin: 2rem 0; }
</style>

<div class="profile-wrap">
    <div class="profile-header">
        <div class="profile-avatar-wrap">
            <div class="profile-avatar">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo h($user['avatar']); ?>" alt="" loading="lazy">
                <?php else: ?>
                    <?php echo mb_substr($user['name'], 0, 1, 'UTF-8'); ?>
                <?php endif; ?>
            </div>
            <form method="POST" action="<?php echo url('profile/update'); ?>" enctype="multipart/form-data" class="avatar-form">
                <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
                <input type="hidden" name="action" value="avatar">
                <label class="avatar-upload-btn">
                    <i class="fas fa-camera"></i>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" onchange="this.form.submit()">
                </label>
            </form>
        </div>
        <div class="profile-title">
            <h1>پروفایل کاربری</h1>
            <p>اطلاعات شخصی و رمز عبور خود را مدیریت کنید.</p>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo h($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo h($error); ?></div>
    <?php endif; ?>

    <div class="profile-card">
        <h2><i class="fas fa-user-edit"></i> اطلاعات شخصی</h2>
        <form method="POST" action="<?php echo url('profile/update'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
            <input type="hidden" name="action" value="info">

            <div class="form-group">
                <label>شماره موبایل</label>
                <input type="tel" value="<?php echo h($user['phone']); ?>" readonly>
                <div class="hint">شماره موبایل قابل تغییر نیست.</div>
            </div>

            <div class="form-group">
                <label>نام و نام خانوادگی</label>
                <input type="text" name="name" value="<?php echo h($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>ایمیل</label>
                <input type="email" name="email" value="<?php echo h($user['email'] ?? ''); ?>" placeholder="email@example.com">
                <div class="hint">اختیاری.</div>
            </div>

            <div class="form-actions">
                <a href="<?php echo url('dashboard'); ?>" class="btn-back">بازگشت</a>
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> ذخیره</button>
            </div>
        </form>
    </div>

    <div class="profile-card">
        <h2><i class="fas fa-lock"></i> تغییر رمز عبور</h2>
        <form method="POST" action="<?php echo url('profile/update'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
            <input type="hidden" name="action" value="password">

            <div class="form-group">
                <label>رمز عبور فعلی</label>
                <input type="password" name="current_password" required placeholder="رمز عبور فعلی">
            </div>

            <div class="form-group">
                <label>رمز عبور جدید</label>
                <input type="password" name="new_password" required placeholder="حداقل ۶ کاراکتر">
            </div>

            <div class="form-group">
                <label>تکرار رمز عبور جدید</label>
                <input type="password" name="confirm_password" required placeholder="تکرار رمز عبور جدید">
            </div>

            <div class="form-actions">
                <a href="<?php echo url('dashboard'); ?>" class="btn-back">بازگشت</a>
                <button type="submit" class="btn-save"><i class="fas fa-key"></i> تغییر رمز</button>
            </div>
        </form>
    </div>
</div>
