<?php
/** @var app\models\Page $model */
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
$this->title = $model->isNewRecord ? 'Новая страница' : 'Страница: ' . $model->title;
?>
<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<?php $form = ActiveForm::begin(['fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
<div class="row g-3">
    <div class="col-lg-9">
        <div class="card"><div class="card-body p-4">
            <?= $form->field($model, 'title') ?>
            <?= $form->field($model, 'slug')->textInput()->hint('Транслит, без пробелов. Будет URL: /page/&lt;slug&gt;') ?>
            <?= $form->field($model, 'content')->textarea(['rows' => 16])->hint('Поддерживаются HTML-теги. Контент проходит через HTMLPurifier на выводе.') ?>
            <?= $form->field($model, 'meta_description')->textarea(['rows' => 2]) ?>
        </div></div>
    </div>
    <div class="col-lg-3">
        <div class="card sticky-top" style="top: 80px;">
            <div class="card-body p-4">
                <div class="form-check mb-3">
                    <?= Html::activeCheckbox($model, 'is_active', ['class' => 'form-check-input']) ?>
                </div>
                <?= Html::submitButton('<i class="fa-solid fa-floppy-disk me-1"></i> Сохранить', ['class' => 'btn btn-primary w-100']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>
