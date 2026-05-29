<?php
/** @var yii\web\View $this */
/** @var app\models\Booking $booking */
/** @var app\models\forms\ReviewForm|null $reviewForm */

use app\models\Booking;
use app\models\BookingCharge;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Поездка #' . $booking->number;
$car = $booking->car;

// Карта маршрута
if ($booking->start_lat && $booking->end_lat) {
    $this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
    $this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);
}
?>

<nav class="mb-3">
    <a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft"><i class="fa-solid fa-arrow-left me-1"></i> Все поездки</a>
</nav>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
    <div>
        <h1 class="m-0">Поездка #<?= $booking->number ?></h1>
        <div class="text-soft mt-1">
            <span class="badge bg-<?= Booking::statusBadge($booking->status) ?>"><?= Booking::statusLabel($booking->status) ?></span>
            <span class="ms-2"><?= Yii::$app->formatter->asDatetime($booking->started_at ?: $booking->created_at) ?></span>
        </div>
    </div>
    <?php if ($booking->status === Booking::STATUS_COMPLETED): ?>
        <a href="<?= Url::to(['receipt', 'id' => $booking->id]) ?>" target="_blank" class="btn btn-soft"><i class="fa-solid fa-file-pdf me-1"></i> Скачать чек</a>
    <?php endif ?>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Авто -->
        <div class="card mb-3">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="<?= $car->getMainPhotoUrl() ?>" class="img-fluid h-100 w-100" style="object-fit: cover; border-radius: var(--vh-radius) 0 0 var(--vh-radius); min-height: 180px;" alt="">
                </div>
                <div class="col-md-8">
                    <div class="card-body p-4">
                        <h4 class="m-0"><?= Html::encode($car->getFullName()) ?></h4>
                        <div class="text-soft mb-3"><?= Html::encode($car->license_plate) ?> · <?= Html::encode($car->color) ?> · <?= $car->year ?></div>
                        <div class="row g-2 small">
                            <div class="col-6"><i class="fa-solid fa-gear text-muted me-1"></i> <?= $car->transmission === 'auto' ? 'АКПП' : 'МКПП' ?></div>
                            <div class="col-6"><i class="fa-solid fa-users text-muted me-1"></i> <?= $car->seats ?> мест</div>
                            <div class="col-6"><i class="fa-solid fa-tags text-muted me-1"></i> <?= Html::encode($booking->tariff->name) ?></div>
                            <div class="col-6"><i class="fa-solid fa-coins text-muted me-1"></i> <?= Yii::$app->formatter->asDecimal($booking->tariff->price_per_minute, 2) ?> ₽/мин</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Маршрут -->
        <div class="card mb-3">
            <div class="card-header"><i class="fa-solid fa-route me-2 text-primary"></i>Маршрут</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-soft small">Старт</div>
                        <div class="fw-semibold"><?= Yii::$app->formatter->asDatetime($booking->started_at) ?></div>
                        <div class="small text-soft"><?= Html::encode($booking->start_address) ?></div>
                        <?php if ($booking->start_mileage !== null): ?>
                            <div class="small text-soft mt-1">Пробег: <?= number_format($booking->start_mileage, 0, '.', ' ') ?> км · топливо <?= $booking->start_fuel ?>%</div>
                        <?php endif ?>
                    </div>
                    <div class="col-md-6">
                        <div class="text-soft small">Финиш</div>
                        <div class="fw-semibold"><?= Yii::$app->formatter->asDatetime($booking->ended_at ?: $booking->cancelled_at) ?></div>
                        <div class="small text-soft"><?= Html::encode($booking->end_address) ?></div>
                        <?php if ($booking->end_mileage !== null): ?>
                            <div class="small text-soft mt-1">Пробег: <?= number_format($booking->end_mileage, 0, '.', ' ') ?> км · топливо <?= $booking->end_fuel ?>%</div>
                        <?php endif ?>
                    </div>
                </div>
                <?php if ($booking->start_lat): ?>
                    <div id="route-map" class="mt-3" style="height: 320px; border-radius: var(--vh-radius); overflow: hidden;"></div>
                <?php endif ?>
            </div>
        </div>

        <!-- Отзыв -->
        <?php if ($reviewForm): ?>
            <div class="card mb-3" id="review">
                <div class="card-body p-4">
                    <h5><i class="fa-solid fa-star me-2 text-warning"></i>Оставить отзыв</h5>
                    <p class="text-soft small mb-3">Нам важно ваше мнение, оно помогает поддерживать качество сервиса.</p>
                    <?php $f = ActiveForm::begin(['action' => Url::to(['review', 'id' => $booking->id]), 'options' => ['enctype' => 'multipart/form-data'], 'fieldConfig' => ['options' => ['class' => 'mb-3']]]) ?>
                        <div class="mb-3">
                            <label class="form-label">Оценка</label>
                            <div class="star-input" data-input="ReviewForm[rating]">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <input type="hidden" name="ReviewForm[rating]" value="5">
                        </div>
                        <?= $f->field($reviewForm, 'text')->textarea(['rows' => 4, 'placeholder' => 'Расскажите о поездке…']) ?>
                        <?= $f->field($reviewForm, 'files[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label('Фото (опционально)') ?>
                        <?= Html::submitButton('<i class="fa-solid fa-paper-plane me-1"></i> Отправить', ['class' => 'btn btn-primary']) ?>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        <?php elseif ($booking->review): ?>
            <div class="card mb-3">
                <div class="card-body p-4">
                    <h5><i class="fa-solid fa-comment-dots me-2 text-primary"></i>Ваш отзыв</h5>
                    <div class="rating mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa-solid fa-star <?= $i <= $booking->review->rating ? '' : 'text-muted' ?>"></i>
                        <?php endfor ?>
                    </div>
                    <p class="m-0"><?= nl2br(Html::encode($booking->review->text)) ?></p>
                    <div class="text-soft small mt-2">
                        Статус: <span class="badge bg-<?= $booking->review->status === 'approved' ? 'success' : ($booking->review->status === 'rejected' ? 'danger' : 'warning') ?>"><?= \app\models\Review::statusLabel($booking->review->status) ?></span>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>

    <!-- Расчёт стоимости -->
    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 90px;">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="fa-solid fa-receipt me-2 text-primary"></i>Расчёт стоимости</h5>

                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-soft">Длительность</span>
                    <span class="fw-semibold"><?= floor($booking->getDurationMinutes() / 60) ?>ч <?= $booking->getDurationMinutes() % 60 ?>м</span>
                </div>
                <?php if ($booking->end_mileage !== null && $booking->start_mileage !== null): ?>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-soft">Пробег</span>
                    <span class="fw-semibold"><?= max(0, $booking->end_mileage - $booking->start_mileage) ?> км</span>
                </div>
                <?php endif ?>

                <hr>

                <?php if ($booking->base_cost > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Базовая стоимость</span>
                        <span><?= Yii::$app->formatter->asCurrency($booking->base_cost) ?></span>
                    </div>
                <?php endif ?>
                <?php if ($booking->extra_km_cost > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Доп. километры</span>
                        <span><?= Yii::$app->formatter->asCurrency($booking->extra_km_cost) ?></span>
                    </div>
                <?php endif ?>
                <?php if ($booking->overdue_cost > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-warning">
                        <span>Просрочка</span>
                        <span><?= Yii::$app->formatter->asCurrency($booking->overdue_cost) ?></span>
                    </div>
                <?php endif ?>
                <?php if ($booking->penalty_cost > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Штрафы</span>
                        <span><?= Yii::$app->formatter->asCurrency($booking->penalty_cost) ?></span>
                    </div>
                <?php endif ?>
                <?php if ($booking->discount_amount > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Скидка <?= $booking->promoCode ? '(' . Html::encode($booking->promoCode->code) . ')' : '' ?></span>
                        <span>-<?= Yii::$app->formatter->asCurrency($booking->discount_amount) ?></span>
                    </div>
                <?php endif ?>

                <hr>

                <div class="d-flex justify-content-between fw-bold h5">
                    <span>Итого</span>
                    <span class="text-primary"><?= Yii::$app->formatter->asCurrency($booking->final_cost) ?></span>
                </div>

                <div class="d-flex justify-content-between small text-soft mt-3">
                    <span>Депозит</span>
                    <span><?= Yii::$app->formatter->asCurrency($booking->deposit) ?> возвращён</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if ($booking->start_lat && $booking->end_lat):
    $startLat = (float)$booking->start_lat; $startLng = (float)$booking->start_lng;
    $endLat = (float)$booking->end_lat; $endLng = (float)$booking->end_lng;
    $this->registerJs(<<<JS
const rmap = L.map('route-map').setView([$startLat, $startLng], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OpenStreetMap'}).addTo(rmap);
const startIcon = L.divIcon({html:'<div style="background:#10b981;color:#fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-weight:700;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.2);">A</div>',className:'',iconSize:[28,28],iconAnchor:[14,14]});
const endIcon = L.divIcon({html:'<div style="background:#ef4444;color:#fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-weight:700;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.2);">B</div>',className:'',iconSize:[28,28],iconAnchor:[14,14]});
L.marker([$startLat, $startLng], {icon:startIcon}).addTo(rmap).bindPopup('Старт');
L.marker([$endLat, $endLng], {icon:endIcon}).addTo(rmap).bindPopup('Финиш');
const line = L.polyline([[$startLat, $startLng], [$endLat, $endLng]], {color: '#00c896', weight: 3, dashArray: '6, 6'}).addTo(rmap);
rmap.fitBounds(line.getBounds(), {padding:[40,40]});
JS);
endif;
?>
