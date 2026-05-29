<?php
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $status, $severity */

use app\models\DamageReport;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
$this->title = 'Повреждения';
?>
<form method="get" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (['reported','reviewing','user_liable','not_liable','resolved'] as $s): ?>
                        <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= DamageReport::statusLabel($s) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Серьёзность</label>
                <select name="severity" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (['minor','moderate','severe'] as $s): ?>
                        <option value="<?= $s ?>" <?= $severity === $s ? 'selected' : '' ?>><?= DamageReport::severityLabel($s) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary"><i class="fa-solid fa-filter"></i></button></div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>ID</th><th>Авто</th><th>Пользователь</th><th>Серьёзность</th><th>Статус</th><th>Стоимость</th><th>Дата</th><th></th></tr></thead>
            <tbody>
            <?php /** @var DamageReport $r */ ?>
            <?php foreach ($dataProvider->getModels() as $r): ?>
                <tr>
                    <td>#<?= $r->id ?></td>
                    <td class="small"><?= Html::encode($r->car->getFullName() ?? '—') ?></td>
                    <td class="small"><?= Html::encode($r->user->name ?? $r->user->email ?? '—') ?></td>
                    <td><span class="badge bg-<?= DamageReport::severityBadge($r->severity) ?>"><?= DamageReport::severityLabel($r->severity) ?></span></td>
                    <td><span class="badge bg-secondary"><?= DamageReport::statusLabel($r->status) ?></span></td>
                    <td><?= $r->repair_cost ? Yii::$app->formatter->asCurrency($r->repair_cost) : '—' ?></td>
                    <td class="small text-soft"><?= Yii::$app->formatter->asDate($r->created_at) ?></td>
                    <td><a href="<?= Url::to(['view', 'id' => $r->id]) ?>" class="btn btn-soft btn-sm">Открыть</a></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <?= LinkPager::widget(['pagination' => $dataProvider->getPagination(), 'options' => ['class' => 'pagination pagination-sm m-0']]) ?>
    </div>
</div>
