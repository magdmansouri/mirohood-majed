<?php
// models/Booking.php

class Booking {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll() {
        return $this->db->fetchAll(
            "SELECT b.*, u.name AS user_name, u.phone AS user_phone, u.email AS user_email
             FROM bookings b
             LEFT JOIN users u ON u.id = b.user_id
             ORDER BY b.created_at DESC"
        );
    }

    public function getByStatus($status) {
        return $this->db->fetchAll(
            "SELECT b.*, u.name AS user_name, u.phone AS user_phone, u.email AS user_email
             FROM bookings b
             LEFT JOIN users u ON u.id = b.user_id
             WHERE b.status = ?
             ORDER BY b.created_at DESC",
            [$status]
        );
    }

    public function getByUser($userId) {
        return $this->db->fetchAll("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }

    public function getByUserAndStatus($userId, $status) {
        return $this->db->fetchAll(
            "SELECT * FROM bookings WHERE user_id = ? AND status = ? ORDER BY created_at DESC",
            [$userId, $status]
        );
    }

    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$id]);
    }

    public function count() {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM bookings");
    }

    public function countByStatus($status) {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM bookings WHERE status = ?", [$status]);
    }

    public function create($data) {
        return $this->db->insert('bookings', $data);
    }

    public function updateStatus($id, $status) {
        return $this->db->update('bookings', ['status' => $status], 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('bookings', 'id = ?', [$id]);
    }

    public function getAvailableTimes($date, $packageId = null) {
        $allTimes = [];
        for ($i = 9; $i <= 20; $i++) {
            $allTimes[] = sprintf('%02d:00', $i);
            $allTimes[] = sprintf('%02d:30', $i);
        }
        $booked = $this->db->fetchAll(
            "SELECT time FROM bookings WHERE gregorian_date = ? AND status != 'CANCELLED'",
            [$date]
        );
        $bookedTimes = array_column($booked, 'time');
        return array_values(array_diff($allTimes, $bookedTimes));
    }

    public function requestCancel($id, $note = '') {
        return $this->db->update(
            'bookings',
            ['request_type' => 'CANCEL', 'request_note' => $note, 'request_status' => 'PENDING'],
            'id = ?',
            [$id]
        );
    }

    public function requestReschedule($id, $newDate, $newTime, $note = '') {
        return $this->db->update(
            'bookings',
            [
                'request_type' => 'RESCHEDULE',
                'request_note' => $note,
                'request_status' => 'PENDING',
                'jalali_date' => $newDate,
                'gregorian_date' => $newDate,
                'time' => $newTime
            ],
            'id = ?',
            [$id]
        );
    }

    public function handleRequest($id, $status, $note = '') {
        $booking = $this->getById($id);
        $update = ['request_status' => $status];
        if ($status === 'APPROVED') {
            if ($booking && $booking['request_type'] === 'CANCEL') {
                $update['status'] = 'CANCELLED';
            } else {
                // For RESCHEDULE keep current status but confirm it
                if ($booking && $booking['status'] === 'PENDING') {
                    $update['status'] = 'CONFIRMED';
                }
            }
        }
        if (!empty($note)) {
            $update['request_note'] = $note;
        }
        return $this->db->update('bookings', $update, 'id = ?', [$id]);
    }

    public function getUpcoming($userId, $limit = 5) {
        return $this->db->fetchAll(
            "SELECT * FROM bookings
             WHERE user_id = ? AND status IN ('PENDING', 'CONFIRMED')
             ORDER BY gregorian_date ASC, time ASC
             LIMIT ?",
            [$userId, $limit]
        );
    }
}
?>