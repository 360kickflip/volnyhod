<?php
/** @var yii\data\ActiveDataProvider $dataProvider */
use app\models\Faq;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
$this->title = 'FAQ';
?>
<div class="d-flex justify-content-between mb-3">
    <p class="text-soft m-0">Всего: <?= $dataProvider->totalCount ?></p>
    <a href="<?= Url::to(['create']) ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Добавить вопрос</a>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Категория</th><th>Вопрос</th><th>Активен</th><th>Порядок</th><th></th></tr></thead>
            <tbody>
            <?php /** @var Faq $f */ ?>
            <?php foreach ($dataProvider->getModels() as $f): ?>
                <tr>
                    <td class="small"><?= Html::encode($f->category->name ?? '—') ?></td>
                    <td><?= Html::encode($f->question) ?></td>
                    <td><span class="badge bg-<?= $f->is_active ? 'success' : 'secondary' ?>"><?= $f->is_active ? 'Да' : 'Нет' ?></span></td>
                    <td><?= $f->sort_order ?></td>
                    <td class="text-end">
                        <a href="<?= Url::to(['update', 'id' => $f->id]) ?>" class="btn btn-soft btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <?= Html::beginForm(['delete', 'id' => $f->id], 'post', ['class' => 'd-inline']) ?><button class="btn btn-soft btn-sm text-danger" onclick="return confirm('Удалить?')"><i class="fa-solid fa-trash"></i></button><?= Html::endForm() ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
