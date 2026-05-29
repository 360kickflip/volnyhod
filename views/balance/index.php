<?php
/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\forms\TopUpForm $form */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $currentType */

use app\models\Transaction;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Баланс и платежи';

$types = [
    '' => 'Все операции',
    Transaction::TYPE_TOPUP => 'Пополнения',
    Transaction::TYPE_RENTAL_CHARGE => 'Списания за аренду',
    Transaction::TYPE_DEPOSIT_HOLD => 'Заморозка депозита',
    Transaction::TYPE_DEPOSIT_RELEASE => 'Возврат депозита',
    Transaction::TYPE_REFUND => 'Возвраты',
    Transaction::TYPE_BONUS => 'Бонусы',
    Transaction::TYPE_PENALTY => 'Штрафы',
];

$available = $user->getAvailableBalance();
?>

<h1 class="mb-4">Баланс и платежи</h1>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="vh-stat h-100">
            <div class="vh-stat__icon"><i class="fa-solid fa-wallet"></i></div>
            <div class="vh-stat__label">Доступный баланс</div>
            <div class="vh-stat__value"><?= Yii::$app->formatter->asCurrency($available) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="vh-stat h-100">
            <div class="vh-stat__icon vh-stat__icon--info"><i class="fa-solid fa-coins"></i></div>
            <div class="vh-stat__label">Общий баланс</div>
            <div class="vh-stat__value"><?= Yii::$app->formatter->asCurrency($user->balance) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="vh-stat h-100">
            <div class="vh-stat__icon vh-stat__icon--warning"><i class="fa-solid fa-lock"></i></div>
            <div class="vh-stat__label">
                Заморожено
                <i class="fa-solid fa-circle-info text-muted small ms-1" data-bs-toggle="tooltip" title="Эти средства удерживаются во время аренды как депозит. Они вернутся на баланс после завершения поездки."></i>
            </div>
            <div class="vh-stat__value"><?= Yii::$app->formatter->asCurrency($user->locked_balance) ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 90px;">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="fa-solid fa-plus-circle me-2 text-primary"></i>Пополнить баланс</h5>
                <?php $f = ActiveForm::begin(['fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
                    <?= $f->field($form, 'amount')->input('number', ['min' => 100, 'max' => 100000, 'step' => 100, 'placeholder' => '500']) ?>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <?php foreach ([500, 1000, 2000, 5000] as $v): ?>
                            <button type="button" class="btn btn-soft btn-sm" onclick="document.getElementById('topupform-amount').value = <?= $v ?>"><?= $v ?> ₽</button>
                        <?php endforeach ?>
                    </div>
                    <?= $f->field($form, 'payment_method')->radioList([
                        'card' => '<i class="fa-solid fa-credit-card me-1"></i> Банковская карта',
                        'sbp' => '<i class="fa-solid fa-mobile-screen me-1"></i> СБП',
                    ], [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            return '<label class="d-flex align-items-center gap-2 p-2 mb-2 border rounded-3 cursor-pointer">'
                                . Html::radio($name, $checked, ['value' => $value, 'class' => 'form-check-input m-0'])
                                . '<span>' . $label . '</span></label>';
                        }
                    ]) ?>
                    <div class="d-grid mt-3">
                        <?= Html::submitButton('<i class="fa-solid fa-bolt me-2"></i>Пополнить', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <p class="small text-soft text-center mt-3 mb-0">Демо-режим: оплата засчитывается мгновенно.</p>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="m-0"><i class="fa-solid fa-list me-2"></i>История транзакций</h5>
                <select class="form-select form-select-sm" style="max-width: 220px" onchange="window.location='?type='+this.value">
                    <?php foreach ($types as $k => $label): ?>
                        <option value="<?= $k ?>" <?= $currentType === $k ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="card-body p-0">
                <?php if ($dataProvider->totalCount === 0): ?>
                    <div class="empty">
                        <div class="empty__icon"><i class="fa-solid fa-receipt"></i></div>
                        <h4>Нет транзакций</h4>
                        <p>Здесь будут отображаться все операции по балансу.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Тип</th>
                                <th>Описание</th>
                                <th class="text-end">Сумма</th>
                                <th class="text-end">Статус</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php /** @var Transaction $tx */ ?>
                            <?php foreach ($dataProvider->getModels() as $tx): ?>
                                <tr>
                                    <td class="small text-soft" style="white-space: nowrap;">
                                        <?= Yii::$app->formatter->asDate($tx->created_at) ?><br>
                                        <small class="text-muted"><?= Yii::$app->formatter->asTime($tx->created_at) ?></small>
                                    </td>
                                    <td><i class="fa-solid <?= Transaction::typeIcon($tx->type) ?> me-2"></i><?= Transaction::typeLabel($tx->type) ?></td>
                                    <td class="small"><?= Html::encode($tx->description) ?></td>
                                    <td class="text-end fw-bold <?= $tx->amount > 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= ($tx->amount > 0 ? '+' : '') . Yii::$app->formatter->asCurrency($tx->amount) ?>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-<?= Transaction::statusBadge($tx->status) ?>"><?= Transaction::statusLabel($tx->status) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>
            </div>
            <?php if ($dataProvider->getPagination()->pageCount > 1): ?>
                <div class="card-footer d-flex justify-content-end">
                    <?= LinkPager::widget([
                        'pagination' => $dataProvider->getPagination(),
                        'options' => ['class' => 'pagination pagination-sm m-0'],
                    ]) ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
[].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]')).map(el => new bootstrap.Tooltip(el));
");
?>
