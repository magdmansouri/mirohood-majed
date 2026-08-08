<?php
// includes/EmailHelper.php - Branded HTML email wrapper

class EmailHelper {

    /**
     * Build a branded HTML email body.
     *
     * @param string $title Email title/heading
     * @param string $content Main HTML content (can include paragraphs, lists, etc.)
     * @param string $ctaUrl Optional call-to-action URL
     * @param string $ctaLabel Optional call-to-action button label
     * @return string
     */
    public static function buildHtmlBody($title, $content, $ctaUrl = null, $ctaLabel = null) {
        $siteName = defined('SITE_NAME') ? SITE_NAME : 'Mirohood';
        $siteUrl = defined('SITE_URL') ? SITE_URL : 'https://mirohood.ir';
        $siteEmail = defined('SITE_EMAIL') ? SITE_EMAIL : 'Parsmiro@gmail.com';
        $year = date('Y');

        $ctaHtml = '';
        if ($ctaUrl && $ctaLabel) {
            $ctaHtml = "\n<div style=\"margin:2rem 0;text-align:center;\">\n" .
                "<a href=\"" . htmlspecialchars($ctaUrl) . "\" style=\"display:inline-block;padding:0.9rem 2rem;background:linear-gradient(135deg,#c8a862,#a8893a);color:#0a0908;text-decoration:none;border-radius:9999px;font-weight:600;font-size:0.95rem;\">" . htmlspecialchars($ctaLabel) . "</a>\n" .
                "</div>\n";
        }

        return "<!DOCTYPE html>
<html lang=\"fa\" dir=\"rtl\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>" . htmlspecialchars($title) . "</title>
</head>
<body style=\"margin:0;padding:0;background:#0a0908;font-family:'Vazirmatn',Tahoma,Arial,sans-serif;direction:rtl;\">
    <table role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"background:#0a0908;\">
        <tr>
            <td align=\"center\" style=\"padding:2rem 1rem;\">
                <table role=\"presentation\" width=\"600\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"max-width:600px;width:100%;background:#151210;border:1px solid rgba(200,168,98,0.15);border-radius:1rem;overflow:hidden;\">
                    <tr>
                        <td style=\"padding:2rem 2rem 1.5rem;text-align:center;border-bottom:1px solid rgba(200,168,98,0.1);\">
                            <a href=\"" . htmlspecialchars($siteUrl) . "\" style=\"font-family:'Cormorant Garamond',Georgia,serif;font-size:1.8rem;color:#f4f1ea;text-decoration:none;\">" . htmlspecialchars($siteName) . "</a>
                            <p style=\"margin:0.5rem 0 0;color:#8a8580;font-size:0.75rem;\">استودیو عکاسی حرفه‌ای</p>
                        </td>
                    </tr>
                    <tr>
                        <td style=\"padding:2rem;\">
                            <h1 style=\"font-family:'Cormorant Garamond',Georgia,serif;font-size:1.5rem;font-weight:300;color:#f4f1ea;margin:0 0 1.5rem;\">" . htmlspecialchars($title) . "</h1>
                            <div style=\"color:#f4f1ea;font-size:0.95rem;line-height:1.8;\">\n" . $content . "\n</div>
                            " . $ctaHtml . "
                        </td>
                    </tr>
                    <tr>
                        <td style=\"padding:1.5rem 2rem;background:rgba(255,255,255,0.02);border-top:1px solid rgba(200,168,98,0.1);text-align:center;\">
                            <p style=\"margin:0 0 0.5rem;color:#8a8580;font-size:0.8rem;\">" . htmlspecialchars($siteName) . " — based on Earth.</p>
                            <p style=\"margin:0;color:#8a8580;font-size:0.75rem;\">
                                <a href=\"mailto:" . htmlspecialchars($siteEmail) . "\" style=\"color:#c8a862;text-decoration:none;\">" . htmlspecialchars($siteEmail) . "</a>
                            </p>
                            <p style=\"margin:0.75rem 0 0;color:#5a5550;font-size:0.7rem;\">© " . $year . " " . htmlspecialchars($siteName) . ". All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>";
    }

    /**
     * Send an HTML email using PHP mail().
     *
     * @param string $to Recipient email
     * @param string $subject Subject
     * @param string $htmlBody HTML content
     * @param string $from Sender email
     * @return bool
     */
    public static function sendHtml($to, $subject, $htmlBody, $from = null) {
        $from = $from ?? (defined('SITE_EMAIL') ? SITE_EMAIL : 'Parsmiro@gmail.com');
        $siteName = defined('SITE_NAME') ? SITE_NAME : 'Mirohood';

        $boundary = md5(time());
        $headers = "From: \"" . $siteName . "\" <" . $from . ">\r\n";
        $headers .= "Reply-To: " . $from . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"" . $boundary . "\"\r\n";

        // Extract plain text from HTML for fallback
        $plainText = self::htmlToPlainText($htmlBody);

        $body = "--" . $boundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $plainText . "\r\n\r\n";
        $body .= "--" . $boundary . "\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $htmlBody . "\r\n\r\n";
        $body .= "--" . $boundary . "--";

        return mail($to, $subject, $body, $headers);
    }

    /**
     * Convert HTML email body to plain text for multipart fallback.
     */
    private static function htmlToPlainText($html) {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\n\s*\n+/u', "\n\n", $text);
        return trim($text);
    }
}
