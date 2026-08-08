<?php
// models/SiteContent.php

class SiteContent {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function get($key) {
        $result = $this->db->fetchOne("SELECT value FROM site_contents WHERE `key` = ?", [$key]);
        return $result ? $result['value'] : null;
    }

    public function getMultiple($keys) {
        if (empty($keys)) return [];

        // Serve from cache when available
        $cacheKey = 'site_content_' . md5(implode('|', $keys));
        $cached = CacheHelper::get($cacheKey, 60);
        if ($cached !== false) {
            return $cached;
        }

        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $results = $this->db->fetchAll(
            "SELECT `key`, value FROM site_contents WHERE `key` IN ({$placeholders})",
            $keys
        );
        $data = [];
        foreach ($results as $row) {
            $data[$row['key']] = $row['value'];
        }
        CacheHelper::set($cacheKey, $data);
        return $data;
    }

    public function getAll() {
        $cacheKey = 'site_content_all';
        $cached = CacheHelper::get($cacheKey, 60);
        if ($cached !== false) {
            return $cached;
        }

        $results = $this->db->fetchAll("SELECT `key`, value FROM site_contents");
        $data = [];
        foreach ($results as $row) {
            $data[$row['key']] = $row['value'];
        }
        CacheHelper::set($cacheKey, $data);
        return $data;
    }

    public function set($key, $value) {
        $exists = $this->db->fetchColumn("SELECT COUNT(*) FROM site_contents WHERE `key` = ?", [$key]);
        if ($exists) {
            $result = $this->db->update('site_contents', ['value' => $value], '`key` = ?', [$key]);
        } else {
            $result = $this->db->insert('site_contents', ['key' => $key, 'value' => $value]);
        }

        // Clear site content caches so the next request reflects the change
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    private function clearCache() {
        foreach (glob(BASE_PATH . '/cache/site_content_*.json') as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        CacheHelper::delete('site_content_all');
    }
}
