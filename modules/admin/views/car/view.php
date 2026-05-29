<?php
/** @var yii\web\View $this */
/** @var app\models\Car $car */
/** @var app\models\Booking[] $bookings */

use app\models\Booking;
use app\models\Car;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $car->getFullName() . ' · ' . $car->license_plate;
?>

<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h3 class="m-0"><?= Html::encode($car->getFullName()) ?> <span class="text-muted fw-normal"><?= $car->year ?></span></h3>
                        <div class="text-soft mt-1">№ <?= Html::encode($car->license_plate) ?> · <?= Html::encode($car->color) ?></div>
                    </div>
                    <span class="badge bg-<?= Car::statusBadge($car->status) ?> fs-6"><?= Car::statusLabel($car->status) ?></span>
                </div>

                <div class="row g-2">
                    <div class="col-md-3"><div class="car-stat"><div class="car-stat__icon"><i class="fa-solid fa-gear"></i></div><div><div class="car-stat__label">КПП</div><div class="car-stat__value"><?= $car->transmission ?></div></div></div></div>
                    <div class="col-md-3"><div class="car-stat"><div class="car-stat__icon"><i class="fa-solid fa-users"></i></div><div><div class="car-stat__label">Мест</div><div class="car-stat__value"><?= $car->seats ?></div></div></div></div>
                    <div class="col-md-3"><div class="car-stat"><div class="car-stat__icon"><i class="fa-solid fa-gas-pump"></i></div><div><div class="car-stat__label">Топливо</div><div class="car-stat__value"><?= $car->fuel_level ?>%</div></div></div></div>
                    <div class="col-md-3"><div class="car-stat"><div class="car-stat__icon"><i class="fa-solid fa-route"></i></div><div><div class="car-stat__label">Пробег</div><div class="car-stat__value"><?= number_format($car->mileage, 0, '.', ' ') ?></div></div></div></div>
                </div>

                <div class="mt-3 small text-soft">
                    <i class="fa-solid fa-location-dot me-1"></i><?= Html::encode($car->address) ?>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">История бронирований</div>
            <div class="card-body p-0">
                <?php if ($bookings): ?>
                    <table class="table mb-0">
                        <thead><tr><th>Номер</th><th>Пользователь</th><th>Дата</th><th>Длительность</th><th>Статус</th><th>Сумма</th></tr></thead>
                        <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><a href="<?= Url::to(['/admin/booking/view', 'id' => $b->id]) ?>" class="text-decoration-none fw-semibold"><?= $b->number ?></a></td>
                                <td class="small"><?= Html::encode($b->user->name ?? $b->user->email ?? '—') ?></td>
                                <td class="small text-soft"><?= Yii::$app->formatter->asDatetime($b->created_at) ?></td>
                                <td class="small"><?= $b->getDurationMinutes() ?> мин</td>
                                <td><span class="badge bg-<?= Booking::statusBadge($b->status) ?>"><?= Booking::statusLabel($b->status) ?></span></td>
                                <td class="fw-bold"><?= Yii::$app->formatter->asCurrency($b->final_cost) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty"><div class="empty__icon"><i class="fa-solid fa-key"></i></div><h6>Нет бронирований</h6></div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <img src="<?= $car->getMainPhotoUrl() ?>" style="width:100%;aspect-ratio:4/3;object-fit:cover;">
        </div>

        <?php if ($car->photos): ?>
            <div class="row g-2 mb-3">
                <?php foreach ($car->photos as $p): ?>
                    <div class="col-4">
                        <img src="<?= $p->getUrl() ?>" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;">
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <div class="d-grid gap-2">
            <a href="<?= Url::to(['update', 'id' => $car->id]) ?>" class="btn btn-primary"><i class="fa-solid fa-pen me-1"></i> Редактировать</a>
            <a href="<?= Url::to(['/car/view', 'id' => $car->id]) ?>" target="_blank" class="btn btn-soft"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> На сайте</a>
        </div>
    </div>
</div>
