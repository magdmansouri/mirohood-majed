<?php
// includes/security.php

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
}

function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return trim(htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
}

function validate_phone($phone) {
    return preg_match('/^09[0-9]{9}$/', preg_replace('/[^0-9]/', '', $phone)) === 1;
}

function rate_limit($key, $limit = 10, $time = 60) {
    $storage = $_SESSION['rate_limit'] ?? [];
    $now = time();
    if (!isset($storage[$key])) {
        $storage[$key] = ['count' => 1, 'first' => $now];
        $_SESSION['rate_limit'] = $storage;
        return true;
    }
    if ($now - $storage[$key]['first'] > $time) {
        $storage[$key] = ['count' => 1, 'first' => $now];
        $_SESSION['rate_limit'] = $storage;
        return true;
    }
    if ($storage[$key]['count'] >= $limit) {
        return false;
    }
    $storage[$key]['count']++;
    $_SESSION['rate_limit'] = $storage;
    return true;
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function generate_uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function log_error($message, $data = null) {
    $log = date('Y-m-d H:i:s') . ' - ' . $message;
    if ($data !== null) {
        $log .= ' - ' . print_r($data, true);
    }
    $log .= PHP_EOL;
    $logFile = LOGS_PATH . '/error.log';
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    error_log($log, 3, $logFile);
}

function set_security_headers() {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
?>