<?php
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $filters */
use app\models\SupportTicket;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
$this->title = 'Обращения в поддержку';
?>
<form method="get" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (SupportTicket::statuses() as $s): ?>
                        <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= SupportTicket::statusLabel($s) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Категория</label>
                <select name="category" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (SupportTicket::categoryDropdown() as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $filters['category'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Приоритет</label>
                <select name="priority" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (SupportTicket::priorities() as $p): ?>
                        <option value="<?= $p ?>" <?= $filters['priority'] === $p ? 'selected' : '' ?>><?= SupportTicket::priorityLabel($p) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary"><i class="fa-solid fa-filter"></i></button></div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Номер</th><th>Тема</th><th>Пользователь</th><th>Категория</th><th>Приоритет</th><th>Статус</th><th>Обновлено</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($dataProvider->getModels() as $t): ?>
                <tr>
                    <td><a href="<?= Url::to(['view', 'id' => $t->id]) ?>" class="text-decoration-none fw-semibold"><?= $t->number ?></a></td>
                    <td><?= Html::encode($t->subject) ?></td>
                    <td class="small"><?= Html::encode($t->user->name ?? $t->user->email ?? '—') ?></td>
                    <td class="small"><?= SupportTicket::categoryLabel($t->category) ?></td>
                    <td><span class="badge bg-<?= SupportTicket::priorityBadge($t->priority) ?>"><?= SupportTicket::priorityLabel($t->priority) ?></span></td>
                    <td><span class="badge bg-<?= SupportTicket::statusBadge($t->status) ?>"><?= SupportTicket::statusLabel($t->status) ?></span></td>
                    <td class="small text-soft"><?= Yii::$app->formatter->asRelativeTime($t->updated_at) ?></td>
                    <td><a href="<?= Url::to(['view', 'id' => $t->id]) ?>" class="btn btn-soft btn-sm">Открыть</a></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <?= LinkPager::widget(['pagination' => $dataProvider->getPagination(), 'options' => ['class' => 'pagination pagination-sm m-0']]) ?>
    </div>
</div>
