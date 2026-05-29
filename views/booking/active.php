<?php
/** @var yii\web\View $this */
/** @var app\models\Booking $booking */
/** @var app\models\forms\DamageReportForm $damageForm */

use app\models\DamageReport;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Активная аренда';
$car = $booking->car;
$startedTs = strtotime($booking->started_at);
$currentCost = $booking->calculateCurrentCost();

// Leaflet для карты локации авто
$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="m-0">Активная аренда</h1>
        <div class="text-soft small">№ <?= $booking->number ?> · начата <?= Yii::$app->formatter->asDatetime($booking->started_at) ?></div>
    </div>
    <span class="badge bg-primary fs-6"><i class="fa-solid fa-circle-play me-1"></i> В процессе</span>
</div>

<div class="row g-4">
    <!-- Левая часть: авто + статистика -->
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="row g-0">
                <div class="col-md-5">
                    <img src="<?= $car->getMainPhotoUrl() ?>" class="img-fluid h-100 w-100" style="object-fit: cover; border-radius: var(--vh-radius) 0 0 var(--vh-radius); min-height: 220px;" alt="">
                </div>
                <div class="col-md-7">
                    <div class="card-body p-4">
                        <h3 class="mb-1"><?= Html::encode($car->getFullName()) ?></h3>
                        <div class="text-soft mb-3"><?= Html::encode($car->license_plate) ?> · <?= Html::encode($car->color) ?></div>

                        <div class="row g-2">
                            <div class="col-6">
                                <div class="car-stat">
                                    <div class="car-stat__icon"><i class="fa-solid fa-gas-pump"></i></div>
                                    <div>
                                        <div class="car-stat__label">Топливо</div>
                                        <div class="car-stat__value"><?= $car->fuel_level ?>%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="car-stat">
                                    <div class="car-stat__icon"><i class="fa-solid fa-route"></i></div>
                                    <div>
                                        <div class="car-stat__label">Пробег</div>
                                        <div class="car-stat__value"><?= number_format($car->mileage, 0, '.', ' ') ?> км</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="<?= Url::to(['/car/view', 'id' => $car->id]) ?>" class="text-decoration-none small mt-3 d-inline-block">Детали авто →</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between gap-3">
                    <div>
                        <div class="text-soft small">Длительность</div>
                        <div class="rental-timer mt-1" data-rental-timer data-started-at="<?= $startedTs ?>">00:00:00</div>
                        <div class="text-soft small mt-1">с <?= Yii::$app->formatter->asTime($booking->started_at) ?></div>
                    </div>
                    <div>
                        <div class="text-soft small">Текущая стоимость</div>
                        <div class="rental-cost mt-1" data-rental-cost data-url="<?= Url::to(['/booking/current-cost', 'id' => $booking->id]) ?>"><?= Yii::$app->formatter->asCurrency($currentCost) ?></div>
                        <div class="text-soft small mt-1">по тарифу «<?= Html::encode($booking->tariff->name) ?>»</div>
                    </div>
                    <div>
                        <div class="text-soft small">Депозит</div>
                        <div class="h4 m-0 mt-1"><?= Yii::$app->formatter->asCurrency($booking->deposit) ?></div>
                        <div class="text-soft small mt-1">заморожен</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Локация на карте -->
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-location-dot me-2 text-primary"></i>Локация автомобиля</div>
            <div class="card-body p-0">
                <div id="rental-map" style="height: 320px;"></div>
                <div class="p-3 text-soft small"><?= Html::encode($car->address) ?></div>
            </div>
        </div>
    </div>

    <!-- Правая часть: действия -->
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="fa-solid fa-flag-checkered me-2 text-primary"></i>Завершение аренды</h5>
                <p class="text-soft small">Припаркуйте авто в зоне обслуживания, заглушите двигатель, закройте окна и нажмите кнопку ниже.</p>
                <?= Html::beginForm(['/booking/finish', 'id' => $booking->id]) ?>
                    <button type="submit" class="btn btn-primary w-100 btn-lg" onclick="return confirm('Вы уверены, что хотите завершить аренду?')">
                        <i class="fa-solid fa-stop me-2"></i>Завершить аренду
                    </button>
                <?= Html::endForm() ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <button class="btn btn-soft w-100" data-bs-toggle="modal" data-bs-target="#damageModal">
                    <i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>Сообщить о проблеме
                </button>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h6 class="mb-3">Поддержка</h6>
                <a href="<?= Url::to(['/support/create']) ?>" class="btn btn-soft w-100 mb-2"><i class="fa-solid fa-life-ring me-2"></i>Создать обращение</a>
                <a href="tel:<?= preg_replace('/[^+\d]/', '', Yii::$app->params['supportPhone']) ?>" class="btn btn-soft w-100"><i class="fa-solid fa-phone me-2"></i>Позвонить</a>
            </div>
        </div>

        <div class="alert alert-info small mb-0">
            <i class="fa-solid fa-circle-info me-1"></i> Стоимость обновляется автоматически каждые 30 секунд. Таймер длительности — каждую секунду.
        </div>
    </div>
</div>

<?= $this->render('_damage_modal', ['booking' => $booking, 'damageForm' => $damageForm]) ?>

<?php
$lat = (float)$car->lat;
$lng = (float)$car->lng;
if ($lat && $lng):
    $this->registerJs("
        const rmap = L.map('rental-map').setView([$lat, $lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OpenStreetMap'}).addTo(rmap);
        L.marker([$lat, $lng]).addTo(rmap).bindPopup('" . addslashes(Html::encode($car->address)) . "').openPopup();
    ");
endif;
?>
