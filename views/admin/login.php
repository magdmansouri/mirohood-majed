<?php
// views/admin/login.php - ورود
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود مدیریت | Mirohood</title>
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
            background-image: radial-gradient(ellipse at center, rgba(200,168,98,0.03), transparent 70%);
        }
        .login-box {
            width: 380px;
            max-width: 100%;
            background: rgba(20,18,16,0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(200,168,98,0.08);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }
        .login-box .logo {
            text-align: center;
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 2rem;
            font-weight: 300;
            margin-bottom: 0.2rem;
        }
        .login-box .logo span { color: #c8a862; }
        .login-box .subtitle {
            text-align: center;
            color: #8a8580;
            font-size: 0.75rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }
        .login-box .error {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.15);
            color: #f87171;
            padding: 0.6rem 1rem;
            border-radius: 0.6rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
        }
        .login-box label {
            display: block;
            font-size: 0.75rem;
            color: #8a8580;
            margin-bottom: 0.3rem;
        }
        .login-box input {
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
        .login-box input:focus {
            outline: none;
            border-color: #c8a862;
            box-shadow: 0 0 0 3px rgba(200,168,98,0.05);
            background: rgba(255,255,255,0.05);
        }
        .login-box button {
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
        .login-box button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(200,168,98,0.15);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">Miro<span>hood</span></div>
        <div class="subtitle">Admin Panel</div>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <label>نام کاربری</label>
            <input type="text" name="username" required autofocus>
            
            <label>رمز عبور</label>
            <input type="password" name="password" required>
            
            <button type="submit">
                <i class="fas fa-arrow-left" style="margin-left:0.4rem;"></i>
                ورود به پنل
            </button>
        </form>
    </div>
</body>
</html>