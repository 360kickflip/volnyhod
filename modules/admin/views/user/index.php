<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $filters */

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Пользователи';
?>

<form method="get" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Поиск</label>
                <input type="search" name="q" class="form-control" value="<?= Html::encode($filters['search'] ?? '') ?>" placeholder="Email, телефон, имя">
            </div>
            <div class="col-md-3">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (['active' => 'Активные', 'blocked' => 'Заблокированные'] as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Верификация</label>
                <select name="verification" class="form-select">
                    <option value="">Все</option>
                    <option value="none" <?= $filters['verification'] === 'none' ? 'selected' : '' ?>>Не загружено</option>
                    <option value="pending" <?= $filters['verification'] === 'pending' ? 'selected' : '' ?>>На проверке</option>
                    <option value="verified" <?= $filters['verification'] === 'verified' ? 'selected' : '' ?>>Верифицирован</option>
                    <option value="rejected" <?= $filters['verification'] === 'rejected' ? 'selected' : '' ?>>Отклонено</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Найти</button></div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr>
                    <th>ID</th><th>ФИО / контакты</th><th>Баланс</th><th>Верификация</th><th>Статус</th><th>Регистрация</th><th></th>
                </tr></thead>
                <tbody>
                <?php /** @var User $u */ ?>
                <?php foreach ($dataProvider->getModels() as $u): ?>
                    <tr>
                        <td>#<?= $u->id ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="vh-avatar" style="width:32px;height:32px;font-size:11px;"><?= Html::encode($u->getInitials()) ?></span>
                                <div>
                                    <div class="fw-semibold"><?= Html::encode($u->name ?: '—') ?></div>
                                    <div class="small text-soft"><?= Html::encode($u->email) ?> · <?= Html::encode($u->phone ?: '—') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><strong><?= Yii::$app->formatter->asCurrency($u->balance) ?></strong></td>
                        <td><span class="badge bg-<?= User::verificationStatusBadge($u->verification_status) ?>"><?= User::verificationStatusLabel($u->verification_status) ?></span></td>
                        <td>
                            <?php if ($u->status === 'active'): ?>
                                <span class="badge bg-success">Активен</span>
                            <?php elseif ($u->status === 'blocked'): ?>
                                <span class="badge bg-danger">Заблокирован</span>
                            <?php endif ?>
                            <?php if ($u->role !== 'user'): ?>
                                <span class="badge bg-warning ms-1"><?= ucfirst($u->role) ?></span>
                            <?php endif ?>
                        </td>
                        <td class="small text-soft"><?= Yii::$app->formatter->asDate($u->created_at) ?></td>
                        <td class="text-end"><a href="<?= Url::to(['view', 'id' => $u->id]) ?>" class="btn btn-soft btn-sm">Открыть</a></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($dataProvider->getPagination()->pageCount > 1): ?>
        <div class="card-footer d-flex justify-content-end">
            <?= LinkPager::widget(['pagination' => $dataProvider->getPagination(), 'options' => ['class' => 'pagination pagination-sm m-0']]) ?>
        </div>
    <?php endif ?>
</div>
