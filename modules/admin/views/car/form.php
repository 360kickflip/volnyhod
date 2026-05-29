<?php
/** @var yii\web\View $this */
/** @var app\models\Car $model */
/** @var app\models\Tariff[] $tariffs */

use app\models\Car;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$this->title = $model->isNewRecord ? 'Новый автомобиль' : 'Редактирование: ' . $model->getFullName();
?>

<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="mb-3">Основное</h5>
                <div class="row">
                    <div class="col-md-6"><?= $form->field($model, 'brand') ?></div>
                    <div class="col-md-6"><?= $form->field($model, 'model') ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'year')->input('number', ['min' => 2010, 'max' => date('Y') + 1]) ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'color') ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'license_plate') ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'vin') ?></div>
                </div>

                <h5 class="mb-3 mt-3">Технические характеристики</h5>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'transmission')->dropDownList([
                            'auto' => 'Автомат', 'manual' => 'Механика', 'robot' => 'Робот', 'variator' => 'Вариатор',
                        ]) ?>
                    </div>
                    <div class="col-md-3"><?= $form->field($model, 'body_type') ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'seats')->input('number', ['min' => 2, 'max' => 9]) ?></div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'fuel_type')->dropDownList([
                            'petrol' => 'Бензин', 'diesel' => 'Дизель', 'hybrid' => 'Гибрид', 'electric' => 'Электро', 'gas' => 'Газ',
                        ]) ?>
                    </div>
                    <div class="col-md-4"><?= $form->field($model, 'mileage')->input('number', ['min' => 0]) ?></div>
                    <div class="col-md-4"><?= $form->field($model, 'fuel_level')->input('number', ['min' => 0, 'max' => 100]) ?></div>
                    <div class="col-md-4"><?= $form->field($model, 'battery_level')->input('number', ['min' => 0, 'max' => 100]) ?></div>
                </div>

                <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
                <?= $form->field($model, 'features')->textarea(['rows' => 2])->hint('JSON-массив, например: ["Кондиционер","Bluetooth"]') ?>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body p-4">
                <h5 class="mb-3">Локация</h5>
                <?= $form->field($model, 'address')->textInput(['placeholder' => 'ул. Тверская, 10']) ?>
                <div class="row">
                    <div class="col-md-6"><?= $form->field($model, 'lat')->input('number', ['step' => '0.0000001']) ?></div>
                    <div class="col-md-6"><?= $form->field($model, 'lng')->input('number', ['step' => '0.0000001']) ?></div>
                </div>
                <p class="small text-soft m-0">Координаты в десятичном формате. Можно скопировать с <a href="https://yandex.ru/maps" target="_blank">Яндекс Карт</a>.</p>
            </div>
        </div>

        <?php if (!$model->isNewRecord): ?>
        <div class="card mt-3">
            <div class="card-body p-4">
                <h5 class="mb-3">Существующие фото</h5>
                <?php if (count($model->photos) === 0): ?>
                    <div class="text-soft small">Фото нет.</div>
                <?php else: ?>
                    <div class="row g-2">
                        <?php foreach ($model->photos as $p): ?>
                            <div class="col-md-3 col-6">
                                <div class="position-relative">
                                    <img src="<?= $p->getUrl() ?>" alt="" style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px;">
                                    <?= Html::beginForm(['delete-photo', 'id' => $p->id], 'post', ['class' => 'position-absolute top-0 end-0 m-1']) ?>
                                        <button class="btn btn-sm btn-danger btn-icon" onclick="return confirm('Удалить фото?')"><i class="fa-solid fa-times"></i></button>
                                    <?= Html::endForm() ?>
                                    <?php if ($p->is_main): ?>
                                        <span class="badge bg-warning position-absolute bottom-0 start-0 m-1">Главное</span>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <?php endif ?>

        <div class="card mt-3">
            <div class="card-body p-4">
                <h5 class="mb-3">Загрузить фото (до 5)</h5>
                <input type="file" name="Car[photos][]" multiple accept="image/*" class="form-control">
                <small class="text-muted">JPG, PNG, WEBP. Максимум 10 МБ каждое. Первое фото нового авто становится главным.</small>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 80px;">
            <div class="card-body p-4">
                <h5 class="mb-3">Тариф и статус</h5>
                <?= $form->field($model, 'tariff_id')->dropDownList(ArrayHelper::map($tariffs, 'id', 'name')) ?>
                <?= $form->field($model, 'status')->dropDownList(Car::statusLabels()) ?>

                <hr>
                <?= Html::submitButton('<i class="fa-solid fa-floppy-disk me-2"></i>Сохранить', ['class' => 'btn btn-primary w-100']) ?>
                <a href="<?= Url::to(['index']) ?>" class="btn btn-soft w-100 mt-2">Отмена</a>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>
