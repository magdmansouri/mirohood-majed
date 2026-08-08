<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنظیم رمز عبور جدید | Mirohood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@300;400;600&display=swap" as="style" onload="this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@300;400;600&display=swap" rel="stylesheet"></noscript>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/assets/images/admin-icon-192.png">
    <meta name="theme-color" content="#0a0908">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Mirohood">
    <script src="/assets/js/security.js" defer></script>
    <script src="/assets/js/pwa-install.js" defer></script>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: #0a0908;
            color: #f4f1ea;
            font-family: 'Vazirmatn', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
        }
        .auth-box {
            width: 400px;
            max-width: 100%;
            background: rgba(20,18,16,0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(200,168,98,0.08);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }
        .auth-box .logo {
            text-align: center;
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 2rem;
            font-weight: 300;
            margin-bottom: 0.2rem;
        }
        .auth-box .logo span { color: #c8a862; }
        .auth-box .subtitle {
            text-align: center;
            color: #8a8580;
            font-size: 0.75rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }
        .auth-box .error {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.15);
            color: #f87171;
            padding: 0.6rem 1rem;
            border-radius: 0.6rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
        }
        .auth-box .success {
            background: rgba(34,197,94,0.08);
            border: 1px solid rgba(34,197,94,0.15);
            color: #4ade80;
            padding: 0.6rem 1rem;
            border-radius: 0.6rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            line-height: 1.5;
        }
        .auth-box label {
            display: block;
            font-size: 0.75rem;
            color: #8a8580;
            margin-bottom: 0.3rem;
        }
        .auth-box input {
            width: 100%;
            padding: 0.7rem 1rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(200,168,98,0.06);
            border-radius: 0.6rem;
            color: #f4f1ea;
            font-size: 0.9rem;
            font-family: 'Vazirmatn', sans-serif;
            transition: all 0.3s ease;
            margin-bottom: 1.2rem;
        }
        .auth-box input:focus {
            outline: none;
            border-color: #c8a862;
            box-shadow: 0 0 0 3px rgba(200,168,98,0.05);
            background: rgba(255,255,255,0.05);
        }
        .auth-box button {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #c8a862, #a8893a);
            border: none;
            border-radius: 9999px;
            color: #0a0908;
            font-family: 'Vazirmatn', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .auth-box button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(200,168,98,0.15);
        }
        .auth-box .footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #8a8580;
        }
        .auth-box .footer a { color: #c8a862; text-decoration: none; }
        .auth-box .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body class="auth-page">
    <div class="auth-bg"></div>
    <div class="auth-box">
        <div class="logo">Miro<span>hood</span></div>
        <div class="subtitle">تنظیم رمز عبور جدید</div>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (empty($success) && empty($error)): ?>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
                <input type="hidden" name="token" value="<?php echo h($token); ?>">

                <label>رمز عبور جدید</label>
                <input type="password" name="password" placeholder="رمز عبور جدید" required minlength="6">

                <label>تکرار رمز عبور جدید</label>
                <input type="password" name="password_confirm" placeholder="تکرار رمز عبور جدید" required minlength="6">

                <button type="submit">
                    <i class="fas fa-lock" style="margin-left:0.4rem;"></i>
                    تغییر رمز عبور
                </button>
            </form>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="footer">
                <a href="<?php echo url('login'); ?>">ورود به حساب کاربری</a>
            </div>
        <?php else: ?>
            <div class="footer">
                <a href="<?php echo url('forgot-password'); ?>">درخواست لینک جدید</a>
            </div>
        <?php endif; ?>
    </div>
    <script>
    document.querySelector('form').addEventListener('submit', function() {
        var btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> در حال تغییر...';
    });
    </script>
</body>
</html>