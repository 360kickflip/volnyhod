<?php
/** @var yii\web\View $this */
/** @var app\models\Tariff $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = $model->isNewRecord ? 'Новый тариф' : 'Тариф: ' . $model->name;
?>
<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<?php $form = ActiveForm::begin(['fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

                <div class="row">
                    <div class="col-md-3"><?= $form->field($model, 'price_per_minute')->input('number', ['step' => '0.01']) ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'price_per_km')->input('number', ['step' => '0.01']) ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'price_per_hour')->input('number', ['step' => '0.01']) ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'price_per_day')->input('number', ['step' => '0.01']) ?></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><?= $form->field($model, 'deposit')->input('number', ['step' => '0.01']) ?></div>
                    <div class="col-md-4"><?= $form->field($model, 'overdue_per_minute')->input('number', ['step' => '0.01']) ?></div>
                    <div class="col-md-4"><?= $form->field($model, 'free_km')->input('number') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body p-4">
                <?= $form->field($model, 'color')->input('color') ?>
                <?= $form->field($model, 'icon')->textInput()->hint('FontAwesome класс, например fa-car') ?>
                <?= $form->field($model, 'sort_order')->input('number') ?>
                <div class="form-check mb-3">
                    <?= Html::activeCheckbox($model, 'is_active', ['class' => 'form-check-input']) ?>
                </div>
                <?= Html::submitButton('<i class="fa-solid fa-floppy-disk me-2"></i>Сохранить', ['class' => 'btn btn-primary w-100']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>
