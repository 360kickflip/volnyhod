<?php
/** @var yii\web\View $this */
/** @var app\models\Tariff[] $tariffs */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Карта автомобилей';

$apiKey = Yii::$app->params['yandexMapsApiKey'] ?? '';
$ymUrl = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU' . ($apiKey ? '&apikey=' . urlencode($apiKey) : '');
$this->registerJsFile($ymUrl, ['position' => \yii\web\View::POS_END]);

$center = Yii::$app->params['defaultCenter'];
$apiUrl = Url::to(['/map/cars']);
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <h1 class="mb-1"><i class="fa-solid fa-map-location-dot me-2"></i>Карта</h1>
        <p class="text-soft m-0">Все доступные автомобили рядом с вами</p>
    </div>
    <a href="<?= Url::to(['/car/index']) ?>" class="btn btn-soft"><i class="fa-solid fa-list me-1"></i> В каталог</a>
</div>

<?php if (!$apiKey && !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()): ?>
    <div class="alert alert-warning d-flex align-items-start gap-2">
        <i class="fa-solid fa-triangle-exclamation mt-1"></i>
        <div>
            <strong>API-ключ Яндекс.Карт не задан.</strong> Карта будет работать с ограничениями.
            Получите ключ на <a href="https://developer.tech.yandex.ru/services/" target="_blank" class="alert-link">developer.tech.yandex.ru</a>
            и добавьте его в <a href="<?= Url::to(['/admin/setting', 'group' => 'maps']) ?>" class="alert-link">настройках админки</a>.
        </div>
    </div>
<?php endif ?>

<div class="row g-3">
    <div class="col-lg-9">
        <div id="vh-map" class="vh-map vh-map--lg"></div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Тарифы</h6>
                <div class="d-flex flex-column gap-2 small">
                    <?php foreach ($tariffs as $t): ?>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle" style="width:14px;height:14px;background: <?= Html::encode($t->color) ?>;"></span>
                            <?= Html::encode($t->name) ?>
                            <span class="ms-auto text-soft"><?= Yii::$app->formatter->asDecimal($t->price_per_minute, 2) ?> ₽/мин</span>
                        </div>
                    <?php endforeach ?>
                </div>
                <hr>
                <p class="small text-soft m-0">Кликните на маркер, чтобы увидеть детали и забронировать.</p>
            </div>
        </div>

        <div id="map-counter" class="card mt-3">
            <div class="card-body text-center">
                <div class="text-soft small">На карте</div>
                <div class="h3 fw-bold m-0" id="cars-count">…</div>
                <div class="text-soft small">автомобилей</div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
ymaps.ready(function () {
    const map = new ymaps.Map('vh-map', {
        center: [{$center['lat']}, {$center['lng']}],
        zoom: 12,
        controls: ['zoomControl', 'geolocationControl', 'typeSelector']
    });

    fetch('{$apiUrl}')
        .then(r => r.json())
        .then(cars => {
            document.getElementById('cars-count').textContent = cars.length;
            const cluster = new ymaps.Clusterer({
                preset: 'islands#greenClusterIcons',
                groupByCoordinates: false,
                clusterDisableClickZoom: false
            });
            const placemarks = cars.map(car => {
                const placemark = new ymaps.Placemark(
                    [car.lat, car.lng],
                    {
                        balloonContentHeader: '<div style="font-weight:700;">' + car.brand + ' ' + car.model + '</div>',
                        balloonContentBody:
                            '<img src="' + car.photo + '" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:8px;">' +
                            '<div style="color:#6b7689;font-size:.85rem;font-family:monospace;">' + car.plate + '</div>' +
                            '<div style="margin:.4rem 0;"><span style="background:' + car.tariffColor + '20;color:' + car.tariffColor + ';padding:2px 8px;border-radius:6px;font-size:.75rem;font-weight:600;">' + car.tariff + '</span></div>' +
                            '<div style="font-size:.85rem;color:#6b7689;">' + (car.address || '') + '</div>' +
                            '<div style="margin-top:.5rem;font-weight:700;">' + car.pricePerMinute.toFixed(2) + ' ₽/мин · топливо ' + car.fuelLevel + '%</div>',
                        balloonContentFooter: '<a href="' + car.url + '" style="display:block;margin-top:8px;text-align:center;background:#00c896;color:#fff;padding:8px;border-radius:8px;text-decoration:none;font-weight:600;">Подробнее</a>',
                        hintContent: car.brand + ' ' + car.model + ' · ' + car.pricePerMinute.toFixed(2) + ' ₽/мин'
                    },
                    {
                        iconLayout: 'default#imageWithContent',
                        iconImageHref: 'data:image/svg+xml;utf8,' + encodeURIComponent(
                            '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="34" viewBox="0 0 60 34"><rect x="1" y="1" width="58" height="28" rx="14" fill="' + car.tariffColor + '" stroke="#fff" stroke-width="2"/><polygon points="26,30 30,34 34,30" fill="' + car.tariffColor + '"/></svg>'
                        ),
                        iconImageSize: [60, 34],
                        iconImageOffset: [-30, -34],
                        iconContentOffset: [8, 7],
                        iconContentLayout: ymaps.templateLayoutFactory.createClass(
                            '<div style="color:#fff;font-weight:700;font-size:11px;text-align:center;width:44px;">' + car.pricePerMinute.toFixed(0) + ' ₽</div>'
                        )
                    }
                );
                return placemark;
            });
            cluster.add(placemarks);
            map.geoObjects.add(cluster);
        })
        .catch(err => console.error('Не удалось загрузить авто:', err));
});
JS;
$this->registerJs($js);
?>
