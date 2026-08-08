<?php
// config/config.php

// ===== مدیریت خطاها =====
// برای دیباگ موقت می‌توانید display_errors را 1 کنید؛ در محیط Production باید 0 باشد.
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');
define('DISPLAY_ERRORS', false);

define('SITE_NAME', 'Mirohood');
define('SITE_EMAIL', 'Parsmiro@gmail.com');
define('SITE_URL', 'https://mirohood.ir');
define('SITE_DESCRIPTION', 'Mirohood — based on Earth. Book portrait, and brand photography sessions.');

define('BASE_PATH', dirname(__DIR__));
define('VIEWS_PATH', BASE_PATH . '/views');
define('PARTIALS_PATH', VIEWS_PATH . '/partials');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');
define('MODELS_PATH', BASE_PATH . '/models');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('LOGS_PATH', BASE_PATH . '/logs');

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 86400);

session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Tehran');

// =============================================
// 🔐 دیتابیس
// =============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'lowfuz_lowfuzion_db');
define('DB_USER', 'lowfuz_lowfuzion_user');
define('DB_PASS', 'Lowfuzion@2024#Secure');
define('DB_CHARSET', 'utf8mb4');

// =============================================
// 🔐 ادمین - رمز: parsamajed2004
// =============================================
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', '$2y$10$UFcEi5gNeyDg4kUXQonSjujgQUQ/78OJGb9ZRtkhCqwpEfJdum3Pu');
?>