<?php
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $status */

use app\models\Review;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
$this->title = 'Отзывы';
?>
<ul class="nav vh-tabs mb-3">
    <?php foreach ([Review::STATUS_PENDING => 'На модерации', Review::STATUS_APPROVED => 'Одобренные', Review::STATUS_REJECTED => 'Отклонённые'] as $s => $label): ?>
        <li class="nav-item">
            <a class="nav-link <?= $status === $s ? 'active' : '' ?>" href="<?= Url::to(['index', 'status' => $s]) ?>"><?= $label ?></a>
        </li>
    <?php endforeach ?>
</ul>

<?php if ($dataProvider->totalCount === 0): ?>
    <div class="card"><div class="card-body"><div class="empty"><div class="empty__icon"><i class="fa-solid fa-star"></i></div><h6>Отзывов нет</h6></div></div></div>
<?php else: ?>
    <div class="row g-3">
        <?php /** @var Review $r */ ?>
        <?php foreach ($dataProvider->getModels() as $r): ?>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><?= Html::encode($r->user->name ?? '—') ?></strong> о <a href="<?= Url::to(['/admin/car/view', 'id' => $r->car_id]) ?>" class="text-decoration-none"><?= Html::encode($r->car->getFullName() ?? '—') ?></a>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-solid fa-star <?= $i <= $r->rating ? '' : 'text-muted' ?>"></i>
                                    <?php endfor ?>
                                </div>
                            </div>
                            <div class="text-soft small"><?= Yii::$app->formatter->asDate($r->created_at) ?></div>
                        </div>
                        <p><?= nl2br(Html::encode($r->text)) ?></p>
                        <?php $photos = $r->getPhotosArray(); if ($photos): ?>
                            <div class="d-flex gap-2 flex-wrap mb-3">
                                <?php foreach ($photos as $p): ?>
                                    <a href="<?= Yii::getAlias('@web/uploads/reviews/' . $p) ?>" target="_blank">
                                        <img src="<?= Yii::getAlias('@web/uploads/reviews/' . $p) ?>" style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                                    </a>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                        <?php if ($status === Review::STATUS_PENDING): ?>
                            <div class="d-flex gap-1">
                                <?= Html::beginForm(['approve', 'id' => $r->id]) ?>
                                    <button class="btn btn-success btn-sm"><i class="fa-solid fa-check me-1"></i> Одобрить</button>
                                <?= Html::endForm() ?>
                                <?= Html::beginForm(['reject', 'id' => $r->id]) ?>
                                    <button class="btn btn-soft btn-sm text-danger"><i class="fa-solid fa-xmark me-1"></i> Отклонить</button>
                                <?= Html::endForm() ?>
                            </div>
                        <?php else: ?>
                            <?= Html::beginForm(['delete', 'id' => $r->id]) ?>
                                <button class="btn btn-soft btn-sm text-danger" onclick="return confirm('Удалить?')"><i class="fa-solid fa-trash me-1"></i> Удалить</button>
                            <?= Html::endForm() ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="d-flex justify-content-center mt-3"><?= LinkPager::widget(['pagination' => $dataProvider->getPagination()]) ?></div>
<?php endif ?>
