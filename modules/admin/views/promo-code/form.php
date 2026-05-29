<?php
/** @var yii\web\View $this */
/** @var app\models\PromoCode $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = $model->isNewRecord ? 'Новый промокод' : 'Промокод: ' . $model->code;
?>
<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<?php $form = ActiveForm::begin(['fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <?= $form->field($model, 'code', ['template' => '{label}<div class="input-group">{input}<button type="button" class="btn btn-soft" data-generate-code data-target="#promocode-code">Сгенерировать</button></div>{error}{hint}'])
                    ->textInput(['style' => 'text-transform:uppercase', 'maxlength' => 50]) ?>
                <?= $form->field($model, 'description')->textInput() ?>

                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'type')->dropDownList(['percent' => 'Процент', 'fixed' => 'Фикс. сумма']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'value')->input('number', ['step' => '0.01', 'min' => 0]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'max_discount')->input('number', ['step' => '0.01']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'min_amount')->input('number', ['step' => '0.01']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'usage_limit')->input('number', ['min' => 0])->hint('0 / пусто = без ограничения') ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'per_user_limit')->input('number', ['min' => 1]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'valid_from')->input('datetime-local') ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'valid_to')->input('datetime-local') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body p-4">
                <h6>Статус</h6>
                <div class="form-check mb-3">
                    <?= Html::activeCheckbox($model, 'is_active', ['class' => 'form-check-input']) ?>
                </div>
                <?php if (!$model->isNewRecord): ?>
                    <div class="text-soft small mb-2">Использовано: <strong><?= $model->usage_count ?></strong></div>
                <?php endif ?>
                <?= Html::submitButton('<i class="fa-solid fa-floppy-disk me-2"></i>Сохранить', ['class' => 'btn btn-primary w-100']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>
