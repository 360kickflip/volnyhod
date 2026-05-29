<?php
/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\forms\PasswordChangeForm $form */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Безопасность';
?>

<h1 class="mb-3">Профиль</h1>
<?= $this->render('_header', ['user' => $user]) ?>
<?= $this->render('_tabs', ['active' => 'security']) ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="mb-3">Смена пароля</h5>
                <p class="text-soft small mb-4">Используйте пароль не менее 6 символов. Лучше — длинный и непредсказуемый.</p>
                <?php $f = ActiveForm::begin(['fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
                    <?= $f->field($form, 'current_password')->passwordInput() ?>
                    <?= $f->field($form, 'password')->passwordInput() ?>
                    <?= $f->field($form, 'password_repeat')->passwordInput() ?>
                    <?= Html::submitButton('<i class="fa-solid fa-lock me-2"></i>Изменить пароль', ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="fa-solid fa-shield-halved me-2 text-success"></i>Безопасность аккаунта</h5>
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex align-items-center gap-2 mb-2"><i class="fa-solid fa-circle-check text-success"></i> Email подтверждён</li>
                    <li class="d-flex align-items-center gap-2 mb-2"><i class="fa-solid fa-circle-check text-success"></i> Длительная сессия 30 дней</li>
                    <li class="d-flex align-items-center gap-2 mb-2"><i class="fa-solid fa-circle-check text-success"></i> CSRF-защита включена</li>
                    <li class="d-flex align-items-center gap-2 text-muted"><i class="fa-solid fa-circle text-muted"></i> Двухфакторная аутентификация (скоро)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
