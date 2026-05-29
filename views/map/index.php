<?php
/** @var yii\web\View $this */
/** @var app\models\Tariff[] $tariffs */

use yii\helpers\Url;
use yii\helpers\Json;

$this->title = 'Карта автомобилей';

// Leaflet (без API ключа)
$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', ['integrity' => 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=', 'crossorigin' => '']);
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END, 'integrity' => 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=', 'crossorigin' => '']);

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

<div class="row g-3">
    <div class="col-lg-9">
        <div id="vh-map" class="vh-map vh-map--lg"></div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Легенда</h6>
                <div class="d-flex flex-column gap-2 small">
                    <?php foreach ($tariffs as $t): ?>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle" style="width:14px;height:14px;background: <?= $t->color ?>;"></span>
                            <?= htmlspecialchars($t->name) ?>
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
const map = L.map('vh-map').setView([{$center['lat']}, {$center['lng']}], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
    maxZoom: 19
}).addTo(map);

fetch('{$apiUrl}')
    .then(r => r.json())
    .then(cars => {
        document.getElementById('cars-count').textContent = cars.length;
        cars.forEach(car => {
            const icon = L.divIcon({
                className: 'vh-car-marker',
                html: `<div style="background:\${car.tariffColor};color:#fff;border-radius:14px;padding:5px 10px;font-weight:700;font-size:12px;box-shadow:0 4px 12px rgba(0,0,0,.2);white-space:nowrap;border:2px solid #fff;"><i class="fa-solid fa-car-side"></i> \${car.pricePerMinute.toFixed(2)}</div>`,
                iconSize: [60, 30],
                iconAnchor: [30, 15]
            });
            const m = L.marker([car.lat, car.lng], { icon }).addTo(map);
            m.bindPopup(`
                <div style="width:240px;font-family:Inter,sans-serif;">
                    <img src="\${car.photo}" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:8px;">
                    <div style="font-weight:700;font-size:1rem;">\${car.brand} \${car.model}</div>
                    <div style="color:#6b7689;font-size:.85rem;font-family:monospace;">\${car.plate}</div>
                    <div style="margin:.5rem 0;">
                        <span style="background:\${car.tariffColor}20;color:\${car.tariffColor};padding:2px 8px;border-radius:6px;font-size:.75rem;font-weight:600;">\${car.tariff}</span>
                    </div>
                    <div style="font-size:.85rem;color:#6b7689;">\${car.address || ''}</div>
                    <div style="margin-top:.5rem;font-weight:700;color:#0a1628;">\${car.pricePerMinute.toFixed(2)} ₽/мин · топливо \${car.fuelLevel}%</div>
                    <a href="\${car.url}" style="display:block;margin-top:8px;text-align:center;background:#00c896;color:#fff;padding:8px;border-radius:8px;text-decoration:none;font-weight:600;">Подробнее</a>
                </div>
            `);
        });
    });
JS;
$this->registerJs($js);
?>
