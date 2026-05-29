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
INSERT INTO `page` (`slug`, `title`, `content`, `meta_description`, `is_active`, `updated_at`) VALUES
("terms", "Правила пользования сервисом «Вольный Ход»", "<blockquote>
<p><strong>Кратко:</strong> кто может пользоваться сервисом, как берётся и возвращается автомобиль, как мы рассчитываем стоимость и кто за что отвечает.</p>
</blockquote>

<h2 id=\"general\">1. Общие положения</h2>
<p>Настоящие Правила определяют порядок предоставления услуги краткосрочной аренды автомобилей сервисом «Вольный Ход» (далее — «Сервис»). Регистрируясь и пользуясь Сервисом, вы подтверждаете, что ознакомились и согласны с настоящими Правилами в полном объёме.</p>
<p>Правила являются неотъемлемой частью <a href=\"/page/user-agreement\">Пользовательского соглашения</a> и обязательны к исполнению всеми пользователями.</p>

<h2 id=\"requirements\">2. Требования к водителю</h2>
<p>Чтобы пользоваться Сервисом, пользователь должен соответствовать всем перечисленным условиям:</p>
<ul>
<li>возраст — <strong>от 21 года</strong>;</li>
<li>стаж вождения по категории «B» — <strong>не менее 2 лет</strong>;</li>
<li>наличие действующего водительского удостоверения, оформленного на территории РФ или признаваемого в РФ;</li>
<li>отсутствие медицинских противопоказаний к управлению транспортным средством;</li>
<li>отсутствие лишения права управления и непогашенных неоплаченных штрафов ГИБДД, превышающих 10&nbsp;000 ₽.</li>
</ul>

<h2 id=\"registration\">3. Регистрация и верификация</h2>
<p>Регистрация в Сервисе является бесплатной и состоит из:</p>
<ol>
<li>заполнения формы регистрации (имя, email, телефон, дата рождения);</li>
<li>подтверждения адреса электронной почты;</li>
<li>загрузки документов: водительское удостоверение (с обеих сторон), страница паспорта с фотографией и страница с регистрацией;</li>
<li>прохождения верификации — занимает до 24 часов в рабочее время.</li>
</ol>
<p>Сервис вправе отказать в верификации без объяснения причин при выявлении признаков мошенничества, недействительных документов или нарушения требований законодательства.</p>

<h2 id=\"rental\">4. Порядок аренды автомобиля</h2>
<h3>4.1. Бронирование и начало аренды</h3>
<p>Перед началом поездки пользователь обязан:</p>
<ul>
<li>выбрать автомобиль в каталоге или на карте, проверить его доступность и соответствие тарифу;</li>
<li>осмотреть автомобиль снаружи и внутри, при наличии повреждений — сделать фото и сообщить в Сервис до начала движения;</li>
<li>проверить наличие документов на автомобиль, страхового полиса и заводской комплектации;</li>
<li>подтвердить согласие с условиями аренды через личный кабинет.</li>
</ul>

<h3>4.2. Использование автомобиля</h3>
<p>Во время аренды запрещается:</p>
<ul>
<li>передавать управление автомобилем третьим лицам, в том числе обладающим водительским удостоверением;</li>
<li>выезжать за пределы зоны обслуживания без согласования с Сервисом;</li>
<li>использовать автомобиль для участия в гонках, соревнованиях, тест-драйвах, обучения вождению, такси, грузоперевозок;</li>
<li>перевозить грузы, превышающие допустимую массу автомобиля, опасные, легковоспламеняющиеся, взрывчатые и иные грузы, запрещённые к перевозке;</li>
<li>управлять автомобилем в состоянии алкогольного, наркотического или иного опьянения;</li>
<li>курить и провозить животных без специальных переносок и защитных накидок.</li>
</ul>

<h3>4.3. Завершение аренды</h3>
<p>Аренда завершается в личном кабинете кнопкой «Завершить аренду» при выполнении всех условий:</p>
<ul>
<li>автомобиль припаркован в зоне обслуживания и в разрешённом для парковки месте;</li>
<li>двигатель заглушён, окна закрыты, фары выключены;</li>
<li>в салоне не оставлено личных вещей, мусора и порчи;</li>
<li>уровень топлива не ниже 20% от полного бака.</li>
</ul>

<h2 id=\"pricing\">5. Финансовые условия</h2>
<p>Стоимость аренды рассчитывается по формуле: <strong>(минуты × цена за минуту) + (километры × цена за километр)</strong> с учётом тарифа выбранного автомобиля.</p>
<p>На время аренды на балансе пользователя замораживается депозит, размер которого зависит от тарифа. Депозит автоматически возвращается на баланс после завершения поездки.</p>
<p>Если на балансе недостаточно средств для покрытия стоимости поездки, Сервис вправе провести списание с привязанной банковской карты пользователя.</p>

<h3>5.1. Просрочка</h3>
<p>Если фактическое время аренды превышает запланированное, на превышение начисляется тариф «Просрочка» — повышенная стоимость минуты, указанная в карточке тарифа.</p>

<h2 id=\"liability\">6. Ответственность</h2>
<p>Пользователь несёт полную материальную ответственность за:</p>
<ul>
<li>повреждения автомобиля во время аренды, не покрываемые страховкой;</li>
<li>штрафы ГИБДД, начисленные за нарушения, совершённые во время его аренды;</li>
<li>эвакуацию автомобиля и стоянку, если она вызвана действиями пользователя;</li>
<li>загрязнение салона, требующее химчистки;</li>
<li>утрату ключей, документов и иных принадлежностей автомобиля.</li>
</ul>
<p>Размер штрафов и компенсаций указан в Приложении №1 к настоящим Правилам и может быть изменён Сервисом с уведомлением пользователя за 14 дней.</p>

<h2 id=\"accident\">7. Действия при ДТП и неисправностях</h2>
<p>В случае дорожно-транспортного происшествия или поломки пользователь обязан:</p>
<ol>
<li>включить аварийную сигнализацию и выставить знак аварийной остановки;</li>
<li>при ДТП — вызвать ГИБДД (102) и оформить происшествие в установленном порядке;</li>
<li>незамедлительно сообщить в поддержку Сервиса по телефону <strong>+7 (800) 555-35-35</strong> или через личный кабинет;</li>
<li>не покидать место происшествия до прибытия сотрудников ГИБДД и/или представителей Сервиса;</li>
<li>оказать содействие в установлении обстоятельств происшествия.</li>
</ol>

<h2 id=\"termination\">8. Расторжение и блокировка</h2>
<p>Сервис вправе заблокировать аккаунт пользователя в случае нарушения настоящих Правил, законодательства РФ, неоплаты задолженности или предоставления недостоверных данных. О блокировке пользователь уведомляется в личном кабинете и по электронной почте.</p>
<p>Пользователь вправе в любое время прекратить использование Сервиса, направив запрос на удаление аккаунта на адрес <a href=\"mailto:support@volnyhod.ru\">support@volnyhod.ru</a>.</p>

<h2 id=\"changes\">9. Изменения Правил</h2>
<p>Сервис оставляет за собой право в одностороннем порядке вносить изменения в настоящие Правила. Актуальная редакция всегда размещена на сайте по адресу <a href=\"/page/terms\">/page/terms</a>. О существенных изменениях пользователь уведомляется по электронной почте не менее чем за 14 дней до их вступления в силу.</p>

<h2 id=\"contacts\">10. Контакты</h2>
<p>Все вопросы и претензии направляйте по адресу:</p>
<ul>
<li>Email: <a href=\"mailto:support@volnyhod.ru\">support@volnyhod.ru</a></li>
<li>Телефон: <strong>+7 (800) 555-35-35</strong> (круглосуточно)</li>
<li>Почтовый адрес: 125009, г. Москва, ул. Тверская, д. 1</li>
</ul>", "Правила использования сервиса каршеринга Вольный Ход — требования к водителю, аренда, ответственность", 1, NOW()),
("privacy", "Политика обработки персональных данных", "<blockquote>
<p><strong>Кратко:</strong> мы собираем минимум персональных данных, нужных для оказания услуги. Не передаём третьим лицам без вашего согласия. Храним в России в соответствии с Федеральным законом № 152-ФЗ.</p>
</blockquote>

<h2 id=\"general\">1. Общие положения</h2>
<p>Настоящая Политика разработана в соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ «О персональных данных» и определяет порядок обработки персональных данных и меры по обеспечению их безопасности, осуществляемые Оператором — сервисом «Вольный Ход».</p>
<p>Оператор: ООО «Вольный Ход» (далее — Оператор), ИНН/ОГРН — указаны в Пользовательском соглашении, юридический адрес: 125009, г. Москва, ул. Тверская, д. 1.</p>

<h2 id=\"terms\">2. Термины и определения</h2>
<ul>
<li><strong>Персональные данные</strong> — любая информация, относящаяся к прямо или косвенно определённому или определяемому физическому лицу.</li>
<li><strong>Обработка персональных данных</strong> — сбор, запись, систематизация, накопление, хранение, уточнение, извлечение, использование, передача, обезличивание, блокирование, удаление, уничтожение.</li>
<li><strong>Субъект данных</strong> — пользователь Сервиса, чьи данные обрабатываются.</li>
</ul>

<h2 id=\"data\">3. Какие данные мы обрабатываем</h2>
<table>
<thead><tr><th>Категория данных</th><th>Состав</th><th>Цель</th></tr></thead>
<tbody>
<tr><td>Идентификационные</td><td>ФИО, дата рождения, фото в документах</td><td>Заключение договора, верификация личности</td></tr>
<tr><td>Контактные</td><td>Email, номер телефона, адрес проживания</td><td>Связь с пользователем, отправка уведомлений</td></tr>
<tr><td>Документы</td><td>Скан/фото водительского удостоверения, паспорта</td><td>Подтверждение права управления и личности</td></tr>
<tr><td>Платёжные</td><td>Маскированные данные карт, история транзакций</td><td>Проведение оплат, возвратов, корректировок</td></tr>
<tr><td>Технические</td><td>IP-адрес, тип устройства, браузер, cookies</td><td>Работа сервиса, безопасность, аналитика</td></tr>
<tr><td>Геолокация</td><td>Координаты автомобиля во время аренды</td><td>Подбор ближайших авто, контроль аренды</td></tr>
</tbody>
</table>

<h2 id=\"purposes\">4. Цели обработки</h2>
<p>Мы обрабатываем персональные данные исключительно в следующих целях:</p>
<ol>
<li>заключение и исполнение договора аренды автомобиля;</li>
<li>верификация личности и подтверждение водительского стажа;</li>
<li>проведение взаиморасчётов с пользователем;</li>
<li>обеспечение безопасности транспорта и предотвращение мошенничества;</li>
<li>информирование о статусе аренд, изменениях в сервисе, акциях (при наличии согласия);</li>
<li>выполнение требований законодательства РФ.</li>
</ol>

<h2 id=\"legal-basis\">5. Правовое основание</h2>
<p>Обработка персональных данных осуществляется на основании:</p>
<ul>
<li>Федерального закона № 152-ФЗ «О персональных данных»;</li>
<li>согласия субъекта персональных данных, выраженного при регистрации;</li>
<li>исполнения договора, заключаемого через Пользовательское соглашение.</li>
</ul>

<h2 id=\"storage\">6. Хранение и защита</h2>
<p>Все персональные данные хранятся на защищённых серверах, расположенных на территории Российской Федерации. Применяются следующие меры защиты:</p>
<ul>
<li>шифрование передаваемых данных по протоколу HTTPS (TLS 1.3);</li>
<li>хранение паролей в виде криптографических хешей (bcrypt);</li>
<li>разграничение прав доступа сотрудников по принципу минимальных привилегий;</li>
<li>регулярное резервное копирование с шифрованием;</li>
<li>аудит действий пользователей и сотрудников;</li>
<li>защита от типовых атак (DDoS, SQL-инъекции, XSS, CSRF).</li>
</ul>
<p>Срок хранения — на период действия аккаунта пользователя и в течение 5 лет после его удаления (для целей бухгалтерского учёта и разрешения возможных споров), либо иной срок, установленный законодательством.</p>

<h2 id=\"transfer\">7. Передача третьим лицам</h2>
<p>Данные могут быть переданы:</p>
<ul>
<li>платёжным системам (ЮKassa, Robokassa) — только маскированные платёжные данные;</li>
<li>страховым компаниям — для оформления страхового случая при ДТП;</li>
<li>государственным органам — по запросу в случаях, установленных законом;</li>
<li>сервисам аналитики (Яндекс.Метрика, Google Analytics) — обезличенные технические данные при наличии согласия пользователя.</li>
</ul>
<p>Мы не продаём персональные данные и не передаём их в маркетинговых целях третьим лицам без явного согласия пользователя.</p>

<h2 id=\"rights\">8. Права пользователя</h2>
<p>В соответствии со статьями 14–17 Федерального закона № 152-ФЗ субъект персональных данных имеет право:</p>
<ul>
<li>получать информацию об обработке своих данных;</li>
<li>требовать уточнения, блокирования или удаления данных, если они неполные, устаревшие, неточные или незаконно полученные;</li>
<li>отозвать согласие на обработку (это повлечёт прекращение оказания услуг);</li>
<li>обжаловать действия Оператора в Роскомнадзоре или в суде.</li>
</ul>

<h2 id=\"cookies\">9. Cookies и аналитика</h2>
<p>Сайт использует файлы cookie для обеспечения работы и аналитики. Подробнее — в <a href=\"/page/cookies\">Политике cookie</a>. Управлять разрешением cookies можно в любой момент через ссылку <strong>«Настройки cookie»</strong> в подвале сайта.</p>

<h2 id=\"contacts\">10. Контакты для обращений</h2>
<p>Запросы по обработке персональных данных направляйте:</p>
<ul>
<li>Email: <a href=\"mailto:privacy@volnyhod.ru\">privacy@volnyhod.ru</a></li>
<li>Email поддержки: <a href=\"mailto:support@volnyhod.ru\">support@volnyhod.ru</a></li>
<li>Почтовый адрес: 125009, г. Москва, ул. Тверская, д. 1, ООО «Вольный Ход»</li>
</ul>
<p>Срок ответа на обращение — не более 10 рабочих дней.</p>", "Политика конфиденциальности и обработки персональных данных в сервисе Вольный Ход", 1, NOW()),
("user-agreement", "Пользовательское соглашение", "<blockquote>
<p><strong>Кратко:</strong> регистрируясь в «Вольном Ходе», вы заключаете с нами договор-оферту. Это соглашение определяет наши взаимные права и обязанности, порядок оплаты и ответственность.</p>
</blockquote>

<h2 id=\"parties\">1. Стороны соглашения</h2>
<p>Настоящее Пользовательское соглашение (далее — «Соглашение») является публичной офертой в соответствии со статьями 435 и 437 Гражданского кодекса РФ и заключается между:</p>
<ul>
<li><strong>Оператором</strong> — ООО «Вольный Ход», действующим на основании Устава, и</li>
<li><strong>Пользователем</strong> — физическим лицом, зарегистрировавшимся на сайте <a href=\"/\">volnyhod.ru</a> и/или в мобильном приложении.</li>
</ul>
<p>Акцептом оферты признаётся совершение пользователем конклюдентных действий — регистрация в Сервисе и подтверждение согласия с настоящим Соглашением.</p>

<h2 id=\"subject\">2. Предмет соглашения</h2>
<p>Оператор предоставляет Пользователю на возмездной основе доступ к Сервису краткосрочной аренды автомобилей, а Пользователь обязуется использовать Сервис в соответствии с настоящим Соглашением, <a href=\"/page/terms\">Правилами пользования</a> и действующим законодательством РФ.</p>

<h2 id=\"account\">3. Регистрация и аккаунт</h2>
<p>Для регистрации Пользователь сообщает достоверные сведения о себе: ФИО, email, телефон, дату рождения, и загружает документы для верификации.</p>
<p>Пользователь обязан:</p>
<ul>
<li>обеспечивать конфиденциальность пароля и не передавать данные доступа третьим лицам;</li>
<li>незамедлительно сообщать в Сервис о подозрении на несанкционированный доступ к аккаунту;</li>
<li>поддерживать актуальность сведений в личном кабинете.</li>
</ul>
<p>Запрещается регистрация одного пользователя нескольких аккаунтов и передача аккаунта другим лицам.</p>

<h2 id=\"services\">4. Перечень услуг</h2>
<p>Оператор предоставляет Пользователю следующие возможности:</p>
<ol>
<li>поиск и бронирование автомобилей в каталоге и на карте;</li>
<li>заключение договоров аренды с возможностью открытия и закрытия автомобиля через мобильное приложение или сайт;</li>
<li>оплата услуг через личный кабинет;</li>
<li>обращение в службу поддержки 24/7;</li>
<li>получение чеков и финансовой отчётности.</li>
</ol>

<h2 id=\"payment\">5. Стоимость и порядок расчётов</h2>
<p>Стоимость услуг определяется выбранным Пользователем тарифом. Действующие тарифы размещены по адресу <a href=\"/tariffs\">/tariffs</a>.</p>
<p>Расчёты производятся в российских рублях. Оператор является налоговым агентом и направляет Пользователю фискальный чек в электронном виде на email, указанный при регистрации.</p>
<p>Способы оплаты:</p>
<ul>
<li>банковская карта (Visa, Mastercard, МИР);</li>
<li>система быстрых платежей (СБП);</li>
<li>списание с баланса в личном кабинете.</li>
</ul>

<h2 id=\"rights-obligations\">6. Права и обязанности сторон</h2>
<h3>6.1. Оператор обязан:</h3>
<ul>
<li>обеспечивать работу Сервиса в соответствии с заявленным функционалом;</li>
<li>предоставлять автомобиль в технически исправном состоянии;</li>
<li>обрабатывать персональные данные в соответствии с <a href=\"/page/privacy\">Политикой конфиденциальности</a>;</li>
<li>оказывать поддержку через каналы, указанные на сайте.</li>
</ul>

<h3>6.2. Оператор вправе:</h3>
<ul>
<li>в одностороннем порядке изменять условия Соглашения с уведомлением Пользователя;</li>
<li>приостанавливать оказание услуг для технических работ с заблаговременным уведомлением;</li>
<li>блокировать аккаунт при нарушении Правил или законодательства;</li>
<li>проверять сведения, указанные Пользователем при регистрации;</li>
<li>отказать в предоставлении услуг при наличии задолженности или подозрении на мошенничество.</li>
</ul>

<h3>6.3. Пользователь обязан:</h3>
<ul>
<li>использовать Сервис добросовестно и по назначению;</li>
<li>своевременно оплачивать оказанные услуги;</li>
<li>соблюдать <a href=\"/page/terms\">Правила пользования</a> и Правила дорожного движения;</li>
<li>возмещать ущерб, причинённый автомобилю и/или Оператору;</li>
<li>не предпринимать действий, направленных на нарушение работы Сервиса.</li>
</ul>

<h3>6.4. Пользователь вправе:</h3>
<ul>
<li>пользоваться всеми функциями Сервиса в соответствии с тарифом;</li>
<li>обращаться в поддержку и получать ответы в разумные сроки;</li>
<li>в любой момент прекратить использование Сервиса и удалить аккаунт.</li>
</ul>

<h2 id=\"liability\">7. Ответственность сторон</h2>
<p>За неисполнение или ненадлежащее исполнение обязательств Стороны несут ответственность в соответствии с действующим законодательством РФ.</p>
<p>Оператор не несёт ответственности за:</p>
<ul>
<li>невозможность использования Сервиса по причинам, не зависящим от Оператора (сбои операторов связи, перебои электропитания, форс-мажор);</li>
<li>действия третьих лиц, использующих аккаунт Пользователя в результате его собственной неосторожности;</li>
<li>штрафы ГИБДД и иные административные взыскания, начисленные за действия Пользователя.</li>
</ul>

<h2 id=\"force-majeure\">8. Форс-мажор</h2>
<p>Стороны освобождаются от ответственности за частичное или полное неисполнение обязательств, если оно явилось следствием обстоятельств непреодолимой силы (стихийные бедствия, военные действия, акты органов власти и т. п.).</p>

<h2 id=\"disputes\">9. Разрешение споров</h2>
<p>Все споры решаются путём переговоров. При недостижении согласия споры подлежат рассмотрению в суде по месту нахождения Оператора в соответствии с законодательством РФ.</p>
<p>До обращения в суд обязателен претензионный порядок: срок ответа на претензию — 30 дней с момента её получения.</p>

<h2 id=\"duration\">10. Срок действия и изменения</h2>
<p>Соглашение вступает в силу с момента регистрации Пользователя и действует бессрочно до его расторжения одной из Сторон.</p>
<p>Действующая редакция Соглашения всегда доступна по адресу <a href=\"/page/user-agreement\">/page/user-agreement</a>. О существенных изменениях Пользователь уведомляется не менее чем за 14 дней.</p>

<h2 id=\"final\">11. Заключительные положения</h2>
<p>Признание судом какого-либо положения Соглашения недействительным не влечёт недействительности остальных положений.</p>
<p>Документ составлен на русском языке. В случае возникновения разночтений преимущественную силу имеет русский текст.</p>

<h2 id=\"requisites\">12. Реквизиты Оператора</h2>
<p>ООО «Вольный Ход»<br>
ИНН: 7700000000 (демо)<br>
ОГРН: 1117700000000 (демо)<br>
Юридический адрес: 125009, г. Москва, ул. Тверская, д. 1<br>
Email: <a href=\"mailto:legal@volnyhod.ru\">legal@volnyhod.ru</a><br>
Телефон: <strong>+7 (800) 555-35-35</strong></p>", "Пользовательское соглашение — публичная оферта сервиса каршеринга Вольный Ход", 1, NOW()),
("cookies", "Политика использования файлов cookie", "<blockquote>
<p><strong>Кратко:</strong> мы используем cookie, чтобы сайт работал и был удобным. Вы можете в любой момент изменить разрешения через ссылку «Настройки cookie» в подвале сайта.</p>
</blockquote>

<h2 id=\"what\">1. Что такое cookies</h2>
<p>Cookies — это небольшие текстовые файлы, которые сайт сохраняет в вашем браузере. Они помогают сайту узнавать вас при повторных визитах и запоминать ваши действия.</p>

<h2 id=\"categories\">2. Какие cookies мы используем</h2>
<table>
<thead><tr><th>Категория</th><th>Назначение</th><th>Можно отключить</th></tr></thead>
<tbody>
<tr><td><strong>Обязательные</strong></td><td>Авторизация, защита от CSRF, сессионные данные. Без них сайт не работает.</td><td>Нет</td></tr>
<tr><td><strong>Функциональные</strong></td><td>Запоминают ваши настройки интерфейса, регион, выбранный тариф фильтра.</td><td>Да</td></tr>
<tr><td><strong>Аналитические</strong></td><td>Помогают понять, как используется сайт (Яндекс.Метрика, Google Analytics). Данные обезличены.</td><td>Да</td></tr>
<tr><td><strong>Маркетинговые</strong></td><td>Используются для показа релевантной рекламы и измерения её эффективности.</td><td>Да</td></tr>
</tbody>
</table>

<h2 id=\"manage\">3. Как управлять cookies</h2>
<p>Управлять разрешениями cookies можно тремя способами:</p>
<ol>
<li>в баннере, который появляется при первом посещении сайта;</li>
<li>через ссылку <strong>«Настройки cookie»</strong> в подвале (футере) сайта;</li>
<li>в настройках вашего браузера — отключение cookies полностью или для конкретного сайта.</li>
</ol>
<p><strong>Внимание:</strong> отключение обязательных cookies приведёт к невозможности использования сайта (вход в личный кабинет, оплата и др.).</p>

<h2 id=\"third-party\">4. Cookies сторонних сервисов</h2>
<p>Сайт может использовать следующие сторонние сервисы (только при наличии вашего согласия на соответствующую категорию):</p>
<ul>
<li><strong>Яндекс.Метрика</strong> — анализ посещаемости сайта. <a href=\"https://yandex.ru/legal/metrica_termsofuse/\" target=\"_blank\" rel=\"noopener\">Условия Яндекса</a>.</li>
<li><strong>Google Analytics</strong> — анализ поведения пользователей. <a href=\"https://policies.google.com/privacy\" target=\"_blank\" rel=\"noopener\">Политика Google</a>.</li>
<li><strong>Yandex Maps API</strong> — отображение интерактивных карт.</li>
</ul>

<h2 id=\"storage\">5. Срок хранения</h2>
<p>Cookies хранятся в вашем браузере от одного сеанса (сессионные) до 1 года (постоянные). Конкретный срок указывается на стороне браузера и зависит от типа cookie.</p>

<h2 id=\"contacts\">6. Контакты</h2>
<p>Вопросы по использованию cookies: <a href=\"mailto:privacy@volnyhod.ru\">privacy@volnyhod.ru</a>.</p>", "Какие cookie использует сайт Вольный Ход и как ими управлять", 1, NOW());


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

-- ----- ПРИВЯЗКА ФОТО К АВТО -----
INSERT INTO `car_photo` (`car_id`, `file_path`, `is_main`, `sort_order`, `created_at`) VALUES
  (1, "car_1_solaris.jpg", 1, 1, NOW()),
  (2, "car_2_kia.jpg", 1, 1, NOW()),
  (3, "car_3_polo.jpg", 1, 1, NOW()),
  (4, "car_4_skoda.jpg", 1, 1, NOW()),
  (5, "car_5_toyota.jpg", 1, 1, NOW()),
  (6, "car_6_bmw.jpg", 1, 1, NOW()),
  (7, "car_7_mercedes.jpg", 1, 1, NOW()),
  (8, "car_8_logan.jpg", 1, 1, NOW());

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
--  ВОЛЬНЫЙ ХОД  ·  Добавление реферальной программы
--  Запускать на ранее установленную БД
--
--  Импорт через phpMyAdmin: открыть БД → Импорт → этот файл → Вперёд
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----- 1. Расширяем таблицу user -----
ALTER TABLE `user`
    ADD COLUMN `referral_code` VARCHAR(20) NULL AFTER `role`,
    ADD COLUMN `referred_by_user_id` INT(11) NULL AFTER `referral_code`,
    ADD COLUMN `referral_bonus_paid` TINYINT(1) NOT NULL DEFAULT 0 AFTER `referred_by_user_id`,
    ADD UNIQUE KEY `idx-user-referral_code` (`referral_code`),
    ADD KEY `idx-user-referred_by` (`referred_by_user_id`),
    ADD CONSTRAINT `fk-user-referred_by` FOREIGN KEY (`referred_by_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ----- 2. Таблица referral_reward -----
DROP TABLE IF EXISTS `referral_reward`;
CREATE TABLE `referral_reward` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `referrer_id` INT(11) NOT NULL,
    `referred_id` INT(11) NOT NULL,
    `booking_id` INT(11) NULL,
    `amount_referrer` DECIMAL(10,2) NOT NULL,
    `amount_referred` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NOT NULL,
    `paid_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx-referral_reward-referred` (`referred_id`),
    KEY `idx-referral_reward-referrer` (`referrer_id`),
    KEY `idx-referral_reward-status` (`status`),
    KEY `fk-referral_reward-booking` (`booking_id`),
    CONSTRAINT `fk-referral_reward-referrer` FOREIGN KEY (`referrer_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk-referral_reward-referred` FOREIGN KEY (`referred_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk-referral_reward-booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----- 3. Сгенерировать реф-коды для существующих пользователей -----
-- Простая генерация: VH-XXXX, где XXXX — псевдослучайные символы из id пользователя
UPDATE `user`
SET `referral_code` = CONCAT('VH-', UPPER(SUBSTRING(MD5(CONCAT(id, email)), 1, 4)), UPPER(SUBSTRING(MD5(CONCAT(id, created_at)), 1, 2)))
WHERE `referral_code` IS NULL;

-- ----- 4. Настройки реферальной программы -----
INSERT INTO `setting` (`key`, `value`, `group`, `type`, `label`, `description`, `sort_order`) VALUES
    ('referral_program_active', '1',          'business', 'bool',   'Реферальная программа активна', 'Включает/выключает программу',          10),
    ('referral_bonus_referrer', '300',        'business', 'float',  'Бонус приглашающему (₽)',       'Сколько получит тот, кто пригласил',   11),
    ('referral_bonus_referred', '300',        'business', 'float',  'Бонус приглашённому (₽)',       'Сколько получит новый пользователь',   12),
    ('referral_bonus_trigger',  'first_trip', 'business', 'string', 'Триггер выплаты',               'first_trip / signup / verification',   13)
ON DUPLICATE KEY UPDATE
    `value` = VALUES(`value`),
    `group` = VALUES(`group`),
    `type` = VALUES(`type`),
    `label` = VALUES(`label`),
    `description` = VALUES(`description`);

-- ----- 5. Регистрация миграции в истории -----
INSERT IGNORE INTO `migration` (`version`, `apply_time`) VALUES
    ('m250101_000011_referrals', UNIX_TIMESTAMP());

SET FOREIGN_KEY_CHECKS = 1;
