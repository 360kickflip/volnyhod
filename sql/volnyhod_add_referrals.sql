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
