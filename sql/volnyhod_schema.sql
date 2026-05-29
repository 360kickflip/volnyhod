-- =========================================================
--  ВОЛЬНЫЙ ХОД  ·  Полный SQL-дамп схемы и демо-данных
--  MySQL 5.7+ / MariaDB 10.3+
--
--  Импорт через phpMyAdmin:
--    1. Создайте БД `volnyhod` (utf8mb4 / utf8mb4_unicode_ci)
--    2. Откройте её, перейдите на вкладку "Импорт"
--    3. Выберите этот файл и нажмите "Вперёд"
--
--  Альтернатива — через миграции Yii2:
--    php yii migrate
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ---------------------------------------------------------
--  Создание БД (раскомментируйте при импорте через CLI mysql)
-- ---------------------------------------------------------
-- CREATE DATABASE IF NOT EXISTS `volnyhod` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `volnyhod`;

-- =========================================================
--  СТРУКТУРА ТАБЛИЦ
-- =========================================================

-- ----- 1. user -----
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(190) NOT NULL,
  `phone` VARCHAR(20) NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `auth_key` VARCHAR(64) NOT NULL,
  `password_reset_token` VARCHAR(255) NULL,
  `verification_token` VARCHAR(255) NULL,
  `email_verified_at` DATETIME NULL,
  `name` VARCHAR(150) NULL,
  `birthdate` DATE NULL,
  `avatar` VARCHAR(255) NULL,
  `balance` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `locked_balance` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `verification_status` ENUM('none','pending','verified','rejected') NOT NULL DEFAULT 'none',
  `status` ENUM('active','blocked','deleted') NOT NULL DEFAULT 'active',
  `role` ENUM('user','admin','manager') NOT NULL DEFAULT 'user',
  `block_reason` VARCHAR(500) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`),
  KEY `idx-user-phone` (`phone`),
  KEY `idx-user-status` (`status`),
  KEY `idx-user-role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 2. user_document -----
DROP TABLE IF EXISTS `user_document`;
CREATE TABLE `user_document` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `type` ENUM('license_front','license_back','passport_main','passport_registration','selfie') NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `comment` TEXT NULL,
  `reviewer_id` INT(11) NULL,
  `uploaded_at` DATETIME NOT NULL,
  `reviewed_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx-user_document-user_id` (`user_id`),
  KEY `idx-user_document-status` (`status`),
  CONSTRAINT `fk-user_document-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 3. tariff -----
DROP TABLE IF EXISTS `tariff`;
CREATE TABLE `tariff` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `price_per_minute` DECIMAL(8,2) NOT NULL DEFAULT 0,
  `price_per_km` DECIMAL(8,2) NOT NULL DEFAULT 0,
  `price_per_hour` DECIMAL(8,2) NULL,
  `price_per_day` DECIMAL(10,2) NULL,
  `free_km` INT(11) NULL DEFAULT 0,
  `deposit` DECIMAL(10,2) NOT NULL DEFAULT 2000,
  `overdue_per_minute` DECIMAL(8,2) NOT NULL DEFAULT 0,
  `color` VARCHAR(20) NULL DEFAULT '#0d6efd',
  `icon` VARCHAR(50) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 4. car -----
DROP TABLE IF EXISTS `car`;
CREATE TABLE `car` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tariff_id` INT(11) NOT NULL,
  `brand` VARCHAR(80) NOT NULL,
  `model` VARCHAR(80) NOT NULL,
  `year` SMALLINT(6) NULL,
  `color` VARCHAR(50) NULL,
  `license_plate` VARCHAR(20) NOT NULL,
  `vin` VARCHAR(17) NULL,
  `transmission` ENUM('manual','auto','robot','variator') NOT NULL DEFAULT 'auto',
  `body_type` VARCHAR(40) NULL,
  `seats` TINYINT(4) NULL DEFAULT 5,
  `fuel_type` ENUM('petrol','diesel','hybrid','electric','gas') NOT NULL DEFAULT 'petrol',
  `mileage` INT(11) NOT NULL DEFAULT 0,
  `fuel_level` TINYINT(4) NOT NULL DEFAULT 100,
  `battery_level` TINYINT(4) NULL,
  `features` TEXT NULL,
  `description` TEXT NULL,
  `lat` DECIMAL(10,7) NULL,
  `lng` DECIMAL(10,7) NULL,
  `address` VARCHAR(255) NULL,
  `status` ENUM('available','rented','reserved','maintenance','blocked','offline') NOT NULL DEFAULT 'available',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `license_plate` (`license_plate`),
  KEY `idx-car-tariff_id` (`tariff_id`),
  KEY `idx-car-status` (`status`),
  KEY `idx-car-brand_model` (`brand`,`model`),
  CONSTRAINT `fk-car-tariff_id` FOREIGN KEY (`tariff_id`) REFERENCES `tariff` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 5. car_photo -----
