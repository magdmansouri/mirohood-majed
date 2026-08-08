<?php
// database/install.php - Run all schema migrations (gallery + bookings + site_contents)
// Visit /database/install.php in browser or run via CLI: php database/install.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

echo "🔧 Starting schema migration...\n\n";

try {
    $db = db();
    $errors = [];
    $executed = 0;

    // ============================================================
    // 1. Gallery tables (people + gallery_images)
    // ============================================================
    echo "🔧 Checking gallery_images columns...\n";
    $existingGalleryColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'gallery_images']
    );
    $galleryColumnNames = array_column($existingGalleryColumns, 'COLUMN_NAME');
    $galleryColumnsToAdd = [
        'medium' => "ALTER TABLE `gallery_images` ADD COLUMN `medium` VARCHAR(255) DEFAULT NULL AFTER `thumbnail`;",
        'large' => "ALTER TABLE `gallery_images` ADD COLUMN `large` VARCHAR(255) DEFAULT NULL AFTER `medium`;",
        'webp_image' => "ALTER TABLE `gallery_images` ADD COLUMN `webp_image` VARCHAR(255) DEFAULT NULL AFTER `large`;",
        'webp_thumbnail' => "ALTER TABLE `gallery_images` ADD COLUMN `webp_thumbnail` VARCHAR(255) DEFAULT NULL AFTER `webp_image`;",
        'webp_medium' => "ALTER TABLE `gallery_images` ADD COLUMN `webp_medium` VARCHAR(255) DEFAULT NULL AFTER `webp_thumbnail`;",
        'webp_large' => "ALTER TABLE `gallery_images` ADD COLUMN `webp_large` VARCHAR(255) DEFAULT NULL AFTER `webp_medium`;",
    ];
    foreach ($galleryColumnsToAdd as $colName => $alterSql) {
        if (in_array($colName, $galleryColumnNames, true)) {
            echo "✅ GALLERY_IMAGES: column `{$colName}` already exists\n";
            continue;
        }
        try {
            $db->exec($alterSql);
            echo "✅ GALLERY_IMAGES: added column `{$colName}`\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ GALLERY_IMAGES: failed to add column `{$colName}` -> " . $e->getMessage() . "\n";
        }
    }

    $sqlFile = __DIR__ . '/install_gallery.sql';
    $sql = file_get_contents($sqlFile);
    if ($sql === false || $sql === '') {
        throw new Exception('Gallery SQL file not found or empty: ' . $sqlFile);
    }

    $sql = str_replace(["\r\n", "\r"], "\n", $sql);
    $sql = preg_replace('/--[^\n]*\n/', "\n", $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    $parts = preg_split('/;\s*/', $sql, -1, PREG_SPLIT_NO_EMPTY);

    foreach ($parts as $part) {
        $stmt = trim($part);
        if ($stmt === '') continue;
        // Skip destructive DROP statements to protect existing data
        if (stripos($stmt, 'DROP TABLE') === 0) {
            echo "⏭️ GALLERY SKIPPED (destructive): " . strtok($stmt, "\n") . "\n";
            continue;
        }
        $stmt .= ';';
        try {
            $db->exec($stmt);
            $executed++;
            echo "✅ GALLERY: " . strtok($stmt, "\n") . "\n";
        } catch (Exception $stmtEx) {
            $errors[] = $stmtEx->getMessage();
            echo "⚠️ GALLERY SKIPPED: " . strtok($stmt, "\n") . "\n   -> " . $stmtEx->getMessage() . "\n";
        }
    }

    // ============================================================
    // 1b. Ensure people table has required columns
    // ============================================================
    echo "\n🔧 Checking people columns...\n";
    $existingPeopleColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'people']
    );
    $peopleColumnNames = array_column($existingPeopleColumns, 'COLUMN_NAME');
    $peopleColumnsToAdd = [
        'featured' => "ALTER TABLE `people` ADD COLUMN `featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `website`;",
        'status' => "ALTER TABLE `people` ADD COLUMN `status` TINYINT(1) NOT NULL DEFAULT 1 AFTER `featured`;",
        'sort_order' => "ALTER TABLE `people` ADD COLUMN `sort_order` INT NOT NULL DEFAULT 0 AFTER `status`;",
        'avatar' => "ALTER TABLE `people` ADD COLUMN `avatar` VARCHAR(500) DEFAULT NULL AFTER `slug`;",
        'cover' => "ALTER TABLE `people` ADD COLUMN `cover` VARCHAR(500) DEFAULT NULL AFTER `avatar`;",
        'bio' => "ALTER TABLE `people` ADD COLUMN `bio` TEXT DEFAULT NULL AFTER `cover`;",
        'instagram' => "ALTER TABLE `people` ADD COLUMN `instagram` VARCHAR(255) DEFAULT NULL AFTER `bio`;",
        'website' => "ALTER TABLE `people` ADD COLUMN `website` VARCHAR(255) DEFAULT NULL AFTER `instagram`;"
    ];
    foreach ($peopleColumnsToAdd as $colName => $alterSql) {
        if (in_array($colName, $peopleColumnNames, true)) {
            echo "✅ PEOPLE: column `{$colName}` already exists\n";
            continue;
        }
        try {
            $db->exec($alterSql);
            echo "✅ PEOPLE: added column `{$colName}`\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ PEOPLE: failed to add column `{$colName}` -> " . $e->getMessage() . "\n";
        }
    }

    // ============================================================
    // 1c. Ensure gallery_images.id is AUTO_INCREMENT
    // ============================================================
    echo "\n🔧 Checking gallery_images id column...\n";
    try {
        $tableExists = $db->fetchOne(
            "SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
            [DB_NAME, 'gallery_images']
        );
        if (!$tableExists) {
            echo "⏭️ GALLERY_IMAGES: table does not exist yet; will be created by schema\n";
        } else {
            $galleryInfo = $db->fetchAll("SHOW COLUMNS FROM `gallery_images`");
            $galleryIdColumn = null;
            foreach ($galleryInfo as $col) {
                if ($col['Field'] === 'id') {
                    $galleryIdColumn = $col;
                    break;
                }
            }
            if ($galleryIdColumn && stripos($galleryIdColumn['Extra'], 'auto_increment') === false) {
                echo "⚠️ GALLERY_IMAGES: id column is not AUTO_INCREMENT. Fixing...\n";
                $db->exec("ALTER TABLE `gallery_images` MODIFY COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT");
                echo "✅ GALLERY_IMAGES: id column set to AUTO_INCREMENT\n";
            } else {
                echo "✅ GALLERY_IMAGES: id column is AUTO_INCREMENT\n";
            }
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "❌ GALLERY_IMAGES: failed to verify id column -> " . $e->getMessage() . "\n";
    }

    // ============================================================
    // 2. Bookings table - ensure it exists with correct columns
    // ============================================================
    echo "\n🔧 Checking bookings table...\n";

    $bookingsTableSql = "CREATE TABLE IF NOT EXISTS `bookings` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `full_name` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `package_id` VARCHAR(50) NOT NULL,
        `jalali_date` VARCHAR(20) DEFAULT NULL,
        `gregorian_date` DATE NOT NULL,
        `time` VARCHAR(10) NOT NULL,
        `note` TEXT DEFAULT NULL,
        `status` VARCHAR(50) NOT NULL DEFAULT 'PENDING',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_bookings_date` (`gregorian_date`),
        KEY `idx_bookings_status` (`status`),
        KEY `idx_bookings_phone` (`phone`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    try {
        $db->exec($bookingsTableSql);
        echo "✅ BOOKINGS: table created or already exists\n";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "⚠️ BOOKINGS CREATE: " . $e->getMessage() . "\n";
    }

    // Fix broken id column if it is not INT AUTO_INCREMENT
    try {
        $tableInfo = $db->fetchAll("SHOW COLUMNS FROM `bookings`");
        $idColumn = null;
        foreach ($tableInfo as $col) {
            if ($col['Field'] === 'id') {
                $idColumn = $col;
                break;
            }
        }
        if ($idColumn && stripos($idColumn['Extra'], 'auto_increment') === false) {
            echo "⚠️ BOOKINGS: id column is not AUTO_INCREMENT. Rebuilding table to fix...\n";
            $db->exec("RENAME TABLE `bookings` TO `bookings_old`");
            $db->exec($bookingsTableSql);
            $db->exec("INSERT INTO `bookings` (`full_name`, `phone`, `package_id`, `jalali_date`, `gregorian_date`, `time`, `note`, `status`, `created_at`, `updated_at`) 
                        SELECT `full_name`, `phone`, `package_id`, `jalali_date`, `gregorian_date`, `time`, `note`, `status`, `created_at`, `updated_at` FROM `bookings_old`");
            $db->exec("DROP TABLE `bookings_old`");
            echo "✅ BOOKINGS: table rebuilt with AUTO_INCREMENT id; old data migrated\n";
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "❌ BOOKINGS: failed to fix id column -> " . $e->getMessage() . "\n";
    }

    // Add missing columns if they don't exist
    $existingColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'bookings']
    );
    $existingColumnNames = array_column($existingColumns, 'COLUMN_NAME');

    $requiredColumns = [
        'full_name'    => "ALTER TABLE `bookings` ADD COLUMN `full_name` VARCHAR(255) NOT NULL AFTER `id`;",
        'phone'        => "ALTER TABLE `bookings` ADD COLUMN `phone` VARCHAR(20) NOT NULL AFTER `full_name`;",
        'package_id'   => "ALTER TABLE `bookings` ADD COLUMN `package_id` VARCHAR(50) NOT NULL AFTER `phone`;",
        'jalali_date'  => "ALTER TABLE `bookings` ADD COLUMN `jalali_date` VARCHAR(20) DEFAULT NULL AFTER `package_id`;",
        'gregorian_date' => "ALTER TABLE `bookings` ADD COLUMN `gregorian_date` DATE NOT NULL AFTER `jalali_date`;",
        'time'         => "ALTER TABLE `bookings` ADD COLUMN `time` VARCHAR(10) NOT NULL AFTER `gregorian_date`;",
        'note'         => "ALTER TABLE `bookings` ADD COLUMN `note` TEXT DEFAULT NULL AFTER `time`;",
        'status'       => "ALTER TABLE `bookings` ADD COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'PENDING' AFTER `note`;",
        'created_at'   => "ALTER TABLE `bookings` ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`;",
        'updated_at'   => "ALTER TABLE `bookings` ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;",
    ];

    foreach ($requiredColumns as $colName => $alterSql) {
        if (in_array($colName, $existingColumnNames, true)) {
            echo "✅ BOOKINGS: column `{$colName}` already exists\n";
            continue;
        }
        try {
            $db->exec($alterSql);
            echo "✅ BOOKINGS: added column `{$colName}`\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ BOOKINGS: failed to add column `{$colName}` -> " . $e->getMessage() . "\n";
        }
    }

    // ============================================================
    // ============================================================
    // 4. Users table - authentication
    // ============================================================
    echo "\n🔧 Checking users table...\n";

    $usersTableSql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `phone` VARCHAR(20) NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) DEFAULT NULL,
        `password_hash` VARCHAR(255) NOT NULL,
        `status` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_users_phone` (`phone`),
        KEY `idx_users_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    try {
        $db->exec($usersTableSql);
        echo "✅ USERS: table created or already exists\n";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "⚠️ USERS CREATE: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // 4b. Password resets table
    // ============================================================
    echo "\n🔧 Checking password_resets table...\n";

    $passwordResetsTableSql = "CREATE TABLE IF NOT EXISTS `password_resets` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT UNSIGNED NOT NULL,
        `token` VARCHAR(255) NOT NULL,
        `expires_at` DATETIME NOT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_password_resets_token` (`token`),
        KEY `idx_password_resets_user` (`user_id`),
        KEY `idx_password_resets_expires` (`expires_at`),
        CONSTRAINT `fk_password_resets_user`
            FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    try {
        $db->exec($passwordResetsTableSql);
        echo "✅ PASSWORD_RESETS: table created or already exists\n";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "⚠️ PASSWORD_RESETS CREATE: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // 5. Add user_id to bookings
    // ============================================================
    echo "\n🔧 Checking bookings.user_id...\n";
    $existingBookingColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'bookings']
    );
    $bookingColumnNames = array_column($existingBookingColumns, 'COLUMN_NAME');
    if (!in_array('user_id', $bookingColumnNames, true)) {
        try {
            $db->exec("ALTER TABLE `bookings` ADD COLUMN `user_id` INT UNSIGNED DEFAULT NULL AFTER `id`");
            $db->exec("ALTER TABLE `bookings` ADD KEY `idx_bookings_user` (`user_id`)");
            echo "✅ BOOKINGS: added user_id column\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ BOOKINGS: failed to add user_id -> " . $e->getMessage() . "\n";
        }
    } else {
        echo "✅ BOOKINGS: user_id column already exists\n";
    }

    // 3. Site contents table - ensure it exists
    // ============================================================
    echo "\n🔧 Checking site_contents table...\n";

    $siteContentsSql = "CREATE TABLE IF NOT EXISTS `site_contents` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `key` VARCHAR(255) NOT NULL,
        `value` TEXT DEFAULT NULL,
        `type` VARCHAR(50) DEFAULT 'text',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_site_contents_key` (`key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    try {
        $db->exec($siteContentsSql);
        echo "✅ SITE_CONTENTS: table created or already exists\n";

        // Insert default page content for fresh installs
        $defaultContents = [
            ['about.eyebrow', 'About', 'text'],
            ['about.title', 'Mirohood', 'text'],
            ['about.intro', 'Portrait photography & brand filming — based on Earth, rooted in light.', 'text'],
            ['about.quote', '"Photography, to us, means holding a moment still — not building a pose."', 'text'],
            ['about.body', 'Mirohood has been working out of Tehran since 2021. Every session is built around light and framing that gets close to who you actually are, instead of repeating the same familiar templates.', 'text'],
            ['about.owner_name', 'Mirohood Studio', 'text'],
            ['about.owner_role', 'Photographer & Filmmaker', 'text'],
            ['about.owner_bio', 'Capturing authentic portraits and brand stories through light, framing, and a quiet attention to what makes each subject real.', 'text'],
            ['about.owner_avatar', '', 'text'],
            ['about.instagram', 'mirohood', 'text'],
            ['about.email', 'Parsmiro@gmail.com', 'text'],
            ['about.website', '', 'text'],
            ['about.address_label', 'Studio Address', 'text'],
            ['about.address', "Valiasr Street, above Saei Park, No. 12\nTehran, Iran", 'text'],
            ['about.contact_label', 'Get in Touch', 'text'],
            ['about.contact', "+98 21 0000 0000\nhello@mirohood.ir", 'text'],
            ['about.button', 'Book a Session', 'text'],
        ];
        foreach ($defaultContents as $row) {
            try {
                $db->exec("INSERT IGNORE INTO `site_contents` (`key`, `value`, `type`) VALUES (" . $db->quote($row[0]) . ", " . $db->quote($row[1]) . ", " . $db->quote($row[2]) . ")");
            } catch (Exception $e) {
                // ignore duplicate key errors
            }
        }
        echo "✅ SITE_CONTENTS: default about values inserted\n";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "⚠️ SITE_CONTENTS CREATE: " . $e->getMessage() . "\n";
    }

    echo "\n";
    // ============================================================
    // 6. Client galleries for user proofing
    // ============================================================
    echo "\n🔧 Checking client gallery tables...\n";

    $clientGallerySql = "CREATE TABLE IF NOT EXISTS `client_galleries` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT UNSIGNED NOT NULL,
        `booking_id` INT UNSIGNED DEFAULT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `share_token` VARCHAR(64) DEFAULT NULL,
        `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
        `share_password` VARCHAR(255) DEFAULT NULL,
        `share_expires_at` DATETIME DEFAULT NULL,
        `status` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_client_gallery_share_token` (`share_token`),
        KEY `idx_client_gallery_user` (`user_id`),
        KEY `idx_client_gallery_booking` (`booking_id`),
        KEY `idx_client_gallery_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $clientGalleryImageSql = "CREATE TABLE IF NOT EXISTS `client_gallery_images` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `gallery_id` INT UNSIGNED NOT NULL,
        `image` VARCHAR(500) NOT NULL,
        `thumbnail` VARCHAR(500) DEFAULT NULL,
        `webp_image` VARCHAR(500) DEFAULT NULL,
        `caption` VARCHAR(500) DEFAULT NULL,
        `sort_order` INT NOT NULL DEFAULT 0,
        `is_favorite` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_client_gallery_image_gallery` (`gallery_id`),
        KEY `idx_client_gallery_image_sort` (`sort_order`),
        CONSTRAINT `fk_client_gallery_image_gallery`
            FOREIGN KEY (`gallery_id`) REFERENCES `client_galleries` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    try {
        $db->exec($clientGallerySql);
        echo "✅ CLIENT_GALLERIES: table created or already exists\n";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "⚠️ CLIENT_GALLERIES CREATE: " . $e->getMessage() . "\n";
    }

    try {
        $db->exec($clientGalleryImageSql);
        echo "✅ CLIENT_GALLERY_IMAGES: table created or already exists\n";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "⚠️ CLIENT_GALLERY_IMAGES CREATE: " . $e->getMessage() . "\n";
    }

    // Ensure is_favorite column exists for existing tables
    $existingClientColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'client_gallery_images']
    );
    $clientImageColumnNames = array_column($existingClientColumns, 'COLUMN_NAME');
    if (!in_array('is_favorite', $clientImageColumnNames, true)) {
        try {
            $db->exec("ALTER TABLE `client_gallery_images` ADD COLUMN `is_favorite` TINYINT(1) NOT NULL DEFAULT 0 AFTER `sort_order`");
            echo "✅ CLIENT_GALLERY_IMAGES: added is_favorite column\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ CLIENT_GALLERY_IMAGES: failed to add is_favorite -> " . $e->getMessage() . "\n";
        }
    } else {
        echo "✅ CLIENT_GALLERY_IMAGES: is_favorite column already exists\n";
    }

    // Ensure share_token column exists for client_galleries
    $existingGalleryColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'client_galleries']
    );
    $galleryColumnNames = array_column($existingGalleryColumns, 'COLUMN_NAME');
    $newGalleryColumns = [
        'share_token'       => "ALTER TABLE `client_galleries` ADD COLUMN `share_token` VARCHAR(64) DEFAULT NULL AFTER `description`",
        'view_count'        => "ALTER TABLE `client_galleries` ADD COLUMN `view_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `share_token`",
        'share_password'    => "ALTER TABLE `client_galleries` ADD COLUMN `share_password` VARCHAR(255) DEFAULT NULL AFTER `view_count`",
        'share_expires_at'  => "ALTER TABLE `client_galleries` ADD COLUMN `share_expires_at` DATETIME DEFAULT NULL AFTER `share_password`"
    ];
    foreach ($newGalleryColumns as $colName => $alterSql) {
        if (in_array($colName, $galleryColumnNames, true)) {
            echo "✅ CLIENT_GALLERIES: column `{$colName}` already exists\n";
            continue;
        }
        try {
            $db->exec($alterSql);
            echo "✅ CLIENT_GALLERIES: added `{$colName}` column\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ CLIENT_GALLERIES: failed to add `{$colName}` -> " . $e->getMessage() . "\n";
        }
    }

    // Ensure unique key for share_token exists
    try {
        $existingKeys = $db->fetchAll(
            "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [DB_NAME, 'client_galleries', 'uk_client_gallery_share_token']
        );
        if (empty($existingKeys)) {
            $db->exec("ALTER TABLE `client_galleries` ADD UNIQUE KEY `uk_client_gallery_share_token` (`share_token`)");
            echo "✅ CLIENT_GALLERIES: added unique key on share_token\n";
        } else {
            echo "✅ CLIENT_GALLERIES: unique key on share_token already exists\n";
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "❌ CLIENT_GALLERIES: failed to add unique key on share_token -> " . $e->getMessage() . "\n";
    }

    // ============================================================
    // 7. User dashboard enhancements: download_logs, notifications
    // ============================================================
    echo "\n🔧 Checking download_logs table...\n";

    $downloadLogsSql = "CREATE TABLE IF NOT EXISTS `download_logs` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT UNSIGNED NOT NULL,
        `gallery_id` INT UNSIGNED NOT NULL,
        `ip_address` VARCHAR(45) DEFAULT NULL,
        `user_agent` VARCHAR(255) DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_download_logs_user` (`user_id`),
        KEY `idx_download_logs_gallery` (`gallery_id`),
        KEY `idx_download_logs_created` (`created_at`),
        CONSTRAINT `fk_download_logs_user`
            FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
        CONSTRAINT `fk_download_logs_gallery`
            FOREIGN KEY (`gallery_id`) REFERENCES `client_galleries` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    try {
        $db->exec($downloadLogsSql);
        echo "✅ DOWNLOAD_LOGS: table created or already exists\n";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "⚠️ DOWNLOAD_LOGS CREATE: " . $e->getMessage() . "\n";
    }

    echo "\n🔧 Checking notifications table...\n";

    $notificationsSql = "CREATE TABLE IF NOT EXISTS `notifications` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT UNSIGNED NOT NULL,
        `type` VARCHAR(50) NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `related_id` INT UNSIGNED DEFAULT NULL,
        `related_type` VARCHAR(50) DEFAULT NULL,
        `is_read` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_notifications_user` (`user_id`),
        KEY `idx_notifications_read` (`is_read`),
        KEY `idx_notifications_created` (`created_at`),
        CONSTRAINT `fk_notifications_user`
            FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    try {
        $db->exec($notificationsSql);
        echo "✅ NOTIFICATIONS: table created or already exists\n";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "⚠️ NOTIFICATIONS CREATE: " . $e->getMessage() . "\n";
    }

    // Add dashboard columns to existing tables
    echo "\n🔧 Checking user dashboard columns...\n";

    $userColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'users']
    );
    $userColumnNames = array_column($userColumns, 'COLUMN_NAME');
    if (!in_array('avatar', $userColumnNames, true)) {
        try {
            $db->exec("ALTER TABLE `users` ADD COLUMN `avatar` VARCHAR(500) DEFAULT NULL AFTER `email`");
            echo "✅ USERS: added avatar column\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ USERS: failed to add avatar -> " . $e->getMessage() . "\n";
        }
    } else {
        echo "✅ USERS: avatar column already exists\n";
    }

    $clientGalleryColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'client_galleries']
    );
    $clientGalleryColumnNames = array_column($clientGalleryColumns, 'COLUMN_NAME');
    $clientGalleryNewColumns = [
        'download_count' => "ALTER TABLE `client_galleries` ADD COLUMN `download_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `view_count`",
        'last_downloaded_at' => "ALTER TABLE `client_galleries` ADD COLUMN `last_downloaded_at` DATETIME DEFAULT NULL AFTER `download_count`",
        'max_selections' => "ALTER TABLE `client_galleries` ADD COLUMN `max_selections` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `last_downloaded_at`"
    ];
    foreach ($clientGalleryNewColumns as $colName => $alterSql) {
        if (in_array($colName, $clientGalleryColumnNames, true)) {
            echo "✅ CLIENT_GALLERIES: column `{$colName}` already exists\n";
            continue;
        }
        try {
            $db->exec($alterSql);
            echo "✅ CLIENT_GALLERIES: added `{$colName}` column\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ CLIENT_GALLERIES: failed to add `{$colName}` -> " . $e->getMessage() . "\n";
        }
    }

    $clientImageColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'client_gallery_images']
    );
    $clientImageColumnNames = array_column($clientImageColumns, 'COLUMN_NAME');
    if (!in_array('is_final_selection', $clientImageColumnNames, true)) {
        try {
            $db->exec("ALTER TABLE `client_gallery_images` ADD COLUMN `is_final_selection` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_favorite`");
            echo "✅ CLIENT_GALLERY_IMAGES: added is_final_selection column\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ CLIENT_GALLERY_IMAGES: failed to add is_final_selection -> " . $e->getMessage() . "\n";
        }
    } else {
        echo "✅ CLIENT_GALLERY_IMAGES: is_final_selection column already exists\n";
    }

    $bookingColumns = $db->fetchAll(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
        [DB_NAME, 'bookings']
    );
    $bookingColumnNames = array_column($bookingColumns, 'COLUMN_NAME');
    $bookingNewColumns = [
        'request_type' => "ALTER TABLE `bookings` ADD COLUMN `request_type` VARCHAR(50) DEFAULT NULL AFTER `status`",
        'request_note' => "ALTER TABLE `bookings` ADD COLUMN `request_note` TEXT DEFAULT NULL AFTER `request_type`",
        'request_status' => "ALTER TABLE `bookings` ADD COLUMN `request_status` VARCHAR(50) NOT NULL DEFAULT 'NONE' AFTER `request_note`"
    ];
    foreach ($bookingNewColumns as $colName => $alterSql) {
        if (in_array($colName, $bookingColumnNames, true)) {
            echo "✅ BOOKINGS: column `{$colName}` already exists\n";
            continue;
        }
        try {
            $db->exec($alterSql);
            echo "✅ BOOKINGS: added `{$colName}` column\n";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            echo "❌ BOOKINGS: failed to add `{$colName}` -> " . $e->getMessage() . "\n";
        }
    }

    if (empty($errors)) {
        echo "✅✅✅ All schema migrations completed successfully.\n";
    } else {
        echo "⚠️⚠️⚠️ Migration finished with " . count($errors) . " error(s).\n";
        echo "Check the output above for details.\n";
    }

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
