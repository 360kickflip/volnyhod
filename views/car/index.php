<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\Tariff[] $tariffs */
/** @var array $filters */

use app\models\Car;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Каталог автомобилей';
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="mb-1">Каталог</h1>
        <p class="text-soft m-0">Найдено: <?= $dataProvider->totalCount ?> автомобилей</p>
    </div>
    <a href="<?= Url::to(['/map/index']) ?>" class="btn btn-soft"><i class="fa-solid fa-map-location-dot me-1"></i> На карте</a>
</div>

<form method="get" class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Поиск</label>
                <input type="search" name="q" value="<?= Html::encode($filters['search'] ?? '') ?>" class="form-control" placeholder="Марка, модель, номер">
            </div>
            <div class="col-md-3">
                <label class="form-label">Тариф</label>
                <select name="tariff" class="form-select">
                    <option value="">Все</option>
                    <?php foreach ($tariffs as $t): ?>
                        <option value="<?= $t->id ?>" <?= $filters['tariffId'] == $t->id ? 'selected' : '' ?>><?= Html::encode($t->name) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">КПП</label>
                <select name="transmission" class="form-select">
                    <option value="">Все</option>
                    <option value="auto" <?= $filters['transmission'] === 'auto' ? 'selected' : '' ?>>АКПП</option>
                    <option value="manual" <?= $filters['transmission'] === 'manual' ? 'selected' : '' ?>>МКПП</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>Доступны</option>
                    <option value="all" <?= ($filters['status'] ?? '') === 'all' ? 'selected' : '' ?>>Все</option>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-primary"><i class="fa-solid fa-filter"></i></button>
            </div>
        </div>
    </div>
</form>

<?php if ($dataProvider->totalCount === 0): ?>
    <div class="card">
        <div class="card-body">
            <div class="empty">
                <div class="empty__icon"><i class="fa-solid fa-car"></i></div>
                <h4>Ничего не найдено</h4>
                <p>Попробуйте изменить параметры фильтра.</p>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($dataProvider->getModels() as $car): /** @var Car $car */ ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= Url::to(['/car/view', 'id' => $car->id]) ?>" class="text-decoration-none text-reset">
                    <article class="car-card">
                        <div class="car-card__media">
                            <img src="<?= $car->getMainPhotoUrl() ?>" alt="<?= Html::encode($car->getFullName()) ?>">
                            <span class="car-card__status">
                                <span class="badge bg-<?= Car::statusBadge($car->status) ?>"><?= Car::statusLabel($car->status) ?></span>
                            </span>
                            <span class="car-card__tariff"><?= Html::encode($car->tariff->name ?? '') ?></span>
                        </div>
                        <div class="car-card__body">
                            <h6 class="car-card__title"><?= Html::encode($car->getFullName()) ?> <span class="text-muted small fw-normal"><?= $car->year ?></span></h6>
                            <div class="car-card__plate"><?= Html::encode($car->license_plate) ?></div>
                            <div class="car-card__features">
                                <span><i class="fa-solid fa-gear"></i> <?= $car->transmission === 'auto' ? 'АКПП' : 'МКПП' ?></span>
                                <span><i class="fa-solid fa-users"></i> <?= $car->seats ?></span>
                                <span><i class="fa-solid fa-gas-pump"></i> <?= $car->fuel_level ?>%</span>
                                <span><i class="fa-solid fa-route"></i> <?= number_format($car->mileage, 0, '.', ' ') ?></span>
                            </div>
                            <div class="car-card__price">
                                <span class="num"><?= Yii::$app->formatter->asDecimal($car->tariff->price_per_minute ?? 0, 2) ?></span>
                                <span class="unit">₽/мин</span>
                                <span class="text-muted small ms-auto">+ <?= Yii::$app->formatter->asDecimal($car->tariff->price_per_km ?? 0, 2) ?> ₽/км</span>
                            </div>
                        </div>
                    </article>
                </a>
            </div>
        <?php endforeach ?>
    </div>

    <?php if ($dataProvider->getPagination()->pageCount > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <?= LinkPager::widget(['pagination' => $dataProvider->getPagination()]) ?>
        </div>
    <?php endif ?>
<?php endif ?>
