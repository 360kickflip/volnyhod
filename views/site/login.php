<?php
/** @var yii\web\View $this */
/** @var app\models\forms\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Вход';
?>

<h1>С возвращением!</h1>
<p class="text-muted mb-4">Войдите в свой аккаунт, чтобы продолжить путешествие.</p>

<?php $form = ActiveForm::begin(['id' => 'login-form', 'fieldConfig' => [
    'options' => ['class' => 'mb-3'],
    'labelOptions' => ['class' => 'form-label'],
]]) ?>

    <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'you@example.com', 'autocomplete' => 'email', 'class' => 'form-control form-control-lg']) ?>

    <?= $form->field($model, 'password')->passwordInput(['placeholder' => '••••••', 'autocomplete' => 'current-password', 'class' => 'form-control form-control-lg']) ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <?= $form->field($model, 'rememberMe', ['template' => '<div class="form-check">{input}{label}</div>'])->checkbox(['class' => 'form-check-input'])->label(null, ['class' => 'form-check-label']) ?>
        <a href="<?= Url::to(['site/request-password-reset']) ?>" class="small">Забыли пароль?</a>
    </div>

    <div class="d-grid">
        <?= Html::submitButton('<i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Войти', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>

<?php ActiveForm::end() ?>

<div class="divider-text">или</div>

<p class="text-center text-muted">
    Ещё нет аккаунта? <a href="<?= Url::to(['site/signup']) ?>" class="fw-semibold">Зарегистрироваться</a>
</p>

<div class="mt-5 p-3 rounded-3 bg-soft small text-soft">
    <strong>Демо-аккаунты:</strong><br>
    👤 user@volnyhod.ru / user12345<br>
    🛡️ admin@volnyhod.ru / admin12345
</div>
