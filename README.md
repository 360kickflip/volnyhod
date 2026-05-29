# Вольный Ход 🚗

> **Каршеринг нового поколения** — современный сайт-каршеринг на Yii2 basic 2.0 с Bootstrap 5 и MySQL.

## ✨ Что внутри

### Личный кабинет
- 🔐 Регистрация / вход / восстановление пароля
- 👤 Профиль: личные данные, документы (5 типов), безопасность
- 💳 Баланс с детализацией (доступный / общий / заморожен), форма пополнения, история транзакций
- 🚙 Каталог автомобилей с фильтрами (тариф, КПП, статус, поиск)
- 🗺 Интерактивная карта с маркерами авто (Leaflet, без API ключа)
- 🔑 Бронирование с расчётом стоимости в реальном времени и промокодами
- ⏱ Активная аренда: live-таймер, авто-обновление стоимости, локация на карте, кнопки «Завершить» и «Сообщить о проблеме»
- 📜 История поездок с фильтром по датам, детализацией, маршрутом A→B и печатью чека (PDF)
- ⭐ Отзывы и рейтинг автомобилей
- 🔔 Уведомления с группировкой по дням и счётчиком непрочитанных
- 💬 Поддержка: чат с прикреплением файлов
- ❓ FAQ с категориями и аккордеоном
- 📄 Статичные страницы (правила, политика, контакты)

### Админ-панель `/admin`
- 📊 Дашборд: доход (день/неделя/месяц), активные аренды, графики (Chart.js), топ авто, новые тикеты
- 🚗 Управление автомобилями: CRUD, загрузка фото, локация, статусы
- 👥 Пользователи: фильтры, верификация документов, корректировка баланса, блокировки
- 📅 Бронирования: фильтры, отмена с возвратом депозита, детализация
- 🏷 Тарифы: CRUD с drag-n-drop сортировкой
- 🎟 Промокоды: CRUD + AJAX-генератор кодов
- ⚠️ Повреждения: смена статуса, ввод стоимости ремонта (автоштраф к бронированию)
- 💁‍♀️ Поддержка: чат, назначение, смена статуса
- ⭐ Модерация отзывов
- 💰 Финансы: отчёты по дням / тарифам / авто, экспорт в CSV
- 📍 История локаций: трек на карте + таблица записей
- ⚙️ Настройки (общие, платежи, карты, бизнес, email/sms)
- 📚 CMS для FAQ и статичных страниц

---

## 🛠 Стек

- **Backend**: PHP 7.4+ / 8.x, Yii2 basic 2.0.55
- **Frontend**: Bootstrap 5.3 + custom CSS-design-system, Inter font, FontAwesome 6
- **Карты**: Leaflet (OpenStreetMap, без API-ключа). Можно подключить Яндекс.Карты в `params.php`
- **Графики**: Chart.js 4
- **Drag-n-drop**: SortableJS
- **БД**: MySQL 5.7+ / MariaDB 10.3+ (utf8mb4)

---

## 🚀 Установка

### 1. Клонирование и зависимости

```bash
git clone <ваш-репозиторий> volnyhod
cd volnyhod
composer install
```

### 2. База данных

#### Вариант А: через phpMyAdmin (импорт SQL-дампа)

1. Создайте БД `volnyhod` с кодировкой `utf8mb4_unicode_ci`
2. Откройте её, перейдите на вкладку **«Импорт»**
3. Выберите файл `sql/volnyhod_full.sql` (схема + демо-данные)
4. Нажмите **«Вперёд»** ✅

> Файлы по отдельности: `sql/volnyhod_schema.sql` (только структура) и `sql/volnyhod_seed.sql` (только данные).

#### Вариант Б: через миграции Yii2

1. Создайте БД `volnyhod`
2. Настройте подключение в `config/db.php`:
   ```php
   'dsn' => 'mysql:host=localhost;dbname=volnyhod',
   'username' => 'root',
   'password' => 'ваш_пароль',
   ```
3. Запустите миграции:
   ```bash
   php yii migrate
   ```

### 3. Настройка веб-сервера

Корнем сайта должна быть папка `web/`.

