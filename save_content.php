<?php
// save_content.php - ذخیره محتوا بدون AJAX (legacy debug helper)
// Access is restricted to logged-in admin users.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/SiteContent.php';

if (!is_logged_in()) {
    http_response_code(403);
    echo '<p style="text-align:center;padding:2rem;color:#fff;background:#0a0908;">دسترسی غیرمجاز. <a href="' . url('admin/login') . '">ورود</a></p>';
    exit;
}

$message = '';
$content = new SiteContent();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = $_POST['key'] ?? '';
    $value = $_POST['value'] ?? '';
    
    if (!empty($key)) {
        try {
            $content->set($key, $value);
            $message = "✅ محتوا با موفقیت ذخیره شد!";
        } catch (Exception $e) {
            $message = "❌ خطا: " . $e->getMessage();
        }
    }
}

// دریافت همه محتوا
$allContents = $content->getAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ذخیره محتوا</title>
    <style>
        body { background: #0a0908; color: #f4f1ea; font-family: Vazirmatn, sans-serif; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.1); border-radius: 1rem; padding: 2rem; margin-bottom: 1rem; }
        input, button { width: 100%; padding: 0.8rem; margin: 0.5rem 0; border-radius: 0.5rem; border: 1px solid rgba(200,168,98,0.15); background: rgba(255,255,255,0.05); color: #f4f1ea; font-size: 1rem; }
        button { background: #c8a862; color: #0a0908; font-weight: 600; cursor: pointer; border: none; }
        button:hover { background: #b8944a; }
        .message { padding: 1rem; border-radius: 0.5rem; margin: 1rem 0; }
        .success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #4ade80; }
        .error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171; }
        .content-item { padding: 0.5rem 0; border-bottom: 1px solid rgba(200,168,98,0.05); }
        .content-item strong { color: #c8a862; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 ذخیره محتوا</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST">
                <div>
                    <label>کلید (key):</label>
                    <input type="text" name="key" placeholder="مثال: home.eyebrow" value="home.eyebrow">
                </div>
                <div>
                    <label>مقدار (value):</label>
                    <input type="text" name="value" placeholder="مقدار جدید">
                </div>
                <button type="submit">💾 ذخیره</button>
            </form>
        </div>
        
        <div class="card">
            <h2>📋 لیست محتوا</h2>
            <?php foreach ($allContents as $key => $value): ?>
                <div class="content-item">
                    <strong><?php echo htmlspecialchars($key); ?>:</strong>
                    <?php echo htmlspecialchars($value); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>