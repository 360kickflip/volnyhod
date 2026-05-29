<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $status */
/** @var string|null $search */

use app\models\Car;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Автомобили';
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <p class="text-soft m-0">Всего: <?= $dataProvider->totalCount ?></p>
    <a href="<?= Url::to(['create']) ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Добавить</a>
</div>

<form method="get" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Поиск</label>
                <input type="search" name="q" class="form-control" value="<?= Html::encode($search) ?>" placeholder="Марка, модель, номер">
            </div>
            <div class="col-md-3">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (Car::statusLabels() as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $status === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Найти</button>
            </div>
            <div class="col-md-2">
                <a href="<?= Url::to(['index']) ?>" class="btn btn-soft w-100">Сброс</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr>
                    <th>Фото</th><th>Марка / модель</th><th>Гос. номер</th><th>Тариф</th><th>Топливо</th><th>Пробег</th><th>Статус</th><th></th>
                </tr></thead>
                <tbody>
                <?php /** @var Car $car */ ?>
                <?php foreach ($dataProvider->getModels() as $car): ?>
                    <tr>
                        <td><img src="<?= $car->getMainPhotoUrl() ?>" alt="" style="width:64px;height:48px;object-fit:cover;border-radius:8px;"></td>
                        <td>
                            <div class="fw-semibold"><?= Html::encode($car->getFullName()) ?></div>
                            <div class="small text-soft"><?= $car->year ?> · <?= Html::encode($car->color) ?></div>
                        </td>
                        <td><code><?= Html::encode($car->license_plate) ?></code></td>
                        <td><span class="badge badge-soft-primary"><?= Html::encode($car->tariff->name ?? '—') ?></span></td>
                        <td>
                            <div style="width:60px;"><div class="fuel-bar"><div class="fuel-bar__fill" style="width: <?= $car->fuel_level ?>%;"></div></div></div>
                            <small class="text-soft"><?= $car->fuel_level ?>%</small>
                        </td>
                        <td class="small"><?= number_format($car->mileage, 0, '.', ' ') ?> км</td>
                        <td><span class="badge bg-<?= Car::statusBadge($car->status) ?>"><?= Car::statusLabel($car->status) ?></span></td>
                        <td class="text-end" style="white-space:nowrap;">
                            <a href="<?= Url::to(['view', 'id' => $car->id]) ?>" class="btn btn-soft btn-sm"><i class="fa-solid fa-eye"></i></a>
                            <a href="<?= Url::to(['update', 'id' => $car->id]) ?>" class="btn btn-soft btn-sm"><i class="fa-solid fa-pen"></i></a>
                            <?= Html::beginForm(['delete', 'id' => $car->id], 'post', ['class' => 'd-inline']) ?>
                                <button class="btn btn-soft btn-sm text-danger" onclick="return confirm('Удалить?')"><i class="fa-solid fa-trash"></i></button>
                            <?= Html::endForm() ?>
                        </td>
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
