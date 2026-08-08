<?php
// models/DownloadLog.php - Track user gallery downloads

class DownloadLog {

    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function log($userId, $galleryId) {
        return $this->db->insert('download_logs', [
            'user_id' => $userId,
            'gallery_id' => $galleryId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
        ]);
    }

    public function getByUser($userId, $limit = 50) {
        return $this->db->fetchAll(
            "SELECT dl.*, cg.title AS gallery_title
             FROM download_logs dl
             JOIN client_galleries cg ON cg.id = dl.gallery_id
             WHERE dl.user_id = ?
             ORDER BY dl.created_at DESC
             LIMIT ?",
            [$userId, $limit]
        );
    }

    public function countByUser($userId) {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM download_logs WHERE user_id = ?",
            [$userId]
        );
    }

    public function countByUserAndGallery($userId, $galleryId) {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM download_logs WHERE user_id = ? AND gallery_id = ?",
            [$userId, $galleryId]
        );
    }
}
