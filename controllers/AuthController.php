<?php
// controllers/AuthController.php - User auth + enhanced dashboard

class AuthController {

    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        $error = '';
        $redirect = $this->sanitizeRedirect($_GET['redirect'] ?? 'dashboard');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
                $error = 'نشست شما منقضی شده، لطفاً دوباره تلاش کنید';
            } else {
                $phone = sanitize_input($_POST['phone'] ?? '');
                $password = $_POST['password'] ?? '';
                $user = $this->userModel->getByPhone($phone);

                if ($user && $this->userModel->verifyPassword($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    header('Location: ' . url($redirect));
                    exit;
                } else {
                    $error = 'شماره موبایل یا رمز عبور اشتباه است';
                }
            }
        }

        $data = [
            'page_title' => 'ورود',
            'error' => $error,
            'redirect' => $redirect,
            'csrf_token' => generate_csrf_token()
        ];
        echo render('auth/login', $data);
    }

    public function register() {
        $error = '';
        $redirect = $this->sanitizeRedirect($_GET['redirect'] ?? 'dashboard');
        $values = [
            'name' => '',
            'phone' => '',
            'email' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
                $error = 'نشست شما منقضی شده، لطفاً دوباره تلاش کنید';
            } else {
                $values = [
                    'name' => sanitize_input($_POST['name'] ?? ''),
                    'phone' => sanitize_input($_POST['phone'] ?? ''),
                    'email' => sanitize_input($_POST['email'] ?? '')
                ];
                $password = $_POST['password'] ?? '';
                $confirm = $_POST['password_confirm'] ?? '';

                $errors = $this->validateRegister($values, $password, $confirm);

                if (empty($errors)) {
                    try {
                        $userId = $this->userModel->create([
                            'name' => $values['name'],
                            'phone' => $values['phone'],
                            'email' => $values['email'],
                            'password' => $password
                        ]);
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['user_name'] = $values['name'];
                        header('Location: ' . url($redirect));
                        exit;
                    } catch (Exception $e) {
                        log_error('User registration failed', $e->getMessage());
                        $error = 'خطا در ثبت‌نام: ' . $e->getMessage();
                    }
                } else {
                    $error = implode(' ', $errors);
                }
            }
        }

        $data = [
            'page_title' => 'ثبت‌نام',
            'error' => $error,
            'redirect' => $redirect,
            'values' => $values,
            'csrf_token' => generate_csrf_token()
        ];
        echo render('auth/register', $data);
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        header('Location: ' . url(''));
        exit;
    }

    public function forgotPassword() {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
                $error = 'نشست شما منقضی شده، لطفاً دوباره تلاش کنید';
            } else {
                $phone = sanitize_input($_POST['phone'] ?? '');
                $user = $this->userModel->getByPhone($phone);

                if (!$user) {
                    $error = 'کاربری با این شماره موبایل یافت نشد';
                } elseif (empty($user['email'])) {
                    $error = 'برای این کاربر ایمیل ثبت نشده است. لطفاً با پشتیبانی تماس بگیرید.';
                } else {
                    $token = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+2 hours'));
                    $resetModel = new PasswordReset();
                    $resetModel->create($user['id'], $token, $expiresAt);

                    $resetUrl = SITE_URL . '/reset-password?token=' . $token;
                    $subject = 'بازیابی رمز عبور Mirohood';

                    $content = '<p style="color:#f4f1ea;">سلام <strong>' . htmlspecialchars($user['name']) . '</strong>،</p>';
                    $content .= '<p style="color:#f4f1ea;">درخواست بازیابی رمز عبور برای حساب شما در Mirohood دریافت شد. اگر این درخواست از سوی شما نبود، لطفاً این ایمیل را نادیده بگیرید.</p>';
                    $content .= '<p style="color:#f4f1ea;">برای تنظیم رمز عبور جدید، روی دکمهٔ زیر کلیک کنید. این لینک تا ۲ ساعت اعتبار دارد.</p>';

                    $htmlBody = EmailHelper::buildHtmlBody(
                        'بازیابی رمز عبور',
                        $content,
                        $resetUrl,
                        'تنظیم رمز عبور جدید'
                    );

                    if (EmailHelper::sendHtml($user['email'], $subject, $htmlBody)) {
                        $success = 'لینک بازیابی رمز عبور به ایمیل شما ارسال شد. لطفاً ایمیل خود را بررسی کنید.';
                    } else {
                        log_error('Password reset email failed', ['user_id' => $user['id'], 'email' => $user['email']]);
                        $error = 'خطا در ارسال ایمیل. لطفاً دوباره تلاش کنید.';
                    }
                }
            }
        }

        $data = [
            'page_title' => 'بازیابی رمز عبور',
            'error' => $error,
            'success' => $success,
            'csrf_token' => generate_csrf_token()
        ];
        echo render('auth/forgot-password', $data);
    }

    public function resetPassword() {
        $error = '';
        $success = '';
        $token = $_GET['token'] ?? ($_POST['token'] ?? '');

        if (empty($token)) {
            header('Location: ' . url('forgot-password'));
            exit;
        }

        $resetModel = new PasswordReset();
        $reset = $resetModel->findByToken($token);

        if (!$reset) {
            $error = 'لینک بازیابی نامعتبر یا منقضی شده است.';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reset) {
            if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
                $error = 'نشست شما منقضی شده، لطفاً دوباره تلاش کنید';
            } else {
                $password = $_POST['password'] ?? '';
                $confirm = $_POST['password_confirm'] ?? '';

                if (strlen($password) < 6) {
                    $error = 'رمز عبور باید حداقل ۶ کاراکتر باشد';
                } elseif ($password !== $confirm) {
                    $error = 'رمز عبور و تکرار آن یکسان نیست';
                } else {
                    try {
                        $this->userModel->update($reset['user_id'], ['password' => $password]);
                        $resetModel->deleteByToken($token);
                        $success = 'رمز عبور با موفقیت تغییر کرد. اکنون می‌توانید وارد شوید.';
                    } catch (Exception $e) {
                        log_error('Password reset failed', $e->getMessage());
                        $error = 'خطا در تغییر رمز عبور';
                    }
                }
            }
        }

        $data = [
            'page_title' => 'تنظیم رمز عبور جدید',
            'error' => $error,
            'success' => $success,
            'token' => $token,
            'csrf_token' => generate_csrf_token()
        ];
        echo render('auth/reset-password', $data);
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login?redirect=dashboard'));
            exit;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            $this->logout();
        }

        $bookingModel = new Booking();
        $galleryModel = new ClientGallery();
        $notificationModel = new Notification();
        $downloadLog = new DownloadLog();

        $statusFilter = $_GET['status'] ?? null;
        $allowedFilters = ['PENDING', 'CONFIRMED', 'CANCELLED', 'COMPLETED'];
        $allBookings = $bookingModel->getByUser($user['id']);
        if ($statusFilter && in_array($statusFilter, $allowedFilters, true)) {
            $bookings = $bookingModel->getByUserAndStatus($user['id'], $statusFilter);
        } else {
            $bookings = $allBookings;
        }

        $upcomingBookings = $bookingModel->getUpcoming($user['id'], 5);
        $galleryStats = $galleryModel->getStatsByUser($user['id']);
        $recentGalleries = $galleryModel->getRecentByUser($user['id'], 4);
        $unreadCount = $notificationModel->countUnread($user['id']);
        $recentNotifications = $notificationModel->getByUser($user['id'], 5);
        $downloadCount = $downloadLog->countByUser($user['id']);

        $contentModel = new SiteContent();
        $content = $contentModel->getMultiple([
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);

        $data = [
            'page_title' => 'داشبورد کاربری',
            'current_page' => 'dashboard',
            'user' => $user,
            'bookings' => $bookings,
            'all_bookings' => $allBookings,
            'upcoming_bookings' => $upcomingBookings,
            'status_filter' => $statusFilter,
            'gallery_stats' => $galleryStats,
            'recent_galleries' => $recentGalleries,
            'unread_count' => $unreadCount,
            'recent_notifications' => $recentNotifications,
            'download_count' => $downloadCount,
            'content' => $content
        ];
        $contentView = render('auth/dashboard', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login?redirect=profile'));
            exit;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            $this->logout();
        }

        $success = $_SESSION['profile_success'] ?? '';
        $error = $_SESSION['profile_error'] ?? '';
        unset($_SESSION['profile_success'], $_SESSION['profile_error']);

        $contentModel = new SiteContent();
        $content = $contentModel->getMultiple([
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);

        $data = [
            'page_title' => 'پروفایل کاربری',
            'current_page' => 'profile',
            'user' => $user,
            'success' => $success,
            'error' => $error,
            'csrf_token' => generate_csrf_token(),
            'content' => $content
        ];
        $contentView = render('auth/profile', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }

    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login?redirect=profile'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('profile'));
            exit;
        }

        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            $_SESSION['profile_error'] = 'نشست شما منقضی شده است';
            header('Location: ' . url('profile'));
            exit;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            $this->logout();
        }

        $action = $_POST['action'] ?? '';

        if ($action === 'info') {
            $name = sanitize_input($_POST['name'] ?? '');
            $email = sanitize_input($_POST['email'] ?? '');
            if (empty($name) || strlen($name) < 2) {
                $_SESSION['profile_error'] = 'نام و نام خانوادگی الزامی است';
            } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['profile_error'] = 'ایمیل معتبر نیست';
            } else {
                try {
                    $this->userModel->update($user['id'], ['name' => $name, 'email' => $email]);
                    $_SESSION['user_name'] = $name;
                    $_SESSION['profile_success'] = 'اطلاعات پروفایل با موفقیت به‌روز شد';
                } catch (Exception $e) {
                    log_error('Profile update failed', $e->getMessage());
                    $_SESSION['profile_error'] = 'خطا در به‌روزرسانی اطلاعات';
                }
            }
        } elseif ($action === 'password') {
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if (!$this->userModel->verifyPassword($current, $user['password_hash'])) {
                $_SESSION['profile_error'] = 'رمز عبور فعلی اشتباه است';
            } elseif (strlen($new) < 6) {
                $_SESSION['profile_error'] = 'رمز عبور جدید باید حداقل ۶ کاراکتر باشد';
            } elseif ($new !== $confirm) {
                $_SESSION['profile_error'] = 'رمز عبور جدید و تکرار آن یکسان نیست';
            } else {
                try {
                    $this->userModel->update($user['id'], ['password' => $new]);
                    $_SESSION['profile_success'] = 'رمز عبور با موفقیت تغییر کرد';
                } catch (Exception $e) {
                    log_error('Password change failed', $e->getMessage());
                    $_SESSION['profile_error'] = 'خطا در تغییر رمز عبور';
                }
            }
        } elseif ($action === 'avatar') {
            $this->handleAvatarUpload($user);
        }

        header('Location: ' . url('profile'));
        exit;
    }

    private function handleAvatarUpload($user) {
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['profile_error'] = 'فایل تصویر ارسال نشد';
            return;
        }
        $file = $_FILES['avatar'];
        if (!ImageHelper::validate($file)) {
            $_SESSION['profile_error'] = 'فرمت تصویر مجاز نیست (JPG یا PNG)';
            return;
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            $_SESSION['profile_error'] = 'حجم تصویر حداکثر ۲ مگابایت';
            return;
        }
        try {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/avatars';
            $ext = ImageHelper::getExtension($file);
            $filename = 'avatar_' . $user['id'] . '_' . ImageHelper::generateFilename($ext);
            $paths = ImageHelper::process($file['tmp_name'], $uploadDir, $filename);
            $url = ImageHelper::toUrl($paths['image']);

            if (!empty($user['avatar'])) {
                ImageHelper::deleteFiles($_SERVER['DOCUMENT_ROOT'] . $user['avatar']);
            }
            $this->userModel->update($user['id'], ['avatar' => $url]);
            $_SESSION['profile_success'] = 'آواتار با موفقیت به‌روز شد';
        } catch (Exception $e) {
            log_error('Avatar upload failed', $e->getMessage());
            $_SESSION['profile_error'] = 'خطا در آپلود آواتار';
        }
    }

    public function myGallery() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login?redirect=my-gallery'));
            exit;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            $this->logout();
        }

        $galleryModel = new ClientGallery();
        $imageModel = new ClientGalleryImage();
        $galleries = $galleryModel->getByUser($user['id']);

        foreach ($galleries as &$gallery) {
            $gallery['images'] = $imageModel->getByGallery($gallery['id']);
            $gallery['favorite_count'] = 0;
            $gallery['final_count'] = 0;
            foreach ($gallery['images'] as $img) {
                if ($img['is_favorite']) $gallery['favorite_count']++;
                if ($img['is_final_selection']) $gallery['final_count']++;
            }
            $gallery['download_count'] = $gallery['download_count'] ?? 0;
            $gallery['view_count'] = $gallery['view_count'] ?? 0;
        }
        unset($gallery);

        $contentModel = new SiteContent();
        $content = $contentModel->getMultiple([
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);

        $data = [
            'page_title' => 'گالری من',
            'current_page' => 'my-gallery',
            'user' => $user,
            'galleries' => $galleries,
            'content' => $content
        ];
        $contentView = render('auth/my-gallery', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }

    public function downloadMyGalleryZip() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login?redirect=my-gallery'));
            exit;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            $this->logout();
        }

        $galleryId = (int) ($_GET['id'] ?? 0);
        if (!$galleryId) {
            http_response_code(400);
            echo 'شناسه گالری مشخص نشده است';
            exit;
        }

        $galleryModel = new ClientGallery();
        $gallery = $galleryModel->getById($galleryId);
        if (!$gallery || (int) $gallery['user_id'] !== (int) $user['id']) {
            http_response_code(403);
            echo 'دسترسی غیرمجاز';
            exit;
        }

        try {
            $quality = ($_GET['quality'] ?? '') === 'optimized' ? 'optimized' : 'original';
            $tempFile = $galleryModel->buildZip($galleryId, null, $quality);
            $filename = 'mirohood-gallery-' . $galleryId . ($quality === 'optimized' ? '-light' : '-original') . '.zip';
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($tempFile));
            header('Cache-Control: no-store, no-cache, must-revalidate');

            (new DownloadLog())->log($user['id'], $galleryId);
            $galleryModel->incrementDownloadCount($galleryId);

            readfile($tempFile);
            unlink($tempFile);
            exit;
        } catch (Exception $e) {
            log_error('downloadMyGalleryZip failed', $e->getMessage());
            http_response_code(500);
            echo 'خطا در دانلود: ' . $e->getMessage();
            exit;
        }
    }

    public function toggleFavorite() {
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'لطفاً وارد شوید'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            json_response(['success' => false, 'message' => 'نشست منقضی شده'], 403);
        }
        $imageId = (int) ($_POST['image_id'] ?? 0);
        if (!$imageId) {
            json_response(['success' => false, 'message' => 'شناسه تصویر نامعتبر'], 400);
        }
        try {
            $imageModel = new ClientGalleryImage();
            $newValue = $imageModel->toggleFavorite($imageId);
            json_response([
                'success' => true,
                'message' => $newValue ? 'به علاقه‌مندی‌ها اضافه شد' : 'از علاقه‌مندی‌ها حذف شد',
                'is_favorite' => (bool) $newValue
            ]);
        } catch (Exception $e) {
            log_error('toggleFavorite failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در تغییر وضعیت'], 500);
        }
    }

    public function toggleFinalSelection() {
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'لطفاً وارد شوید'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            json_response(['success' => false, 'message' => 'نشست منقضی شده'], 403);
        }
        $imageId = (int) ($_POST['image_id'] ?? 0);
        if (!$imageId) {
            json_response(['success' => false, 'message' => 'شناسه تصویر نامعتبر'], 400);
        }
        try {
            $imageModel = new ClientGalleryImage();
            $image = $imageModel->getById($imageId);
            if (!$image) {
                json_response(['success' => false, 'message' => 'تصویر پیدا نشد'], 404);
            }
            $gallery = (new ClientGallery())->getById($image['gallery_id']);
            if (!$gallery || (int) $gallery['user_id'] !== (int) $_SESSION['user_id']) {
                json_response(['success' => false, 'message' => 'دسترسی غیرمجاز'], 403);
            }

            $current = $image['is_final_selection'];
            if (!$current) {
                $maxSelections = (int) ($gallery['max_selections'] ?? 0);
                if ($maxSelections > 0) {
                    $selectedCount = $imageModel->getFinalSelectionCount($gallery['id']);
                    if ($selectedCount >= $maxSelections) {
                        json_response(['success' => false, 'message' => 'حداکثر ' . $maxSelections . ' عکس قابل انتخاب است'], 400);
                    }
                }
            }
            $newValue = $imageModel->toggleFinalSelection($imageId);
            json_response([
                'success' => true,
                'message' => $newValue ? 'به انتخاب نهایی اضافه شد' : 'از انتخاب نهایی حذف شد',
                'is_final_selection' => (bool) $newValue
            ]);
        } catch (Exception $e) {
            log_error('toggleFinalSelection failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در تغییر وضعیت'], 500);
        }
    }

    public function confirmFinalSelection() {
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'لطفاً وارد شوید'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            json_response(['success' => false, 'message' => 'نشست منقضی شده'], 403);
        }
        $galleryId = (int) ($_POST['gallery_id'] ?? 0);
        if (!$galleryId) {
            json_response(['success' => false, 'message' => 'شناسه گالری نامعتبر'], 400);
        }
        try {
            $galleryModel = new ClientGallery();
            $gallery = $galleryModel->getById($galleryId);
            if (!$gallery || (int) $gallery['user_id'] !== (int) $_SESSION['user_id']) {
                json_response(['success' => false, 'message' => 'دسترسی غیرمجاز'], 403);
            }
            $imageModel = new ClientGalleryImage();
            $count = $imageModel->getFinalSelectionCount($galleryId);
            if ($count === 0) {
                json_response(['success' => false, 'message' => 'حداقل یک عکس انتخاب کنید'], 400);
            }
            $notificationModel = new Notification();
            $notificationModel->create(
                $gallery['user_id'],
                'SELECTION_CONFIRMED',
                'انتخاب نهایی عکس‌ها تأیید شد',
                'کاربر انتخاب نهایی خود را از گالری «' . $gallery['title'] . '» ثبت کرد. تعداد انتخاب: ' . $count . ' عکس.',
                $gallery['id'],
                'client_gallery'
            );
            json_response([
                'success' => true,
                'message' => 'انتخاب نهایی شما با موفقیت ثبت شد. تیم Mirohood آن را بررسی می‌کند.'
            ]);
        } catch (Exception $e) {
            log_error('confirmFinalSelection failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در ثبت انتخاب'], 500);
        }
    }

    public function requestBookingAction() {
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'لطفاً وارد شوید'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            json_response(['success' => false, 'message' => 'نشست منقضی شده'], 403);
        }
        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            $this->logout();
        }

        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $action = $_POST['request_action'] ?? '';
        $note = sanitize_input($_POST['note'] ?? '');
        $newDate = sanitize_input($_POST['new_date'] ?? '');
        $newTime = sanitize_input($_POST['new_time'] ?? '');

        if (!$bookingId || !in_array($action, ['cancel', 'reschedule'], true)) {
            json_response(['success' => false, 'message' => 'درخواست نامعتبر'], 400);
        }

        try {
            $bookingModel = new Booking();
            $booking = $bookingModel->getById($bookingId);
            if (!$booking || (int) $booking['user_id'] !== (int) $user['id']) {
                json_response(['success' => false, 'message' => 'دسترسی غیرمجاز'], 403);
            }
            if (in_array($booking['status'], ['CANCELLED', 'COMPLETED'], true)) {
                json_response(['success' => false, 'message' => 'امکان درخواست برای این نوبت وجود ندارد'], 400);
            }

            if ($action === 'cancel') {
                $bookingModel->requestCancel($bookingId, $note);
                $message = 'درخواست لغو نوبت ثبت شد. پس از بررسی ادمین به شما اطلاع‌رسانی می‌شود.';
            } else {
                if (empty($newDate) || empty($newTime)) {
                    json_response(['success' => false, 'message' => 'تاریخ و ساعت جدید را وارد کنید'], 400);
                }
                $bookingModel->requestReschedule($bookingId, $newDate, $newTime, $note);
                $message = 'درخواست تغییر زمان نوبت ثبت شد. پس از بررسی ادمین به شما اطلاع‌رسانی می‌شود.';
            }

            json_response(['success' => true, 'message' => $message]);
        } catch (Exception $e) {
            log_error('requestBookingAction failed', $e->getMessage());
            json_response(['success' => false, 'message' => 'خطا در ثبت درخواست'], 500);
        }
    }

    public function notifications() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login?redirect=notifications'));
            exit;
        }
        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            $this->logout();
        }
        $notificationModel = new Notification();
        $all = $notificationModel->getByUser($user['id'], 100);
        $unreadCount = $notificationModel->countUnread($user['id']);

        $contentModel = new SiteContent();
        $content = $contentModel->getMultiple([
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);

        $data = [
            'page_title' => 'اعلانات',
            'current_page' => 'notifications',
            'user' => $user,
            'notifications' => $all,
            'unread_count' => $unreadCount,
            'content' => $content
        ];
        $contentView = render('auth/notifications', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }

    public function markNotificationRead() {
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'لطفاً وارد شوید'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            json_response(['success' => false, 'message' => 'نشست منقضی شده'], 403);
        }
        $id = (int) ($_POST['id'] ?? 0);
        $notificationModel = new Notification();
        $notificationModel->markAsRead($id, $_SESSION['user_id']);
        json_response(['success' => true, 'unread_count' => $notificationModel->countUnread($_SESSION['user_id'])]);
    }

    public function markAllNotificationsRead() {
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'لطفاً وارد شوید'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            json_response(['success' => false, 'message' => 'نشست منقضی شده'], 403);
        }
        $notificationModel = new Notification();
        $notificationModel->markAllAsRead($_SESSION['user_id']);
        json_response(['success' => true, 'unread_count' => 0]);
    }

    public function deleteNotification() {
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'لطفاً وارد شوید'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            json_response(['success' => false, 'message' => 'نشست منقضی شده'], 403);
        }
        $id = (int) ($_POST['id'] ?? 0);
        $notificationModel = new Notification();
        $notificationModel->delete($id, $_SESSION['user_id']);
        json_response(['success' => true, 'unread_count' => $notificationModel->countUnread($_SESSION['user_id'])]);
    }

    public function downloads() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login?redirect=downloads'));
            exit;
        }
        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            $this->logout();
        }
        $downloadLog = new DownloadLog();
        $logs = $downloadLog->getByUser($user['id'], 100);

        $contentModel = new SiteContent();
        $content = $contentModel->getMultiple([
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);

        $data = [
            'page_title' => 'تاریخچه دانلودها',
            'current_page' => 'downloads',
            'user' => $user,
            'downloads' => $logs,
            'content' => $content
        ];
        $contentView = render('auth/downloads', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }

    private function sanitizeRedirect($redirect) {
        $allowed = ['dashboard', 'booking', 'gallery', 'about', 'my-gallery', 'profile', 'notifications', 'downloads', ''];
        $redirect = trim($redirect, '/');
        if (in_array($redirect, $allowed, true)) {
            return $redirect;
        }
        return 'dashboard';
    }

    private function validateRegister($values, $password, $confirm) {
        $errors = [];
        if (empty($values['name']) || strlen($values['name']) < 2) {
            $errors[] = 'نام و نام خانوادگی الزامی است';
        }
        $cleanPhone = preg_replace('/[^0-9]/', '', $values['phone']);
        if (!preg_match('/^09[0-9]{9}$/', $cleanPhone)) {
            $errors[] = 'شماره موبایل معتبر وارد کنید';
        }
        if (!empty($values['email']) && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'ایمیل معتبر نیست';
        }
        if (strlen($password) < 6) {
            $errors[] = 'رمز عبور باید حداقل ۶ کاراکتر باشد';
        }
        if ($password !== $confirm) {
            $errors[] = 'رمز عبور و تکرار آن یکسان نیست';
        }
        if ($this->userModel->phoneExists($values['phone'])) {
            $errors[] = 'این شماره موبایل قبلاً ثبت‌نام شده است';
        }
        return $errors;
    }
}
