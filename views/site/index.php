<?php
/** @var yii\web\View $this */
/** @var app\models\Car[] $popularCars */
/** @var app\models\Tariff[] $tariffs */
/** @var app\models\Review[] $reviews */

use app\components\ReferralBootstrap;
use app\models\Setting;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Вольный Ход — Каршеринг нового поколения';

// Если пришёл по реф-ссылке — показываем промо-баннер
$refCode = ReferralBootstrap::getCookieCode();
$refUser = $refCode && Yii::$app->user->isGuest ? User::findByReferralCode($refCode) : null;
$bonusReferred = (float)Setting::get('referral_bonus_referred', 300);
?>

<?php if ($refUser): ?>
    <div class="vh-ref-banner mb-3 fade-up">
        <i class="fa-solid fa-gift"></i>
        <div class="flex-grow-1">
            <div class="fw-bold">Вас пригласил <?= Html::encode($refUser->name ?: 'друг') ?> 🎉</div>
            <div class="small" style="opacity:.9;">Зарегистрируйтесь и получите <strong><?= Yii::$app->formatter->asCurrency($bonusReferred) ?></strong> на баланс после первой поездки</div>
        </div>
        <a href="<?= Url::to(['/site/signup']) ?>" class="btn btn-light btn-sm">Регистрация</a>
    </div>
<?php endif ?>

