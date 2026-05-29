<?php
/** @var app\models\Booking $booking */
/** @var app\models\forms\DamageReportForm $damageForm */

use app\models\DamageReport;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
?>

<div class="modal fade" id="damageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Сообщение о проблеме</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?php $form = ActiveForm::begin([
                'action' => Url::to(['/booking/damage', 'id' => $booking->id]),
                'options' => ['enctype' => 'multipart/form-data'],
                'fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']],
            ]) ?>
                <div class="modal-body">
                    <?= $form->field($damageForm, 'severity')->dropDownList([
                        DamageReport::SEVERITY_MINOR => 'Незначительная',
                        DamageReport::SEVERITY_MODERATE => 'Средняя',
                        DamageReport::SEVERITY_SEVERE => 'Серьёзная',
                    ]) ?>
                    <?= $form->field($damageForm, 'description')->textarea(['rows' => 4, 'placeholder' => 'Опишите подробно, что случилось']) ?>
                    <?= $form->field($damageForm, 'files[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label('Фото повреждений (опционально)') ?>
                    <p class="small text-soft m-0">Если ситуация требует немедленного вмешательства — позвоните в поддержку.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Отмена</button>
                    <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
