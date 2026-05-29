<?php
/** @var yii\web\View $this */
/** @var app\models\forms\ResetPasswordForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Новый пароль';
?>

<h1>Новый пароль</h1>
<p class="text-muted mb-4">Придумайте надёжный пароль. Минимум 6 символов.</p>

<?php $form = ActiveForm::begin(['fieldConfig' => [
    'options' => ['class' => 'mb-3'],
    'labelOptions' => ['class' => 'form-label'],
]]) ?>
    <?= $form->field($model, 'password')->passwordInput(['autofocus' => true, 'class' => 'form-control form-control-lg']) ?>
    <?= $form->field($model, 'password_repeat')->passwordInput(['class' => 'form-control form-control-lg']) ?>
    <div class="d-grid">
        <?= Html::submitButton('Изменить пароль', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
<?php ActiveForm::end() ?>
