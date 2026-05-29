<?php
/** @var app\models\SupportMessage $msg */
/** @var bool $isMine */
use yii\helpers\Html;
?>
<div class="chat-msg <?= $isMine ? 'chat-msg--mine' : '' ?>">
    <div class="chat-msg__avatar">
        <?php if ($msg->author_role === 'admin'): ?>
            <i class="fa-solid fa-headset text-primary"></i>
        <?php else: ?>
            <?= Html::encode($msg->author->getInitials() ?? '?') ?>
        <?php endif ?>
    </div>
    <div class="chat-msg__bubble">
        <div class="chat-msg__author"><?= Html::encode($msg->author_role === 'admin' ? 'Поддержка' : ($msg->author->name ?? 'Вы')) ?></div>
        <div><?= nl2br(Html::encode($msg->message)) ?></div>
        <?php $atts = $msg->getAttachmentsArray(); if ($atts): ?>
            <div class="mt-2 d-flex flex-wrap gap-2">
                <?php foreach ($atts as $a): ?>
                    <a href="<?= Yii::getAlias('@web/uploads/support/' . ($a['path'] ?? $a)) ?>" target="_blank" class="badge bg-soft text-dark text-decoration-none">
                        <i class="fa-solid fa-paperclip me-1"></i><?= Html::encode($a['name'] ?? $a) ?>
                    </a>
                <?php endforeach ?>
            </div>
        <?php endif ?>
        <div class="chat-msg__time"><?= Yii::$app->formatter->asDatetime($msg->created_at) ?></div>
    </div>
</div>
