<?php
/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\User[] $referrals */
/** @var app\models\ReferralReward[] $rewards */
/** @var array $stats */
/** @var float $bonusReferrer */
/** @var float $bonusReferred */
/** @var bool $programActive */

use app\models\ReferralReward;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Пригласить друзей';
$link = $user->getReferralLink();
$shareText = "Я катаюсь с «Вольный Ход» — современный каршеринг. По моему коду {$user->referral_code} ты получишь {$bonusReferred} ₽ на первую поездку!";
?>

<h1 class="mb-4"><i class="fa-solid fa-gift text-warning me-2"></i>Пригласить друзей</h1>

<?php if (!$programActive): ?>
    <div class="alert alert-warning">Реферальная программа временно приостановлена.</div>
<?php endif ?>

<!-- Статистика -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="vh-stat h-100">
            <div class="vh-stat__icon"><i class="fa-solid fa-coins"></i></div>
            <div class="vh-stat__label">Заработано</div>
            <div class="vh-stat__value"><?= Yii::$app->formatter->asCurrency($stats['total_earned']) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="vh-stat h-100">
            <div class="vh-stat__icon vh-stat__icon--info"><i class="fa-solid fa-user-plus"></i></div>
            <div class="vh-stat__label">Приглашено</div>
            <div class="vh-stat__value"><?= $stats['total_invited'] ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="vh-stat h-100">
            <div class="vh-stat__icon vh-stat__icon--purple"><i class="fa-solid fa-car-side"></i></div>
            <div class="vh-stat__label">Совершили поездку</div>
            <div class="vh-stat__value"><?= $stats['total_rode'] ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="vh-stat h-100">
            <div class="vh-stat__icon vh-stat__icon--warning"><i class="fa-solid fa-clock"></i></div>
            <div class="vh-stat__label">Ждём первой поездки</div>
            <div class="vh-stat__value"><?= $stats['pending_count'] ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <!-- Реф-код и условия -->
        <div class="card mb-3" style="background: linear-gradient(135deg, var(--vh-primary-light), #fff);">
            <div class="card-body p-4">
                <h5 class="mb-3">Ваш реферальный код</h5>
                <div class="vh-promo-code mb-3">
                    <code id="ref-code"><?= Html::encode($user->referral_code) ?></code>
                    <button type="button" class="btn btn-soft btn-sm" data-copy="<?= Html::encode($user->referral_code) ?>" title="Копировать"><i class="fa-solid fa-copy"></i></button>
                </div>

                <label class="form-label small">Реферальная ссылка</label>
                <div class="input-group mb-3">
                    <input type="text" id="ref-link" value="<?= Html::encode($link) ?>" class="form-control" readonly>
                    <button class="btn btn-primary" type="button" data-copy="<?= Html::encode($link) ?>"><i class="fa-solid fa-copy me-1"></i> Скопировать</button>
                </div>

                <h6 class="mb-2 mt-3">Поделиться</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="https://vk.com/share.php?url=<?= urlencode($link) ?>&title=<?= urlencode('Вольный Ход — каршеринг') ?>&description=<?= urlencode($shareText) ?>" target="_blank" rel="noopener" class="btn btn-soft btn-sm"><i class="fa-brands fa-vk me-1"></i> VK</a>
                    <a href="https://t.me/share/url?url=<?= urlencode($link) ?>&text=<?= urlencode($shareText) ?>" target="_blank" rel="noopener" class="btn btn-soft btn-sm"><i class="fa-brands fa-telegram me-1"></i> Telegram</a>
                    <a href="https://api.whatsapp.com/send?text=<?= urlencode($shareText . ' ' . $link) ?>" target="_blank" rel="noopener" class="btn btn-soft btn-sm"><i class="fa-brands fa-whatsapp me-1"></i> WhatsApp</a>
                    <a href="viber://forward?text=<?= urlencode($shareText . ' ' . $link) ?>" class="btn btn-soft btn-sm"><i class="fa-brands fa-viber me-1"></i> Viber</a>
                    <a href="mailto:?subject=<?= urlencode('Вольный Ход — каршеринг') ?>&body=<?= urlencode($shareText . "\n\n" . $link) ?>" class="btn btn-soft btn-sm"><i class="fa-solid fa-envelope me-1"></i> Email</a>
                </div>
            </div>
        </div>

        <!-- Условия -->
        <div class="card">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="fa-solid fa-circle-info text-primary me-2"></i>Как это работает</h5>
                <ol class="ps-3 mb-0">
                    <li class="mb-2"><strong>Поделитесь</strong> своим кодом или ссылкой с другом</li>
                    <li class="mb-2"><strong>Друг регистрируется</strong> по вашей ссылке или вводит код при регистрации</li>
                    <li class="mb-2"><strong>Друг совершает первую поездку</strong> — вы оба получаете бонус</li>
                </ol>
                <hr>
                <div class="row g-2 small text-soft">
                    <div class="col-6"><i class="fa-solid fa-gift me-1 text-primary"></i> Вам: <strong class="text-success"><?= Yii::$app->formatter->asCurrency($bonusReferrer) ?></strong></div>
                    <div class="col-6"><i class="fa-solid fa-gift me-1 text-primary"></i> Другу: <strong class="text-success"><?= Yii::$app->formatter->asCurrency($bonusReferred) ?></strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <!-- Список приглашённых -->
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-users me-2"></i>Ваши приглашения</div>
            <div class="card-body p-0">
                <?php if (!$referrals): ?>
                    <div class="empty">
                        <div class="empty__icon"><i class="fa-solid fa-user-plus"></i></div>
                        <h4>Пока никого нет</h4>
                        <p>Поделитесь ссылкой с друзьями — здесь появится статистика.</p>
                    </div>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($referrals as $r): ?>
                            <?php
                            $reward = $rewards[$r->id] ?? null;
                            $rewardStatus = $reward ? $reward->status : ReferralReward::STATUS_PENDING;
                            ?>
                            <li class="d-flex align-items-center gap-3 p-3 border-bottom">
                                <div class="vh-avatar" style="width:42px;height:42px;font-size:14px;"><?= Html::encode($r->getInitials()) ?></div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold"><?= Html::encode($r->name ?: $r->email) ?></div>
                                    <div class="small text-soft">Зарегистрирован <?= Yii::$app->formatter->asDate($r->created_at) ?></div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-<?= ReferralReward::statusBadge($rewardStatus) ?>"><?= ReferralReward::statusLabel($rewardStatus) ?></span>
                                    <?php if ($reward && $reward->status === ReferralReward::STATUS_PAID): ?>
                                        <div class="small fw-bold text-success mt-1">+<?= Yii::$app->formatter->asCurrency($reward->amount_referrer) ?></div>
                                    <?php endif ?>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-copy]');
    if (!btn) return;
    const text = btn.dataset.copy;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Скопировано!';
            btn.classList.add('btn-success');
            setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('btn-success'); }, 1500);
        });
    }
});
JS); ?>