#### nginx (пример)
```nginx
server {
    listen 80;
    server_name volnyhod.local;
    root /path/to/volnyhod/web;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

#### Apache
В `web/.htaccess` уже есть правила (поставляются с Yii2 basic).

#### Простой запуск (для разработки)
```bash
php yii serve --port=8080
# открыть http://localhost:8080
```

### 4. Права на каталоги

```bash
chmod -R 0775 runtime web/assets web/uploads
```

---

## 🔐 Демо-аккаунты

| Логин                | Пароль       | Роль   | Описание                              |
|----------------------|--------------|--------|---------------------------------------|
| `admin@volnyhod.ru`  | `admin12345` | admin  | Полный доступ к админ-панели          |
| `user@volnyhod.ru`   | `user12345`  | user   | Верифицирован, баланс 1500 ₽          |
| `demo@volnyhod.ru`   | `demo12345`  | user   | На проверке документов                |

> Промокоды: `WELCOME10` (-10%), `VOLNY200` (-200₽), `SUMMER25` (-25%)

---

## 📁 Структура проекта

```
volnyhod/
├── assets/                   # AssetBundle
├── commands/                 # Console commands
├── components/
│   └── BookingService.php    # Бизнес-логика аренды (атомарные транзакции)
├── config/                   # web.php, console.php, params.php, db.php
├── controllers/              # Site, Profile, Balance, Car, Map, Booking, Trip, Notification, Support, Faq, Page
├── migrations/               # Миграции БД
├── models/
│   ├── *.php                 # ActiveRecord (User, Car, Booking, Tariff…)
│   └── forms/                # Form-моделей (LoginForm, SignupForm, BookingForm…)
├── modules/
│   └── admin/                # Админ-модуль с собственным layout
│       ├── Module.php
│       ├── controllers/
│       └── views/
├── runtime/
├── sql/                      # SQL-дампы для phpMyAdmin
│   ├── volnyhod_full.sql     # Схема + данные
│   ├── volnyhod_schema.sql   # Только структура
│   └── volnyhod_seed.sql     # Только данные
├── views/
│   ├── layouts/
│   │   ├── main.php          # Основной layout
│   │   └── auth.php          # Split-screen для login/signup
│   └── ... (по контроллерам)
├── web/
│   ├── css/site.css          # Кастомная design-system
│   ├── js/site.js            # Live-таймер, AJAX, рейтинг
│   ├── img/                  # logo.svg, car-placeholder.svg
│   ├── uploads/              # Загруженные файлы
│   └── index.php
└── README.md
```

---

## 🎨 Design system

В `web/css/site.css` определена дизайн-система через CSS-переменные:

```css
--vh-primary: #00c896;          /* Акцент (свобода движения) */
--vh-secondary: #0a1628;        /* Тёмный navy */
--vh-accent: #fbbf24;           /* Янтарь */
--vh-radius: 14px;
--vh-shadow-lg: 0 12px 32px rgba(...);
```

Готовые компоненты: `.car-card`, `.vh-stat`, `.vh-balance-pill`, `.notif`, `.chat-msg`, `.admin-sidebar`, `.auth-wrap`, `.empty`.

---

## 🔧 Конфигурация

### `config/params.php`
```php
'siteName'        => 'Вольный Ход',
'supportPhone'    => '+7 (800) 555-35-35',
'supportEmail'    => 'support@volnyhod.ru',
'defaultDeposit'  => 2000.00,
'defaultCenter'   => ['lat' => 55.7558, 'lng' => 37.6173],
'yandexMapsApiKey' => '',  // вставьте при необходимости
```

### Платежи
Демо-режим: пополнение баланса засчитывается мгновенно (см. `models/forms/TopUpForm::process()`).
Для интеграции с **ЮKassa** или **Robokassa** заполните настройки в админке `/admin/setting?group=payment` и расширьте `TopUpForm`.

### Карты
По умолчанию используется Leaflet + OpenStreetMap (без API-ключа). Чтобы переключиться на Яндекс.Карты — поставьте `yandexMapsApiKey` в настройках и поправьте JS в `views/map/index.php`.

---

## 🧪 Что проверить после установки

1. ✅ Открыть `/` — должна показаться главная с тарифами и доступными авто
2. ✅ Войти как `user@volnyhod.ru` — увидеть аватар, баланс, уведомления в шапке
3. ✅ `/catalog` — фильтры работают, карточки авто отображаются
4. ✅ `/map` — Leaflet-карта с маркерами по тарифам
5. ✅ Открыть карточку авто → «Забронировать» → модалка с расчётом и промокодом → создаётся аренда
6. ✅ `/booking/active` — таймер тикает, стоимость обновляется
7. ✅ Завершить аренду → попадаешь в детали поездки → можно скачать чек
8. ✅ `/admin` (admin@) — Dashboard с графиками
9. ✅ `/admin/car` — CRUD, загрузка фото
10. ✅ `/admin/finance` — графики и экспорт CSV

---

## 📝 Лицензия

BSD-3-Clause (как у Yii2). Кастомный код — MIT.

---

## 🙌 Авторы

Сделано с ❤️ для свободы передвижения.
