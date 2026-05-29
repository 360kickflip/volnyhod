<?php
/** @var yii\web\View $this */
/** @var app\models\SupportTicket[] $tickets */

use app\models\SupportTicket;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Поддержка';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="m-0">Поддержка</h1>
        <p class="text-soft m-0 mt-1">Мы на связи 24/7 · <?= Yii::$app->params['supportPhone'] ?></p>
    </div>
    <a href="<?= Url::to(['create']) ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Создать обращение</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fa-solid fa-phone fa-2x text-primary mb-2"></i>
                <h5>Позвоните</h5>
                <a href="tel:<?= preg_replace('/[^+\d]/', '', Yii::$app->params['supportPhone']) ?>" class="text-decoration-none"><?= Yii::$app->params['supportPhone'] ?></a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fa-solid fa-envelope fa-2x text-primary mb-2"></i>
                <h5>Напишите</h5>
                <a href="mailto:<?= Yii::$app->params['supportEmail'] ?>" class="text-decoration-none"><?= Yii::$app->params['supportEmail'] ?></a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fa-solid fa-circle-question fa-2x text-primary mb-2"></i>
                <h5>FAQ</h5>
                <a href="<?= Url::to(['/faq/index']) ?>" class="text-decoration-none">Частые вопросы</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="m-0">Мои обращения</h5></div>
    <div class="card-body p-0">
        <?php if (!$tickets): ?>
            <div class="empty">
                <div class="empty__icon"><i class="fa-solid fa-life-ring"></i></div>
                <h4>Нет обращений</h4>
                <p>Если у вас возник вопрос — создайте новое обращение.</p>
                <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">Создать</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Тема</th>
                        <th>Категория</th>
                        <th>Статус</th>
                        <th>Обновлено</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td><a href="<?= Url::to(['view', 'id' => $t->id]) ?>" class="text-decoration-none fw-semibold"><?= Html::encode($t->number) ?></a></td>
                            <td><?= Html::encode($t->subject) ?></td>
                            <td><?= SupportTicket::categoryLabel($t->category) ?></td>
                            <td><span class="badge bg-<?= SupportTicket::statusBadge($t->status) ?>"><?= SupportTicket::statusLabel($t->status) ?></span></td>
                            <td class="small text-soft"><?= Yii::$app->formatter->asRelativeTime($t->updated_at) ?></td>
                            <td><a href="<?= Url::to(['view', 'id' => $t->id]) ?>" class="btn btn-soft btn-sm">Открыть</a></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</div>
