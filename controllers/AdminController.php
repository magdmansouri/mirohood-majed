<?php
// controllers/AdminController.php

class AdminController {

    private $bookingModel = null;
    private $personModel = null;
    private $photoModel = null;
    private $contentModel = null;

    private function loadBooking() {
        if ($this->bookingModel === null) {
            $this->bookingModel = new Booking();
        }
        return $this->bookingModel;
    }

    private function loadPerson() {
        if ($this->personModel === null) {
            $this->personModel = new Person();
        }
        return $this->personModel;
    }

    private function loadPhoto() {
        if ($this->photoModel === null) {
            $this->photoModel = new GalleryPhoto();
        }
        return $this->photoModel;
    }

    private function loadContent() {
        if ($this->contentModel === null) {
            $this->contentModel = new SiteContent();
        }
        return $this->contentModel;
    }

    private function checkAuth() {
        if (!is_logged_in()) {
            header('Location: ' . url('admin/login'));
            exit;
        }
    }

    private function renderAdminPage($view, $data = []) {
        extract($data);
        ob_start();
        include VIEWS_PATH . '/' . $view . '.php';
        $content = ob_get_clean();
        $page_title = $page_title ?? 'پنل ادمین';
        $current_page = $current_page ?? '';
        include VIEWS_PATH . '/admin/layout.php';
        exit;
    }

