<?php
// send.php - Contact form handler (legacy standalone page)
// Recipient email configured for Mirohood contact form.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    $to = "majed@example.com";
    $subject = "پیام جدید از سایت Majed Mansouri";
    $body = "نام: " . $name . "\nایمیل: " . $email . "\nپیام:\n" . $message;
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo "<div style='text-align:center;padding:50px;color:green;font-size:24px;'>✅ پیام شما با موفقیت ارسال شد!</div>";
    } else {
        echo "<div style='text-align:center;padding:50px;color:red;font-size:24px;'>❌ ارسال ناموفق بود!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3;url=index.php">
    <style>
        body { font-family: Vazirmatn, sans-serif; background: #0a0a0a; color: #fff; text-align:center; padding:50px; }
    </style>
</head>
<body>
</body>
</html>
