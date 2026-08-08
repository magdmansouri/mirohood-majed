<?php
// models/ClientGallery.php

class ClientGallery {

    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll() {
        return $this->db->fetchAll(
            "SELECT cg.*, u.name AS user_name, u.phone AS user_phone
             FROM client_galleries cg
             JOIN users u ON u.id = cg.user_id
             ORDER BY cg.created_at DESC"
        );
    }

    public function getById($id) {
        return $this->db->fetchOne(
            "SELECT cg.*, u.name AS user_name, u.phone AS user_phone
             FROM client_galleries cg
             JOIN users u ON u.id = cg.user_id
             WHERE cg.id = ?",
            [$id]
        );
    }

    public function getByUser($userId) {
        return $this->db->fetchAll(
            "SELECT * FROM client_galleries WHERE user_id = ? AND status = 1 ORDER BY created_at DESC",
            [$userId]
        );
    }

    public function create($data) {
        return $this->db->insert('client_galleries', $data);
    }

    public function update($id, $data) {
        return $this->db->update('client_galleries', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('client_galleries', 'id = ?', [$id]);
    }

    public function countByUser($userId) {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM client_galleries WHERE user_id = ?",
            [$userId]
        );
    }

    public function getByShareToken($token) {
        return $this->db->fetchOne(
            "SELECT cg.*, u.name AS user_name, u.phone AS user_phone, u.email AS user_email
             FROM client_galleries cg
             JOIN users u ON u.id = cg.user_id
             WHERE cg.share_token = ? AND cg.status = 1",
            [$token]
        );
    }

    public function generateShareToken($galleryId) {
        $gallery = $this->getById($galleryId);
        if (!$gallery) {
            throw new Exception('گالری پیدا نشد');
        }
        if (!empty($gallery['share_token'])) {
            return $gallery['share_token'];
        }
        $token = null;
        for ($i = 0; $i < 10; $i++) {
            $candidate = bin2hex(random_bytes(16));
            $existing = $this->getByShareToken($candidate);
            if (!$existing) {
                $token = $candidate;
                break;
            }
        }
        if (!$token) {
            throw new Exception('امکان ساخت لینک اشتراک‌گذاری وجود ندارد');
        }
        $this->update($galleryId, ['share_token' => $token]);
        return $token;
    }

    public function revokeShareToken($galleryId) {
        return $this->update($galleryId, [
            'share_token' => null,
            'share_password' => null,
            'share_expires_at' => null
        ]);
    }

    public function isShareEnabled($galleryId) {
        $gallery = $this->getById($galleryId);
        return $gallery && !empty($gallery['share_token']) && (int) $gallery['status'] === 1;
    }

    public function setSharePassword($galleryId, $password) {
        if ($password === '' || $password === null) {
            return $this->update($galleryId, ['share_password' => null]);
        }
        return $this->update($galleryId, ['share_password' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    public function verifySharePassword($galleryId, $password) {
        $gallery = $this->getById($galleryId);
        if (!$gallery || empty($gallery['share_password'])) {
            return true;
        }
        return password_verify($password, $gallery['share_password']);
    }

    public function setShareExpiration($galleryId, $expiresAt) {
        if (empty($expiresAt)) {
            return $this->update($galleryId, ['share_expires_at' => null]);
        }
        return $this->update($galleryId, ['share_expires_at' => $expiresAt]);
    }

    public function isShareExpired($gallery) {
        if (empty($gallery['share_expires_at'])) {
            return false;
        }
        return strtotime($gallery['share_expires_at']) < time();
    }

    public function incrementViewCount($galleryId) {
        return $this->db->query(
            "UPDATE client_galleries SET view_count = view_count + 1 WHERE id = ?",
            [$galleryId]
        );
    }

    public function incrementDownloadCount($galleryId) {
        return $this->db->query(
            "UPDATE client_galleries SET download_count = download_count + 1, last_downloaded_at = NOW() WHERE id = ?",
            [$galleryId]
        );
    }

    public function getDownloadCount($galleryId) {
        return (int) $this->db->fetchColumn(
            "SELECT download_count FROM client_galleries WHERE id = ?",
            [$galleryId]
        );
    }

    public function getViewCount($galleryId) {
        return (int) $this->db->fetchColumn(
            "SELECT view_count FROM client_galleries WHERE id = ?",
            [$galleryId]
        );
    }

    public function getStatsByUser($userId) {
        return $this->db->fetchOne(
            "SELECT COUNT(*) AS total_galleries,
                    COALESCE(SUM(view_count), 0) AS total_views,
                    COALESCE(SUM(download_count), 0) AS total_downloads
             FROM client_galleries
             WHERE user_id = ? AND status = 1",
            [$userId]
        );
    }

    public function getRecentByUser($userId, $limit = 5) {
        return $this->db->fetchAll(
            "SELECT * FROM client_galleries WHERE user_id = ? AND status = 1 ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    /**
     * Build a ZIP archive of the original images for a gallery.
     * Returns absolute path to a temporary ZIP file, or false on failure.
     */
    public function buildZip($galleryId, $zipBasename = null, $quality = 'original') {
        if (!class_exists('ZipArchive')) {
            throw new Exception('ZipArchive در PHP فعال نیست');
        }

        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $imageModel = new ClientGalleryImage();
        $images = $imageModel->getByGallery($galleryId);
        if (empty($images)) {
            throw new Exception('این گالری تصویری ندارد');
        }

        $zipName = $zipBasename ?: 'mirohood-gallery-' . $galleryId . '.zip';
        $tempDir = sys_get_temp_dir();
        $tempFile = $tempDir . '/' . uniqid('mirohood_zip_', true) . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception('امکان ساخت فایل ZIP وجود ندارد');
        }

        $quality = $quality === 'optimized' ? 'optimized' : 'original';
        foreach ($images as $index => $img) {
            $imageUrl = $img['image'];
            if ($quality === 'optimized') {
                // نسخهٔ 1200px برای دانلود کم‌حجم؛ ImageHelper هنگام آپلود آن را می‌سازد.
                $dir = dirname($img['image']);
                $base = pathinfo($img['image'], PATHINFO_FILENAME);
                $candidates = [
                    $dir . '/' . $base . '_medium.webp',
                    $dir . '/' . $base . '_medium.jpg',
                    !empty($img['webp_image']) ? $img['webp_image'] : '',
                    !empty($img['thumbnail']) ? $img['thumbnail'] : ''
                ];
                foreach ($candidates as $candidate) {
                    if ($candidate && file_exists($_SERVER['DOCUMENT_ROOT'] . $candidate)) {
                        $imageUrl = $candidate;
                        break;
                    }
                }
            }
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . $imageUrl;
            if (!file_exists($imagePath)) {
                continue;
            }
            $entryName = sprintf('%02d_', $index + 1) . basename($imagePath);
            $zip->addFile($imagePath, $entryName);
        }

        $zip->close();
        return $tempFile;
    }
}
