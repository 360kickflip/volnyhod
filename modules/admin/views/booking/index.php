<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $filters */

use app\models\Booking;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Бронирования';
?>

<form method="get" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Поиск (номер)</label>
                <input type="search" name="q" class="form-control" value="<?= Html::encode($filters['search'] ?? '') ?>" placeholder="BR-XXXXXX">
            </div>
            <div class="col-md-2">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (Booking::statusLabels() as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">С</label><input type="date" name="from" value="<?= Html::encode($filters['from']) ?>" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">По</label><input type="date" name="to" value="<?= Html::encode($filters['to']) ?>" class="form-control"></div>
            <div class="col-md-3">
                <button class="btn btn-primary me-1"><i class="fa-solid fa-filter"></i></button>
                <a href="<?= Url::to(['index']) ?>" class="btn btn-soft">Сброс</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr>
                    <th>Номер</th><th>Пользователь</th><th>Авто</th><th>Тариф</th><th>Период</th><th>Длит-сть</th><th>Сумма</th><th>Статус</th><th></th>
                </tr></thead>
                <tbody>
                <?php /** @var Booking $b */ ?>
                <?php foreach ($dataProvider->getModels() as $b): ?>
                    <tr>
                        <td><a href="<?= Url::to(['view', 'id' => $b->id]) ?>" class="fw-semibold text-decoration-none"><?= $b->number ?></a></td>
                        <td class="small"><a href="<?= Url::to(['/admin/user/view', 'id' => $b->user_id]) ?>" class="text-decoration-none"><?= Html::encode($b->user->name ?? $b->user->email ?? '—') ?></a></td>
                        <td class="small">
                            <a href="<?= Url::to(['/admin/car/view', 'id' => $b->car_id]) ?>" class="text-decoration-none"><?= Html::encode($b->car->getFullName() ?? '—') ?></a>
                            <div class="text-muted"><?= Html::encode($b->car->license_plate ?? '') ?></div>
                        </td>
                        <td class="small"><?= Html::encode($b->tariff->name ?? '') ?></td>
                        <td class="small text-soft"><?= Yii::$app->formatter->asDate($b->started_at ?: $b->created_at) ?></td>
                        <td class="small"><?= $b->getDurationMinutes() ?> мин</td>
                        <td class="fw-bold"><?= Yii::$app->formatter->asCurrency($b->final_cost) ?></td>
                        <td><span class="badge bg-<?= Booking::statusBadge($b->status) ?>"><?= Booking::statusLabel($b->status) ?></span></td>
                        <td><a href="<?= Url::to(['view', 'id' => $b->id]) ?>" class="btn btn-soft btn-sm"><i class="fa-solid fa-eye"></i></a></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($dataProvider->getPagination()->pageCount > 1): ?>
        <div class="card-footer d-flex justify-content-end">
            <?= LinkPager::widget(['pagination' => $dataProvider->getPagination(), 'options' => ['class' => 'pagination pagination-sm m-0']]) ?>
        </div>
    <?php endif ?>
</div>
