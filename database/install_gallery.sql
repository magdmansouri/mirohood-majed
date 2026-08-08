DROP TABLE IF EXISTS `gallery_images`;
DROP TABLE IF EXISTS `people`;

CREATE TABLE `people` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(500) DEFAULT NULL,
    `cover` VARCHAR(500) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,
    `instagram` VARCHAR(255) DEFAULT NULL,
    `website` VARCHAR(255) DEFAULT NULL,
    `featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_people_slug` (`slug`),
    KEY `idx_people_status` (`status`),
    KEY `idx_people_featured` (`featured`),
    KEY `idx_people_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gallery_images` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `person_id` INT UNSIGNED NOT NULL,
    `image` VARCHAR(500) NOT NULL,
    `thumbnail` VARCHAR(500) DEFAULT NULL,
    `medium` VARCHAR(500) DEFAULT NULL,
    `large` VARCHAR(500) DEFAULT NULL,
    `webp_image` VARCHAR(500) DEFAULT NULL,
    `webp_thumbnail` VARCHAR(500) DEFAULT NULL,
    `webp_medium` VARCHAR(500) DEFAULT NULL,
    `webp_large` VARCHAR(500) DEFAULT NULL,
    `caption` VARCHAR(500) DEFAULT NULL,
    `alt` VARCHAR(255) DEFAULT NULL,
    `seo_title` VARCHAR(255) DEFAULT NULL,
    `image_hash` VARCHAR(64) DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_gallery_person` (`person_id`),
    KEY `idx_gallery_sort` (`sort_order`),
    CONSTRAINT `fk_gallery_person`
        FOREIGN KEY (`person_id`) REFERENCES `people` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
