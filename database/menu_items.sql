-- SQL menu 3GFood
-- Database: web_penjualan

CREATE TABLE IF NOT EXISTS `menu_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `category` VARCHAR(255) NOT NULL,
    `price` INT UNSIGNED NOT NULL,
    `image_url` VARCHAR(255) NULL,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `menu_items_category_index` (`category`),
    KEY `menu_items_is_available_index` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contoh data menu. Hapus bagian INSERT ini jika hanya ingin membuat tabel.
INSERT INTO `menu_items`
    (`name`, `description`, `category`, `price`, `image_url`, `is_available`, `created_at`, `updated_at`)
VALUES
    ('Nasi Liwet Ayam', 'Nasi liwet dengan ayam dan lauk pilihan.', 'Paket Nasi Liwet', 48000, NULL, 1, NOW(), NOW()),
    ('Nasi Liwet Komplit', 'Nasi liwet dengan lauk komplit.', 'Paket Nasi Liwet', 55000, NULL, 1, NOW(), NOW()),
    ('Ayam Goreng', 'Ayam goreng gurih dengan sambal.', 'Lauk Utama', 25000, NULL, 1, NOW(), NOW()),
    ('Ikan Bakar', 'Ikan bakar dengan bumbu khas 3GFood.', 'Lauk Utama', 30000, NULL, 1, NOW(), NOW()),
    ('Tempe Goreng', 'Tempe goreng renyah.', 'Menu Tambahan', 10000, NULL, 1, NOW(), NOW());
