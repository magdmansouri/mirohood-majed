<?php
// models/Statistics.php - Advanced statistics for admin dashboard

class Statistics {

    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function bookingsByMonth($months = 6) {
        return $this->db->fetchAll(
            "SELECT DATE_FORMAT(gregorian_date, '%Y-%m') AS month, COUNT(*) AS total
             FROM bookings
             WHERE gregorian_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY month
             ORDER BY month ASC",
            [$months]
        );
    }

    public function bookingsByStatus() {
        return $this->db->fetchAll(
            "SELECT status, COUNT(*) AS total FROM bookings GROUP BY status ORDER BY total DESC"
        );
    }

    public function bookingsByPackage() {
        return $this->db->fetchAll(
            "SELECT package_id, COUNT(*) AS total FROM bookings GROUP BY package_id ORDER BY total DESC"
        );
    }

    public function usersByMonth($months = 6) {
        return $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
             FROM users
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY month
             ORDER BY month ASC",
            [$months]
        );
    }

    public function upcomingBookings($limit = 10) {
        return $this->db->fetchAll(
            "SELECT b.*, u.name AS user_name, u.phone AS user_phone
             FROM bookings b
             LEFT JOIN users u ON u.id = b.user_id
             WHERE b.gregorian_date >= CURDATE()
               AND b.status IN ('PENDING', 'CONFIRMED')
             ORDER BY b.gregorian_date ASC, b.time ASC
             LIMIT ?",
            [$limit]
        );
    }

    public function galleryStats() {
        return $this->db->fetchOne(
            "SELECT COUNT(*) AS total_galleries,
                    COALESCE(SUM(view_count), 0) AS total_views,
                    COALESCE(SUM(download_count), 0) AS total_downloads
             FROM client_galleries
             WHERE status = 1"
        );
    }

    public function topGalleries($limit = 5) {
        return $this->db->fetchAll(
            "SELECT cg.*, u.name AS user_name
             FROM client_galleries cg
             JOIN users u ON u.id = cg.user_id
             WHERE cg.status = 1
             ORDER BY (cg.view_count + cg.download_count) DESC
             LIMIT ?",
            [$limit]
        );
    }

    public function recentActivity($limit = 10) {
        $bookings = $this->db->fetchAll(
            "SELECT 'booking' AS type, id, full_name AS title, status, created_at
             FROM bookings
             ORDER BY created_at DESC
             LIMIT ?",
            [$limit]
        );
        $galleries = $this->db->fetchAll(
            "SELECT 'gallery' AS type, cg.id, cg.title, '' AS status, cg.created_at
             FROM client_galleries cg
             ORDER BY cg.created_at DESC
             LIMIT ?",
            [$limit]
        );
        $users = $this->db->fetchAll(
            "SELECT 'user' AS type, id, name AS title, '' AS status, created_at
             FROM users
             ORDER BY created_at DESC
             LIMIT ?",
            [$limit]
        );

        $all = array_merge($bookings, $galleries, $users);
        usort($all, function($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });
        return array_slice($all, 0, $limit);
    }

    public function bookingConversionRate() {
        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM bookings");
        if ($total === 0) return 0;
        $confirmed = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM bookings WHERE status = 'CONFIRMED' OR status = 'COMPLETED'");
        return round(($confirmed / $total) * 100, 1);
    }
}
