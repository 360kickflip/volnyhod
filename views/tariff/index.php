<?php
/** @var yii\web\View $this */
/** @var app\models\Tariff[] $tariffs */
/** @var array $carCounts */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Тарифы';
$this->params['meta_description'] = 'Тарифы каршеринга Вольный Ход — поминутная аренда автомобилей в Москве';
?>

<section class="vh-hero mt-3 mb-5" style="padding: 3rem 2rem;">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge badge-soft-primary mb-3" style="background: rgba(255,255,255,.12); color:#fff;">
                <i class="fa-solid fa-tags me-1"></i> Прозрачные тарифы
            </span>
            <h1 class="mb-3">Платите только за то,<br><span class="grad-text" style="-webkit-text-fill-color: transparent;">сколько ездите</span></h1>
            <p class="lead mb-0" style="color: rgba(255,255,255,.85)">
                Поминутная тарификация. Без скрытых платежей. Депозит замораживается на время аренды и возвращается после.
            </p>
        </div>
        <div class="col-lg-5 d-none d-lg-block text-center">
            <i class="fa-solid fa-coins" style="font-size: 10rem; color: rgba(255,255,255,.1)"></i>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="row g-4">
        <?php foreach ($tariffs as $i => $tariff): ?>
            <?php
            $isPopular = $i === 1; // выделяем второй тариф как популярный
            ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 <?= $isPopular ? 'shadow-lg border-2' : '' ?>" style="<?= $isPopular ? 'border-color: ' . Html::encode($tariff->color) . '; transform: translateY(-4px);' : '' ?>">
                    <?php if ($isPopular): ?>
                        <div class="text-center" style="background: <?= Html::encode($tariff->color) ?>; color:#fff; padding: 4px 0; font-size: .8rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;">
                            ★ Популярный
                        </div>
                    <?php endif ?>
                    <div class="card-body p-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;border-radius:16px;background: <?= Html::encode($tariff->color) ?>20; color: <?= Html::encode($tariff->color) ?>;">
                            <i class="fa-solid <?= Html::encode($tariff->icon ?: 'fa-car') ?> fa-xl"></i>
                        </div>
                        <h3 class="card-title mb-1"><?= Html::encode($tariff->name) ?></h3>
                        <p class="text-soft small mb-4" style="min-height:2.4em;"><?= Html::encode($tariff->description) ?></p>

                        <div class="d-flex align-items-baseline gap-1 mb-1">
                            <span class="display-6 fw-bold" style="letter-spacing: -.02em;"><?= Yii::$app->formatter->asDecimal($tariff->price_per_minute, 2) ?></span>
                            <span class="text-muted">₽/мин</span>
                        </div>
                        <div class="text-muted small mb-4">
                            <i class="fa-solid fa-route me-1"></i> + <?= Yii::$app->formatter->asDecimal($tariff->price_per_km, 2) ?> ₽/км
                            <?php if ($tariff->price_per_hour): ?>
                                <br><i class="fa-solid fa-clock me-1"></i> или <?= Yii::$app->formatter->asDecimal($tariff->price_per_hour, 2) ?> ₽/час
                            <?php endif ?>
                            <?php if ($tariff->price_per_day): ?>
                                <br><i class="fa-solid fa-calendar-day me-1"></i> или <?= Yii::$app->formatter->asDecimal($tariff->price_per_day, 2) ?> ₽/сутки
                            <?php endif ?>
                        </div>

                        <hr>

                        <ul class="list-unstyled small mb-4">
                            <li class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>Депозит <?= Yii::$app->formatter->asCurrency($tariff->deposit) ?></li>
                            <li class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>Страховка включена</li>
                            <li class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>Заправка по необходимости</li>
                            <li class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>Парковка в зоне</li>
                            <li class="mb-2 text-soft">
                                <i class="fa-solid fa-car me-2 text-muted"></i>
                                <?= $carCounts[$tariff->id] ?? 0 ?> авто на линии
                            </li>
                        </ul>

                        <a href="<?= Url::to(['/car/index', 'tariff' => $tariff->id]) ?>" class="btn <?= $isPopular ? 'btn-primary' : 'btn-soft' ?> w-100">
                            Смотреть автомобили
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>

