<?php
// models/Person.php

class Person {

    private $db;

    public function __construct() {
        $this->db = db();
    }

    /**
     * Get all people (including inactive for admin)
     */
    public function getAll($onlyActive = false) {
        $cacheKey = $onlyActive ? 'people_active' : 'people_all';
        $cached = CacheHelper::get($cacheKey, 300);
        if ($cached !== false) {
            return $cached;
        }

        $sql = "SELECT p.*, COUNT(gi.id) AS photo_count
                FROM people p
                LEFT JOIN gallery_images gi ON gi.person_id = p.id
                ";
        if ($onlyActive) {
            $sql .= "WHERE p.status = 1 ";
        }
        $sql .= "GROUP BY p.id
                 ORDER BY p.sort_order ASC, p.created_at DESC";
        $people = $this->db->fetchAll($sql);
        CacheHelper::set($cacheKey, $people);
        return $people;
    }

    /**
     * Get active people only
     */
    public function getActive() {
        return $this->getAll(true);
    }

    /**
     * Get featured people
     */
    public function getFeatured($limit = 6) {
        $cacheKey = 'people_featured_' . (int) $limit;
        $cached = CacheHelper::get($cacheKey, 300);
        if ($cached !== false) {
            return $cached;
        }
        $people = $this->db->fetchAll(
            "SELECT p.*, COUNT(gi.id) AS photo_count
             FROM people p
             LEFT JOIN gallery_images gi ON gi.person_id = p.id
             WHERE p.status = 1 AND p.featured = 1
             GROUP BY p.id
             ORDER BY p.sort_order ASC, p.created_at DESC
             LIMIT ?",
            [$limit]
        );
        CacheHelper::set($cacheKey, $people);
        return $people;
    }

    /**
     * Find person by ID
     */
    public function getById($id) {
        return $this->db->fetchOne(
            "SELECT p.*, COUNT(gi.id) AS photo_count
             FROM people p
             LEFT JOIN gallery_images gi ON gi.person_id = p.id
             WHERE p.id = ?
             GROUP BY p.id",
            [$id]
        );
    }

    /**
     * Find person by slug (public route)
     */
    public function getBySlug($slug) {
        return $this->db->fetchOne(
            "SELECT p.*, COUNT(gi.id) AS photo_count
             FROM people p
             LEFT JOIN gallery_images gi ON gi.person_id = p.id
             WHERE p.slug = ? AND p.status = 1
             GROUP BY p.id",
            [$slug]
        );
    }

    /**
     * Find person by slug for admin (ignore status)
     */
    public function getBySlugAdmin($slug) {
        return $this->db->fetchOne(
            "SELECT p.*, COUNT(gi.id) AS photo_count
             FROM people p
             LEFT JOIN gallery_images gi ON gi.person_id = p.id
             WHERE p.slug = ?
             GROUP BY p.id",
            [$slug]
        );
    }

    /**
     * Create a new person
     */
    public function create($data) {
        $defaults = [
            'name' => '',
            'slug' => '',
            'avatar' => null,
            'cover' => null,
            'bio' => null,
            'instagram' => null,
            'website' => null,
            'featured' => 0,
            'status' => 1,
            'sort_order' => 0
        ];
        $data = array_merge($defaults, $data);
        $data['slug'] = $this->ensureUniqueSlug($data['slug']);
        $id = $this->db->insert('people', $data);
        CacheHelper::delete('people_active');
        CacheHelper::delete('people_all');
        return $id;
    }

    /**
     * Update person
     */
    public function update($id, $data) {
        if (isset($data['slug']) && !empty($data['slug'])) {
            $data['slug'] = $this->ensureUniqueSlug($data['slug'], $id);
        }
        $result = $this->db->update('people', $data, 'id = ?', [$id]);
        CacheHelper::delete('people_active');
        CacheHelper::delete('people_all');
        CacheHelper::delete('person_photos_' . $id);
        return $result;
    }

    /**
     * Delete person and related photos (cascade handled by FK)
     */
    public function delete($id) {
        $result = $this->db->delete('people', 'id = ?', [$id]);
        CacheHelper::delete('people_active');
        CacheHelper::delete('people_all');
        CacheHelper::delete('people_featured');
        CacheHelper::delete('person_photos_' . $id);
        return $result;
    }

    /**
     * Ensure slug is unique
     */
    private function ensureUniqueSlug($slug, $excludeId = null) {
        $slug = $this->slugify($slug);
        if (empty($slug)) {
            $slug = 'person';
        }
        $baseSlug = $slug;
        $counter = 1;
        do {
            $sql = "SELECT id FROM people WHERE slug = ?";
            $params = [$slug];
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            $exists = $this->db->fetchOne($sql, $params);
            if (!$exists) {
                break;
            }
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        } while (true);
        return $slug;
    }

    /**
     * Create URL-friendly slug
     */
    private function slugify($text) {
        if (empty($text)) {
            return '';
        }
        $text = trim($text);
        // Convert Persian/Arabic numerals to Latin if needed, keep letters and numbers
        $text = preg_replace('/[^\p{L}\p{N}\s_-]/u', '', $text);
        $text = preg_replace('/[\s_]+/', '-', $text);
        $text = trim($text, '-');
        $text = mb_strtolower($text, 'UTF-8');
        return $text;
    }

    /**
     * Get max sort order
     */
    public function getMaxSortOrder() {
        return (int) $this->db->fetchColumn("SELECT COALESCE(MAX(sort_order), 0) FROM people");
    }
}
