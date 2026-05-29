<?php
/** @var yii\web\View $this */
/** @var app\models\Tariff[] $tariffs */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Тарифы';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-soft m-0">Тяните строки за «☰» чтобы изменить порядок.</p>
    <a href="<?= Url::to(['create']) ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Новый тариф</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th width="40"></th><th>Название</th><th>Цена/мин</th><th>Цена/км</th><th>Цена/час</th><th>Депозит</th><th>Активен</th><th></th></tr></thead>
                <tbody id="tariff-sortable">
                <?php foreach ($tariffs as $t): ?>
                    <tr data-id="<?= $t->id ?>">
                        <td class="cursor-pointer" style="color: <?= Html::encode($t->color) ?>;"><i class="fa-solid fa-grip-vertical drag-handle"></i></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-inline-block rounded-circle" style="width:14px;height:14px;background: <?= Html::encode($t->color) ?>;"></span>
                                <span class="fw-semibold"><?= Html::encode($t->name) ?></span>
                            </div>
                            <div class="small text-soft"><?= Html::encode($t->description) ?></div>
                        </td>
                        <td class="fw-semibold"><?= Yii::$app->formatter->asDecimal($t->price_per_minute, 2) ?> ₽</td>
                        <td><?= Yii::$app->formatter->asDecimal($t->price_per_km, 2) ?> ₽</td>
                        <td><?= $t->price_per_hour ? Yii::$app->formatter->asDecimal($t->price_per_hour, 2) . ' ₽' : '—' ?></td>
                        <td><?= Yii::$app->formatter->asCurrency($t->deposit) ?></td>
                        <td>
                            <?= Html::beginForm(['toggle', 'id' => $t->id]) ?>
                                <button class="btn btn-sm <?= $t->is_active ? 'btn-success' : 'btn-soft' ?>"><?= $t->is_active ? 'Активен' : 'Выключен' ?></button>
                            <?= Html::endForm() ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= Url::to(['update', 'id' => $t->id]) ?>" class="btn btn-soft btn-sm"><i class="fa-solid fa-pen"></i></a>
                            <?= Html::beginForm(['delete', 'id' => $t->id], 'post', ['class' => 'd-inline']) ?>
                                <button class="btn btn-soft btn-sm text-danger" onclick="return confirm('Удалить?')"><i class="fa-solid fa-trash"></i></button>
                            <?= Html::endForm() ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js', ['position' => \yii\web\View::POS_END]);
$url = Url::to(['sort']);
$csrf = Yii::$app->request->csrfToken;
$this->registerJs(<<<JS
const tbody = document.getElementById('tariff-sortable');
if (tbody && window.Sortable) {
    Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: () => {
            const order = [...tbody.querySelectorAll('tr')].map(tr => tr.dataset.id);
            const fd = new FormData();
            order.forEach(id => fd.append('order[]', id));
            fd.append('_csrf', '$csrf');
            fetch('$url', {method: 'POST', body: fd}).then(r=>r.json());
        }
    });
}
JS);
?>
