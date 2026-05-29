<?php
/** @var yii\web\View $this */
use yii\helpers\Url;
$this->title = 'Активная аренда';
?>
<h1 class="mb-4">Активная аренда</h1>

<div class="card">
    <div class="card-body">
        <div class="empty">
            <div class="empty__icon"><i class="fa-solid fa-car-side"></i></div>
            <h4>У вас нет активных аренд</h4>
            <p>Выберите автомобиль в каталоге или на карте, чтобы начать поездку.</p>
            <div class="d-flex justify-content-center gap-2 mt-4">
                <a href="<?= Url::to(['/car/index']) ?>" class="btn btn-primary"><i class="fa-solid fa-list me-1"></i> Каталог</a>
                <a href="<?= Url::to(['/map/index']) ?>" class="btn btn-soft"><i class="fa-solid fa-map-location-dot me-1"></i> На карте</a>
            </div>
        </div>
    </div>
</div>
