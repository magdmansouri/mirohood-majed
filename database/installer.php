<?php
/**
 * One-time guarded database installer.
 * 1) Add INSTALLER_KEY to config/config.php.
 * 2) Open /database/installer.php, enter the same key and run it.
 * 3) Delete this file immediately after a successful install.
 */
require_once __DIR__ . '/../config/config.php';

$installerKey = defined('INSTALLER_KEY') ? INSTALLER_KEY : '';
$submittedKey = isset($_POST['key']) ? (string) $_POST['key'] : '';
$allowed = $installerKey !== '' && hash_equals($installerKey, $submittedKey);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $allowed) {
    // install.php has its own connection handling and prints a plain-text report.
    require __DIR__ . '/install.php';
    exit;
}

http_response_code($_SERVER['REQUEST_METHOD'] === 'POST' ? 403 : 200);
?><!doctype html>
<html lang="fa" dir="rtl">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>نصب دیتابیس</title>
<style>body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#0a0908;color:#f4f1ea;font-family:Tahoma,Arial,sans-serif}.box{max-width:520px;padding:32px;border:1px solid rgba(200,168,98,.25);border-radius:18px;background:#151413}.note{color:#aaa29a;line-height:2;font-size:14px}input,button{box-sizing:border-box;width:100%;padding:13px;border-radius:10px;font:inherit}input{background:#0a0908;color:#fff;border:1px solid rgba(200,168,98,.3);margin:12px 0}button{border:0;background:#c8a862;color:#0a0908;font-weight:bold;cursor:pointer}code{color:#d8bd7b;direction:ltr;display:inline-block}</style></head>
<body><main class="box"><h1>نصب یک‌باره دیتابیس</h1>
<?php if ($installerKey === ''): ?>
<p class="note">برای امنیت، ابتدا در <code>config/config.php</code> یک کلید موقت تعریف کنید:<br><code>define('INSTALLER_KEY', 'یک-کلید-طولانی-و-تصادفی');</code><br>سپس همین صفحه را باز و آن کلید را وارد کنید.</p>
<?php else: ?>
<p class="note">این عملیات جدول‌های موردنیاز گالری، رزرو، کاربران، محتوا و اعلان‌ها را ایجاد یا به‌روزرسانی می‌کند. پیش از اجرا از دیتابیس دارای اطلاعات، نسخه پشتیبان بگیرید.</p>
<form method="post"><label>کلید نصب<input type="password" name="key" required autofocus></label><button type="submit">اجرای نصب دیتابیس</button></form>
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?><p class="note" style="color:#ffaaa0">کلید واردشده صحیح نیست.</p><?php endif; ?>
<?php endif; ?>
</main></body></html>
