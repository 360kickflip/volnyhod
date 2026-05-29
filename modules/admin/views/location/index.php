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
$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);
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
const tmap = L.map('track-map').setView([{$center['lat']}, {$center['lng']}], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OpenStreetMap'}).addTo(tmap);
const points = $pointsJson;
if (points.length > 1) {
    const latlngs = points.map(p => [p.lat, p.lng]);
    const line = L.polyline(latlngs, {color:'#00c896', weight: 4}).addTo(tmap);
    tmap.fitBounds(line.getBounds(), {padding: [40, 40]});
    L.marker(latlngs[0]).bindPopup('Старт').addTo(tmap);
    L.marker(latlngs[latlngs.length - 1]).bindPopup('Финиш').addTo(tmap);
} else if (points.length === 1) {
    L.marker([points[0].lat, points[0].lng]).addTo(tmap);
}
JS);
    ?>
<?php endif ?>
