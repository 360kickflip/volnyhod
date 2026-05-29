-- =========================================================
--  ВОЛЬНЫЙ ХОД  ·  Привязка фото к автомобилям
--  Запускать после того, как файлы car_*.jpg уже лежат в web/uploads/cars/
--
--  Импорт через phpMyAdmin: открыть БД → Импорт → этот файл → Вперёд
-- =========================================================

SET NAMES utf8mb4;

-- Чистим существующие фото (если есть) — иначе будут дубликаты
DELETE FROM `car_photo`
WHERE `file_path` IN (
    'car_1_solaris.jpg', 'car_2_kia.jpg', 'car_3_polo.jpg', 'car_4_skoda.jpg',
    'car_5_toyota.jpg', 'car_6_bmw.jpg', 'car_7_mercedes.jpg', 'car_8_logan.jpg'
);

-- Привязываем фото к автомобилям (по гос. номерам, чтобы работало и при разных id)
INSERT INTO `car_photo` (`car_id`, `file_path`, `is_main`, `sort_order`, `created_at`)
SELECT id, 'car_1_solaris.jpg', 1, 1, NOW() FROM `car` WHERE `license_plate` = 'А123АА777';

INSERT INTO `car_photo` (`car_id`, `file_path`, `is_main`, `sort_order`, `created_at`)
SELECT id, 'car_2_kia.jpg', 1, 1, NOW() FROM `car` WHERE `license_plate` = 'В456ВВ777';

INSERT INTO `car_photo` (`car_id`, `file_path`, `is_main`, `sort_order`, `created_at`)
SELECT id, 'car_3_polo.jpg', 1, 1, NOW() FROM `car` WHERE `license_plate` = 'С789СС777';

INSERT INTO `car_photo` (`car_id`, `file_path`, `is_main`, `sort_order`, `created_at`)
SELECT id, 'car_4_skoda.jpg', 1, 1, NOW() FROM `car` WHERE `license_plate` = 'Е111ЕЕ777';

INSERT INTO `car_photo` (`car_id`, `file_path`, `is_main`, `sort_order`, `created_at`)
SELECT id, 'car_5_toyota.jpg', 1, 1, NOW() FROM `car` WHERE `license_plate` = 'Н222НН777';

INSERT INTO `car_photo` (`car_id`, `file_path`, `is_main`, `sort_order`, `created_at`)
SELECT id, 'car_6_bmw.jpg', 1, 1, NOW() FROM `car` WHERE `license_plate` = 'К333КК777';

INSERT INTO `car_photo` (`car_id`, `file_path`, `is_main`, `sort_order`, `created_at`)
SELECT id, 'car_7_mercedes.jpg', 1, 1, NOW() FROM `car` WHERE `license_plate` = 'М444ММ777';

INSERT INTO `car_photo` (`car_id`, `file_path`, `is_main`, `sort_order`, `created_at`)
SELECT id, 'car_8_logan.jpg', 1, 1, NOW() FROM `car` WHERE `license_plate` = 'Р555РР777';

-- Проверка результата
SELECT c.id, c.brand, c.model, c.license_plate, p.file_path, p.is_main
FROM `car` c
LEFT JOIN `car_photo` p ON p.car_id = c.id
ORDER BY c.id;
