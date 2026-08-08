<?php
// models/GalleryImage.php
// Legacy-compatible wrapper around GalleryPhoto.
// New code should use GalleryPhoto directly.

class GalleryImage {

    private $photoModel;

    public function __construct() {
        $this->photoModel = new GalleryPhoto();
    }

    public function getAll() {
        return $this->mapRows($this->photoModel->getAll(true));
    }

    public function getFeatured($limit = 4) {
        return $this->mapRows($this->photoModel->getFeatured($limit));
    }

    public function getByCategory($category) {
        // Legacy method mapped to first person matching slug
        $personModel = new Person();
        $person = $personModel->getBySlug($category);
        if (!$person) {
            return [];
        }
        return $this->mapRows($this->photoModel->getByPerson($person['id']));
    }

    public function getById($id) {
        return $this->mapRow($this->photoModel->getById($id));
    }

    public function create($data) {
        if (!empty($data['src'])) {
            $data['image'] = $data['src'];
            unset($data['src']);
        }
        if (!empty($data['category'])) {
            // Map legacy category to a person slug; default to first person if none
            $personModel = new Person();
            $person = $personModel->getBySlug($data['category']);
            if (!$person) {
                $person = $personModel->getActive()[0] ?? null;
            }
            if ($person) {
                $data['person_id'] = $person['id'];
            }
            unset($data['category']);
        }
        return $this->photoModel->create($data);
    }

    public function delete($id) {
        return $this->photoModel->delete($id);
    }

    public function getCategories() {
        $personModel = new Person();
        return array_column($personModel->getActive(), 'slug');
    }

    private function mapRows($rows) {
        return array_map([$this, 'mapRow'], $rows);
    }

    private function mapRow($row) {
        if (!$row) {
            return $row;
        }
        if (isset($row['image']) && !isset($row['src'])) {
            $row['src'] = $row['image'];
        }
        return $row;
    }
}