DROP TABLE IF EXISTS `car_photo`;
CREATE TABLE `car_photo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `car_id` INT(11) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `is_main` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-car_photo-car_id` (`car_id`),
  CONSTRAINT `fk-car_photo-car_id` FOREIGN KEY (`car_id`) REFERENCES `car` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 6. car_location_history -----
DROP TABLE IF EXISTS `car_location_history`;
CREATE TABLE `car_location_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `car_id` INT(11) NOT NULL,
  `booking_id` INT(11) NULL,
  `lat` DECIMAL(10,7) NOT NULL,
  `lng` DECIMAL(10,7) NOT NULL,
  `speed` SMALLINT(6) NULL,
  `mileage` INT(11) NULL,
  `address` VARCHAR(255) NULL,
  `recorded_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-car_location_history-car_id` (`car_id`),
  KEY `idx-car_location_history-booking_id` (`booking_id`),
  KEY `idx-car_location_history-recorded_at` (`recorded_at`),
  CONSTRAINT `fk-car_location_history-car_id` FOREIGN KEY (`car_id`) REFERENCES `car` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 7. promo_code -----
DROP TABLE IF EXISTS `promo_code`;
CREATE TABLE `promo_code` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) NULL,
  `type` ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  `value` DECIMAL(10,2) NOT NULL,
  `min_amount` DECIMAL(10,2) NULL,
  `max_discount` DECIMAL(10,2) NULL,
  `usage_limit` INT(11) NULL,
  `usage_count` INT(11) NOT NULL DEFAULT 0,
  `per_user_limit` INT(11) NOT NULL DEFAULT 1,
  `valid_from` DATETIME NULL,
  `valid_to` DATETIME NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx-promo_code-is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 8. booking -----
DROP TABLE IF EXISTS `booking`;
CREATE TABLE `booking` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `number` VARCHAR(20) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `car_id` INT(11) NOT NULL,
  `tariff_id` INT(11) NOT NULL,
  `promo_code_id` INT(11) NULL,
  `status` ENUM('pending','active','completed','cancelled','expired') NOT NULL DEFAULT 'pending',
  `planned_minutes` INT(11) NULL,
  `started_at` DATETIME NULL,
  `planned_end_at` DATETIME NULL,
  `ended_at` DATETIME NULL,
  `start_mileage` INT(11) NULL,
  `end_mileage` INT(11) NULL,
  `start_fuel` TINYINT(4) NULL,
  `end_fuel` TINYINT(4) NULL,
  `start_lat` DECIMAL(10,7) NULL,
  `start_lng` DECIMAL(10,7) NULL,
  `start_address` VARCHAR(255) NULL,
  `end_lat` DECIMAL(10,7) NULL,
  `end_lng` DECIMAL(10,7) NULL,
  `end_address` VARCHAR(255) NULL,
  `deposit` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `estimated_cost` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `base_cost` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `extra_km_cost` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `overdue_cost` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `penalty_cost` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `final_cost` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `cancel_reason` VARCHAR(500) NULL,
  `cancelled_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `idx-booking-user_id` (`user_id`),
  KEY `idx-booking-car_id` (`car_id`),
  KEY `idx-booking-status` (`status`),
  KEY `idx-booking-created_at` (`created_at`),
  KEY `fk-booking-tariff_id` (`tariff_id`),
  KEY `fk-booking-promo_code_id` (`promo_code_id`),
  CONSTRAINT `fk-booking-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk-booking-car_id` FOREIGN KEY (`car_id`) REFERENCES `car` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk-booking-tariff_id` FOREIGN KEY (`tariff_id`) REFERENCES `tariff` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk-booking-promo_code_id` FOREIGN KEY (`promo_code_id`) REFERENCES `promo_code` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 9. booking_charge -----
