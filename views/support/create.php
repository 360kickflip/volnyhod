<?php
/** @var yii\web\View $this */
/** @var app\models\forms\SupportTicketForm $form */

use app\models\SupportTicket;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Новое обращение';
?>

<nav class="mb-3">
    <a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft"><i class="fa-solid fa-arrow-left me-1"></i> К обращениям</a>
</nav>

<h1 class="mb-4">Новое обращение</h1>

<div class="card" style="max-width: 720px;">
    <div class="card-body p-4">
        <?php $f = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
            <?= $f->field($form, 'category')->dropDownList(SupportTicket::categoryDropdown(), ['prompt' => '— выберите категорию —']) ?>
            <?= $f->field($form, 'subject')->textInput(['placeholder' => 'Кратко о проблеме']) ?>
            <?= $f->field($form, 'message')->textarea(['rows' => 6, 'placeholder' => 'Опишите ситуацию подробно']) ?>
            <?= $f->field($form, 'files[]')->fileInput(['multiple' => true, 'accept' => 'image/*,.pdf'])->label('Прикрепить файлы (опционально)') ?>
            <div class="d-flex gap-2">
                <?= Html::submitButton('<i class="fa-solid fa-paper-plane me-1"></i> Отправить', ['class' => 'btn btn-primary']) ?>
                <a href="<?= Url::to(['index']) ?>" class="btn btn-soft">Отмена</a>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
