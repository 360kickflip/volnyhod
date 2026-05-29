<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\models\PromoCode;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Промокоды';
?>
<div class="d-flex justify-content-between mb-3">
    <p class="text-soft m-0">Всего: <?= $dataProvider->totalCount ?></p>
    <a href="<?= Url::to(['create']) ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Новый промокод</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Код</th><th>Описание</th><th>Тип</th><th>Значение</th><th>Использований</th><th>Срок</th><th>Активен</th><th></th></tr></thead>
                <tbody>
                <?php /** @var PromoCode $p */ ?>
                <?php foreach ($dataProvider->getModels() as $p): ?>
                    <tr>
                        <td><code class="fs-6 fw-bold"><?= Html::encode($p->code) ?></code></td>
                        <td class="small"><?= Html::encode($p->description) ?></td>
                        <td><?= $p->type === 'percent' ? '%' : '₽' ?></td>
                        <td class="fw-bold"><?= $p->type === 'percent' ? $p->value . '%' : Yii::$app->formatter->asCurrency($p->value) ?></td>
                        <td><?= $p->usage_count ?><?= $p->usage_limit ? ' / ' . $p->usage_limit : '' ?></td>
                        <td class="small">
                            <?php if ($p->valid_to): ?>
                                до <?= Yii::$app->formatter->asDate($p->valid_to) ?>
                            <?php else: ?>
                                бессрочно
                            <?php endif ?>
                        </td>
                        <td>
                            <?= Html::beginForm(['toggle', 'id' => $p->id]) ?>
                                <button class="btn btn-sm <?= $p->is_active ? 'btn-success' : 'btn-soft' ?>"><?= $p->is_active ? 'Активен' : 'Выкл' ?></button>
                            <?= Html::endForm() ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= Url::to(['update', 'id' => $p->id]) ?>" class="btn btn-soft btn-sm"><i class="fa-solid fa-pen"></i></a>
                            <?= Html::beginForm(['delete', 'id' => $p->id], 'post', ['class' => 'd-inline']) ?>
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
