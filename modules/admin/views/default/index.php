<?php
/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $chartLabels */
/** @var array $chartValues */
/** @var array $topCars */
/** @var app\models\Booking[] $recentBookings */
/** @var app\models\SupportTicket[] $newTickets */

use app\models\Booking;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Дашборд';

// Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);

$labelsJson = json_encode($chartLabels);
$valuesJson = json_encode($chartValues);
?>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="vh-stat">
            <div class="vh-stat__icon"><i class="fa-solid fa-coins"></i></div>
            <div class="vh-stat__label">Доход за день</div>
            <div class="vh-stat__value"><?= Yii::$app->formatter->asCurrency($stats['income_today']) ?></div>
            <div class="vh-stat__change">за неделю: <?= Yii::$app->formatter->asCurrency($stats['income_week']) ?></div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="vh-stat">
            <div class="vh-stat__icon vh-stat__icon--info"><i class="fa-solid fa-key"></i></div>
            <div class="vh-stat__label">Активные аренды</div>
            <div class="vh-stat__value"><?= $stats['active_rentals'] ?></div>
            <div class="vh-stat__change">средняя длительность: <?= $stats['avg_duration_min'] ?> мин</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="vh-stat">
            <div class="vh-stat__icon vh-stat__icon--purple"><i class="fa-solid fa-users"></i></div>
            <div class="vh-stat__label">Пользователи</div>
            <div class="vh-stat__value"><?= $stats['total_users'] ?></div>
            <div class="vh-stat__change">+<?= $stats['new_users_week'] ?> за неделю</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="vh-stat">
            <div class="vh-stat__icon vh-stat__icon--warning"><i class="fa-solid fa-car"></i></div>
            <div class="vh-stat__label">Автопарк</div>
            <div class="vh-stat__value"><?= $stats['available_cars'] ?> / <?= $stats['total_cars'] ?></div>
            <div class="vh-stat__change">доступных / всего</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-chart-area me-2"></i>Доход за 14 дней</div>
            <div class="card-body">
                <div class="chart-wrap"><canvas id="incomeChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-trophy me-2"></i>Топ автомобилей</div>
            <div class="card-body p-0">
                <?php if ($topCars): ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>Авто</th><th class="text-end">Поездок</th></tr></thead>
                            <tbody>
                            <?php foreach ($topCars as $c): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= Html::encode($c['brand'] . ' ' . $c['model']) ?></div>
                                        <div class="small text-soft"><?= Html::encode($c['license_plate']) ?></div>
                                    </td>
                                    <td class="text-end fw-bold"><?= $c['cnt'] ?></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty"><div class="empty__icon"><i class="fa-solid fa-car"></i></div><h6>Пока нет данных</h6></div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-clock me-2"></i>Последние бронирования</span>
                <a href="<?= Url::to(['/admin/booking/index']) ?>" class="small text-decoration-none">Все →</a>
            </div>
            <div class="card-body p-0">
                <?php if ($recentBookings): ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                            <tr><th>Номер</th><th>Пользователь</th><th>Авто</th><th>Статус</th><th class="text-end">Сумма</th><th>Дата</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentBookings as $b): ?>
                                <tr>
                                    <td><a href="<?= Url::to(['/admin/booking/view', 'id' => $b->id]) ?>" class="fw-semibold text-decoration-none"><?= $b->number ?></a></td>
                                    <td><?= Html::encode($b->user->name ?: $b->user->email) ?></td>
                                    <td class="small"><?= Html::encode($b->car->getFullName() . ' · ' . $b->car->license_plate) ?></td>
                                    <td><span class="badge bg-<?= Booking::statusBadge($b->status) ?>"><?= Booking::statusLabel($b->status) ?></span></td>
                                    <td class="text-end fw-bold"><?= Yii::$app->formatter->asCurrency($b->final_cost) ?></td>
                                    <td class="small text-soft" style="white-space:nowrap;"><?= Yii::$app->formatter->asRelativeTime($b->created_at) ?></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty"><div class="empty__icon"><i class="fa-solid fa-key"></i></div><h6>Бронирований нет</h6></div>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-life-ring me-2"></i>Новые обращения</span>
                <a href="<?= Url::to(['/admin/support/index']) ?>" class="small text-decoration-none">Все →</a>
            </div>
            <div class="card-body p-0">
                <?php if ($newTickets): ?>
                    <ul class="list-unstyled m-0">
                        <?php foreach ($newTickets as $t): ?>
                            <li class="border-bottom p-3">
                                <a href="<?= Url::to(['/admin/support/view', 'id' => $t->id]) ?>" class="text-decoration-none text-reset">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold small"><?= Html::encode($t->subject) ?></div>
                                            <div class="small text-soft"><?= Html::encode($t->user->name ?: $t->user->email) ?></div>
                                        </div>
                                        <span class="small text-soft" style="white-space:nowrap;"><?= Yii::$app->formatter->asRelativeTime($t->created_at) ?></span>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php else: ?>
                    <div class="empty"><div class="empty__icon"><i class="fa-solid fa-thumbs-up"></i></div><h6>Нет новых обращений</h6></div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
const ctx = document.getElementById('incomeChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: $labelsJson,
        datasets: [{
            label: 'Доход, ₽',
            data: $valuesJson,
            borderColor: '#00c896',
            backgroundColor: 'rgba(0,200,150,.1)',
            borderWidth: 3,
            fill: true,
            tension: .4,
            pointBackgroundColor: '#00c896',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: {display: false} },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('ru-RU') + ' ₽' }, grid: {color: '#eef2f7'} },
            x: { grid: {display: false} }
        }
    }
});
JS;
$this->registerJs($js);
?>
