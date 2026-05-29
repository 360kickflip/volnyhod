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
-- =========================================================
--  ВОЛЬНЫЙ ХОД  ·  Демо-данные
--  Запускать ПОСЛЕ volnyhod_schema.sql
--
--  Демо-аккаунты:
--    admin@volnyhod.ru / admin12345  (роль admin)
--    user@volnyhod.ru  / user12345   (роль user, верифицирован, баланс 1500₽)
--    demo@volnyhod.ru  / demo12345   (роль user, на проверке)
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----- ПОЛЬЗОВАТЕЛИ -----
INSERT INTO `user` (`id`, `email`, `phone`, `password_hash`, `auth_key`, `name`, `birthdate`, `balance`, `verification_status`, `status`, `role`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'admin@volnyhod.ru', '+79991110001', '$2y$13$UduJGar8q2C3RclSoOStCuluTQXQO5pAux0uu0O4kMWTgvorFpU/2', 'XVGJ0JXLDrerK-LdDvNcA6mSELktZJfe', 'Администратор', '1990-01-01', 0.00, 'verified', 'active', 'admin', NOW(), NOW(), NOW()),
(2, 'user@volnyhod.ru',  '+79991110002', '$2y$13$q/LGWO2rYGB1AkkB.DBn8O9GS9kyoE44JhvlRLN/Zn6LZrWJuyXsS', 'MRj_pRhhT8fNuOru-C51dsDtsqLgH6JP', 'Иван Петров',     '1995-05-15', 1500.00, 'verified', 'active', 'user', NOW(), NOW(), NOW()),
(3, 'demo@volnyhod.ru',  '+79991110003', '$2y$13$bZl8hB3tKAVibyNiOJn6subKAgk5b0jQu804F/rD8.Z.2wJL2rtDm', 'qJi0piL-CkH_2GhmQUCyB7he9xu320AU', 'Демо Пользователь', '1992-08-20', 500.00, 'pending', 'active', 'user', NULL, NOW(), NOW());

