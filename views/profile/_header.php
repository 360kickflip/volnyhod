<?php
/** @var app\models\User $user */
use app\models\User;
use yii\helpers\Html;

$verifyLabel = User::verificationStatusLabel($user->verification_status);
$verifyClass = match($user->verification_status) {
    User::VERIFICATION_VERIFIED => 'verify-pill verify-pill--verified',
    User::VERIFICATION_PENDING => 'verify-pill verify-pill--pending',
    User::VERIFICATION_REJECTED => 'verify-pill verify-pill--rejected',
    default => 'verify-pill verify-pill--none',
};
$verifyIcon = match($user->verification_status) {
    User::VERIFICATION_VERIFIED => 'fa-circle-check',
    User::VERIFICATION_PENDING => 'fa-clock',
    User::VERIFICATION_REJECTED => 'fa-circle-xmark',
    default => 'fa-circle-info',
};
?>

<div class="card mb-4 shadow-soft">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="vh-avatar" style="width:64px;height:64px;font-size:22px;">
                <?= $user->avatar ? '<img src="' . $user->getAvatarUrl() . '" alt="">' : Html::encode($user->getInitials()) ?>
            </div>
            <div class="flex-grow-1">
                <h3 class="m-0"><?= Html::encode($user->name ?: 'Пользователь') ?></h3>
                <div class="text-soft small"><?= Html::encode($user->email) ?> · ID #<?= $user->id ?></div>
            </div>
            <span class="<?= $verifyClass ?>">
                <i class="fa-solid <?= $verifyIcon ?>"></i>
                <?= Html::encode($verifyLabel) ?>
            </span>
        </div>
    </div>
</div>