DROP TABLE IF EXISTS `booking_charge`;
CREATE TABLE `booking_charge` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `booking_id` INT(11) NOT NULL,
  `type` ENUM('base','extra_km','overdue','penalty','damage','discount','manual') NOT NULL,
  `description` VARCHAR(255) NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-booking_charge-booking_id` (`booking_id`),
  CONSTRAINT `fk-booking_charge-booking_id` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 10. review -----
DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `booking_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `car_id` INT(11) NOT NULL,
  `rating` TINYINT(4) NOT NULL,
  `text` TEXT NULL,
  `photos` TEXT NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `moderator_id` INT(11) NULL,
  `moderator_comment` VARCHAR(500) NULL,
  `moderated_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-review-booking_id` (`booking_id`),
  KEY `idx-review-user_id` (`user_id`),
  KEY `idx-review-car_id` (`car_id`),
  KEY `idx-review-status` (`status`),
  CONSTRAINT `fk-review-booking_id` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-review-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-review-car_id` FOREIGN KEY (`car_id`) REFERENCES `car` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 11. damage_report -----
DROP TABLE IF EXISTS `damage_report`;
CREATE TABLE `damage_report` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `booking_id` INT(11) NULL,
  `car_id` INT(11) NOT NULL,
  `user_id` INT(11) NULL,
  `description` TEXT NOT NULL,
  `severity` ENUM('minor','moderate','severe') NOT NULL DEFAULT 'minor',
  `photos` TEXT NULL,
  `status` ENUM('reported','reviewing','user_liable','not_liable','resolved') NOT NULL DEFAULT 'reported',
  `repair_cost` DECIMAL(10,2) NULL,
  `reviewer_id` INT(11) NULL,
  `reviewer_comment` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `reviewed_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx-damage_report-booking_id` (`booking_id`),
  KEY `idx-damage_report-car_id` (`car_id`),
  KEY `idx-damage_report-status` (`status`),
  KEY `fk-damage_report-user_id` (`user_id`),
  CONSTRAINT `fk-damage_report-booking_id` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-damage_report-car_id` FOREIGN KEY (`car_id`) REFERENCES `car` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-damage_report-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 12. transaction -----
DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `booking_id` INT(11) NULL,
  `type` ENUM('topup','withdraw','rental_charge','deposit_hold','deposit_release','refund','penalty','correction','bonus') NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `balance_after` DECIMAL(12,2) NULL,
  `status` ENUM('pending','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` ENUM('card','sbp','wallet','manual','bonus') NULL,
  `external_id` VARCHAR(100) NULL,
  `description` VARCHAR(500) NULL,
  `created_at` DATETIME NOT NULL,
  `completed_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx-transaction-user_id` (`user_id`),
  KEY `idx-transaction-booking_id` (`booking_id`),
  KEY `idx-transaction-type` (`type`),
  KEY `idx-transaction-status` (`status`),
  KEY `idx-transaction-created_at` (`created_at`),
  CONSTRAINT `fk-transaction-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-transaction-booking_id` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 13. notification -----
DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `type` ENUM('info','success','warning','danger','booking','payment','support','promo','system') NOT NULL DEFAULT 'info',
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NULL,
  `icon` VARCHAR(50) NULL,
  `url` VARCHAR(255) NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `read_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx-notification-user_id` (`user_id`),
  KEY `idx-notification-is_read` (`is_read`),
  KEY `idx-notification-created_at` (`created_at`),
  CONSTRAINT `fk-notification-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 14. support_ticket -----
DROP TABLE IF EXISTS `support_ticket`;
CREATE TABLE `support_ticket` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `number` VARCHAR(20) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `assigned_admin_id` INT(11) NULL,
  `category` ENUM('account','payment','rental','car','technical','other') NOT NULL DEFAULT 'other',
  `subject` VARCHAR(255) NOT NULL,
  `priority` ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `status` ENUM('open','in_progress','waiting_user','resolved','closed') NOT NULL DEFAULT 'open',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `closed_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `idx-support_ticket-user_id` (`user_id`),
  KEY `idx-support_ticket-status` (`status`),
  KEY `idx-support_ticket-category` (`category`),
  KEY `fk-support_ticket-assigned_admin_id` (`assigned_admin_id`),
  CONSTRAINT `fk-support_ticket-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-support_ticket-assigned_admin_id` FOREIGN KEY (`assigned_admin_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 15. support_message -----
DROP TABLE IF EXISTS `support_message`;
CREATE TABLE `support_message` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` INT(11) NOT NULL,
  `author_id` INT(11) NOT NULL,
  `author_role` ENUM('user','admin','system') NOT NULL DEFAULT 'user',
  `message` TEXT NOT NULL,
  `attachments` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-support_message-ticket_id` (`ticket_id`),
  KEY `idx-support_message-author_id` (`author_id`),
  CONSTRAINT `fk-support_message-ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `support_ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-support_message-author_id` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 16. faq_category -----
DROP TABLE IF EXISTS `faq_category`;
CREATE TABLE `faq_category` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `icon` VARCHAR(50) NULL,
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 17. faq -----
DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_id` INT(11) NULL,
  `question` VARCHAR(500) NOT NULL,
  `answer` TEXT NOT NULL,
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-faq-category_id` (`category_id`),
  CONSTRAINT `fk-faq-category_id` FOREIGN KEY (`category_id`) REFERENCES `faq_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 18. page -----
DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(190) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NULL,
  `meta_description` VARCHAR(500) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 19. setting -----
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(100) NOT NULL,
  `value` TEXT NULL,
  `group` VARCHAR(50) NOT NULL DEFAULT 'general',
  `type` ENUM('string','int','float','bool','text','json') NOT NULL DEFAULT 'string',
  `label` VARCHAR(255) NULL,
  `description` VARCHAR(500) NULL,
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `idx-setting-group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 20. email_template -----
DROP TABLE IF EXISTS `email_template`;
CREATE TABLE `email_template` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(100) NOT NULL,
  `channel` ENUM('email','sms') NOT NULL DEFAULT 'email',
  `subject` VARCHAR(255) NULL,
  `body` TEXT NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `description` VARCHAR(500) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- migration history (Yii2) -----
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` VARCHAR(180) NOT NULL,
  `apply_time` INT(11) NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `migration` (`version`, `apply_time`) VALUES
  ('m000000_000000_base', UNIX_TIMESTAMP()),
  ('m250101_000001_create_users_tables', UNIX_TIMESTAMP()),
  ('m250101_000002_create_cars_tables', UNIX_TIMESTAMP()),
  ('m250101_000003_create_promo_codes_table', UNIX_TIMESTAMP()),
  ('m250101_000004_create_bookings_tables', UNIX_TIMESTAMP()),
  ('m250101_000005_create_transactions_table', UNIX_TIMESTAMP()),
  ('m250101_000006_create_notifications_table', UNIX_TIMESTAMP()),
  ('m250101_000007_create_support_tables', UNIX_TIMESTAMP()),
  ('m250101_000008_create_faq_pages_settings', UNIX_TIMESTAMP()),
  ('m250101_000009_seed_demo_data', UNIX_TIMESTAMP());

SET FOREIGN_KEY_CHECKS = 1;
