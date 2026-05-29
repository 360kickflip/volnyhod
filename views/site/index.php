<?php
/** @var yii\web\View $this */
/** @var app\models\Car[] $popularCars */
/** @var app\models\Tariff[] $tariffs */
/** @var app\models\Review[] $reviews */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Вольный Ход — Каршеринг нового поколения';
?>

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
            <div class="d-flex flex-wrap gap-2">
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Url::to(['site/signup']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-rocket me-2"></i>Начать пользоваться</a>
                    <a href="<?= Url::to(['car/index']) ?>" class="btn btn-outline-light btn-lg" style="border-color: rgba(255,255,255,.3); color:#fff;">Каталог автомобилей</a>
                <?php else: ?>
                    <a href="<?= Url::to(['car/index']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-magnifying-glass me-2"></i>Найти автомобиль</a>
                    <a href="<?= Url::to(['map/index']) ?>" class="btn btn-outline-light btn-lg" style="border-color: rgba(255,255,255,.3); color:#fff;"><i class="fa-solid fa-map-location-dot me-2"></i>На карте</a>
                <?php endif ?>
            </div>
        </div>
        <div class="col-lg-5 d-none d-lg-block text-center">
            <i class="fa-solid fa-car-side" style="font-size: 14rem; color: rgba(255,255,255,.1)"></i>
        </div>
    </div>
</section>

<section class="my-5">
    <div class="row g-3">
        <?php
        $features = [
            ['fa-clock', 'Без брони', 'Открыли — поехали. Никакого ожидания.'],
            ['fa-coins', 'Прозрачно', 'Поминутная тарификация. Депозит возвращается.'],
            ['fa-shield-halved', 'Безопасно', 'Все авто застрахованы и регулярно обслуживаются.'],
            ['fa-mobile-screen', 'В одно касание', 'Бронирование, открытие и оплата — в личном кабинете.'],
        ];
        foreach ($features as [$icon, $title, $text]): ?>
        <div class="col-md-6 col-lg-3">
            <div class="vh-stat h-100">
                <div class="vh-stat__icon"><i class="fa-solid <?= $icon ?>"></i></div>
                <h5 class="mb-1"><?= $title ?></h5>
                <div class="text-soft small"><?= $text ?></div>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</section>

<section class="my-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="section-title m-0">Тарифы</h2>
        <a href="<?= Url::to(['/page/view', 'slug' => 'tariffs-info']) ?>" class="text-decoration-none small">Подробнее →</a>
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

<section class="my-5 p-4 p-md-5 rounded-2xl text-center" style="background: linear-gradient(135deg, var(--vh-primary-light), #fff);">
    <h2 class="mb-3">Готовы попробовать?</h2>
    <p class="lead text-soft mb-4">Регистрация занимает 2 минуты. Загрузите документы и поезжайте.</p>
    <?php if (Yii::$app->user->isGuest): ?>
        <a href="<?= Url::to(['site/signup']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-key me-2"></i>Зарегистрироваться</a>
    <?php else: ?>
        <a href="<?= Url::to(['car/index']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-car me-2"></i>Выбрать автомобиль</a>
    <?php endif ?>
</section>
