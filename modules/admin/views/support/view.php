<?php
/** @var app\models\SupportTicket $ticket */
/** @var app\models\User[] $admins */
use app\models\SupportTicket;
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = $ticket->number . ' · ' . $ticket->subject;
?>
<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <h3 class="m-0"><?= Html::encode($ticket->subject) ?></h3>
                    <span class="badge bg-<?= SupportTicket::statusBadge($ticket->status) ?>"><?= SupportTicket::statusLabel($ticket->status) ?></span>
                </div>
                <div class="text-soft mb-2">
                    <?= $ticket->number ?> · <a href="<?= Url::to(['/admin/user/view', 'id' => $ticket->user_id]) ?>" class="text-decoration-none"><?= Html::encode($ticket->user->name ?? $ticket->user->email ?? '—') ?></a>
                    · <?= SupportTicket::categoryLabel($ticket->category) ?>
                    · <span class="badge bg-<?= SupportTicket::priorityBadge($ticket->priority) ?>"><?= SupportTicket::priorityLabel($ticket->priority) ?></span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="chat" id="chat">
                    <?php foreach ($ticket->messages as $msg): ?>
                        <?= $this->render('//support/_message', ['msg' => $msg, 'isMine' => $msg->author_role === 'admin']) ?>
                    <?php endforeach ?>
                </div>

                <form action="<?= Url::to(['reply', 'id' => $ticket->id]) ?>" method="post" class="border-top p-3">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <textarea name="message" class="form-control mb-2" rows="3" placeholder="Ответ для клиента…" required></textarea>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <select name="status" class="form-select form-select-sm" style="max-width:240px;">
                            <option value="">Не менять статус</option>
                            <?php foreach (SupportTicket::statuses() as $s): ?>
                                <option value="<?= $s ?>" <?= $ticket->status === $s ? 'selected' : '' ?>><?= SupportTicket::statusLabel($s) ?></option>
                            <?php endforeach ?>
                        </select>
                        <button class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i> Ответить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body p-4">
                <h6 class="mb-3">Назначение</h6>
                <?= Html::beginForm(['assign', 'id' => $ticket->id]) ?>
                    <select name="admin_id" class="form-select mb-2">
                        <option value="">— не назначен —</option>
                        <?php foreach ($admins as $a): ?>
                            <option value="<?= $a->id ?>" <?= $ticket->assigned_admin_id == $a->id ? 'selected' : '' ?>><?= Html::encode($a->name ?: $a->email) ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-soft w-100">Назначить</button>
                <?= Html::endForm() ?>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <h6 class="mb-3">Быстрые действия</h6>
                <?php foreach ([SupportTicket::STATUS_RESOLVED => 'fa-check', SupportTicket::STATUS_CLOSED => 'fa-lock'] as $s => $icon): ?>
                    <?= Html::beginForm(['status', 'id' => $ticket->id]) ?>
                        <input type="hidden" name="status" value="<?= $s ?>">
                        <button class="btn btn-soft w-100 mb-2"><i class="fa-solid <?= $icon ?> me-1"></i> <?= SupportTicket::statusLabel($s) ?></button>
                    <?= Html::endForm() ?>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>
<?php $this->registerJs("const c=document.getElementById('chat'); if(c) c.scrollTop=c.scrollHeight;") ?>