<!-- HERO -->
<section class="vh-hero mt-3">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge badge-soft-primary mb-3" style="background: rgba(255,255,255,.12); color:#fff;">
                <i class="fa-solid fa-bolt me-1"></i> Поминутная аренда автомобилей
            </span>
            <h1 class="mb-3">Свобода передвижения<br><span class="grad-text" style="-webkit-text-fill-color: transparent;">в каждом километре</span></h1>
            <p class="lead mb-4" style="color: rgba(255,255,255,.85)">
                Сотни автомобилей по всему городу. Открывайте машину телефоном, плати поминутно, никаких скрытых платежей.
            </p>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Url::to(['site/signup']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-rocket me-2"></i>Начать пользоваться</a>
                    <a href="<?= Url::to(['car/index']) ?>" class="btn btn-outline-light btn-lg" style="border-color: rgba(255,255,255,.3); color:#fff;">Каталог автомобилей</a>
                <?php else: ?>
                    <a href="<?= Url::to(['car/index']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-magnifying-glass me-2"></i>Найти автомобиль</a>
                    <a href="<?= Url::to(['map/index']) ?>" class="btn btn-outline-light btn-lg" style="border-color: rgba(255,255,255,.3); color:#fff;"><i class="fa-solid fa-map-location-dot me-2"></i>На карте</a>
                <?php endif ?>
            </div>
            <!-- Mini-trust под CTA -->
            <div class="d-flex flex-wrap gap-4 small" style="color: rgba(255,255,255,.7)">
                <span><i class="fa-solid fa-shield-halved text-success me-1"></i> Все авто застрахованы</span>
                <span><i class="fa-solid fa-clock text-success me-1"></i> Поддержка 24/7</span>
                <span><i class="fa-solid fa-star text-warning me-1"></i> 4.8 / 5 у пользователей</span>
            </div>
        </div>
        <div class="col-lg-5 d-none d-lg-block text-center">
            <i class="fa-solid fa-car-side" style="font-size: 14rem; color: rgba(255,255,255,.1)"></i>
        </div>
    </div>
</section>

<!-- BIG NUMBERS — социальные доказательства -->
<section class="my-5">
    <div class="row g-3 text-center">
        <?php
        $numbers = [
            ['value' => '500+', 'label' => 'автомобилей на линии', 'icon' => 'fa-car-side', 'color' => '#00c896'],
            ['value' => '50K', 'label' => 'довольных клиентов', 'icon' => 'fa-users', 'color' => '#0d6efd'],
            ['value' => '5', 'label' => 'городов присутствия', 'icon' => 'fa-map-location-dot', 'color' => '#f59e0b'],
            ['value' => '24/7', 'label' => 'поддержка на связи', 'icon' => 'fa-headset', 'color' => '#ef4444'],
        ];
        foreach ($numbers as $n): ?>
            <div class="col-md-3 col-6">
                <div class="vh-stat h-100 align-items-center text-center">
                    <div class="vh-stat__icon mx-auto" style="background: <?= $n['color'] ?>20; color: <?= $n['color'] ?>;">
                        <i class="fa-solid <?= $n['icon'] ?>"></i>
                    </div>
                    <div class="vh-stat__value mx-auto" style="font-size:2.25rem;"><?= $n['value'] ?></div>
                    <div class="vh-stat__label" style="text-align:center;"><?= $n['label'] ?></div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>

<!-- ПОЧЕМУ ВЫБИРАЮТ НАС -->
<section class="my-5">
    <div class="text-center mb-4">
        <span class="badge badge-soft-primary mb-2"><i class="fa-solid fa-heart me-1"></i> Почему мы</span>
        <h2 class="section-title m-0 mb-2">Почему выбирают нас?</h2>
        <p class="text-soft lead-thin mb-0">Шесть причин, по которым 50 000 водителей доверяют нам свои поездки</p>
    </div>
    <div class="row g-4">
        <?php
        $why = [
            ['fa-clock',           '#00c896', 'Поминутная аренда',        'Платите только за фактическое время поездки. Минимум 15 минут — никаких суточных переплат.'],
            ['fa-shield-halved',   '#0d6efd', 'Полная страховка',         'ОСАГО + КАСКО включены в стоимость. В случае ДТП мы решаем все вопросы со страховой.'],
            ['fa-mobile-screen',   '#6366f1', 'Открытие телефоном',       'Бронирование, открытие двери и завершение аренды — в одно касание через приложение.'],
            ['fa-coins',           '#f59e0b', 'Прозрачные тарифы',        'Никаких скрытых платежей. Стоимость поездки видна заранее, чек приходит на email.'],
            ['fa-headset',         '#ef4444', 'Поддержка 24/7',           'Реальные люди отвечают в течение минуты — звонком или в чате. Поможем в любой ситуации.'],
            ['fa-leaf',            '#10b981', 'Свежие автомобили',        'Парк обновляется каждые 2 года. Все авто проходят регулярное ТО, моются и заправляются.'],
        ];
        foreach ($why as [$icon, $color, $title, $text]): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;border-radius:16px;background: <?= $color ?>15; color: <?= $color ?>;">
                            <i class="fa-solid <?= $icon ?> fa-xl"></i>
                        </div>
                        <h5 class="mb-2"><?= $title ?></h5>
                        <p class="text-soft mb-0"><?= $text ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>

<!-- КАК ЭТО РАБОТАЕТ -->
<section class="my-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-5">
            <span class="badge badge-soft-primary mb-2"><i class="fa-solid fa-list-check me-1"></i> Как это работает</span>
            <h2 class="section-title m-0 mb-3">От регистрации до поездки —<br><span class="grad-text">за 10 минут</span></h2>
            <p class="text-soft mb-4">Никаких очередей, договоров на бумаге и ожиданий. Всё происходит онлайн.</p>
            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Url::to(['site/signup']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-arrow-right me-2"></i>Зарегистрироваться</a>
            <?php else: ?>
                <a href="<?= Url::to(['car/index']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-arrow-right me-2"></i>Выбрать авто</a>
            <?php endif ?>
        </div>
        <div class="col-lg-7">
            <div class="vh-steps">
                <?php
                $steps = [
                    ['1', 'Регистрация', 'Заполните форму, подтвердите email и загрузите документы. Верификация — до 2 часов.'],
                    ['2', 'Выбор авто',   'Найдите ближайший автомобиль на карте или в каталоге. Видны цена, топливо и расположение.'],
                    ['3', 'Бронирование', 'Жмите «Забронировать», подтвердите условия. Депозит замораживается на балансе.'],
                    ['4', 'Поездка',      'Открывайте авто и поезжайте. По окончании — припаркуйтесь и нажмите «Завершить».'],
                ];
                foreach ($steps as $s): ?>
                    <div class="vh-step">
                        <div class="vh-step__num"><?= $s[0] ?></div>
                        <div class="vh-step__body">
                            <h6 class="m-0 mb-1"><?= $s[1] ?></h6>
                            <p class="m-0 small text-soft"><?= $s[2] ?></p>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</section>

<!-- ТАРИФЫ -->
<section class="my-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="section-title m-0">Тарифы</h2>
        <a href="<?= Url::to(['/tariff/index']) ?>" class="text-decoration-none small">Подробнее →</a>
    </div>
    <div class="row g-3">
        <?php foreach ($tariffs as $tariff): ?>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;border-radius:14px;background: <?= Html::encode($tariff->color) ?>20; color: <?= Html::encode($tariff->color) ?>;">
                        <i class="fa-solid <?= Html::encode($tariff->icon ?: 'fa-car') ?> fa-lg"></i>
                    </div>
                    <h5 class="card-title"><?= Html::encode($tariff->name) ?></h5>
                    <p class="text-soft small mb-3"><?= Html::encode($tariff->description) ?></p>
                    <div class="d-flex align-items-baseline gap-1 mb-1">
                        <span class="h3 fw-bold m-0"><?= Yii::$app->formatter->asDecimal($tariff->price_per_minute, 2) ?></span>
                        <span class="text-muted">₽/мин</span>
                    </div>
                    <div class="text-muted small">+ <?= Yii::$app->formatter->asDecimal($tariff->price_per_km, 2) ?> ₽/км · депозит <?= Yii::$app->formatter->asCurrency($tariff->deposit) ?></div>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</section>

<?php if ($popularCars): ?>
<section class="my-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="section-title m-0">Доступные сейчас</h2>
        <a href="<?= Url::to(['car/index']) ?>" class="text-decoration-none small">Все автомобили →</a>
    </div>
    <div class="row g-3">
        <?php foreach ($popularCars as $car): ?>
        <div class="col-md-6 col-lg-4">
            <a href="<?= Url::to(['car/view', 'id' => $car->id]) ?>" class="text-decoration-none text-reset">
                <article class="car-card">
                    <div class="car-card__media">
                        <img src="<?= $car->getMainPhotoUrl() ?>" alt="<?= Html::encode($car->getFullName()) ?>">
                        <span class="car-card__status"><span class="badge bg-success">Доступен</span></span>
                        <span class="car-card__tariff"><?= Html::encode($car->tariff->name ?? '') ?></span>
                    </div>
                    <div class="car-card__body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="car-card__title"><?= Html::encode($car->getFullName()) ?></h6>
                                <div class="car-card__plate"><?= Html::encode($car->license_plate) ?></div>
                            </div>
                        </div>
                        <div class="car-card__features">
                            <span><i class="fa-solid fa-gear"></i> <?= $car->transmission === 'auto' ? 'АКПП' : 'МКПП' ?></span>
                            <span><i class="fa-solid fa-users"></i> <?= $car->seats ?></span>
                            <span><i class="fa-solid fa-gas-pump"></i> <?= $car->fuel_level ?>%</span>
                        </div>
                        <div class="car-card__price">
                            <span class="num"><?= Yii::$app->formatter->asDecimal($car->tariff->price_per_minute ?? 0, 2) ?></span>
                            <span class="unit">₽/мин</span>
                        </div>
                    </div>
                </article>
            </a>
        </div>
        <?php endforeach ?>
    </div>
</section>
<?php endif ?>

<!-- ОТЗЫВЫ -->
<section class="my-5">
    <div class="text-center mb-4">
        <span class="badge badge-soft-primary mb-2"><i class="fa-solid fa-quote-left me-1"></i> Отзывы</span>
        <h2 class="section-title m-0">Что говорят клиенты</h2>
    </div>
    <div class="row g-3">
        <?php
        // Если нет настоящих отзывов в БД — показываем дефолтные
        $defaultReviews = [
            ['Анна К.',     'Менеджер',         5, 'Пользуюсь полгода — это просто космос! Машины всегда чистые, поддержка отзывчивая. Особенно радует поминутная оплата: за 20 минут до метро вышло 200 ₽.', 'clients/anna.jpg'],
            ['Дмитрий П.',  'IT-специалист',    5, 'Регистрация заняла буквально пару часов. С тех пор езжу только так. Цены прозрачные, никаких сюрпризов в чеке. Рекомендую всем!',                            'clients/dmitry.jpg'],
            ['Мария С.',    'Студентка',        5, 'Удобно ездить из института домой, особенно поздно вечером. Дешевле такси, машины качественные, ничего не ломается. Спасибо!',                                'clients/maria.jpg'],
        ];
        $displayReviews = $reviews ?: $defaultReviews;
        $useReal = !empty($reviews);
        foreach ($displayReviews as $i => $r):
            if ($useReal) {
                $name = $r->user->name ?? 'Пользователь';
                $role = 'Клиент';
                $rating = $r->rating;
                $text = $r->text;
                $photo = null;
            } else {
                [$name, $role, $rating, $text, $photo] = $r;
            }
            ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="rating mb-3">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                <i class="fa-solid fa-star <?= $s <= $rating ? '' : 'text-muted' ?>"></i>
                            <?php endfor ?>
                        </div>
                        <p class="mb-4"><?= Html::encode($text) ?></p>
                        <div class="d-flex align-items-center gap-2 mt-auto">
                            <?php if ($photo): ?>
                                <img src="<?= Yii::getAlias('@web/img/' . $photo) ?>"
                                     onerror="this.src='<?= Yii::getAlias('@web/img/clients/placeholder.svg') ?>';"
                                     alt="" style="width:42px;height:42px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                            <?php else: ?>
                                <div class="vh-avatar" style="width:42px;height:42px;font-size:14px;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            <?php endif ?>
                            <div>
                                <div class="fw-bold small"><?= Html::encode($name) ?></div>
                                <div class="text-soft" style="font-size:.8rem;"><?= Html::encode($role) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="text-center mt-4">
        <a href="<?= Url::to(['/page/view', 'slug' => 'terms']) ?>#liability" class="text-decoration-none small">Все отзывы и оценки →</a>
    </div>
</section>

<!-- БЕЗОПАСНОСТЬ И ДОВЕРИЕ -->
<section class="my-5">
    <div class="card border-0" style="background: linear-gradient(135deg, #0a1628, #102540); color: #fff;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge mb-3" style="background: rgba(255,255,255,.15); color:#fff;">
                        <i class="fa-solid fa-shield-halved me-1"></i> Безопасность
                    </span>
                    <h2 style="color:#fff;" class="mb-3">Ваша безопасность — наш приоритет</h2>
                    <p style="color: rgba(255,255,255,.8)" class="lead-thin mb-4">
                        Мы работаем по всем стандартам безопасности и соблюдаем требования законодательства РФ.
                    </p>
                    <div class="row g-3">
                        <?php
                        $safety = [
                            ['fa-shield-halved', 'ОСАГО + КАСКО',         'Все авто застрахованы. ДТП — без хлопот.'],
                            ['fa-lock',           'Шифрование данных',     '152-ФЗ. Серверы в России.'],
                            ['fa-credit-card',    'Безопасные платежи',    'PCI DSS. Не храним номера карт.'],
                            ['fa-camera',         'Контроль состояния',    'Каждое авто проходит ТО раз в 2 недели.'],
                        ];
                        foreach ($safety as [$icon, $title, $text]): ?>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="d-inline-flex align-items-center justify-content-center" style="width:42px;height:42px;border-radius:12px;background:rgba(0,200,150,.2);color:#00c896;flex-shrink:0;">
                                        <i class="fa-solid <?= $icon ?>"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold mb-1" style="color:#fff;"><?= $title ?></div>
                                        <div class="small" style="color: rgba(255,255,255,.65)"><?= $text ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <i class="fa-solid fa-shield-halved" style="font-size: 14rem; color: rgba(0,200,150,.18)"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ПАРТНЁРЫ И НАГРАДЫ -->
<section class="my-5">
    <div class="text-center mb-4">
        <span class="text-uppercase small text-soft" style="letter-spacing:.1em; font-weight:600;">Нам доверяют</span>
        <h3 class="mt-2 mb-0">Партнёры и награды</h3>
    </div>
    <div class="row g-3 align-items-center justify-content-center">
        <?php
        // Замените на свои логотипы партнёров (web/img/partners/*.png)
        $partners = [
            ['Партнёр-Банк',      'partners/sber.svg'],
            ['Финанс-Альянс',     'partners/alfa.svg'],
            ['Платёжный сервис',  'partners/yookassa.svg'],
            ['Карты & API',       'partners/yandex.svg'],
            ['Банк-Партнёр',      'partners/vtb.svg'],
            ['Страх-Группа',      'partners/osago.svg'],
        ];
        foreach ($partners as [$name, $img]): ?>
            <div class="col-md-2 col-4 text-center">
                <div class="vh-partner">
                    <img src="<?= Yii::getAlias('@web/img/' . $img) ?>" alt="<?= Html::encode($name) ?>"
                         onerror="this.src='<?= Yii::getAlias('@web/img/partners/placeholder.svg') ?>';"
                         style="max-width:120px; max-height:48px; opacity:.6; filter: grayscale(100%); transition:all .25s;">
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <p class="text-center text-soft small mt-3 mb-0">Замените логотипы в <code>web/img/partners/</code></p>
</section>

<!-- FAQ -->
<section class="my-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-5">
            <span class="badge badge-soft-primary mb-2"><i class="fa-solid fa-circle-question me-1"></i> FAQ</span>
            <h2 class="section-title m-0 mb-3">Частые вопросы</h2>
            <p class="text-soft mb-4">Не нашли ответ? Загляните в полный <a href="<?= Url::to(['/faq/index']) ?>">список FAQ</a> или напишите нам.</p>
            <a href="<?= Url::to(['/faq/index']) ?>" class="btn btn-soft">Все вопросы и ответы →</a>
        </div>
        <div class="col-lg-7">
            <div class="accordion" id="homeFaq">
                <?php
                $faqs = [
                    ['С какого возраста можно пользоваться?', 'С 21 года при наличии стажа от 2 лет и действующего водительского удостоверения категории B.'],
                    ['Что входит в стоимость поездки?', 'Поминутная оплата + километраж по тарифу. Топливо, страховка ОСАГО+КАСКО, парковка в зоне обслуживания — включены.'],
                    ['Как быстро возвращается депозит?', 'Депозит автоматически разблокируется на балансе сразу после завершения поездки.'],
                    ['Что делать при ДТП?', 'Включить аварийку, вызвать ГИБДД (102) и сразу же позвонить нам. Мы возьмём все вопросы на себя.'],
                ];
                foreach ($faqs as $i => [$q, $a]): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $i ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#hf-<?= $i ?>">
                                <?= $q ?>
                            </button>
                        </h2>
                        <div id="hf-<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#homeFaq">
                            <div class="accordion-body"><?= $a ?></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="my-5 p-4 p-md-5 rounded-2xl text-center" style="background: linear-gradient(135deg, var(--vh-primary-light), #fff);">
    <h2 class="mb-3">Готовы попробовать?</h2>
    <p class="lead text-soft mb-4">Регистрация занимает 2 минуты. Загрузите документы и поезжайте.</p>
    <?php if (Yii::$app->user->isGuest): ?>
        <a href="<?= Url::to(['site/signup']) ?>" class="btn btn-primary btn-lg me-2"><i class="fa-solid fa-key me-2"></i>Зарегистрироваться</a>
        <a href="<?= Url::to(['site/about']) ?>" class="btn btn-soft btn-lg">О компании</a>
    <?php else: ?>
        <a href="<?= Url::to(['car/index']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-car me-2"></i>Выбрать автомобиль</a>
    <?php endif ?>
</section>
