<?php
/** @var yii\web\View $this */
/** @var app\models\SupportTicket $ticket */

use app\models\SupportTicket;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = $ticket->number . ' · ' . $ticket->subject;
?>

<nav class="mb-3">
    <a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft"><i class="fa-solid fa-arrow-left me-1"></i> К обращениям</a>
</nav>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
    <div>
        <h1 class="m-0"><?= Html::encode($ticket->subject) ?></h1>
        <div class="text-soft mt-1">
            № <?= Html::encode($ticket->number) ?> ·
            <?= SupportTicket::categoryLabel($ticket->category) ?> ·
            <span class="badge bg-<?= SupportTicket::statusBadge($ticket->status) ?>"><?= SupportTicket::statusLabel($ticket->status) ?></span>
        </div>
    </div>
    <?php if (!in_array($ticket->status, [SupportTicket::STATUS_CLOSED])): ?>
        <?= Html::beginForm(['close', 'id' => $ticket->id]) ?>
            <button class="btn btn-soft" onclick="return confirm('Закрыть обращение?')"><i class="fa-solid fa-circle-check me-1"></i> Закрыть</button>
        <?= Html::endForm() ?>
    <?php endif ?>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="chat" id="chat">
            <?php foreach ($ticket->messages as $msg): ?>
                <?= $this->render('_message', ['msg' => $msg, 'isMine' => $msg->author_id === Yii::$app->user->id]) ?>
            <?php endforeach ?>
        </div>

        <?php if ($ticket->status !== SupportTicket::STATUS_CLOSED): ?>
            <form action="<?= Url::to(['reply', 'id' => $ticket->id]) ?>" method="post" class="border-top p-3">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <div class="input-group">
                    <textarea name="message" class="form-control" rows="2" placeholder="Введите сообщение…" required></textarea>
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                </div>
            </form>
        <?php else: ?>
            <div class="border-top p-3 text-center text-soft small">Обращение закрыто</div>
        <?php endif ?>
    </div>
</div>

<?php $this->registerJs("const chat = document.getElementById('chat'); if (chat) chat.scrollTop = chat.scrollHeight;") ?>
