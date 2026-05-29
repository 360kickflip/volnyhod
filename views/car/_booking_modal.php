<?php
/** @var yii\web\View $this */
/** @var app\models\Car $car */
/** @var app\models\forms\BookingForm $bookingForm */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$user = Yii::$app->user->identity;
?>

<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Бронирование</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?php $form = ActiveForm::begin(['action' => Url::to(['/booking/create']), 'fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
                <div class="modal-body">
                    <div class="d-flex gap-3 align-items-center mb-3 p-3 bg-soft rounded-3">
                        <img src="<?= $car->getMainPhotoUrl() ?>" alt="" style="width:80px;height:60px;object-fit:cover;border-radius:8px;">
                        <div>
                            <div class="fw-bold"><?= Html::encode($car->getFullName()) ?></div>
                            <div class="small text-soft"><?= Html::encode($car->license_plate) ?></div>
                            <div class="small">
                                <span class="badge badge-soft-primary"><?= Html::encode($car->tariff->name ?? '') ?></span>
                                <?= Yii::$app->formatter->asDecimal($car->tariff->price_per_minute ?? 0, 2) ?> ₽/мин
                            </div>
                        </div>
                    </div>

                    <?= $form->field($bookingForm, 'car_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($bookingForm, 'planned_minutes')
                        ->input('number', ['min' => 15, 'max' => 1440, 'step' => 5, 'data-car-id' => $car->id])
                        ->hint('От 15 минут до 24 часов') ?>

                    <div class="mb-3">
                        <label class="form-label">Промокод</label>
                        <div class="input-group">
                            <input type="text" name="BookingForm[promo_code]" class="form-control" placeholder="Например, WELCOME10" data-promo-input>
                            <button type="button" class="btn btn-soft" data-promo-apply data-car-id="<?= $car->id ?>">Применить</button>
                        </div>
                        <small data-promo-result class="text-soft"></small>
                    </div>

                    <div class="bg-soft p-3 rounded-3">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-soft">Депозит</span>
                            <span class="fw-semibold" data-deposit><?= Yii::$app->formatter->asCurrency($bookingForm->deposit) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-soft">Примерная стоимость</span>
                            <span class="fw-semibold" data-estimated><?= Yii::$app->formatter->asCurrency($bookingForm->estimated) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small d-none" data-discount-row>
                            <span class="text-success">Скидка</span>
                            <span class="fw-semibold text-success" data-discount>-0 ₽</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>К оплате (заморозится)</span>
                            <span class="text-primary" data-total><?= Yii::$app->formatter->asCurrency($bookingForm->total) ?></span>
                        </div>
                        <div class="small text-soft mt-2">Доступный баланс: <?= Yii::$app->formatter->asCurrency($user->getAvailableBalance()) ?></div>
                    </div>

                    <?= $form->field($bookingForm, 'agreement', ['template' => '<div class="form-check mt-3">{input}{label}{error}</div>'])
                        ->checkbox(['class' => 'form-check-input'], false)
                        ->label('Я согласен с <a href="' . Url::to(['/page/view', 'slug' => 'terms']) . '" target="_blank">условиями аренды</a>') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Отмена</button>
                    <?= Html::submitButton('<i class="fa-solid fa-key me-2"></i>Забронировать', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
