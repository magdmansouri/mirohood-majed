<?php
// models/Notification.php - User notifications

class Notification {

    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function getByUser($userId, $limit = 50, $offset = 0) {
        return $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY is_read ASC, created_at DESC LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }

    public function getUnreadByUser($userId, $limit = 50) {
        return $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public function countUnread($userId) {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
    }

    public function create($userId, $type, $title, $message, $relatedId = null, $relatedType = null) {
        return $this->db->insert('notifications', [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'is_read' => 0
        ]);
    }

    public function markAsRead($id, $userId) {
        return $this->db->update(
            'notifications',
            ['is_read' => 1],
            'id = ? AND user_id = ?',
            [$id, $userId]
        );
    }

    public function markAllAsRead($userId) {
        return $this->db->update(
            'notifications',
            ['is_read' => 1],
            'user_id = ? AND is_read = 0',
            [$userId]
        );
    }

    public function delete($id, $userId) {
        return $this->db->delete('notifications', 'id = ? AND user_id = ?', [$id, $userId]);
    }

    public function deleteOld($userId, $days = 90) {
        return $this->db->query(
            "DELETE FROM notifications WHERE user_id = ? AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$userId, $days]
        );
    }
}
