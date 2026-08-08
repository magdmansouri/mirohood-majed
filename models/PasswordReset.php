<?php
// models/PasswordReset.php

class PasswordReset {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function create($userId, $token, $expiresAt) {
        $this->deleteByUser($userId);
        return $this->db->insert('password_resets', [
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    }

    public function findByToken($token) {
        return $this->db->fetchOne(
            "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1",
            [$token]
        );
    }

    public function deleteByUser($userId) {
        return $this->db->delete('password_resets', 'user_id = ?', [$userId]);
    }

    public function deleteByToken($token) {
        return $this->db->delete('password_resets', 'token = ?', [$token]);
    }
}
