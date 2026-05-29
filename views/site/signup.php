<?php
/** @var yii\web\View $this */
/** @var app\models\forms\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Регистрация';
?>

<h1>Создать аккаунт</h1>
<p class="text-muted mb-4">Несколько шагов до первой поездки.</p>

<?php $form = ActiveForm::begin(['fieldConfig' => [
    'options' => ['class' => 'mb-3'],
    'labelOptions' => ['class' => 'form-label'],
]]) ?>

    <?= $form->field($model, 'name')->textInput(['autofocus' => true, 'placeholder' => 'Иван Петров', 'class' => 'form-control form-control-lg']) ?>
    <?= $form->field($model, 'email')->textInput(['placeholder' => 'you@example.com', 'autocomplete' => 'email', 'class' => 'form-control form-control-lg']) ?>
    <?= $form->field($model, 'phone')->textInput(['placeholder' => '+7 (___) ___-__-__', 'class' => 'form-control form-control-lg']) ?>
    <?= $form->field($model, 'birthdate')->input('date', ['class' => 'form-control form-control-lg']) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'мин. 6 символов', 'class' => 'form-control form-control-lg']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'password_repeat')->passwordInput(['placeholder' => 'ещё раз', 'class' => 'form-control form-control-lg']) ?>
        </div>
    </div>

    <?= $form->field($model, 'agreement', ['template' => '<div class="form-check">{input}{label}{error}</div>'])
        ->checkbox(['class' => 'form-check-input'], false)
        ->label('Я принимаю <a href="' . Url::to(['/page/view', 'slug' => 'terms']) . '" target="_blank">правила сервиса</a> и <a href="' . Url::to(['/page/view', 'slug' => 'privacy']) . '" target="_blank">политику конфиденциальности</a>', ['class' => 'form-check-label']) ?>

    <details class="mb-3 small">
        <summary class="text-soft" style="cursor:pointer;">У меня есть реферальный код</summary>
        <div class="mt-2">
            <?= $form->field($model, 'referral_code', ['options' => ['class' => 'mb-0']])->textInput([
                'placeholder' => 'Например, IVAN-7K2X',
                'style' => 'text-transform:uppercase',
            ])->label(false) ?>
            <?php if ($model->referral_code): ?>
                <div class="alert alert-success small mt-2 mb-0">
                    <i class="fa-solid fa-gift me-1"></i> Применён код <strong><?= Html::encode($model->referral_code) ?></strong> — вы получите бонус после первой поездки!
                </div>
            <?php endif ?>
        </div>
    </details>

    <div class="d-grid mt-3">
        <?= Html::submitButton('Создать аккаунт', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>

<?php ActiveForm::end() ?>

<p class="text-center text-muted mt-4">
    Уже зарегистрированы? <a href="<?= Url::to(['site/login']) ?>" class="fw-semibold">Войти</a>
</p>
