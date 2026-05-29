<?php
/** @var yii\web\View $this */
/** @var array $grouped */

use app\models\Notification;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Уведомления';
$total = array_sum(array_map('count', $grouped));
$unread = 0;
foreach ($grouped as $items) foreach ($items as $n) if (!$n->is_read) $unread++;
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="m-0">Уведомления</h1>
        <p class="text-soft m-0 mt-1">Всего: <?= $total ?> · Непрочитанных: <strong><?= $unread ?></strong></p>
    </div>
    <?php if ($unread > 0): ?>
        <?= Html::beginForm(['mark-all-read']) ?>
            <button class="btn btn-soft"><i class="fa-solid fa-check-double me-1"></i> Отметить всё прочитанным</button>
        <?= Html::endForm() ?>
    <?php endif ?>
</div>

<?php if (empty($grouped)): ?>
    <div class="card"><div class="card-body">
        <div class="empty">
            <div class="empty__icon"><i class="fa-solid fa-bell-slash"></i></div>
            <h4>Нет уведомлений</h4>
            <p>Здесь появятся уведомления о бронированиях, платежах и поддержке.</p>
        </div>
    </div></div>
<?php else: ?>
    <?php foreach ($grouped as $day => $items): ?>
        <h6 class="notif-day-title"><?= $day ?></h6>
        <div class="d-flex flex-column gap-2">
            <?php /** @var Notification $n */ ?>
            <?php foreach ($items as $n): ?>
                <a class="notif <?= $n->is_read ? '' : 'notif--unread' ?> text-decoration-none text-reset" href="<?= $n->url ? Url::to($n->url) : '#' ?>" data-notification-id="<?= $n->id ?>">
                    <div class="notif__icon">
                        <i class="fa-solid <?= Html::encode($n->icon ?: Notification::typeIcon($n->type)) ?> fa-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="notif__title"><?= Html::encode($n->title) ?></h6>
                        <?php if ($n->message): ?>
                            <div class="notif__msg"><?= nl2br(Html::encode($n->message)) ?></div>
                        <?php endif ?>
                        <div class="notif__time">
                            <?= Yii::$app->formatter->asTime($n->created_at) ?>
                            <?php if (!$n->is_read): ?>
                                · <span class="text-primary fw-semibold">Новое</span>
                            <?php endif ?>
                        </div>
                    </div>
                </a>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>
<?php endif ?>
