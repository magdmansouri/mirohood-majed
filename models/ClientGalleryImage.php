<?php
// models/ClientGalleryImage.php

class ClientGalleryImage {

    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function getByGallery($galleryId) {
        return $this->db->fetchAll(
            "SELECT * FROM client_gallery_images WHERE gallery_id = ? ORDER BY is_favorite DESC, sort_order ASC, created_at DESC",
            [$galleryId]
        );
    }

    public function toggleFavorite($imageId) {
        $image = $this->getById($imageId);
        if (!$image) {
            return false;
        }
        $newValue = $image['is_favorite'] ? 0 : 1;
        $this->update($imageId, ['is_favorite' => $newValue]);
        return $newValue;
    }

    public function getFavoriteCount($galleryId) {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM client_gallery_images WHERE gallery_id = ? AND is_favorite = 1",
            [$galleryId]
        );
    }

    public function getById($id) {
        return $this->db->fetchOne(
            "SELECT * FROM client_gallery_images WHERE id = ?",
            [$id]
        );
    }

    public function create($data) {
        return $this->db->insert('client_gallery_images', $data);
    }

    public function update($id, $data) {
        return $this->db->update('client_gallery_images', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('client_gallery_images', 'id = ?', [$id]);
    }

    public function getNextSortOrder($galleryId) {
        return (int) $this->db->fetchColumn(
            "SELECT COALESCE(MAX(sort_order), 0) + 1 FROM client_gallery_images WHERE gallery_id = ?",
            [$galleryId]
        );
    }

    public function getFinalSelectionCount($galleryId) {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM client_gallery_images WHERE gallery_id = ? AND is_final_selection = 1",
            [$galleryId]
        );
    }

    public function getFinalSelections($galleryId) {
        return $this->db->fetchAll(
            "SELECT * FROM client_gallery_images WHERE gallery_id = ? AND is_final_selection = 1 ORDER BY sort_order ASC",
            [$galleryId]
        );
    }

    public function setFinalSelection($imageId, $isSelected) {
        return $this->update($imageId, ['is_final_selection' => $isSelected ? 1 : 0]);
    }

    public function clearFinalSelections($galleryId) {
        return $this->db->query(
            "UPDATE client_gallery_images SET is_final_selection = 0 WHERE gallery_id = ?",
            [$galleryId]
        );
    }

    public function toggleFinalSelection($imageId) {
        $image = $this->getById($imageId);
        if (!$image) {
            return null;
        }
        $newValue = $image['is_final_selection'] ? 0 : 1;
        $this->setFinalSelection($imageId, $newValue);
        return $newValue;
    }
}
