<?php

use yii\db\Migration;

/**
 * Демо-данные: админ, тестовый юзер, тарифы, авто, FAQ, страницы, настройки
 */
class m250101_000009_seed_demo_data extends Migration
{
    public function safeUp()
    {
        $now = date('Y-m-d H:i:s');

        // ===== Пользователи =====
        $authKeyAdmin = Yii::$app->security->generateRandomString();
        $authKeyUser = Yii::$app->security->generateRandomString();
        $authKeyDemo = Yii::$app->security->generateRandomString();

        // admin / admin12345
        $this->insert('{{%user}}', [
            'email' => 'admin@volnyhod.ru',
            'phone' => '+79991110001',
            'password_hash' => Yii::$app->security->generatePasswordHash('admin12345'),
            'auth_key' => $authKeyAdmin,
            'name' => 'Администратор',
            'birthdate' => '1990-01-01',
            'balance' => 0,
            'verification_status' => 'verified',
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $adminId = $this->db->getLastInsertID();

        // user / user12345
        $this->insert('{{%user}}', [
            'email' => 'user@volnyhod.ru',
            'phone' => '+79991110002',
            'password_hash' => Yii::$app->security->generatePasswordHash('user12345'),
            'auth_key' => $authKeyUser,
            'name' => 'Иван Петров',
            'birthdate' => '1995-05-15',
            'balance' => 1500.00,
            'verification_status' => 'verified',
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $userId = $this->db->getLastInsertID();

        // demo / demo12345 (на проверке)
        $this->insert('{{%user}}', [
            'email' => 'demo@volnyhod.ru',
            'phone' => '+79991110003',
            'password_hash' => Yii::$app->security->generatePasswordHash('demo12345'),
            'auth_key' => $authKeyDemo,
            'name' => 'Демо Пользователь',
            'birthdate' => '1992-08-20',
            'balance' => 500.00,
            'verification_status' => 'pending',
            'role' => 'user',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ===== Тарифы =====
        $tariffs = [
            ['Эконом', 'Городские поездки и компактные авто', 8.50, 12.00, 350.00, 2500.00, 2000, 15.00, '#10b981', 'fa-leaf', 1],
            ['Стандарт', 'Седаны и кроссоверы для каждого дня', 11.00, 15.00, 450.00, 3500.00, 2000, 18.00, '#0d6efd', 'fa-car', 2],
            ['Комфорт', 'Бизнес-класс с расширенной комплектацией', 14.50, 18.00, 600.00, 4500.00, 3000, 22.00, '#6366f1', 'fa-couch', 3],
            ['Премиум', 'Премиальные авто для особых случаев', 22.00, 25.00, 950.00, 7500.00, 5000, 30.00, '#f59e0b', 'fa-crown', 4],
        ];
        $tariffIds = [];
        foreach ($tariffs as [$name, $desc, $ppm, $ppk, $pph, $ppd, $deposit, $overdue, $color, $icon, $sort]) {
            $this->insert('{{%tariff}}', [
                'name' => $name,
                'description' => $desc,
                'price_per_minute' => $ppm,
                'price_per_km' => $ppk,
                'price_per_hour' => $pph,
                'price_per_day' => $ppd,
                'free_km' => 0,
                'deposit' => $deposit,
                'overdue_per_minute' => $overdue,
                'color' => $color,
                'icon' => $icon,
                'is_active' => 1,
                'sort_order' => $sort,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $tariffIds[$name] = $this->db->getLastInsertID();
        }

        // ===== Автомобили (рандомно по Москве) =====
        $cars = [
            ['Hyundai', 'Solaris', 2023, 'Белый', 'А123АА777', 'auto', 'Седан', 5, 'petrol', 'Эконом', 55.7558, 37.6173, 'ул. Тверская, 10'],
            ['Kia', 'Rio', 2022, 'Серебро', 'В456ВВ777', 'auto', 'Седан', 5, 'petrol', 'Эконом', 55.7611, 37.6086, 'Газетный пер., 5'],
            ['Volkswagen', 'Polo', 2023, 'Чёрный', 'С789СС777', 'auto', 'Седан', 5, 'petrol', 'Стандарт', 55.7494, 37.5395, 'Кутузовский пр., 24'],
            ['Skoda', 'Rapid', 2023, 'Синий', 'Е111ЕЕ777', 'auto', 'Лифтбэк', 5, 'petrol', 'Стандарт', 55.7308, 37.6201, 'ул. Пятницкая, 18'],
            ['Toyota', 'Camry', 2023, 'Чёрный', 'Н222НН777', 'auto', 'Седан', 5, 'petrol', 'Комфорт', 55.7601, 37.6184, 'Театральный пр-д, 2'],
            ['BMW', '3 Series', 2024, 'Белый', 'К333КК777', 'auto', 'Седан', 5, 'petrol', 'Премиум', 55.7522, 37.5947, 'Кутузовский пр., 9'],
            ['Mercedes-Benz', 'E-Class', 2024, 'Чёрный', 'М444ММ777', 'auto', 'Седан', 5, 'petrol', 'Премиум', 55.7558, 37.5894, 'Смоленская пл., 3'],
            ['Renault', 'Logan', 2022, 'Серый', 'Р555РР777', 'manual', 'Седан', 5, 'petrol', 'Эконом', 55.7702, 37.6357, 'Каланчёвская ул., 11'],
        ];

        $statuses = ['available', 'available', 'available', 'available', 'available', 'rented', 'available', 'available'];

        foreach ($cars as $i => [$brand, $model, $year, $color, $plate, $trans, $body, $seats, $fuel, $tariff, $lat, $lng, $addr]) {
            $this->insert('{{%car}}', [
                'tariff_id' => $tariffIds[$tariff],
                'brand' => $brand,
                'model' => $model,
                'year' => $year,
                'color' => $color,
                'license_plate' => $plate,
                'transmission' => $trans,
                'body_type' => $body,
                'seats' => $seats,
                'fuel_type' => $fuel,
                'mileage' => rand(15000, 80000),
                'fuel_level' => rand(40, 95),
                'features' => json_encode(['Кондиционер', 'Bluetooth', 'USB', 'Подогрев сидений'], JSON_UNESCAPED_UNICODE),
                'description' => "$brand $model — отличный выбор для городских поездок.",
                'lat' => $lat,
                'lng' => $lng,
                'address' => $addr,
                'status' => $statuses[$i],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ===== Промокоды =====
        $this->batchInsert('{{%promo_code}}', ['code', 'description', 'type', 'value', 'min_amount', 'max_discount', 'usage_limit', 'usage_count', 'per_user_limit', 'valid_from', 'valid_to', 'is_active', 'created_at', 'updated_at'], [
            ['WELCOME10', 'Скидка 10% для новых пользователей', 'percent', 10, 100, 500, 1000, 0, 1, $now, date('Y-m-d H:i:s', strtotime('+1 year')), 1, $now, $now],
            ['VOLNY200', 'Скидка 200₽ на любую поездку', 'fixed', 200, 500, null, 500, 0, 1, $now, date('Y-m-d H:i:s', strtotime('+6 months')), 1, $now, $now],
            ['SUMMER25', 'Летняя акция: -25%', 'percent', 25, 200, 1000, 200, 0, 2, $now, date('Y-m-d H:i:s', strtotime('+3 months')), 1, $now, $now],
        ]);

        // ===== FAQ =====
        $faqCategories = [
            ['Регистрация и верификация', 'registration', 'fa-user-check', 1],
            ['Бронирование и аренда', 'booking', 'fa-key', 2],
            ['Оплата и тарифы', 'payment', 'fa-credit-card', 3],
            ['Авто и техника', 'cars', 'fa-car', 4],
            ['Штрафы и спорные ситуации', 'fines', 'fa-gavel', 5],
        ];
        $faqCatIds = [];
        foreach ($faqCategories as [$name, $slug, $icon, $sort]) {
            $this->insert('{{%faq_category}}', [
                'name' => $name,
                'slug' => $slug,
                'icon' => $icon,
                'sort_order' => $sort,
            ]);
            $faqCatIds[$slug] = $this->db->getLastInsertID();
        }

        $faqs = [
            ['registration', 'Как зарегистрироваться?', 'Заполните форму регистрации, подтвердите email, загрузите водительское удостоверение и паспорт. Верификация занимает до 24 часов.'],
            ['registration', 'Какие документы нужны?', 'Водительское удостоверение (с двух сторон), главная страница паспорта и страница с пропиской. Стаж — от 2 лет, возраст — от 21 года.'],
            ['registration', 'Сколько длится верификация?', 'Обычно от 15 минут до 2 часов в рабочее время. Максимум — 24 часа.'],
            ['booking', 'Как забронировать автомобиль?', 'Откройте каталог или карту, выберите авто, нажмите «Забронировать», укажите длительность и подтвердите. Депозит будет заморожен на балансе.'],
            ['booking', 'Что делать в конце поездки?', 'Припаркуйте авто в зоне обслуживания, заглушите двигатель, закройте окна и нажмите «Завершить аренду» в личном кабинете.'],
            ['booking', 'Можно ли продлить аренду?', 'Да, прямо во время поездки на странице активной аренды. Списание идёт по тарифу.'],
            ['payment', 'Как пополнить баланс?', 'В разделе «Баланс» нажмите «Пополнить», укажите сумму и выберите способ оплаты (карта/СБП).'],
            ['payment', 'Что такое заблокированные средства?', 'Это депозит, удерживаемый во время аренды. После завершения поездки депозит возвращается на баланс.'],
            ['payment', 'Возвращается ли депозит?', 'Да, после завершения поездки депозит автоматически разблокируется на балансе.'],
            ['cars', 'Что делать, если мало топлива?', 'Заправьтесь на любой АЗС — чек загрузите в раздел «Поддержка», стоимость будет компенсирована.'],
            ['cars', 'Авто не заводится. Что делать?', 'Сообщите в поддержку через приложение. Мы пришлём другой автомобиль или возместим простой.'],
            ['fines', 'Кто оплачивает штрафы ГИБДД?', 'Штрафы, полученные во время вашей аренды, выставляются вам. Уведомление приходит в личный кабинет.'],
            ['fines', 'Что делать при ДТП?', 'Включите аварийку, выставите знак, вызовите ГИБДД и сообщите в нашу поддержку. Все действия описаны в правилах.'],
        ];

        foreach ($faqs as $sort => [$catSlug, $q, $a]) {
            $this->insert('{{%faq}}', [
                'category_id' => $faqCatIds[$catSlug] ?? null,
                'question' => $q,
                'answer' => $a,
                'sort_order' => $sort + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ===== Статичные страницы =====
        $pages = [
            ['terms', 'Правила и условия пользования сервисом', "<h2>Общие положения</h2><p>Настоящие правила регулируют отношения между сервисом «Вольный Ход» и пользователями. Регистрируясь в сервисе, вы соглашаетесь с правилами в полном объёме.</p><h2>Требования к водителю</h2><ul><li>Возраст — от 21 года</li><li>Стаж вождения — от 2 лет</li><li>Действующее водительское удостоверение категории B</li></ul><h2>Аренда автомобиля</h2><p>Тарификация поминутная. Депозит замораживается на время аренды и возвращается после завершения. Запрещено передавать управление третьим лицам.</p><h2>Ответственность</h2><p>Пользователь несёт полную материальную ответственность за повреждения автомобиля во время аренды.</p>"],
            ['privacy', 'Политика конфиденциальности', "<h2>Сбор данных</h2><p>Мы собираем минимум данных, необходимых для оказания услуг: контактные данные, документы для верификации, данные о поездках.</p><h2>Использование</h2><p>Данные используются исключительно для предоставления сервиса и не передаются третьим лицам без согласия пользователя, за исключением случаев, предусмотренных законом.</p><h2>Защита</h2><p>Все персональные данные хранятся в зашифрованном виде на защищённых серверах в РФ.</p>"],
            ['tariffs-info', 'Тарифы и стоимость', '<p>Подробное описание тарифов смотрите в каталоге автомобилей. Стоимость рассчитывается по формуле: время + пробег. Депозит замораживается на время аренды.</p>'],
            ['contacts', 'Контакты', '<p>Поддержка: +7 (800) 555-35-35</p><p>Email: support@volnyhod.ru</p><p>Москва, ул. Тверская, 1</p>'],
        ];
        foreach ($pages as [$slug, $title, $content]) {
            $this->insert('{{%page}}', [
                'slug' => $slug,
                'title' => $title,
                'content' => $content,
                'is_active' => 1,
                'updated_at' => $now,
            ]);
        }

        // ===== Настройки =====
        $settings = [
            ['site_name', 'Вольный Ход', 'general', 'string', 'Название сервиса', '', 1],
            ['site_email', 'admin@volnyhod.ru', 'general', 'string', 'Email для уведомлений', '', 2],
            ['support_phone', '+7 (800) 555-35-35', 'general', 'string', 'Телефон поддержки', '', 3],
            ['support_email', 'support@volnyhod.ru', 'general', 'string', 'Email поддержки', '', 4],

            ['yookassa_shop_id', '', 'payment', 'string', 'YooKassa Shop ID', 'Идентификатор магазина', 1],
            ['yookassa_secret_key', '', 'payment', 'string', 'YooKassa Secret Key', 'Секретный ключ', 2],
            ['robokassa_login', '', 'payment', 'string', 'Robokassa Login', '', 3],
            ['robokassa_password1', '', 'payment', 'string', 'Robokassa Password 1', '', 4],

            ['yandex_maps_api_key', '', 'maps', 'string', 'Яндекс.Карты API ключ', 'Ключ для подключения карт', 1],
            ['default_lat', '55.7558', 'maps', 'float', 'Центр карты по умолчанию (lat)', '', 2],
            ['default_lng', '37.6173', 'maps', 'float', 'Центр карты по умолчанию (lng)', '', 3],

            ['min_age', '21', 'business', 'int', 'Минимальный возраст водителя', '', 1],
            ['min_driving_experience', '2', 'business', 'int', 'Минимальный стаж (лет)', '', 2],
            ['default_deposit', '2000', 'business', 'float', 'Депозит по умолчанию', '', 3],
        ];
        foreach ($settings as [$k, $v, $g, $t, $l, $d, $s]) {
            $this->insert('{{%setting}}', [
                'key' => $k,
                'value' => $v,
                'group' => $g,
                'type' => $t,
                'label' => $l,
                'description' => $d,
                'sort_order' => $s,
            ]);
        }

        // ===== Email шаблоны =====
        $templates = [
            ['signup_welcome', 'email', 'Добро пожаловать в Вольный Ход!', "Здравствуйте, {name}!\n\nДобро пожаловать в сервис каршеринга «Вольный Ход». Подтвердите email и загрузите документы для начала поездок."],
            ['booking_started', 'email', 'Аренда началась', "Здравствуйте, {name}!\n\nАренда автомобиля {car} началась. Депозит {deposit}₽ заблокирован на балансе."],
            ['booking_completed', 'email', 'Аренда завершена', "Здравствуйте, {name}!\n\nАренда автомобиля {car} завершена. Стоимость поездки: {total}₽."],
            ['password_reset', 'email', 'Сброс пароля', "Здравствуйте!\n\nДля сброса пароля перейдите по ссылке: {url}"],
        ];
        foreach ($templates as [$code, $channel, $subj, $body]) {
            $this->insert('{{%email_template}}', [
                'code' => $code,
                'channel' => $channel,
                'subject' => $subj,
                'body' => $body,
                'is_active' => 1,
            ]);
        }

        // ===== Демо: уведомления и транзакции для test user =====
        $this->insert('{{%notification}}', [
            'user_id' => $userId,
            'type' => 'success',
            'title' => 'Аккаунт верифицирован',
            'message' => 'Документы успешно проверены. Можете арендовать автомобили.',
            'icon' => 'fa-check-circle',
            'is_read' => 0,
            'created_at' => $now,
        ]);
        $this->insert('{{%notification}}', [
            'user_id' => $userId,
            'type' => 'promo',
            'title' => 'Промокод WELCOME10',
            'message' => 'Используйте код WELCOME10 для скидки 10% на первую поездку.',
            'icon' => 'fa-gift',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);
        $this->insert('{{%notification}}', [
            'user_id' => $userId,
            'type' => 'info',
            'title' => 'Добро пожаловать!',
            'message' => 'Спасибо за регистрацию в Вольный Ход.',
            'icon' => 'fa-bell',
            'is_read' => 1,
            'read_at' => $now,
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        ]);

        // Транзакции для пользователя
        $this->insert('{{%transaction}}', [
            'user_id' => $userId,
            'type' => 'topup',
            'amount' => 1500.00,
            'balance_after' => 1500.00,
            'status' => 'completed',
            'payment_method' => 'card',
            'description' => 'Пополнение баланса',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
            'completed_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        ]);
        $this->insert('{{%transaction}}', [
            'user_id' => $userId,
            'type' => 'bonus',
            'amount' => 100.00,
            'balance_after' => 1600.00,
            'status' => 'completed',
            'payment_method' => 'bonus',
            'description' => 'Приветственный бонус',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
            'completed_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%email_template}}');
        $this->delete('{{%setting}}');
        $this->delete('{{%page}}');
        $this->delete('{{%faq}}');
        $this->delete('{{%faq_category}}');
        $this->delete('{{%notification}}');
        $this->delete('{{%transaction}}');
        $this->delete('{{%promo_code}}');
        $this->delete('{{%car}}');
        $this->delete('{{%tariff}}');
        $this->delete('{{%user}}');
    }
}
