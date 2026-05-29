<?php
/** @var yii\web\View $this */
/** @var app\models\Car $car */
/** @var app\models\forms\BookingForm|null $bookingForm */
/** @var app\models\Review[] $reviews */

use app\models\Car;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = $car->getFullName() . ' · ' . $car->license_plate;
$photos = $car->getPhotoUrls();
$features = $car->getFeaturesArray();
$avgRating = $car->getAverageRating();
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Url::to(['/car/index']) ?>">Каталог</a></li>
        <li class="breadcrumb-item active"><?= Html::encode($car->getFullName()) ?></li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card overflow-hidden">
            <div id="carPhotos" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($photos as $i => $url): ?>
                        <button type="button" data-bs-target="#carPhotos" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active"' : '' ?>></button>
                    <?php endforeach ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($photos as $i => $url): ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" style="background:#f1f5f9; aspect-ratio: 16/10;">
                            <img src="<?= $url ?>" class="d-block w-100 h-100" alt="" style="object-fit: cover;">
                        </div>
                    <?php endforeach ?>
                </div>
                <?php if (count($photos) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carPhotos" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carPhotos" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                <?php endif ?>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h2 class="m-0"><?= Html::encode($car->getFullName()) ?> <span class="text-muted fw-normal"><?= $car->year ?></span></h2>
                        <div class="text-soft small mt-1">№ <?= Html::encode($car->license_plate) ?> · <?= Html::encode($car->color) ?></div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-<?= Car::statusBadge($car->status) ?> fs-6"><?= Car::statusLabel($car->status) ?></span>
                        <?php if ($avgRating > 0): ?>
                            <div class="rating mt-2 justify-content-end">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-solid fa-star <?= $i <= $avgRating ? '' : 'text-muted' ?>"></i>
                                <?php endfor ?>
                                <span class="ms-1 small text-soft"><?= $avgRating ?></span>
                            </div>
                        <?php endif ?>
                    </div>
                </div>

                <p class="text-soft"><?= Html::encode($car->description) ?></p>

                <h5 class="mt-4">Характеристики</h5>
                <div class="row g-2">
                    <?php
                    $specs = [
                        ['fa-gear', 'КПП', $car->transmission === 'auto' ? 'Автоматическая' : 'Механическая'],
                        ['fa-users', 'Мест', $car->seats],
                        ['fa-car-side', 'Кузов', $car->body_type],
                        ['fa-gas-pump', 'Топливо', ucfirst($car->fuel_type) . ' · ' . $car->fuel_level . '%'],
                        ['fa-road', 'Пробег', number_format($car->mileage, 0, '.', ' ') . ' км'],
                        ['fa-location-dot', 'Локация', $car->address],
                    ];
                    foreach ($specs as [$icon, $label, $value]): ?>
                        <div class="col-md-6">
                            <div class="car-stat">
                                <div class="car-stat__icon"><i class="fa-solid <?= $icon ?>"></i></div>
                                <div>
                                    <div class="car-stat__label"><?= $label ?></div>
                                    <div class="car-stat__value"><?= Html::encode($value) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>

                <?php if ($features): ?>
                    <h5 class="mt-4">Опции</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($features as $feat): ?>
                            <span class="badge bg-soft text-dark"><i class="fa-solid fa-check text-success me-1"></i><?= Html::encode($feat) ?></span>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        </div>

        <?php if ($reviews): ?>
        <div class="card mt-4">
            <div class="card-body p-4">
                <h5>Отзывы</h5>
                <?php foreach ($reviews as $review): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <strong><?= Html::encode($review->user->name ?? 'Пользователь') ?></strong>
                            <div class="rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-solid fa-star <?= $i <= $review->rating ? '' : 'text-muted' ?>"></i>
                                <?php endfor ?>
                            </div>
                        </div>
                        <div class="text-soft small mb-2"><?= Yii::$app->formatter->asRelativeTime($review->created_at) ?></div>
                        <p class="m-0"><?= nl2br(Html::encode($review->text)) ?></p>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
        <?php endif ?>
    </div>

    <div class="col-lg-5">
        <div class="card sticky-top shadow-md" style="top: 90px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <span class="h2 fw-bold m-0"><?= Yii::$app->formatter->asDecimal($car->tariff->price_per_minute ?? 0, 2) ?> ₽</span>
                    <span class="text-muted">/мин</span>
                </div>
                <div class="text-soft small mb-3">
                    Тариф: <strong><?= Html::encode($car->tariff->name ?? '') ?></strong> · +<?= Yii::$app->formatter->asDecimal($car->tariff->price_per_km ?? 0, 2) ?> ₽/км · депозит <?= Yii::$app->formatter->asCurrency($car->tariff->deposit ?? 0) ?>
                </div>

                <?php if ($car->status !== Car::STATUS_AVAILABLE): ?>
                    <div class="alert alert-warning"><i class="fa-solid fa-info-circle me-2"></i>Автомобиль сейчас недоступен (<?= Car::statusLabel($car->status) ?>).</div>
                    <a href="<?= Url::to(['/car/index']) ?>" class="btn btn-soft w-100">Выбрать другой</a>
                <?php elseif (Yii::$app->user->isGuest): ?>
                    <a href="<?= Url::to(['/site/login']) ?>" class="btn btn-primary w-100 btn-lg"><i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Войдите для бронирования</a>
                <?php elseif (!Yii::$app->user->identity->isVerified()): ?>
                    <div class="alert alert-warning"><i class="fa-solid fa-id-card me-2"></i>Чтобы арендовать, пройдите верификацию документов.</div>
                    <a href="<?= Url::to(['/profile/documents']) ?>" class="btn btn-primary w-100">Загрузить документы</a>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#bookingModal">
                        <i class="fa-solid fa-key me-2"></i>Забронировать
                    </button>
                <?php endif ?>

                <hr>

                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fa-solid fa-location-dot text-primary"></i>
                    <div>
                        <div class="fw-semibold small"><?= Html::encode($car->address ?: 'Локация недоступна') ?></div>
                        <?php if ($car->lat && $car->lng): ?>
                            <a href="https://yandex.ru/maps/?pt=<?= $car->lng ?>,<?= $car->lat ?>&z=16" target="_blank" class="small">Открыть на карте →</a>
                        <?php endif ?>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 small text-soft">
                    <span><i class="fa-solid fa-gas-pump me-1"></i> <?= $car->fuel_level ?>%</span>
                    <span><i class="fa-solid fa-route me-1"></i> <?= number_format($car->mileage, 0, '.', ' ') ?> км</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isVerified() && $car->status === Car::STATUS_AVAILABLE && $bookingForm): ?>
    <?= $this->render('_booking_modal', ['car' => $car, 'bookingForm' => $bookingForm]) ?>
<?php endif ?>
