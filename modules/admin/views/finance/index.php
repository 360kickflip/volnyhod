<?php
/** @var string $from, $to */
/** @var array $labels, $values */
/** @var float $totalIncome */
/** @var array $byTariff */
/** @var array $byCar */

use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Финансы и отчёты';
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);

$labelsJson = json_encode($labels);
$valuesJson = json_encode($values);
$tariffLabels = json_encode(array_map(fn($r) => $r['name'] ?? '—', $byTariff));
$tariffValues = json_encode(array_map(fn($r) => (float)$r['revenue'], $byTariff));
$tariffColors = json_encode(array_map(fn($r) => $r['color'] ?? '#0d6efd', $byTariff));
?>

<form method="get" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label">С</label><input type="date" name="from" value="<?= Html::encode($from) ?>" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">По</label><input type="date" name="to" value="<?= Html::encode($to) ?>" class="form-control"></div>
            <div class="col-md-3"><button class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i> Применить</button></div>
            <div class="col-md-3 text-end"><a href="<?= Url::to(['export', 'from' => $from, 'to' => $to]) ?>" class="btn btn-soft"><i class="fa-solid fa-file-csv me-1"></i> Экспорт CSV</a></div>
        </div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="vh-stat">
            <div class="vh-stat__icon"><i class="fa-solid fa-coins"></i></div>
            <div class="vh-stat__label">Доход за период</div>
            <div class="vh-stat__value"><?= Yii::$app->formatter->asCurrency($totalIncome) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="vh-stat">
            <div class="vh-stat__icon vh-stat__icon--info"><i class="fa-solid fa-key"></i></div>
            <div class="vh-stat__label">Поездок</div>
            <div class="vh-stat__value"><?= array_sum(array_map(fn($r) => $r['cnt'], $byTariff)) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="vh-stat">
            <div class="vh-stat__icon vh-stat__icon--purple"><i class="fa-solid fa-chart-pie"></i></div>
            <div class="vh-stat__label">Дней в периоде</div>
            <div class="vh-stat__value"><?= count($labels) ?></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">Доход по дням</div>
            <div class="card-body"><div class="chart-wrap"><canvas id="chart1"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">Доля по тарифам</div>
            <div class="card-body"><div class="chart-wrap" style="height: 300px;"><canvas id="chart2"></canvas></div></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Топ-10 авто по доходу</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Авто</th><th>Гос.номер</th><th class="text-end">Поездок</th><th class="text-end">Доход</th></tr></thead>
            <tbody>
            <?php foreach ($byCar as $c): ?>
                <tr>
                    <td class="fw-semibold"><?= Html::encode(($c['brand'] ?? '') . ' ' . ($c['model'] ?? '')) ?></td>
                    <td><code><?= Html::encode($c['license_plate'] ?? '—') ?></code></td>
                    <td class="text-end"><?= $c['cnt'] ?></td>
                    <td class="text-end fw-bold"><?= Yii::$app->formatter->asCurrency($c['revenue']) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->registerJs(<<<JS
new Chart(document.getElementById('chart1'), {
    type: 'bar',
    data: { labels: $labelsJson, datasets: [{ label: 'Доход', data: $valuesJson, backgroundColor: '#00c896', borderRadius: 6 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: {display: false} }, scales: { y: {beginAtZero: true} } }
});
new Chart(document.getElementById('chart2'), {
    type: 'doughnut',
    data: { labels: $tariffLabels, datasets: [{ data: $tariffValues, backgroundColor: $tariffColors }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: {position: 'bottom'} } }
});
JS); ?>
