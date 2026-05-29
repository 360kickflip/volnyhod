<?php
/** @var app\models\Faq $model */
/** @var array $categories */
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
$this->title = $model->isNewRecord ? 'Новый вопрос' : 'Редактировать вопрос';
?>
<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<?php $form = ActiveForm::begin(['fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
<div class="card"><div class="card-body p-4">
    <?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '— без категории —']) ?>
    <?= $form->field($model, 'question')->textInput() ?>
    <?= $form->field($model, 'answer')->textarea(['rows' => 6]) ?>
    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'sort_order')->input('number') ?></div>
        <div class="col-md-3 d-flex align-items-end pb-3">
            <div class="form-check">
                <?= Html::activeCheckbox($model, 'is_active', ['class' => 'form-check-input']) ?>
            </div>
        </div>
    </div>
    <?= Html::submitButton('<i class="fa-solid fa-floppy-disk me-1"></i> Сохранить', ['class' => 'btn btn-primary']) ?>
</div></div>
<?php ActiveForm::end() ?>
