<?php
// controllers/BookingController.php

class BookingController {
    private $bookingModel;
    private $contentModel;

    public function __construct() {
        $this->bookingModel = new Booking();
        $this->contentModel = new SiteContent();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login?redirect=booking'));
            exit;
        }

        $user = (new User())->getById($_SESSION['user_id']);
        if (!$user) {
            unset($_SESSION['user_id']);
            header('Location: ' . url('login?redirect=booking'));
            exit;
        }

        $packageId = $_GET['package'] ?? null;
        $content = $this->contentModel->getMultiple([
            'booking.title', 'booking.subtitle', 'booking.badge',
            'booking.heading_line1', 'booking.heading_line2',
            'booking.package1_name', 'booking.package1_badge',
            'booking.package2_name', 'booking.package2_badge',
            'booking.package3_name', 'booking.package3_badge',
            'booking.form_name', 'booking.form_phone', 'booking.form_package',
            'booking.form_date', 'booking.form_time', 'booking.form_note',
            'booking.form_submit', 'booking.success_title', 'booking.success_message',
            'booking.success_back', 'booking.error_message', 'booking.select_placeholder',
            'booking.option_portrait', 'booking.option_brand', 'booking.option_editorial',
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);
        $bookingBg = $this->contentModel->get('booking.background') ?? '';
        $packages = [
            ['id' => 'portrait', 'name' => $content['booking.option_portrait'] ?? '📷 جلسه پرتره حرفه‌ای'],
            ['id' => 'brand', 'name' => $content['booking.option_brand'] ?? '🎬 جلسه فیلم‌برداری برند'],
            ['id' => 'editorial', 'name' => $content['booking.option_editorial'] ?? '🎥 جلسه فیلم و عکس']
        ];
        $data = [
            'page_title' => 'رزرو نوبت',
            'page_description' => 'رزرو جلسه عکاسی با Mirohood',
            'current_page' => 'booking',
            'content' => $content,
            'packages' => $packages,
            'selected_package' => $packageId,
            'csrf_token' => generate_csrf_token(),
            'bookingBg' => $bookingBg,
            'user' => $user
        ];
        $contentView = render('booking', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }

    public function submit() {
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'برای رزرو باید وارد شوید'], 401);
        }
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            json_response(['success' => false, 'message' => 'Invalid security token'], 403);
        }
        // Rate limiting removed so users can book freely without 429 errors.
        $user = (new User())->getById($_SESSION['user_id']);
        if (!$user) {
            unset($_SESSION['user_id']);
            json_response(['success' => false, 'message' => 'نشست شما نامعتبر است. لطفاً دوباره وارد شوید'], 401);
        }

        $fullName = $user['name'];
        $phone = $user['phone'];
        $packageId = sanitize_input($_POST['package_id'] ?? '');
        $jalaliDate = sanitize_input($_POST['jalali_date'] ?? '');
        $gregorianDate = sanitize_input($_POST['gregorian_date'] ?? '');
        $time = sanitize_input($_POST['time'] ?? '');
        $note = sanitize_input($_POST['note'] ?? '');

        $errors = [];
        if (empty($packageId)) {
            $errors['package_id'] = 'لطفاً یک پکیج انتخاب کنید';
        }
        if (empty($gregorianDate)) {
            $errors['date'] = 'لطفاً یک تاریخ معتبر انتخاب کنید';
        }
        if (empty($time)) {
            $errors['time'] = 'لطفاً یک ساعت انتخاب کنید';
        }
        if (empty($errors)) {
            $availableTimes = $this->bookingModel->getAvailableTimes($gregorianDate, $packageId);
            if (!in_array($time, $availableTimes)) {
                $errors['time'] = 'این ساعت در دسترس نیست';
            }
        }
        if (!empty($errors)) {
            json_response(['success' => false, 'errors' => $errors], 400);
        }
        try {
            $data = [
                'user_id' => $user['id'],
                'full_name' => $fullName,
                'phone' => $phone,
                'package_id' => $packageId,
                'jalali_date' => $jalaliDate ?: $gregorianDate,
                'gregorian_date' => $gregorianDate,
                'time' => $time,
                'note' => $note
            ];
            $bookingId = $this->bookingModel->create($data);

            // Send email notification to admin
            $this->sendBookingNotification($bookingId, $data);

            json_response([
                'success' => true,
                'message' => 'رزرو شما با موفقیت ثبت شد! وضعیت را در داشبورد کاربری می‌توانید پیگیری کنید.',
                'booking_id' => $bookingId
            ]);
        } catch (Exception $e) {
            log_error('Booking failed', $e->getMessage());
            $debugMessage = (defined('DISPLAY_ERRORS') && DISPLAY_ERRORS) || ini_get('display_errors') ? ' (' . $e->getMessage() . ')' : '';
            json_response(['success' => false, 'message' => 'خطایی رخ داد' . $debugMessage], 500);
        }
    }

    /**
     * Send email notification to admin when a new booking is created.
     */
    private function sendBookingNotification($bookingId, $data) {
        $to = defined('SITE_EMAIL') ? SITE_EMAIL : 'Parsmiro@gmail.com';
        $subject = 'رزرو جدید در Mirohood — #' . $bookingId;

        $packageLabels = [
            'portrait' => 'جلسه پرتره حرفه‌ای',
            'brand' => 'جلسه فیلم‌برداری برند',
            'editorial' => 'جلسه فیلم و عکس'
        ];
        $packageName = $packageLabels[$data['package_id']] ?? $data['package_id'];

        $detailRows = [
            ['شناسه رزرو', $bookingId],
            ['نام', $data['full_name']],
            ['موبایل', $data['phone']],
            ['پکیج', $packageName],
            ['تاریخ شمسی', $data['jalali_date']],
            ['تاریخ میلادی', $data['gregorian_date']],
            ['ساعت', $data['time']],
        ];
        if (!empty($data['note'])) {
            $detailRows[] = ['یادداشت', $data['note']];
        }
        $detailRows[] = ['وضعیت', 'در انتظار تایید'];

        $detailsHtml = '<table style="width:100%;border-collapse:collapse;margin:1rem 0;">';
        foreach ($detailRows as $row) {
            $detailsHtml .= '<tr style="border-bottom:1px solid rgba(200,168,98,0.1);">';
            $detailsHtml .= '<td style="padding:0.6rem 0;color:#8a8580;font-size:0.9rem;width:35%;">' . htmlspecialchars($row[0]) . '</td>';
            $detailsHtml .= '<td style="padding:0.6rem 0;color:#f4f1ea;font-size:0.9rem;">' . htmlspecialchars($row[1]) . '</td>';
            $detailsHtml .= '</tr>';
        }
        $detailsHtml .= '</table>';

        $content = '<p style="color:#f4f1ea;">رزرو جدیدی در سایت Mirohood ثبت شد. جزئیات رزرو به شرح زیر است:</p>' . $detailsHtml;

        $htmlBody = EmailHelper::buildHtmlBody(
            'رزرو جدید در Mirohood',
            $content,
            url('admin/bookings'),
            'مدیریت رزروها'
        );

        $sent = EmailHelper::sendHtml($to, $subject, $htmlBody);
        if (!$sent) {
            log_error('Booking notification email failed', ['booking_id' => $bookingId, 'to' => $to]);
        }
        return $sent;
    }

    public function getAvailableTimes() {
        // ===== دریافت تاریخ از درخواست =====
        $date = $_GET['date'] ?? '';
        
        // ===== اگر تاریخ خالی بود، تاریخ امروز رو بگیر =====
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        
        // ===== اعتبارسنجی تاریخ =====
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            json_response(['success' => false, 'message' => 'فرمت تاریخ نامعتبر است'], 400);
            return;
        }
        
        // ===== گرفتن ساعات آزاد =====
        $times = $this->bookingModel->getAvailableTimes($date);
        
        // ===== اگر ساعتی وجود نداشت، ساعات پیش‌فرض رو برگردان =====
        if (empty($times)) {
            $times = [];
            for ($i = 9; $i <= 20; $i++) {
                $times[] = sprintf('%02d:00', $i);
                $times[] = sprintf('%02d:30', $i);
            }
        }
        
        json_response(['success' => true, 'times' => $times]);
    }
}
?>