<!-- Сравнительная таблица -->
<section class="mb-5">
    <h2 class="section-title">Сравнение тарифов</h2>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            <?php foreach ($tariffs as $t): ?>
                                <th class="text-center" style="color: <?= Html::encode($t->color) ?>;">
                                    <?= Html::encode($t->name) ?>
                                </th>
                            <?php endforeach ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">Стоимость минуты</td>
                            <?php foreach ($tariffs as $t): ?>
                                <td class="text-center"><?= Yii::$app->formatter->asDecimal($t->price_per_minute, 2) ?> ₽</td>
                            <?php endforeach ?>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Стоимость километра</td>
                            <?php foreach ($tariffs as $t): ?>
                                <td class="text-center"><?= Yii::$app->formatter->asDecimal($t->price_per_km, 2) ?> ₽</td>
                            <?php endforeach ?>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Час аренды</td>
                            <?php foreach ($tariffs as $t): ?>
                                <td class="text-center"><?= $t->price_per_hour ? Yii::$app->formatter->asDecimal($t->price_per_hour, 2) . ' ₽' : '—' ?></td>
                            <?php endforeach ?>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Сутки</td>
                            <?php foreach ($tariffs as $t): ?>
                                <td class="text-center"><?= $t->price_per_day ? Yii::$app->formatter->asCurrency($t->price_per_day) : '—' ?></td>
                            <?php endforeach ?>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Депозит</td>
                            <?php foreach ($tariffs as $t): ?>
                                <td class="text-center"><?= Yii::$app->formatter->asCurrency($t->deposit) ?></td>
                            <?php endforeach ?>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Просрочка/мин</td>
                            <?php foreach ($tariffs as $t): ?>
                                <td class="text-center"><?= Yii::$app->formatter->asDecimal($t->overdue_per_minute, 2) ?> ₽</td>
                            <?php endforeach ?>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Авто на линии</td>
                            <?php foreach ($tariffs as $t): ?>
                                <td class="text-center"><span class="badge bg-secondary"><?= $carCounts[$t->id] ?? 0 ?></span></td>
                            <?php endforeach ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Калькулятор -->
<section class="mb-5">
    <div class="card">
        <div class="card-body p-4 p-md-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <h3 class="mb-2"><i class="fa-solid fa-calculator me-2 text-primary"></i>Калькулятор</h3>
                    <p class="text-soft">Посчитайте примерную стоимость поездки по любому тарифу.</p>
                </div>
                <div class="col-lg-7">
                    <div class="row g-2 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small">Тариф</label>
                            <select id="calc-tariff" class="form-select">
                                <?php foreach ($tariffs as $t): ?>
                                    <option value="<?= $t->id ?>"
                                            data-min="<?= $t->price_per_minute ?>"
                                            data-km="<?= $t->price_per_km ?>"
                                            data-deposit="<?= $t->deposit ?>">
                                        <?= Html::encode($t->name) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Минут</label>
                            <input type="number" id="calc-min" class="form-control" value="60" min="15" step="5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Километров</label>
                            <input type="number" id="calc-km" class="form-control" value="20" min="0" step="1">
                        </div>
                    </div>
                    <div class="bg-soft p-3 rounded-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-soft">Поездка</span>
                            <span id="calc-trip-cost">—</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-soft">Депозит (вернётся)</span>
                            <span id="calc-deposit">—</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>К списанию (с депозитом)</span>
                            <span class="text-primary fs-5" id="calc-total">—</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-5 p-4 p-md-5 rounded-2xl text-center" style="background: linear-gradient(135deg, var(--vh-primary-light), #fff);">
    <h2 class="mb-3">Готовы попробовать?</h2>
    <p class="lead text-soft mb-4">Регистрация занимает 2 минуты — и можно ехать.</p>
    <?php if (Yii::$app->user->isGuest): ?>
        <a href="<?= Url::to(['/site/signup']) ?>" class="btn btn-primary btn-lg me-2"><i class="fa-solid fa-key me-2"></i>Зарегистрироваться</a>
        <a href="<?= Url::to(['/car/index']) ?>" class="btn btn-soft btn-lg">Каталог авто</a>
    <?php else: ?>
        <a href="<?= Url::to(['/car/index']) ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-car me-2"></i>Выбрать автомобиль</a>
    <?php endif ?>
</section>

<?php $this->registerJs(<<<JS
function recalc() {
    const opt = document.querySelector('#calc-tariff option:checked');
    const ppm = parseFloat(opt.dataset.min);
    const ppk = parseFloat(opt.dataset.km);
    const dep = parseFloat(opt.dataset.deposit);
    const m = parseInt(document.getElementById('calc-min').value) || 0;
    const k = parseInt(document.getElementById('calc-km').value) || 0;
    const trip = m * ppm + k * ppk;
    const fmt = n => n.toLocaleString('ru-RU', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ₽';
    document.getElementById('calc-trip-cost').textContent = fmt(trip);
    document.getElementById('calc-deposit').textContent = fmt(dep);
    document.getElementById('calc-total').textContent = fmt(trip + dep);
}
document.querySelectorAll('#calc-tariff, #calc-min, #calc-km').forEach(el => el.addEventListener('input', recalc));
recalc();
JS); ?>