-- ----- ТАРИФЫ -----
INSERT INTO `tariff` (`id`, `name`, `description`, `price_per_minute`, `price_per_km`, `price_per_hour`, `price_per_day`, `free_km`, `deposit`, `overdue_per_minute`, `color`, `icon`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Эконом',   'Городские поездки и компактные авто',         8.50,  12.00, 350.00, 2500.00, 0, 2000.00, 15.00, '#10b981', 'fa-leaf',  1, 1, NOW(), NOW()),
(2, 'Стандарт', 'Седаны и кроссоверы для каждого дня',         11.00, 15.00, 450.00, 3500.00, 0, 2000.00, 18.00, '#0d6efd', 'fa-car',   1, 2, NOW(), NOW()),
(3, 'Комфорт',  'Бизнес-класс с расширенной комплектацией',    14.50, 18.00, 600.00, 4500.00, 0, 3000.00, 22.00, '#6366f1', 'fa-couch', 1, 3, NOW(), NOW()),
(4, 'Премиум',  'Премиальные авто для особых случаев',         22.00, 25.00, 950.00, 7500.00, 0, 5000.00, 30.00, '#f59e0b', 'fa-crown', 1, 4, NOW(), NOW());

-- ----- АВТОМОБИЛИ -----
INSERT INTO `car` (`id`, `tariff_id`, `brand`, `model`, `year`, `color`, `license_plate`, `transmission`, `body_type`, `seats`, `fuel_type`, `mileage`, `fuel_level`, `features`, `description`, `lat`, `lng`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hyundai',       'Solaris',    2023, 'Белый',    'А123АА777', 'auto',   'Седан',   5, 'petrol', 28430, 78, '["Кондиционер","Bluetooth","USB","Подогрев сидений"]', 'Hyundai Solaris — отличный выбор для городских поездок.',     55.7558000, 37.6173000, 'ул. Тверская, 10',     'available', NOW(), NOW()),
(2, 1, 'Kia',           'Rio',        2022, 'Серебро',  'В456ВВ777', 'auto',   'Седан',   5, 'petrol', 41200, 65, '["Кондиционер","Bluetooth","USB","Подогрев сидений"]', 'Kia Rio — отличный выбор для городских поездок.',             55.7611000, 37.6086000, 'Газетный пер., 5',     'available', NOW(), NOW()),
(3, 2, 'Volkswagen',    'Polo',       2023, 'Чёрный',   'С789СС777', 'auto',   'Седан',   5, 'petrol', 19500, 84, '["Кондиционер","Bluetooth","USB","Подогрев сидений"]', 'Volkswagen Polo — отличный выбор для городских поездок.',     55.7494000, 37.5395000, 'Кутузовский пр., 24',  'available', NOW(), NOW()),
(4, 2, 'Skoda',         'Rapid',      2023, 'Синий',    'Е111ЕЕ777', 'auto',   'Лифтбэк', 5, 'petrol', 22150, 92, '["Кондиционер","Bluetooth","USB","Подогрев сидений"]', 'Skoda Rapid — отличный выбор для городских поездок.',         55.7308000, 37.6201000, 'ул. Пятницкая, 18',    'available', NOW(), NOW()),
(5, 3, 'Toyota',        'Camry',      2023, 'Чёрный',   'Н222НН777', 'auto',   'Седан',   5, 'petrol', 31000, 71, '["Кондиционер","Bluetooth","USB","Подогрев сидений"]', 'Toyota Camry — отличный выбор для городских поездок.',        55.7601000, 37.6184000, 'Театральный пр-д, 2',  'available', NOW(), NOW()),
(6, 4, 'BMW',           '3 Series',   2024, 'Белый',    'К333КК777', 'auto',   'Седан',   5, 'petrol', 8420,  88, '["Кондиционер","Bluetooth","USB","Подогрев сидений","Кожаный салон"]', 'BMW 3 Series — премиальный седан.',  55.7522000, 37.5947000, 'Кутузовский пр., 9',   'rented',    NOW(), NOW()),
(7, 4, 'Mercedes-Benz', 'E-Class',    2024, 'Чёрный',   'М444ММ777', 'auto',   'Седан',   5, 'petrol', 12300, 80, '["Кондиционер","Bluetooth","USB","Подогрев сидений","Кожаный салон"]', 'Mercedes-Benz E-Class — премиум-класс.', 55.7558000, 37.5894000, 'Смоленская пл., 3',    'available', NOW(), NOW()),
(8, 1, 'Renault',       'Logan',      2022, 'Серый',    'Р555РР777', 'manual', 'Седан',   5, 'petrol', 56700, 53, '["Кондиционер","Bluetooth","USB"]',                  'Renault Logan — экономичный седан.',                          55.7702000, 37.6357000, 'Каланчёвская ул., 11', 'available', NOW(), NOW());

-- ----- ПРОМОКОДЫ -----
INSERT INTO `promo_code` (`id`, `code`, `description`, `type`, `value`, `min_amount`, `max_discount`, `usage_limit`, `usage_count`, `per_user_limit`, `valid_from`, `valid_to`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'Скидка 10% для новых пользователей', 'percent', 10.00, 100.00, 500.00,  1000, 0, 1, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR),    1, NOW(), NOW()),
(2, 'VOLNY200',  'Скидка 200₽ на любую поездку',       'fixed',   200.00, 500.00, NULL,    500, 0, 1, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH),  1, NOW(), NOW()),
(3, 'SUMMER25',  'Летняя акция: -25%',                  'percent', 25.00, 200.00, 1000.00, 200, 0, 2, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH),  1, NOW(), NOW());

-- ----- FAQ КАТЕГОРИИ -----
INSERT INTO `faq_category` (`id`, `name`, `slug`, `icon`, `sort_order`) VALUES
(1, 'Регистрация и верификация',        'registration', 'fa-user-check', 1),
(2, 'Бронирование и аренда',            'booking',      'fa-key',        2),
(3, 'Оплата и тарифы',                  'payment',      'fa-credit-card', 3),
(4, 'Авто и техника',                   'cars',         'fa-car',        4),
(5, 'Штрафы и спорные ситуации',        'fines',        'fa-gavel',      5);

