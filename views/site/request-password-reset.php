<?php
/** @var yii\web\View $this */
/** @var app\models\forms\PasswordResetRequestForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Сброс пароля';
?>

<h1>Сброс пароля</h1>
<p class="text-muted mb-4">Введите email, на который зарегистрирован аккаунт. Мы отправим инструкцию.</p>

<?php $form = ActiveForm::begin(['fieldConfig' => [
    'options' => ['class' => 'mb-3'],
    'labelOptions' => ['class' => 'form-label'],
]]) ?>
    <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'class' => 'form-control form-control-lg']) ?>
    <div class="d-grid">
        <?= Html::submitButton('Отправить инструкцию', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
<?php ActiveForm::end() ?>

<p class="text-center text-muted mt-4">
    <a href="<?= Url::to(['site/login']) ?>" class="fw-semibold">← Вернуться ко входу</a>
</p>