    public function login() {
        $error = '';
        $csrfToken = generate_csrf_token();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
            $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
            $postedToken = $_POST['csrf_token'] ?? null;
            $attemptKey = 'admin_login_' . hash('sha256', get_client_ip());

            if (!verify_csrf_token($postedToken)) {
                $error = 'نشست شما منقضی شده است. لطفاً دوباره تلاش کنید.';
            } elseif (!rate_limit($attemptKey, 5, 900)) {
                $error = 'تعداد تلاش‌های ورود زیاد است. لطفاً ۱۵ دقیقه دیگر دوباره امتحان کنید.';
            } elseif (hash_equals(ADMIN_USERNAME, $username) && verify_password($password, ADMIN_PASSWORD_HASH)) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_login_at'] = time();
                header('Location: ' . url('admin'));
                exit;
            } else {
                $error = 'نام کاربری یا رمز عبور درست نیست.';
            }
        }
        include VIEWS_PATH . '/admin/login.php';
        exit;
    }

    public function logout() {
        session_destroy();
        header('Location: ' . url('admin/login'));
        exit;
    }

    public function index() {
        $this->checkAuth();

        $booking = $this->loadBooking();
        $person = $this->loadPerson();
        $photo = $this->loadPhoto();
        $userModel = new User();
        $statsModel = new Statistics();
        $galleryModel = new ClientGallery();

        $baseStats = [
            'total_bookings' => $booking->count(),
            'pending_bookings' => $booking->countByStatus('PENDING'),
            'confirmed_bookings' => $booking->countByStatus('CONFIRMED'),
            'cancelled_bookings' => $booking->countByStatus('CANCELLED'),
            'completed_bookings' => $booking->countByStatus('COMPLETED'),
            'total_people' => count($person->getAll()),
            'total_photos' => count($photo->getAll()),
            'total_users' => $userModel->countAll()
        ];

        $data = [
            'page_title' => 'داشبورد',
            'current_page' => 'admin',
            'stats' => $baseStats,
            'recent_bookings' => array_slice($booking->getAll(), 0, 10),
            'users' => $userModel->getAll(true),
            'bookings_by_month' => $statsModel->bookingsByMonth(6),
            'bookings_by_status' => $statsModel->bookingsByStatus(),
            'bookings_by_package' => $statsModel->bookingsByPackage(),
            'users_by_month' => $statsModel->usersByMonth(6),
            'upcoming_bookings' => $statsModel->upcomingBookings(10),
            'gallery_stats' => $statsModel->galleryStats(),
            'top_galleries' => $statsModel->topGalleries(5),
            'recent_activity' => $statsModel->recentActivity(10),
            'conversion_rate' => $statsModel->bookingConversionRate()
        ];

        $this->renderAdminPage('admin/index', $data);
    }

    public function bookings() {
        $this->checkAuth();
        $booking = $this->loadBooking();
        $status_filter = $_GET['status'] ?? null;

        $data = [
            'page_title' => 'مدیریت رزروها',
            'current_page' => 'admin-bookings',
            'bookings' => $status_filter ? $booking->getByStatus($status_filter) : $booking->getAll(),
            'status_filter' => $status_filter,
            'total_bookings' => $booking->count()
        ];

        $this->renderAdminPage('admin/bookings', $data);
    }

    public function updateBookingStatus() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $id = $_POST['booking_id'] ?? '';
        $status = $_POST['status'] ?? '';

        if (!$id || !$status) {
            json_response(['success' => false, 'message' => 'اطلاعات ناقص است'], 400);
        }

        try {
            $booking = $this->loadBooking()->getById($id);
            $this->loadBooking()->updateStatus($id, $status);

            // Notify user by email if available
            if ($booking && !empty($booking['user_id'])) {
                $this->sendStatusChangeEmail($booking, $status);

                $statusLabels = [
                    'PENDING' => 'در انتظار تایید',
                    'CONFIRMED' => 'تایید شده',
                    'CANCELLED' => 'لغو شده',
                    'COMPLETED' => 'انجام شده'
                ];
                $notification = new Notification();
                $notification->create(
                    $booking['user_id'],
                    'BOOKING_STATUS',
                    'وضعیت رزرو تغییر کرد',
                    'رزرو شما با شماره ' . $booking['id'] . ' به وضعیت «' . ($statusLabels[$status] ?? $status) . '» تغییر یافت.',
                    $booking['id'],
                    'booking'
                );
            }

            json_response(['success' => true, 'message' => 'وضعیت رزرو تغییر کرد']);
        } catch (Exception $e) {
            log_error('updateBookingStatus failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در تغییر وضعیت'], 500);
        }
    }

    public function deleteBooking() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $id = $_POST['booking_id'] ?? '';
        if (!$id) {
            json_response(['success' => false, 'message' => 'شناسه رزرو نامعتبر است'], 400);
        }

        try {
            $booking = $this->loadBooking()->getById($id);
            if (!$booking) {
                json_response(['success' => false, 'message' => 'رزرو پیدا نشد'], 404);
            }

            $this->loadBooking()->delete($id);
            json_response(['success' => true, 'message' => 'رزرو حذف شد']);
        } catch (Exception $e) {
            log_error('deleteBooking failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در حذف رزرو'], 500);
        }
    }

    public function handleBookingRequest() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $id = (int) ($_POST['booking_id'] ?? 0);
        $decision = $_POST['decision'] ?? '';
        $adminNote = sanitize_input($_POST['admin_note'] ?? '');

        if (!$id || !in_array($decision, ['APPROVED', 'REJECTED'], true)) {
            json_response(['success' => false, 'message' => 'درخواست نامعتبر'], 400);
        }

        try {
            $booking = $this->loadBooking()->getById($id);
            if (!$booking) {
                json_response(['success' => false, 'message' => 'رزرو پیدا نشد'], 404);
            }

            $this->loadBooking()->handleRequest($id, $decision, $adminNote);

            if (!empty($booking['user_id'])) {
                $notification = new Notification();
                $requestTypeLabel = ($booking['request_type'] ?? 'NONE') === 'CANCEL' ? 'لغو' : 'تغییر زمان';
                $notification->create(
                    $booking['user_id'],
                    'BOOKING_REQUEST',
                    'نتیجه درخواست نوبت',
                    'درخواست ' . $requestTypeLabel . ' نوبت شما با شماره ' . $booking['id'] . ' ' . ($decision === 'APPROVED' ? 'تایید' : 'رد') . ' شد.',
                    $booking['id'],
                    'booking'
                );
            }

            json_response(['success' => true, 'message' => 'درخواست بررسی شد']);
        } catch (Exception $e) {
            log_error('handleBookingRequest failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در بررسی درخواست'], 500);
        }
    }

    /**
     * Send email notification to user when booking status changes.
     */
    private function sendStatusChangeEmail($booking, $status) {
        $user = (new User())->getById($booking['user_id']);
        if (!$user || empty($user['email'])) {
            return false;
        }

        $statusLabels = [
            'PENDING' => 'در انتظار تایید',
            'CONFIRMED' => 'تایید شده',
            'CANCELLED' => 'لغو شده',
            'COMPLETED' => 'انجام شده'
        ];
        $statusColors = [
            'PENDING' => '#fbbf24',
            'CONFIRMED' => '#4ade80',
            'CANCELLED' => '#f87171',
            'COMPLETED' => '#8a8580'
        ];
        $statusLabel = $statusLabels[$status] ?? $status;
        $statusColor = $statusColors[$status] ?? '#f4f1ea';

        $packageLabels = [
            'portrait' => 'جلسه پرتره حرفه‌ای',
            'brand' => 'جلسه فیلم‌برداری برند',
            'editorial' => 'جلسه فیلم و عکس'
        ];
        $packageName = $packageLabels[$booking['package_id']] ?? $booking['package_id'];

        $subject = 'وضعیت رزرو شما در Mirohood: ' . $statusLabel;

        $detailsHtml = '<table style="width:100%;border-collapse:collapse;margin:1rem 0;">';
        $detailsHtml .= '<tr style="border-bottom:1px solid rgba(200,168,98,0.1);"><td style="padding:0.6rem 0;color:#8a8580;font-size:0.9rem;width:35%;">شناسه رزرو</td><td style="padding:0.6rem 0;color:#f4f1ea;font-size:0.9rem;">' . htmlspecialchars($booking['id']) . '</td></tr>';
        $detailsHtml .= '<tr style="border-bottom:1px solid rgba(200,168,98,0.1);"><td style="padding:0.6rem 0;color:#8a8580;font-size:0.9rem;">پکیج</td><td style="padding:0.6rem 0;color:#f4f1ea;font-size:0.9rem;">' . htmlspecialchars($packageName) . '</td></tr>';
        $detailsHtml .= '<tr style="border-bottom:1px solid rgba(200,168,98,0.1);"><td style="padding:0.6rem 0;color:#8a8580;font-size:0.9rem;">تاریخ</td><td style="padding:0.6rem 0;color:#f4f1ea;font-size:0.9rem;">' . htmlspecialchars($booking['jalali_date']) . '</td></tr>';
        $detailsHtml .= '<tr style="border-bottom:1px solid rgba(200,168,98,0.1);"><td style="padding:0.6rem 0;color:#8a8580;font-size:0.9rem;">ساعت</td><td style="padding:0.6rem 0;color:#f4f1ea;font-size:0.9rem;">' . htmlspecialchars($booking['time']) . '</td></tr>';
        $detailsHtml .= '<tr style="border-bottom:1px solid rgba(200,168,98,0.1);"><td style="padding:0.6rem 0;color:#8a8580;font-size:0.9rem;">وضعیت جدید</td><td style="padding:0.6rem 0;color:' . $statusColor . ';font-size:0.9rem;font-weight:600;">' . htmlspecialchars($statusLabel) . '</td></tr>';
        $detailsHtml .= '</table>';

        $content = '<p style="color:#f4f1ea;">سلام <strong>' . htmlspecialchars($user['name']) . '</strong>،</p>';
        $content .= '<p style="color:#f4f1ea;">وضعیت رزرو شما در Mirohood تغییر کرد:</p>';
        $content .= $detailsHtml;
        $content .= '<p style="color:#8a8580;font-size:0.85rem;">برای مشاهده جزئیات بیشتر به داشبورد خود مراجعه کنید.</p>';

        $htmlBody = EmailHelper::buildHtmlBody(
            'وضعیت رزرو شما در Mirohood',
            $content,
            url('dashboard'),
            'مشاهده داشبورد'
        );

        $sent = EmailHelper::sendHtml($user['email'], $subject, $htmlBody);
        if (!$sent) {
            log_error('Status change email failed', ['booking_id' => $booking['id'], 'user_id' => $user['id'], 'email' => $user['email']]);
        }
        return $sent;
    }

    // ============================================================
    // GALLERY — People list
    // ============================================================

    public function gallery() {
        $this->checkAuth();

        $data = [
            'page_title' => 'مدیریت گالری',
            'current_page' => 'admin-gallery',
            'people' => $this->loadPerson()->getAll()
        ];

        $this->renderAdminPage('admin/gallery', $data);
    }

    public function personCreate() {
        $this->checkAuth();

        $person = $this->loadPerson();
        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $data = $this->sanitizePersonData($_POST);
            $errors = $this->validatePersonData($data);

            if (empty($errors)) {
                try {
                    $data['sort_order'] = $person->getMaxSortOrder() + 1;
                    $id = $person->create($data);
                    $this->handlePersonUploads($id, $data['slug']);
                    header('Location: ' . url('admin/gallery'));
                    exit;
                } catch (Exception $e) {
                    $errors[] = 'خطا در ذخیره: ' . $e->getMessage();
                    log_error('personCreate failed', $e->getMessage());
                }
            }
        }

        $this->renderAdminPage('admin/person-form', [
            'page_title' => 'افزودن شخص جدید',
            'current_page' => 'admin-gallery',
            'person' => null,
            'data' => $data,
            'errors' => $errors,
            'mode' => 'create'
        ]);
    }

    public function personEdit() {
        $this->checkAuth();

        $id = (int) ($_GET['id'] ?? 0);
        $person = $this->loadPerson()->getById($id);
        if (!$person) {
            header('Location: ' . url('admin/gallery'));
            exit;
        }

        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $data = $this->sanitizePersonData($_POST);
            $errors = $this->validatePersonData($data, $id);

            if (empty($errors)) {
                try {
                    $this->loadPerson()->update($id, $data);
                    $this->handlePersonUploads($id, $data['slug']);
                    header('Location: ' . url('admin/gallery'));
                    exit;
                } catch (Exception $e) {
                    $errors[] = 'خطا در ویرایش: ' . $e->getMessage();
                    log_error('personEdit failed', $e->getMessage());
                }
            }
        } else {
            $data = $person;
            $data['featured'] = (bool) $person['featured'];
            $data['status'] = (bool) $person['status'];
        }

        $this->renderAdminPage('admin/person-form', [
            'page_title' => 'ویرایش ' . $person['name'],
            'current_page' => 'admin-gallery',
            'person' => $person,
            'data' => $data,
            'errors' => $errors,
            'mode' => 'edit'
        ]);
    }

    public function personDelete() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            json_response(['success' => false, 'message' => 'شناسه نامعتبر'], 400);
        }

        try {
            $person = $this->loadPerson()->getById($id);
            if (!$person) {
                json_response(['success' => false, 'message' => 'شخص پیدا نشد'], 404);
            }
            $this->loadPerson()->delete($id);

            // Delete physical files
            if (!empty($person['avatar'])) {
                ImageHelper::deleteFiles($_SERVER['DOCUMENT_ROOT'] . $person['avatar']);
            }
            if (!empty($person['cover'])) {
                ImageHelper::deleteFiles($_SERVER['DOCUMENT_ROOT'] . $person['cover']);
            }

            json_response(['success' => true, 'message' => 'شخص حذف شد']);
        } catch (Exception $e) {
            log_error('personDelete failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در حذف'], 500);
        }
    }

    // ============================================================
    // Manage Photos for a Person
    // ============================================================

    public function personPhotos() {
        $this->checkAuth();

        $slug = $_GET['slug'] ?? '';
        $person = $this->loadPerson()->getBySlugAdmin($slug);
        if (!$person) {
            header('Location: ' . url('admin/gallery'));
            exit;
        }

        $images = $this->loadPhoto()->getByPerson($person['id']);

        $this->renderAdminPage('admin/person-photos', [
            'page_title' => 'مدیریت تصاویر ' . $person['name'],
            'current_page' => 'admin-gallery',
            'person' => $person,
            'images' => $images
        ]);
    }

    // ============================================================
    // Photo Upload (Drag & Drop, Multiple)
    // ============================================================

    public function uploadGalleryImage() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $personId = (int) ($_POST['person_id'] ?? 0);
        if (!$personId) {
            json_response(['success' => false, 'message' => 'شخص انتخاب نشده'], 400);
        }

        $person = $this->loadPerson()->getById($personId);
        if (!$person) {
            json_response(['success' => false, 'message' => 'شخص پیدا نشد'], 404);
        }

        $files = $_FILES['images'] ?? [];
        if (empty($files['tmp_name']) || !is_array($files['tmp_name'])) {
            json_response(['success' => false, 'message' => 'فایل ارسال نشده'], 400);
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/gallery';
        $uploaded = [];
        $failed = [];

        foreach ($files['tmp_name'] as $index => $tmpName) {
            if ($files['error'][$index] !== UPLOAD_ERR_OK) {
                $failed[] = $files['name'][$index] . ' (upload error)';
                continue;
            }

            $file = [
                'tmp_name' => $tmpName,
                'error' => $files['error'][$index],
                'name' => $files['name'][$index]
            ];

            if (!ImageHelper::validate($file)) {
                $failed[] = $files['name'][$index] . ' (فرمت نامعتبر)';
                continue;
            }

            // Duplicate check by hash
            $hash = hash_file('sha256', $tmpName);
            $existing = $this->loadPhoto()->findByHash($hash);
            if ($existing) {
                $failed[] = $files['name'][$index] . ' (تصویر تکراری)';
                continue;
            }

            $ext = ImageHelper::getExtension($file);
            $filename = ImageHelper::generateFilename($ext);

            try {
                $paths = ImageHelper::process($tmpName, $uploadDir, $filename);
                $imageUrl = ImageHelper::toUrl($paths['image']);
                $thumbUrl = ImageHelper::toUrl($paths['thumbnail']);
                $mediumUrl = ImageHelper::toUrl($paths['medium']);
                $largeUrl = ImageHelper::toUrl($paths['large']);
                $webpUrl = ImageHelper::toUrl($paths['webp_image']);
                $webpThumbUrl = ImageHelper::toUrl($paths['webp_thumbnail']);
                $webpMediumUrl = ImageHelper::toUrl($paths['webp_medium']);
                $webpLargeUrl = ImageHelper::toUrl($paths['webp_large']);

                $photoId = $this->loadPhoto()->create([
                    'person_id' => $personId,
                    'image' => $imageUrl,
                    'thumbnail' => $thumbUrl,
                    'medium' => $mediumUrl,
                    'large' => $largeUrl,
                    'webp_image' => $webpUrl,
                    'webp_thumbnail' => $webpThumbUrl,
                    'webp_medium' => $webpMediumUrl,
                    'webp_large' => $webpLargeUrl,
                    'image_hash' => $hash,
                    'sort_order' => $this->loadPhoto()->getNextSortOrder($personId),
                    'alt' => sanitize_input($_POST['alt'] ?? '')
                ]);

                $uploaded[] = [
                    'id' => $photoId,
                    'image' => $imageUrl,
                    'thumbnail' => $thumbUrl
                ];
            } catch (Exception $e) {
                log_error('uploadGalleryImage failed', $e->getMessage());
                $failed[] = $files['name'][$index] . ' (پردازش ناموفق)';
            }
        }

        json_response([
            'success' => count($uploaded) > 0,
            'uploaded' => $uploaded,
            'failed' => $failed,
            'message' => count($uploaded) . ' تصویر آپلود شد' . (count($failed) ? ' / ' . count($failed) . ' خطا' : '')
        ]);
    }

    public function deleteGalleryImage() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $id = (int) ($_POST['image_id'] ?? 0);
        if (!$id) {
            json_response(['success' => false, 'message' => 'شناسه تصویر الزامی است'], 400);
        }

        try {
            $photo = $this->loadPhoto()->getById($id);
            if (!$photo) {
                json_response(['success' => false, 'message' => 'تصویر پیدا نشد'], 404);
            }

            $this->loadPhoto()->delete($id);

            ImageHelper::deleteFiles(
                $_SERVER['DOCUMENT_ROOT'] . $photo['image'],
                !empty($photo['thumbnail']) ? $_SERVER['DOCUMENT_ROOT'] . $photo['thumbnail'] : null,
                !empty($photo['medium']) ? $_SERVER['DOCUMENT_ROOT'] . $photo['medium'] : null,
                !empty($photo['large']) ? $_SERVER['DOCUMENT_ROOT'] . $photo['large'] : null,
                [
                    !empty($photo['webp_image']) ? $_SERVER['DOCUMENT_ROOT'] . $photo['webp_image'] : null,
                    !empty($photo['webp_thumbnail']) ? $_SERVER['DOCUMENT_ROOT'] . $photo['webp_thumbnail'] : null,
                    !empty($photo['webp_medium']) ? $_SERVER['DOCUMENT_ROOT'] . $photo['webp_medium'] : null,
                    !empty($photo['webp_large']) ? $_SERVER['DOCUMENT_ROOT'] . $photo['webp_large'] : null
                ]
            );

            json_response(['success' => true, 'message' => 'تصویر حذف شد']);
        } catch (Exception $e) {
            log_error('deleteGalleryImage failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در حذف تصویر'], 500);
        }
    }

    public function updatePhotoMeta() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $id = (int) ($_POST['image_id'] ?? 0);
        if (!$id) {
            json_response(['success' => false, 'message' => 'شناسه تصویر الزامی است'], 400);
        }

        $data = [
            'caption' => sanitize_input($_POST['caption'] ?? ''),
            'alt' => sanitize_input($_POST['alt'] ?? ''),
            'seo_title' => sanitize_input($_POST['seo_title'] ?? '')
        ];

        try {
            $this->loadPhoto()->update($id, $data);
            json_response(['success' => true, 'message' => 'اطلاعات تصویر ذخیره شد']);
        } catch (Exception $e) {
            log_error('updatePhotoMeta failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در ذخیره'], 500);
        }
    }

    public function reorderPhotos() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $personId = (int) ($_POST['person_id'] ?? 0);
        $order = $_POST['order'] ?? [];

        if (!$personId || !is_array($order) || empty($order)) {
            json_response(['success' => false, 'message' => 'اطلاعات ناقص است'], 400);
        }

        try {
            $this->loadPhoto()->reorder($personId, $order);
            json_response(['success' => true, 'message' => 'ترتیب تصاویر ذخیره شد']);
        } catch (Exception $e) {
            log_error('reorderPhotos failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در ذخیره ترتیب'], 500);
        }
    }

    // ============================================================
    // Content & Media
    // ============================================================

    public function content() {
        $this->checkAuth();

        $data = [
            'page_title' => 'مدیریت محتوا',
            'current_page' => 'admin-content'
        ];

        $this->renderAdminPage('admin/content', $data);
    }

    public function updateContent() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        try {
            $contentModel = $this->loadContent();

            if (isset($_POST['content']) && is_array($_POST['content'])) {
                foreach ($_POST['content'] as $key => $value) {
                    if (!empty($key)) {
                        $contentModel->set($key, $value);
                    }
                }
                json_response(['success' => true, 'message' => 'همه محتوا ذخیره شد']);
                return;
            }

            $key = $_POST['key'] ?? '';
            $value = $_POST['value'] ?? '';

            if (!$key) {
                json_response(['success' => false, 'message' => 'کلید محتوا خالی است'], 400);
                return;
            }

            $contentModel->set($key, $value);
            json_response(['success' => true, 'message' => 'ذخیره شد']);
        } catch (Exception $e) {
            log_error('updateContent failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در ذخیره محتوا'], 500);
        }
    }

    public function media() {
        $this->checkAuth();

        $data = [
            'page_title' => 'مدیریت رسانه',
            'current_page' => 'admin-media'
        ];

        $this->renderAdminPage('admin/media', $data);
    }

    public function uploadMedia() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $type = $_POST['type'] ?? '';
        $allowedTypes = ['hero_video', 'hero_poster', 'booking_background', 'gallery_background', 'about_background', 'about_avatar'];
        if (!in_array($type, $allowedTypes, true)) {
            json_response(['success' => false, 'message' => 'نوع رسانه نامعتبر'], 400);
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            json_response(['success' => false, 'message' => 'فایل ارسال نشده'], 400);
        }

        $file = $_FILES['file'];
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/media';

        $isVideo = $type === 'hero_video';
        if ($isVideo) {
            $allowedMime = ['video/mp4', 'video/webm', 'video/ogg'];
            if (!in_array($file['type'], $allowedMime, true)) {
                json_response(['success' => false, 'message' => 'فرمت ویدیو مجاز نیست'], 400);
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'media_' . ImageHelper::generateFilename($ext);
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $target = $uploadDir . '/' . $filename;
            if (!move_uploaded_file($file['tmp_name'], $target)) {
                json_response(['success' => false, 'message' => 'ذخیره فایل انجام نشد'], 500);
            }
            $url = ImageHelper::toUrl($target);
        } else {
            if (!ImageHelper::validate($file)) {
                json_response(['success' => false, 'message' => 'فرمت تصویر مجاز نیست'], 400);
            }
            $ext = ImageHelper::getExtension($file);
            $filename = 'media_' . ImageHelper::generateFilename($ext);
            try {
                $paths = ImageHelper::process($file['tmp_name'], $uploadDir, $filename);
                $url = ImageHelper::toUrl($paths['image']);
            } catch (Exception $e) {
                log_error('uploadMedia failed', $e->getMessage());
                json_response(['success' => false, 'message' => 'خطا در پردازش تصویر'], 500);
            }
        }

        $keyMap = [
            'hero_video' => 'home.hero_video',
            'hero_poster' => 'home.hero_poster',
            'booking_background' => 'booking.background',
            'gallery_background' => 'gallery.background',
            'about_background' => 'about.background',
            'about_avatar' => 'about.owner_avatar'
        ];

        try {
            $this->loadContent()->set($keyMap[$type], $url);
            json_response(['success' => true, 'message' => 'رسانه آپلود شد', 'url' => $url]);
        } catch (Exception $e) {
            log_error('uploadMedia save failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در ذخیره اطلاعات'], 500);
        }
    }

    public function deleteMedia() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $type = $_POST['type'] ?? '';
        $keyMap = [
            'hero_video' => 'home.hero_video',
            'hero_poster' => 'home.hero_poster',
            'booking_background' => 'booking.background',
            'gallery_background' => 'gallery.background',
            'about_background' => 'about.background',
            'about_avatar' => 'about.owner_avatar'
        ];

        if (!isset($keyMap[$type])) {
            json_response(['success' => false, 'message' => 'نوع رسانه نامعتبر'], 400);
        }

        try {
            $contentModel = $this->loadContent();
            $contentModel->set($keyMap[$type], '');
            json_response(['success' => true, 'message' => 'رسانه حذف شد']);
        } catch (Exception $e) {
            log_error('deleteMedia failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در حذف رسانه'], 500);
        }
    }

    public function hours() {
        $this->checkAuth();

        $data = [
            'page_title' => 'تنظیمات ساعت کاری',
            'current_page' => 'admin-hours'
        ];

        $this->renderAdminPage('admin/hours', $data);
    }

    public function updateExceptions() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        try {
            $contentModel = $this->loadContent();
            $holidaysJson = $contentModel->get('holiday_dates') ?? '[]';
            $holidays = json_decode($holidaysJson, true) ?? [];
            $holidays = array_values(array_unique($holidays));

            if (isset($_POST['new_holiday'])) {
                $date = sanitize_input($_POST['new_holiday']);
                if (!preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $date)) {
                    json_response(['success' => false, 'message' => 'فرمت تاریخ نامعتبر (YYYY/MM/DD)'], 400);
                }
                if (!in_array($date, $holidays, true)) {
                    $holidays[] = $date;
                }
                $contentModel->set('holiday_dates', json_encode($holidays, JSON_UNESCAPED_UNICODE));
                json_response(['success' => true, 'message' => 'تعطیلی اضافه شد']);
                return;
            }

            if (isset($_POST['remove'])) {
                $date = sanitize_input($_POST['remove']);
                $holidays = array_values(array_diff($holidays, [$date]));
                $contentModel->set('holiday_dates', json_encode($holidays, JSON_UNESCAPED_UNICODE));
                json_response(['success' => true, 'message' => 'تعطیلی حذف شد']);
                return;
            }

            json_response(['success' => false, 'message' => 'درخواست نامعتبر'], 400);
        } catch (Exception $e) {
            log_error('updateExceptions failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در ذخیره'], 500);
        }
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function verifyCsrf() {
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            json_response(['success' => false, 'message' => 'نشست شما منقضی شده'], 403);
        }
    }

    private function sanitizePersonData(array $input): array {
        return [
            'name' => sanitize_input($input['name'] ?? ''),
            'slug' => $this->sanitizeSlug($input['slug'] ?? ''),
            'bio' => sanitize_input($input['bio'] ?? ''),
            'instagram' => $this->sanitizeInstagram($input['instagram'] ?? ''),
            'website' => $this->sanitizeUrl($input['website'] ?? ''),
            'featured' => isset($input['featured']) ? 1 : 0,
            'status' => isset($input['status']) ? 1 : 0,
            'sort_order' => (int) ($input['sort_order'] ?? 0)
        ];
    }

    private function sanitizeSlug(string $slug): string {
        return trim(strip_tags($slug));
    }

    private function sanitizeInstagram(string $handle): string {
        $handle = trim(strip_tags($handle));
        return ltrim($handle, '@');
    }

    private function sanitizeUrl(string $url): string {
        $url = trim(strip_tags($url));
        if ($url === '') {
            return '';
        }
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        return $url;
    }

    private function validatePersonData(array $data, int $excludeId = 0): array {
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = 'نام شخص الزامی است';
        }
        if (empty($data['slug'])) {
            $errors[] = 'اسلاگ الزامی است';
        }
        if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            $errors[] = 'آدرس وب‌سایت معتبر نیست';
        }
        return $errors;
    }

    private function handlePersonUploads($personId, $slug) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/people';
        $person = $this->loadPerson()->getById($personId);
        if (!$person) {
            return;
        }

        $fields = ['avatar', 'cover'];
        foreach ($fields as $field) {
            if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                continue;
            }
            $file = $_FILES[$field];
            if (!ImageHelper::validate($file)) {
                continue;
            }
            $ext = ImageHelper::getExtension($file);
            $filename = $slug . '-' . $field . '-' . ImageHelper::generateFilename($ext);

            $paths = ImageHelper::process($file['tmp_name'], $uploadDir, $filename);
            $url = ImageHelper::toUrl($paths['image']);

            // Delete old file
            $oldField = $field === 'avatar' ? $person['avatar'] : $person['cover'];
            if (!empty($oldField)) {
                ImageHelper::deleteFiles($_SERVER['DOCUMENT_ROOT'] . $oldField);
            }

            $this->loadPerson()->update($personId, [$field => $url]);

            // Reload person after update
            $person = $this->loadPerson()->getById($personId);
        }
    }

    // ============================================================
    // Client Galleries (Private User Galleries)
    // ============================================================

    public function clientGalleries() {
        $this->checkAuth();

        $galleryModel = new ClientGallery();
        $data = [
            'page_title' => 'گالری مشتریان',
            'current_page' => 'admin-client-galleries',
            'galleries' => $galleryModel->getAll()
        ];
        $this->renderAdminPage('admin/client-galleries', $data);
    }

    public function clientGalleryCreate() {
        $this->checkAuth();

        $userModel = new User();
        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $data = [
                'user_id' => (int) ($_POST['user_id'] ?? 0),
                'booking_id' => !empty($_POST['booking_id']) ? (int) $_POST['booking_id'] : null,
                'title' => sanitize_input($_POST['title'] ?? ''),
                'description' => sanitize_input($_POST['description'] ?? ''),
                'max_selections' => (int) ($_POST['max_selections'] ?? 0),
                'status' => isset($_POST['status']) ? 1 : 0
            ];

            if (empty($data['user_id'])) {
                $errors[] = 'انتخاب کاربر الزامی است';
            }
            if (empty($data['title'])) {
                $errors[] = 'عنوان گالری الزامی است';
            }

            if (empty($errors)) {
                try {
                    $galleryModel = new ClientGallery();
                    $galleryModel->create($data);
                    header('Location: ' . url('admin/client-galleries'));
                    exit;
                } catch (Exception $e) {
                    $errors[] = 'خطا در ذخیره: ' . $e->getMessage();
                    log_error('clientGalleryCreate failed', $e->getMessage());
                }
            }
        }

        $data['users'] = $userModel->getAll();

        $this->renderAdminPage('admin/client-gallery-form', [
            'page_title' => 'گالری جدید مشتری',
            'current_page' => 'admin-client-galleries',
            'gallery' => null,
            'data' => $data,
            'errors' => $errors,
            'mode' => 'create'
        ]);
    }

    public function clientGalleryEdit() {
        $this->checkAuth();

        $id = (int) ($_GET['id'] ?? 0);
        $galleryModel = new ClientGallery();
        $gallery = $galleryModel->getById($id);
        if (!$gallery) {
            header('Location: ' . url('admin/client-galleries'));
            exit;
        }

        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $data = [
                'title' => sanitize_input($_POST['title'] ?? ''),
                'description' => sanitize_input($_POST['description'] ?? ''),
                'max_selections' => (int) ($_POST['max_selections'] ?? 0),
                'status' => isset($_POST['status']) ? 1 : 0
            ];

            if (empty($data['title'])) {
                $errors[] = 'عنوان گالری الزامی است';
            }

            if (empty($errors)) {
                try {
                    $galleryModel->update($id, $data);
                    header('Location: ' . url('admin/client-galleries'));
                    exit;
                } catch (Exception $e) {
                    $errors[] = 'خطا در ویرایش: ' . $e->getMessage();
                    log_error('clientGalleryEdit failed', $e->getMessage());
                }
            }
        } else {
            $data = $gallery;
        }

        $this->renderAdminPage('admin/client-gallery-form', [
            'page_title' => 'ویرایش گالری مشتری',
            'current_page' => 'admin-client-galleries',
            'gallery' => $gallery,
            'data' => $data,
            'errors' => $errors,
            'mode' => 'edit'
        ]);
    }

    public function clientGalleryDelete() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            json_response(['success' => false, 'message' => 'شناسه نامعتبر'], 400);
        }

        try {
            $galleryModel = new ClientGallery();
            $imageModel = new ClientGalleryImage();
            $images = $imageModel->getByGallery($id);

            foreach ($images as $img) {
                ImageHelper::deleteFiles(
                    $_SERVER['DOCUMENT_ROOT'] . $img['image'],
                    !empty($img['thumbnail']) ? $_SERVER['DOCUMENT_ROOT'] . $img['thumbnail'] : null,
                    !empty($img['webp_image']) ? $_SERVER['DOCUMENT_ROOT'] . $img['webp_image'] : null
                );
            }

            $galleryModel->delete($id);
            json_response(['success' => true, 'message' => 'گالری حذف شد']);
        } catch (Exception $e) {
            log_error('clientGalleryDelete failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در حذف گالری'], 500);
        }
    }

    public function clientGalleryPhotos() {
        $this->checkAuth();

        $id = (int) ($_GET['id'] ?? 0);
        $galleryModel = new ClientGallery();
        $gallery = $galleryModel->getById($id);
        if (!$gallery) {
            header('Location: ' . url('admin/client-galleries'));
            exit;
        }

        $imageModel = new ClientGalleryImage();
        $images = $imageModel->getByGallery($id);

        $this->renderAdminPage('admin/client-gallery-photos', [
            'page_title' => 'مدیریت تصاویر گالری: ' . $gallery['title'],
            'current_page' => 'admin-client-galleries',
            'gallery' => $gallery,
            'images' => $images
        ]);
    }

    public function uploadClientGalleryImage() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $galleryId = (int) ($_POST['gallery_id'] ?? 0);
        if (!$galleryId) {
            json_response(['success' => false, 'message' => 'گالری انتخاب نشده'], 400);
        }

        $galleryModel = new ClientGallery();
        $gallery = $galleryModel->getById($galleryId);
        if (!$gallery) {
            json_response(['success' => false, 'message' => 'گالری پیدا نشد'], 404);
        }

        $files = $_FILES['images'] ?? [];
        if (empty($files['tmp_name']) || !is_array($files['tmp_name'])) {
            json_response(['success' => false, 'message' => 'فایل ارسال نشده'], 400);
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/client-galleries';
        $uploaded = [];
        $failed = [];
        $imageModel = new ClientGalleryImage();

        foreach ($files['tmp_name'] as $index => $tmpName) {
            if ($files['error'][$index] !== UPLOAD_ERR_OK) {
                $failed[] = $files['name'][$index] . ' (upload error)';
                continue;
            }

            $file = [
                'tmp_name' => $tmpName,
                'error' => $files['error'][$index],
                'name' => $files['name'][$index]
            ];

            if (!ImageHelper::validate($file)) {
                $failed[] = $files['name'][$index] . ' (فرمت نامعتبر)';
                continue;
            }

            $ext = ImageHelper::getExtension($file);
            $filename = ImageHelper::generateFilename($ext);

            try {
                $paths = ImageHelper::process($tmpName, $uploadDir, $filename);
                $imageUrl = ImageHelper::toUrl($paths['image']);
                $thumbUrl = ImageHelper::toUrl($paths['thumbnail']);
                $webpUrl = ImageHelper::toUrl($paths['webp_image']);

                $imageId = $imageModel->create([
                    'gallery_id' => $galleryId,
                    'image' => $imageUrl,
                    'thumbnail' => $thumbUrl,
                    'webp_image' => $webpUrl,
                    'caption' => sanitize_input($_POST['caption'] ?? ''),
                    'sort_order' => $imageModel->getNextSortOrder($galleryId)
                ]);

                $uploaded[] = ['id' => $imageId, 'image' => $imageUrl, 'thumbnail' => $thumbUrl];
            } catch (Exception $e) {
                log_error('uploadClientGalleryImage failed', $e->getMessage());
                $failed[] = $files['name'][$index] . ' (پردازش ناموفق)';
            }
        }

        json_response([
            'success' => count($uploaded) > 0,
            'uploaded' => $uploaded,
            'failed' => $failed,
            'message' => count($uploaded) . ' تصویر آپلود شد' . (count($failed) ? ' / ' . count($failed) . ' خطا' : '')
        ]);
    }

    public function deleteClientGalleryImage() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $id = (int) ($_POST['image_id'] ?? 0);
        if (!$id) {
            json_response(['success' => false, 'message' => 'شناسه تصویر الزامی است'], 400);
        }

        try {
            $imageModel = new ClientGalleryImage();
            $image = $imageModel->getById($id);
            if (!$image) {
                json_response(['success' => false, 'message' => 'تصویر پیدا نشد'], 404);
            }

            $imageModel->delete($id);

            ImageHelper::deleteFiles(
                $_SERVER['DOCUMENT_ROOT'] . $image['image'],
                !empty($image['thumbnail']) ? $_SERVER['DOCUMENT_ROOT'] . $image['thumbnail'] : null,
                !empty($image['webp_image']) ? $_SERVER['DOCUMENT_ROOT'] . $image['webp_image'] : null
            );

            json_response(['success' => true, 'message' => 'تصویر حذف شد']);
        } catch (Exception $e) {
            log_error('deleteClientGalleryImage failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در حذف تصویر'], 500);
        }
    }

    public function toggleClientGalleryFavorite() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $id = (int) ($_POST['image_id'] ?? 0);
        if (!$id) {
            json_response(['success' => false, 'message' => 'شناسه تصویر الزامی است'], 400);
        }

        try {
            $imageModel = new ClientGalleryImage();
            $newValue = $imageModel->toggleFavorite($id);
            json_response([
                'success' => true,
                'message' => $newValue ? 'به علاقه‌مندی‌ها اضافه شد' : 'از علاقه‌مندی‌ها حذف شد',
                'is_favorite' => (bool) $newValue
            ]);
        } catch (Exception $e) {
            log_error('toggleClientGalleryFavorite failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در تغییر وضعیت'], 500);
        }
    }

    public function updateClientGalleryPhotoMeta() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $id = (int) ($_POST['image_id'] ?? 0);
        if (!$id) {
            json_response(['success' => false, 'message' => 'شناسه تصویر الزامی است'], 400);
        }

        $data = ['caption' => sanitize_input($_POST['caption'] ?? '')];

        try {
            $imageModel = new ClientGalleryImage();
            $imageModel->update($id, $data);
            json_response(['success' => true, 'message' => 'اطلاعات تصویر ذخیره شد']);
        } catch (Exception $e) {
            log_error('updateClientGalleryPhotoMeta failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در ذخیره'], 500);
        }
    }

    public function downloadClientGalleryZip() {
        $this->checkAuth();

        $galleryId = (int) ($_GET['id'] ?? 0);
        if (!$galleryId) {
            http_response_code(400);
            echo 'شناسه گالری مشخص نشده است';
            exit;
        }

        $galleryModel = new ClientGallery();
        try {
            $quality = ($_GET['quality'] ?? '') === 'optimized' ? 'optimized' : 'original';
            $tempFile = $galleryModel->buildZip($galleryId, null, $quality);
            $filename = 'mirohood-gallery-' . $galleryId . ($quality === 'optimized' ? '-light' : '-original') . '.zip';
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($tempFile));
            header('Cache-Control: no-store, no-cache, must-revalidate');
            readfile($tempFile);
            unlink($tempFile);
            exit;
        } catch (Exception $e) {
            log_error('downloadClientGalleryZip failed', $e->getMessage());
            http_response_code(500);
            echo 'خطا در دانلود: ' . $e->getMessage();
            exit;
        }
    }

    public function manageClientGalleryShare() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $this->verifyCsrf();

        $galleryId = (int) ($_POST['gallery_id'] ?? 0);
        $action = $_POST['share_action'] ?? '';
        $sharePassword = $_POST['share_password'] ?? '';
        $shareExpires = $_POST['share_expires_at'] ?? '';
        if (!empty($shareExpires)) {
            $shareExpires = str_replace('T', ' ', $shareExpires) . ':00';
        }

        if (!$galleryId || !in_array($action, ['enable', 'disable', 'regenerate', 'update'], true)) {
            json_response(['success' => false, 'message' => 'درخواست نامعتبر'], 400);
        }

        $galleryModel = new ClientGallery();
        $gallery = $galleryModel->getById($galleryId);
        if (!$gallery) {
            json_response(['success' => false, 'message' => 'گالری پیدا نشد'], 404);
        }

        try {
            if ($action === 'disable') {
                $galleryModel->revokeShareToken($galleryId);
                json_response(['success' => true, 'message' => 'لینک اشتراک‌گذاری غیرفعال شد', 'share_url' => '', 'share_token' => '']);
            }

            $token = $galleryModel->generateShareToken($galleryId);
            if ($action === 'regenerate') {
                $galleryModel->revokeShareToken($galleryId);
                $token = $galleryModel->generateShareToken($galleryId);
            }

            // Update password and expiration if provided
            if (in_array($action, ['enable', 'regenerate', 'update'], true)) {
                $galleryModel->setSharePassword($galleryId, $sharePassword);
                $galleryModel->setShareExpiration($galleryId, $shareExpires);
            }

            // Re-fetch to get updated fields
            $gallery = $galleryModel->getById($galleryId);
            $shareUrl = url('gallery/share/' . $token);

            // Send email notification to client on enable/regenerate
            if (in_array($action, ['enable', 'regenerate'], true)) {
                $this->sendGalleryShareEmail($gallery, $shareUrl, $sharePassword, $shareExpires);

                $notification = new Notification();
                $notification->create(
                    $gallery['user_id'],
                    'GALLERY_SHARED',
                    'گالری جدید برای شما به اشتراک گذاشته شد',
                    'گالری «' . $gallery['title'] . '» آماده مشاهده است. می‌توانید از طریق داشبورد یا لینک ارسالی به آن دسترسی داشته باشید.',
                    $gallery['id'],
                    'client_gallery'
                );
            }

            json_response([
                'success' => true,
                'message' => 'لینک اشتراک‌گذاری فعال شد',
                'share_token' => $token,
                'share_url' => $shareUrl
            ]);
        } catch (Exception $e) {
            log_error('manageClientGalleryShare failed', $e->getMessage());
            json_response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Send email notification to client when gallery share link is enabled.
     */
    private function sendGalleryShareEmail($gallery, $shareUrl, $password = '', $expires = '') {
        if (empty($gallery['user_email'])) {
            return false;
        }

        $subject = 'گالری عکس‌های شما در Mirohood آماده است';

        $content = '<p style="color:#f4f1ea;">سلام <strong>' . htmlspecialchars($gallery['user_name']) . '</strong>،</p>';
        $content .= '<p style="color:#f4f1ea;">گالری عکس‌های شما با عنوان <strong>«' . htmlspecialchars($gallery['title']) . '»</strong> در Mirohood آماده مشاهده است.</p>';
        $content .= '<p style="color:#f4f1ea;">لینک گالری:</p>';
        $content .= '<p style="direction:ltr;text-align:left;word-break:break-all;background:rgba(0,0,0,0.2);padding:0.75rem 1rem;border-radius:0.5rem;border:1px solid rgba(200,168,98,0.15);color:#c8a862;font-size:0.9rem;">' . htmlspecialchars($shareUrl) . '</p>';
        if (!empty($password)) {
            $content .= '<p style="color:#f4f1ea;margin-top:1rem;">رمز عبور گالری: <strong>' . htmlspecialchars($password) . '</strong></p>';
        }
        if (!empty($expires)) {
            $content .= '<p style="color:#8a8580;font-size:0.85rem;margin-top:1rem;">این لینک تا تاریخ <strong>' . htmlspecialchars($expires) . '</strong> فعال است.</p>';
        }

        $htmlBody = EmailHelper::buildHtmlBody(
            'گالری عکس‌های شما در Mirohood',
            $content,
            $shareUrl,
            'مشاهده گالری'
        );

        $sent = EmailHelper::sendHtml($gallery['user_email'], $subject, $htmlBody);
        if (!$sent) {
            log_error('Gallery share email failed', ['gallery_id' => $gallery['id'], 'user_id' => $gallery['user_id'], 'email' => $gallery['user_email']]);
        }
        return $sent;
    }
}
