<?php
/** @var yii\web\View $this */
/** @var app\models\Booking $booking */

use app\models\Booking;
use app\models\BookingCharge;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Бронирование ' . $booking->number;
?>

<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h3 class="m-0">№ <?= $booking->number ?></h3>
                        <div class="text-soft mt-1">Создано: <?= Yii::$app->formatter->asDatetime($booking->created_at) ?></div>
                    </div>
                    <span class="badge bg-<?= Booking::statusBadge($booking->status) ?> fs-6"><?= Booking::statusLabel($booking->status) ?></span>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <h6>Пользователь</h6>
                        <p class="m-0"><a href="<?= Url::to(['/admin/user/view', 'id' => $booking->user_id]) ?>" class="text-decoration-none"><?= Html::encode($booking->user->name ?? $booking->user->email ?? '—') ?></a></p>
                        <p class="small text-soft m-0"><?= Html::encode($booking->user->email ?? '') ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Автомобиль</h6>
                        <p class="m-0"><a href="<?= Url::to(['/admin/car/view', 'id' => $booking->car_id]) ?>" class="text-decoration-none"><?= Html::encode($booking->car->getFullName() ?? '—') ?></a></p>
                        <p class="small text-soft m-0"><?= Html::encode($booking->car->license_plate ?? '') ?> · Тариф «<?= Html::encode($booking->tariff->name ?? '') ?>»</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Маршрут</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-soft small">Начало</div>
                        <div class="fw-semibold"><?= $booking->started_at ? Yii::$app->formatter->asDatetime($booking->started_at) : '—' ?></div>
                        <div class="small text-soft"><?= Html::encode($booking->start_address ?: '—') ?></div>
                        <?php if ($booking->start_mileage !== null): ?>
                            <div class="small text-soft mt-1">Пробег: <?= $booking->start_mileage ?> км · топливо <?= $booking->start_fuel ?>%</div>
                        <?php endif ?>
                    </div>
                    <div class="col-md-6">
                        <div class="text-soft small">Окончание</div>
                        <div class="fw-semibold"><?= $booking->ended_at ? Yii::$app->formatter->asDatetime($booking->ended_at) : '—' ?></div>
                        <div class="small text-soft"><?= Html::encode($booking->end_address ?: '—') ?></div>
                        <?php if ($booking->end_mileage !== null): ?>
                            <div class="small text-soft mt-1">Пробег: <?= $booking->end_mileage ?> км · топливо <?= $booking->end_fuel ?>%</div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Детализация</div>
            <div class="card-body p-0">
                <?php if ($booking->charges): ?>
                    <table class="table mb-0">
                        <thead><tr><th>Тип</th><th>Описание</th><th class="text-end">Сумма</th></tr></thead>
                        <tbody>
                        <?php foreach ($booking->charges as $c): ?>
                            <tr>
                                <td><?= BookingCharge::typeLabel($c->type) ?></td>
                                <td class="small"><?= Html::encode($c->description) ?></td>
                                <td class="text-end fw-bold <?= $c->amount < 0 ? 'text-success' : '' ?>"><?= Yii::$app->formatter->asCurrency($c->amount) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                        <tfoot>
                        <tr><th>Итого</th><th></th><th class="text-end fw-bold"><?= Yii::$app->formatter->asCurrency($booking->final_cost) ?></th></tr>
                        </tfoot>
                    </table>
                <?php else: ?>
                    <div class="empty"><h6>Расчёт ещё не выполнен</h6></div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body p-4">
                <h6>Параметры</h6>
                <div class="d-flex justify-content-between mb-1"><span class="text-soft">Длительность</span><strong><?= $booking->getDurationMinutes() ?> мин</strong></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-soft">Депозит</span><strong><?= Yii::$app->formatter->asCurrency($booking->deposit) ?></strong></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-soft">Оценка</span><strong><?= Yii::$app->formatter->asCurrency($booking->estimated_cost) ?></strong></div>
                <?php if ($booking->discount_amount > 0): ?>
                    <div class="d-flex justify-content-between mb-1 text-success"><span>Скидка</span><strong>-<?= Yii::$app->formatter->asCurrency($booking->discount_amount) ?></strong></div>
                <?php endif ?>
                <hr>
                <div class="d-flex justify-content-between"><span class="fw-bold">Итого</span><span class="fw-bold text-primary"><?= Yii::$app->formatter->asCurrency($booking->final_cost) ?></span></div>
            </div>
        </div>

        <?php if ($booking->status === Booking::STATUS_ACTIVE): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h6>Действия</h6>
                <?= Html::beginForm(['finish', 'id' => $booking->id]) ?>
                    <button class="btn btn-primary w-100 mb-2" onclick="return confirm('Завершить аренду?')"><i class="fa-solid fa-flag-checkered me-1"></i> Завершить</button>
                <?= Html::endForm() ?>
                <?= Html::beginForm(['cancel', 'id' => $booking->id]) ?>
                    <input type="text" name="reason" class="form-control form-control-sm mb-2" placeholder="Причина отмены">
                    <button class="btn btn-soft w-100 text-danger" onclick="return confirm('Отменить?')"><i class="fa-solid fa-xmark me-1"></i> Отменить</button>
                <?= Html::endForm() ?>
            </div>
        </div>
        <?php endif ?>

        <?php if ($booking->status === Booking::STATUS_COMPLETED): ?>
            <a href="<?= Url::to(['/trip/receipt', 'id' => $booking->id]) ?>" target="_blank" class="btn btn-soft w-100"><i class="fa-solid fa-file-pdf me-1"></i> Чек</a>
        <?php endif ?>
    </div>
</div>
