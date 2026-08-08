<?php
// models/GalleryPhoto.php

class GalleryPhoto {

    private $db;

    public function __construct() {
        $this->db = db();
    }

    /**
     * Get all gallery images across all people
     */
    public function getAll($onlyActive = false) {
        $cacheKey = $onlyActive ? 'gallery_all_active' : 'gallery_all';
        $cached = CacheHelper::get($cacheKey, 300);
        if ($cached !== false) {
            return $cached;
        }

        $sql = "SELECT gi.*, p.name AS person_name, p.slug AS person_slug
                FROM gallery_images gi
                JOIN people p ON p.id = gi.person_id";
        if ($onlyActive) {
            $sql .= " WHERE p.status = 1";
        }
        $sql .= " ORDER BY gi.sort_order ASC, gi.created_at DESC";
        $images = $this->db->fetchAll($sql);
        CacheHelper::set($cacheKey, $images);
        return $images;
    }

    /**
     * Get images by person ID
     */
    public function getByPerson($personId, $limit = null) {
        $cacheKey = 'person_photos_' . (int) $personId;
        $cached = CacheHelper::get($cacheKey, 300);
        if ($cached !== false) {
            return $cached;
        }

        $sql = "SELECT * FROM gallery_images
                WHERE person_id = ?
                ORDER BY sort_order ASC, created_at DESC";
        $params = [$personId];
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = (int) $limit;
        }
        $images = $this->db->fetchAll($sql, $params);
        CacheHelper::set($cacheKey, $images);
        return $images;
    }

    /**
     * Get featured images from active people
     */
    public function getFeatured($limit = 4) {
        $cacheKey = 'gallery_featured_' . (int) $limit;
        $cached = CacheHelper::get($cacheKey, 300);
        if ($cached !== false) {
            return $cached;
        }
        $images = $this->db->fetchAll(
            "SELECT gi.*, p.name AS person_name, p.slug AS person_slug
             FROM gallery_images gi
             JOIN people p ON p.id = gi.person_id
             WHERE p.status = 1
             ORDER BY p.featured DESC, gi.sort_order ASC, gi.created_at DESC
             LIMIT ?",
            [(int) $limit]
        );
        CacheHelper::set($cacheKey, $images);
        return $images;
    }

    /**
     * Get single image by ID
     */
    public function getById($id) {
        return $this->db->fetchOne(
            "SELECT gi.*, p.name AS person_name, p.slug AS person_slug
             FROM gallery_images gi
             JOIN people p ON p.id = gi.person_id
             WHERE gi.id = ?",
            [$id]
        );
    }

    /**
     * Create new image
     */
    public function create($data) {
        $defaults = [
            'person_id' => 0,
            'image' => '',
            'thumbnail' => null,
            'medium' => null,
            'large' => null,
            'webp_image' => null,
            'webp_thumbnail' => null,
            'webp_medium' => null,
            'webp_large' => null,
            'caption' => null,
            'alt' => null,
            'seo_title' => null,
            'sort_order' => 0
        ];
        $data = array_merge($defaults, $data);
        $id = $this->db->insert('gallery_images', $data);
        CacheHelper::delete('person_photos_' . (int) $data['person_id']);
        CacheHelper::delete('gallery_all');
        CacheHelper::delete('gallery_featured');
        return $id;
    }

    /**
     * Update image metadata
     */
    public function update($id, $data) {
        $image = $this->getById($id);
        $result = $this->db->update('gallery_images', $data, 'id = ?', [$id]);
        if ($image) {
            CacheHelper::delete('person_photos_' . (int) $image['person_id']);
        }
        return $result;
    }

    /**
     * Delete image
     */
    public function delete($id) {
        $image = $this->getById($id);
        $result = $this->db->delete('gallery_images', 'id = ?', [$id]);
        if ($image) {
            CacheHelper::delete('person_photos_' . (int) $image['person_id']);
        }
        CacheHelper::delete('gallery_all');
        CacheHelper::delete('gallery_featured');
        return $result;
    }

    /**
     * Count images for a person
     */
    public function countByPerson($personId) {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM gallery_images WHERE person_id = ?",
            [$personId]
        );
    }

    /**
     * Get next sort order for a person
     */
    public function getNextSortOrder($personId) {
        return (int) $this->db->fetchColumn(
            "SELECT COALESCE(MAX(sort_order), 0) + 1 FROM gallery_images WHERE person_id = ?",
            [$personId]
        );
    }

    /**
     * Reorder images for a person
     * Expects array of IDs in desired order
     */
    public function reorder($personId, $orderedIds) {
        $this->db->beginTransaction();
        try {
            foreach ($orderedIds as $index => $id) {
                $this->db->update(
                    'gallery_images',
                    ['sort_order' => (int) $index + 1],
                    'id = ? AND person_id = ?',
                    [$id, $personId]
                );
            }
            $this->db->commit();
            CacheHelper::delete('person_photos_' . (int) $personId);
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Check for duplicate image by hash
     */
    public function findByHash($hash) {
        return $this->db->fetchOne(
            "SELECT * FROM gallery_images WHERE image_hash = ? LIMIT 1",
            [$hash]
        );
    }
}
