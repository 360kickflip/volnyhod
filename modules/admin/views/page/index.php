<?php
/** @var yii\data\ActiveDataProvider $dataProvider */
use app\models\Page;
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Страницы';
?>
<div class="d-flex justify-content-between mb-3">
    <p class="text-soft m-0">Всего: <?= $dataProvider->totalCount ?></p>
    <a href="<?= Url::to(['create']) ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Новая страница</a>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Заголовок</th><th>URL</th><th>Активна</th><th>Обновлено</th><th></th></tr></thead>
            <tbody>
            <?php /** @var Page $p */ ?>
            <?php foreach ($dataProvider->getModels() as $p): ?>
                <tr>
                    <td class="fw-semibold"><?= Html::encode($p->title) ?></td>
                    <td><a href="<?= Url::to(['/page/view', 'slug' => $p->slug]) ?>" target="_blank" class="text-decoration-none"><code>/<?= Html::encode($p->slug) ?></code></a></td>
                    <td><span class="badge bg-<?= $p->is_active ? 'success' : 'secondary' ?>"><?= $p->is_active ? 'Да' : 'Нет' ?></span></td>
                    <td class="small text-soft"><?= Yii::$app->formatter->asDate($p->updated_at) ?></td>
                    <td class="text-end">
                        <a href="<?= Url::to(['update', 'id' => $p->id]) ?>" class="btn btn-soft btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <?= Html::beginForm(['delete', 'id' => $p->id], 'post', ['class' => 'd-inline']) ?><button class="btn btn-soft btn-sm text-danger" onclick="return confirm('Удалить?')"><i class="fa-solid fa-trash"></i></button><?= Html::endForm() ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
