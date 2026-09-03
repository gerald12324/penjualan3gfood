-- SQL data kurir 3GFood
-- Database: web_penjualan

CREATE TABLE IF NOT EXISTS `couriers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NOT NULL,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `couriers_is_available_index` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data kurir sesuai contoh pada dashboard admin
INSERT INTO `couriers`
    (`name`, `phone`, `is_available`, `created_at`, `updated_at`)
SELECT 'kurir 1', '+62 838-9165-4105', 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `couriers` WHERE `phone` = '+62 838-9165-4105'
);

INSERT INTO `couriers`
    (`name`, `phone`, `is_available`, `created_at`, `updated_at`)
SELECT 'kurir 2', '+62 831-9222-4671', 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `couriers` WHERE `phone` = '+62 831-9222-4671'
);

-- Catatan:
-- Jumlah pengantaran pada dashboard dihitung dari orders.courier_id.
-- Pastikan kolom orders.courier_id sudah tersedia sebelum menghubungkan pesanan.
-- Jika tabel orders belum memiliki kolom tersebut, jalankan:
-- ALTER TABLE `orders`
--     ADD COLUMN `courier_id` BIGINT UNSIGNED NULL,
--     ADD CONSTRAINT `orders_courier_id_foreign`
--         FOREIGN KEY (`courier_id`) REFERENCES `couriers` (`id`)
--         ON DELETE SET NULL;