-- ----- FAQ -----
INSERT INTO `faq` (`category_id`, `question`, `answer`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Как зарегистрироваться?',           'Заполните форму регистрации, подтвердите email, загрузите водительское удостоверение и паспорт. Верификация занимает до 24 часов.', 1, 1, NOW(), NOW()),
(1, 'Какие документы нужны?',            'Водительское удостоверение (с двух сторон), главная страница паспорта и страница с пропиской. Стаж — от 2 лет, возраст — от 21 года.', 2, 1, NOW(), NOW()),
(1, 'Сколько длится верификация?',       'Обычно от 15 минут до 2 часов в рабочее время. Максимум — 24 часа.', 3, 1, NOW(), NOW()),
(2, 'Как забронировать автомобиль?',     'Откройте каталог или карту, выберите авто, нажмите «Забронировать», укажите длительность и подтвердите. Депозит будет заморожен на балансе.', 4, 1, NOW(), NOW()),
(2, 'Что делать в конце поездки?',       'Припаркуйте авто в зоне обслуживания, заглушите двигатель, закройте окна и нажмите «Завершить аренду» в личном кабинете.', 5, 1, NOW(), NOW()),
(2, 'Можно ли продлить аренду?',         'Да, прямо во время поездки на странице активной аренды. Списание идёт по тарифу.', 6, 1, NOW(), NOW()),
(3, 'Как пополнить баланс?',             'В разделе «Баланс» нажмите «Пополнить», укажите сумму и выберите способ оплаты (карта/СБП).', 7, 1, NOW(), NOW()),
(3, 'Что такое заблокированные средства?', 'Это депозит, удерживаемый во время аренды. После завершения поездки депозит возвращается на баланс.', 8, 1, NOW(), NOW()),
(3, 'Возвращается ли депозит?',          'Да, после завершения поездки депозит автоматически разблокируется на балансе.', 9, 1, NOW(), NOW()),
(4, 'Что делать, если мало топлива?',    'Заправьтесь на любой АЗС — чек загрузите в раздел «Поддержка», стоимость будет компенсирована.', 10, 1, NOW(), NOW()),
(4, 'Авто не заводится. Что делать?',    'Сообщите в поддержку через приложение. Мы пришлём другой автомобиль или возместим простой.', 11, 1, NOW(), NOW()),
(5, 'Кто оплачивает штрафы ГИБДД?',      'Штрафы, полученные во время вашей аренды, выставляются вам. Уведомление приходит в личный кабинет.', 12, 1, NOW(), NOW()),
(5, 'Что делать при ДТП?',               'Включите аварийку, выставите знак, вызовите ГИБДД и сообщите в нашу поддержку. Все действия описаны в правилах.', 13, 1, NOW(), NOW());

-- ----- СТАТИЧНЫЕ СТРАНИЦЫ -----
INSERT INTO `page` (`slug`, `title`, `content`, `is_active`, `updated_at`) VALUES
('terms',         'Правила и условия пользования сервисом',
  '<h2>Общие положения</h2><p>Настоящие правила регулируют отношения между сервисом «Вольный Ход» и пользователями. Регистрируясь в сервисе, вы соглашаетесь с правилами в полном объёме.</p><h2>Требования к водителю</h2><ul><li>Возраст — от 21 года</li><li>Стаж вождения — от 2 лет</li><li>Действующее водительское удостоверение категории B</li></ul><h2>Аренда автомобиля</h2><p>Тарификация поминутная. Депозит замораживается на время аренды и возвращается после завершения. Запрещено передавать управление третьим лицам.</p><h2>Ответственность</h2><p>Пользователь несёт полную материальную ответственность за повреждения автомобиля во время аренды.</p>',
  1, NOW()),
('privacy',       'Политика конфиденциальности',
  '<h2>Сбор данных</h2><p>Мы собираем минимум данных, необходимых для оказания услуг: контактные данные, документы для верификации, данные о поездках.</p><h2>Использование</h2><p>Данные используются исключительно для предоставления сервиса и не передаются третьим лицам без согласия пользователя, за исключением случаев, предусмотренных законом.</p><h2>Защита</h2><p>Все персональные данные хранятся в зашифрованном виде на защищённых серверах в РФ.</p>',
  1, NOW()),
('tariffs-info',  'Тарифы и стоимость',
  '<p>Подробное описание тарифов смотрите в каталоге автомобилей. Стоимость рассчитывается по формуле: время + пробег. Депозит замораживается на время аренды.</p>',
  1, NOW()),
('contacts',      'Контакты',
  '<p>Поддержка: +7 (800) 555-35-35</p><p>Email: support@volnyhod.ru</p><p>Москва, ул. Тверская, 1</p>',
  1, NOW());

-- ----- НАСТРОЙКИ -----
INSERT INTO `setting` (`key`, `value`, `group`, `type`, `label`, `description`, `sort_order`) VALUES
('site_name',              'Вольный Ход',          'general',  'string', 'Название сервиса',                '', 1),
('site_email',             'admin@volnyhod.ru',    'general',  'string', 'Email для уведомлений',           '', 2),
('support_phone',          '+7 (800) 555-35-35',   'general',  'string', 'Телефон поддержки',               '', 3),
('support_email',          'support@volnyhod.ru',  'general',  'string', 'Email поддержки',                 '', 4),
('yookassa_shop_id',       '',                     'payment',  'string', 'YooKassa Shop ID',                'Идентификатор магазина', 1),
('yookassa_secret_key',    '',                     'payment',  'string', 'YooKassa Secret Key',             'Секретный ключ', 2),
('robokassa_login',        '',                     'payment',  'string', 'Robokassa Login',                 '', 3),
('robokassa_password1',    '',                     'payment',  'string', 'Robokassa Password 1',            '', 4),
('yandex_maps_api_key',    '',                     'maps',     'string', 'Яндекс.Карты API ключ',           'Ключ для подключения карт', 1),
('default_lat',            '55.7558',              'maps',     'float',  'Центр карты по умолчанию (lat)',  '', 2),
('default_lng',            '37.6173',              'maps',     'float',  'Центр карты по умолчанию (lng)',  '', 3),
('min_age',                '21',                   'business', 'int',    'Минимальный возраст водителя',    '', 1),
('min_driving_experience', '2',                    'business', 'int',    'Минимальный стаж (лет)',          '', 2),
('default_deposit',        '2000',                 'business', 'float',  'Депозит по умолчанию',            '', 3);

-- ----- EMAIL ШАБЛОНЫ -----
INSERT INTO `email_template` (`code`, `channel`, `subject`, `body`, `is_active`) VALUES
('signup_welcome',     'email', 'Добро пожаловать в Вольный Ход!', 'Здравствуйте, {name}!\n\nДобро пожаловать в сервис каршеринга «Вольный Ход». Подтвердите email и загрузите документы для начала поездок.', 1),
('booking_started',    'email', 'Аренда началась',                 'Здравствуйте, {name}!\n\nАренда автомобиля {car} началась. Депозит {deposit}₽ заблокирован на балансе.', 1),
('booking_completed',  'email', 'Аренда завершена',                'Здравствуйте, {name}!\n\nАренда автомобиля {car} завершена. Стоимость поездки: {total}₽.', 1),
('password_reset',     'email', 'Сброс пароля',                    'Здравствуйте!\n\nДля сброса пароля перейдите по ссылке: {url}', 1);

-- ----- УВЕДОМЛЕНИЯ ДЛЯ ТЕСТОВОГО ЮЗЕРА -----
INSERT INTO `notification` (`user_id`, `type`, `title`, `message`, `icon`, `is_read`, `created_at`, `read_at`) VALUES
(2, 'success', 'Аккаунт верифицирован',  'Документы успешно проверены. Можете арендовать автомобили.', 'fa-check-circle', 0, NOW(), NULL),
(2, 'promo',   'Промокод WELCOME10',     'Используйте код WELCOME10 для скидки 10% на первую поездку.', 'fa-gift', 0, DATE_SUB(NOW(), INTERVAL 1 DAY), NULL),
(2, 'info',    'Добро пожаловать!',      'Спасибо за регистрацию в Вольный Ход.', 'fa-bell', 1, DATE_SUB(NOW(), INTERVAL 2 DAY), NOW());

-- ----- ТРАНЗАКЦИИ -----
INSERT INTO `transaction` (`user_id`, `type`, `amount`, `balance_after`, `status`, `payment_method`, `description`, `created_at`, `completed_at`) VALUES
(2, 'topup', 1500.00, 1500.00, 'completed', 'card',  'Пополнение баланса',  DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 'bonus',  100.00, 1600.00, 'completed', 'bonus', 'Приветственный бонус', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY));

SET FOREIGN_KEY_CHECKS = 1;
