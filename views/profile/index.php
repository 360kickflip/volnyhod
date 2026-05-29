<?php
/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\forms\ProfileForm $form */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Профиль';
?>

<h1 class="mb-3">Профиль</h1>
<?= $this->render('_header', ['user' => $user]) ?>
<?= $this->render('_tabs', ['active' => 'index']) ?>

<div class="card">
    <div class="card-body p-4">
        <h5 class="mb-4">Личные данные</h5>
        <?php $f = ActiveForm::begin(['fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
            <div class="row g-3">
                <div class="col-md-6"><?= $f->field($form, 'name')->textInput() ?></div>
                <div class="col-md-6"><?= $f->field($form, 'email')->textInput() ?></div>
                <div class="col-md-6"><?= $f->field($form, 'phone')->textInput(['placeholder' => '+7 (___) ___-__-__']) ?></div>
                <div class="col-md-6"><?= $f->field($form, 'birthdate')->input('date') ?></div>
            </div>
            <div class="mt-3">
                <?= Html::submitButton('<i class="fa-solid fa-floppy-disk me-2"></i>Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
