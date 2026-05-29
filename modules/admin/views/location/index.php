<?php
/** @var app\models\Car[] $cars */
/** @var app\models\Car|null $car */
/** @var int|null $carId */
/** @var string|null $from, $to */
/** @var yii\data\ActiveDataProvider|null $dataProvider */
/** @var array $points */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
$this->title = 'История локаций';

$apiKey = Yii::$app->params['yandexMapsApiKey'] ?? '';
$ymUrl = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU' . ($apiKey ? '&apikey=' . urlencode($apiKey) : '');
$this->registerJsFile($ymUrl, ['position' => \yii\web\View::POS_END]);
?>

<form method="get" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4"><label class="form-label">Автомобиль</label>
                <select name="car_id" class="form-select" required>
                    <option value="">— выберите авто —</option>
                    <?php foreach ($cars as $c): ?>
                        <option value="<?= $c->id ?>" <?= $carId == $c->id ? 'selected' : '' ?>><?= Html::encode($c->getFullName() . ' · ' . $c->license_plate) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">С</label><input type="date" name="from" value="<?= Html::encode($from) ?>" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">По</label><input type="date" name="to" value="<?= Html::encode($to) ?>" class="form-control"></div>
            <div class="col-md-2"><button class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i> Показать</button></div>
        </div>
    </div>
</form>

<?php if (!$car): ?>
    <div class="card"><div class="card-body"><div class="empty"><div class="empty__icon"><i class="fa-solid fa-map-location"></i></div><h6>Выберите автомобиль для просмотра трека</h6></div></div></div>
<?php else: ?>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><?= Html::encode($car->getFullName() . ' · ' . $car->license_plate) ?></div>
                <div class="card-body p-0">
                    <div id="track-map" style="height: 480px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Записи (<?= $dataProvider->totalCount ?>)</div>
                <div class="card-body p-0">
                    <table class="table mb-0 small">
                        <thead><tr><th>Время</th><th>Скорость</th><th>Пробег</th></tr></thead>
                        <tbody>
                        <?php foreach ($dataProvider->getModels() as $h): ?>
                            <tr>
                                <td><?= Yii::$app->formatter->asDatetime($h->recorded_at) ?></td>
                                <td><?= $h->speed ?> км/ч</td>
                                <td><?= number_format($h->mileage ?: 0, 0, '.', ' ') ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($dataProvider->getPagination()->pageCount > 1): ?>
                    <div class="card-footer d-flex justify-content-end">
                        <?= LinkPager::widget(['pagination' => $dataProvider->getPagination(), 'options' => ['class' => 'pagination pagination-sm m-0']]) ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?php
    $pointsJson = json_encode($points);
    $center = $points[0] ?? ['lat' => $car->lat ?: 55.7558, 'lng' => $car->lng ?: 37.6173];
    $this->registerJs(<<<JS
ymaps.ready(function () {
    const tmap = new ymaps.Map('track-map', {
        center: [{$center['lat']}, {$center['lng']}],
        zoom: 12,
        controls: ['zoomControl', 'typeSelector']
    });
    const points = $pointsJson;
    if (points.length > 1) {
        const coords = points.map(p => [p.lat, p.lng]);
        const polyline = new ymaps.Polyline(coords, {}, {
            strokeColor: '#00c896',
            strokeWidth: 4,
            strokeOpacity: 0.85
        });
        tmap.geoObjects.add(polyline);
        tmap.geoObjects.add(new ymaps.Placemark(coords[0], { balloonContent: 'Старт' }, { preset: 'islands#greenCircleDotIcon' }));
        tmap.geoObjects.add(new ymaps.Placemark(coords[coords.length - 1], { balloonContent: 'Финиш' }, { preset: 'islands#redCircleDotIcon' }));
        tmap.setBounds(polyline.geometry.getBounds(), { checkZoomRange: true, zoomMargin: 30 });
    } else if (points.length === 1) {
        tmap.geoObjects.add(new ymaps.Placemark([points[0].lat, points[0].lng]));
    }
});
JS);
    ?>
<?php endif ?>
