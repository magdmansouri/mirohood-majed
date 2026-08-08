<?php
// controllers/ClientGalleryController.php - Public shared client galleries

class ClientGalleryController {

    private $galleryModel;
    private $imageModel;

    public function __construct() {
        $this->galleryModel = new ClientGallery();
        $this->imageModel = new ClientGalleryImage();
    }

    public function shared($token) {
        $gallery = $this->galleryModel->getByShareToken($token);
        if (!$gallery) {
            http_response_code(404);
            echo '<h1 style="color:#f4f1ea;text-align:center;padding:5rem;background:#0a0908;min-height:100vh;">گالری پیدا نشد یا لینک منقضی شده است</h1>';
            exit;
        }

        if ($this->galleryModel->isShareExpired($gallery)) {
            http_response_code(403);
            echo '<h1 style="color:#f4f1ea;text-align:center;padding:5rem;background:#0a0908;min-height:100vh;">این لینک گالری منقضی شده است</h1>';
            exit;
        }

        // Password protection
        $sessionKey = 'gallery_unlocked_' . $gallery['id'];
        if (!empty($gallery['share_password'])) {
            $providedPassword = $_POST['gallery_password'] ?? $_SESSION[$sessionKey] ?? '';
            if (!$this->galleryModel->verifySharePassword($gallery['id'], $providedPassword)) {
                $this->renderPasswordForm($gallery, $token);
                exit;
            }
            if (!empty($_POST['gallery_password'])) {
                $_SESSION[$sessionKey] = $_POST['gallery_password'];
            }
        }

        // Increment view count
        $this->galleryModel->incrementViewCount($gallery['id']);
        $gallery['view_count'] = ($gallery['view_count'] ?? 0) + 1;

        $images = $this->imageModel->getByGallery($gallery['id']);

        $contentModel = new SiteContent();
        $content = $contentModel->getMultiple([
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);

        $data = [
            'page_title' => $gallery['title'] . ' | گالری مشتری',
            'current_page' => 'my-gallery',
            'gallery' => $gallery,
            'images' => $images,
            'content' => $content,
            'share_url' => url('gallery/share/' . $token)
        ];
        $contentView = render('client-gallery/shared', $data);
        echo render('layout', array_merge($data, ['mainContent' => $contentView]));
    }

    private function renderPasswordForm($gallery, $token) {
        $contentModel = new SiteContent();
        $content = $contentModel->getMultiple([
            'layout.nav_home', 'layout.nav_gallery', 'layout.nav_booking', 'layout.nav_about',
            'layout.nav_dashboard', 'layout.nav_login', 'layout.nav_logout', 'layout.footer_text'
        ]);
        $error = !empty($_POST['gallery_password']) ? 'رمز عبور اشتباه است' : '';

        $formHtml = '<div style="max-width:420px;margin:6rem auto;padding:2rem;background:#151413;border:1px solid rgba(200,168,98,0.15);border-radius:1rem;text-align:center;">
            <h1 style="font-family:Cormorant Garamond,serif;font-size:1.8rem;font-weight:300;color:#f4f1ea;margin-bottom:0.5rem;">' . h($gallery['title']) . '</h1>
            <p style="color:#8a8580;font-size:0.85rem;margin-bottom:1.5rem;">این گالری رمز عبور دارد.</p>
            ' . ($error ? '<p style="color:#f87171;font-size:0.85rem;margin-bottom:1rem;">' . $error . '</p>' : '') . '
            <form method="POST" action="' . url('gallery/share/' . $token) . '" style="display:flex;flex-direction:column;gap:1rem;">
                <input type="password" name="gallery_password" placeholder="رمز عبور گالری" style="padding:0.7rem 1rem;background:rgba(255,255,255,0.04);border:1px solid rgba(200,168,98,0.15);border-radius:0.6rem;color:#f4f1ea;font-size:0.9rem;" required>
                <button type="submit" style="padding:0.7rem 1.5rem;background:linear-gradient(135deg,#c8a862,#a8893a);color:#0a0908;border:none;border-radius:9999px;font-weight:600;cursor:pointer;">مشاهده گالری</button>
            </form>
        </div>';

        echo render('layout', [
            'page_title' => 'ورود به گالری | Mirohood',
            'current_page' => 'my-gallery',
            'content' => $content,
            'mainContent' => $formHtml
        ]);
        exit;
    }

    public function downloadShared($token) {
        try {
            $gallery = $this->galleryModel->getByShareToken($token);
            if (!$gallery) {
                http_response_code(404);
                echo 'گالری پیدا نشد';
                exit;
            }

            if ($this->galleryModel->isShareExpired($gallery)) {
                http_response_code(403);
                echo 'این لینک گالری منقضی شده است';
                exit;
            }

            $sessionKey = 'gallery_unlocked_' . $gallery['id'];
            if (!empty($gallery['share_password'])) {
                $providedPassword = $_GET['gallery_password'] ?? $_SESSION[$sessionKey] ?? '';
                if (!$this->galleryModel->verifySharePassword($gallery['id'], $providedPassword)) {
                    http_response_code(403);
                    echo 'برای دانلود گالری باید رمز عبور را وارد کنید';
                    exit;
                }
            }

            $quality = ($_GET['quality'] ?? '') === 'optimized' ? 'optimized' : 'original';
            $this->streamZip($gallery['id'], $quality);
        } catch (Exception $e) {
            log_error('downloadShared failed', $e->getMessage());
            http_response_code(500);
            echo 'خطا در دانلود: ' . $e->getMessage();
            exit;
        }
    }

    private function streamZip($galleryId, $quality = 'original') {
        $tempFile = $this->galleryModel->buildZip($galleryId, null, $quality);
        if (!file_exists($tempFile)) {
            throw new Exception('فایل ZIP ساخته نشد');
        }

        $filename = 'mirohood-gallery-' . $galleryId . ($quality === 'optimized' ? '-light' : '-original') . '.zip';
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tempFile));
        header('Cache-Control: no-store, no-cache, must-revalidate');

        readfile($tempFile);
        unlink($tempFile);
        exit;
    }
}
