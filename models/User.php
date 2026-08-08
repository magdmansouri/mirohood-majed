<?php
// models/User.php

class User {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function getByPhone($phone) {
        $cleanPhone = $this->cleanPhone($phone);
        return $this->db->fetchOne("SELECT * FROM users WHERE phone = ?", [$cleanPhone]);
    }

    public function create($data) {
        $cleanPhone = $this->cleanPhone($data['phone'] ?? '');
        return $this->db->insert('users', [
            'phone' => $cleanPhone,
            'name' => trim($data['name'] ?? ''),
            'email' => !empty($data['email']) ? trim($data['email']) : null,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'status' => 1
        ]);
    }

    public function update($id, $data) {
        $update = [];
        if (isset($data['name'])) {
            $update['name'] = trim($data['name']);
        }
        if (isset($data['email'])) {
            $update['email'] = !empty($data['email']) ? trim($data['email']) : null;
        }
        if (!empty($data['password'])) {
            $update['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (isset($data['avatar'])) {
            $update['avatar'] = $data['avatar'];
        }
        if (empty($update)) {
            return 0;
        }
        return $this->db->update('users', $update, 'id = ?', [$id]);
    }

    public function phoneExists($phone) {
        $cleanPhone = $this->cleanPhone($phone);
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE phone = ?", [$cleanPhone]) > 0;
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function cleanPhone($phone) {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    public function getBookings($userId) {
        return $this->db->fetchAll(
            "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    public function countAll() {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users");
    }

    public function getAll($withBookingCount = false) {
        if (!$withBookingCount) {
            return $this->db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
        }
        return $this->db->fetchAll(
            "SELECT u.*, COUNT(b.id) AS booking_count
             FROM users u
             LEFT JOIN bookings b ON b.user_id = u.id
             GROUP BY u.id
             ORDER BY u.created_at DESC"
        );
    }
}
