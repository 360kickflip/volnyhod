<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $from */
/** @var string|null $to */

use app\models\Booking;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Мои поездки';
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="mb-1">Мои поездки</h1>
        <p class="text-soft m-0">Всего поездок: <?= $dataProvider->totalCount ?></p>
    </div>
    <a href="<?= Url::to(['/booking/active']) ?>" class="btn btn-soft"><i class="fa-solid fa-circle-play me-1"></i> Активная</a>
</div>

<form method="get" class="card mb-3">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">С</label>
                <input type="date" name="from" value="<?= Html::encode($from) ?>" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">По</label>
                <input type="date" name="to" value="<?= Html::encode($to) ?>" class="form-control">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1"><i class="fa-solid fa-filter me-1"></i> Применить</button>
                <a href="<?= Url::to(['index']) ?>" class="btn btn-soft">Сбросить</a>
            </div>
        </div>
    </div>
</form>

<?php if ($dataProvider->totalCount === 0): ?>
    <div class="card"><div class="card-body">
        <div class="empty">
            <div class="empty__icon"><i class="fa-solid fa-route"></i></div>
            <h4>Поездок пока нет</h4>
            <p>Завершённые поездки будут отображаться здесь.</p>
            <a href="<?= Url::to(['/car/index']) ?>" class="btn btn-primary mt-2">Выбрать автомобиль</a>
        </div>
    </div></div>
<?php else: ?>
    <div class="row g-3">
        <?php /** @var Booking $b */ ?>
        <?php foreach ($dataProvider->getModels() as $b): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-2">
                            <img src="<?= $b->car->getMainPhotoUrl() ?>" alt="" style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:10px;">
                        </div>
                        <div class="col-md-3">
                            <div class="fw-bold"><?= Html::encode($b->car->getFullName()) ?></div>
                            <div class="small text-soft"><?= Html::encode($b->car->license_plate) ?></div>
                            <div class="small text-soft mt-1">№ <?= $b->number ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-soft small">Дата</div>
                            <div class="fw-semibold"><?= Yii::$app->formatter->asDatetime($b->started_at ?: $b->created_at) ?></div>
                            <div class="text-soft small mt-1">Длительность: <?= floor($b->getDurationMinutes() / 60) ?>ч <?= $b->getDurationMinutes() % 60 ?>м</div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-soft small">Стоимость</div>
                            <div class="h5 m-0"><?= Yii::$app->formatter->asCurrency($b->final_cost) ?></div>
                            <span class="badge bg-<?= Booking::statusBadge($b->status) ?> mt-1"><?= Booking::statusLabel($b->status) ?></span>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="<?= Url::to(['view', 'id' => $b->id]) ?>" class="btn btn-soft btn-sm w-100 mb-1"><i class="fa-solid fa-eye me-1"></i> Детали</a>
                            <?php if ($b->car->status === \app\models\Car::STATUS_AVAILABLE && $b->status === Booking::STATUS_COMPLETED): ?>
                                <a href="<?= Url::to(['/car/view', 'id' => $b->car_id]) ?>" class="btn btn-soft btn-sm w-100 mb-1"><i class="fa-solid fa-rotate me-1"></i> Повторить</a>
                            <?php endif ?>
                            <?php if ($b->canReview()): ?>
                                <a href="<?= Url::to(['view', 'id' => $b->id]) ?>#review" class="btn btn-primary btn-sm w-100"><i class="fa-solid fa-star me-1"></i> Отзыв</a>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </div>

    <?php if ($dataProvider->getPagination()->pageCount > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <?= LinkPager::widget(['pagination' => $dataProvider->getPagination()]) ?>
        </div>
    <?php endif ?>
<?php endif ?>
