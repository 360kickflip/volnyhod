<?php
/** @var array $top */
/** @var array $stats */
/** @var yii\data\ActiveDataProvider $rewardsProvider */

use app\models\ReferralReward;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Реферальная программа';
?>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="vh-stat">
            <div class="vh-stat__icon"><i class="fa-solid fa-user-plus"></i></div>
            <div class="vh-stat__label">Приглашений всего</div>
            <div class="vh-stat__value"><?= $stats['total_invited'] ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="vh-stat">
            <div class="vh-stat__icon vh-stat__icon--info"><i class="fa-solid fa-car-side"></i></div>
            <div class="vh-stat__label">Совершили первую поездку</div>
            <div class="vh-stat__value"><?= $stats['total_rode'] ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="vh-stat">
            <div class="vh-stat__icon vh-stat__icon--purple"><i class="fa-solid fa-percent"></i></div>
            <div class="vh-stat__label">Конверсия</div>
            <div class="vh-stat__value"><?= $stats['conversion'] ?>%</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="vh-stat">
            <div class="vh-stat__icon vh-stat__icon--warning"><i class="fa-solid fa-coins"></i></div>
            <div class="vh-stat__label">Выплачено бонусов</div>
            <div class="vh-stat__value"><?= Yii::$app->formatter->asCurrency($stats['total_paid']) ?></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-trophy me-2"></i>Топ рефереров</div>
            <div class="card-body p-0">
                <?php if (!$top): ?>
                    <div class="empty"><div class="empty__icon"><i class="fa-solid fa-gift"></i></div><h6>Пока нет приглашений</h6></div>
                <?php else: ?>
                    <table class="table mb-0 small">
                        <thead><tr><th>Пользователь</th><th>Код</th><th class="text-end">Пригласил</th><th class="text-end">Поехали</th><th class="text-end">Заработал</th></tr></thead>
                        <tbody>
                        <?php foreach ($top as $row): ?>
                            <tr>
                                <td>
                                    <a href="<?= Url::to(['/admin/user/view', 'id' => $row['id']]) ?>" class="text-decoration-none fw-semibold"><?= Html::encode($row['name'] ?: $row['email']) ?></a>
                                </td>
                                <td><code><?= Html::encode($row['referral_code']) ?></code></td>
                                <td class="text-end"><?= $row['invited'] ?></td>
                                <td class="text-end"><?= $row['rode'] ?></td>
                                <td class="text-end fw-bold"><?= Yii::$app->formatter->asCurrency($row['earned']) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-clock-rotate-left me-2"></i>Последние награды</div>
            <div class="card-body p-0">
                <table class="table mb-0 small">
                    <thead><tr><th>Когда</th><th>Кто пригласил</th><th>Кого пригласил</th><th>Статус</th><th class="text-end">Сумма</th></tr></thead>
                    <tbody>
                    <?php /** @var ReferralReward $r */ ?>
                    <?php foreach ($rewardsProvider->getModels() as $r): ?>
                        <tr>
                            <td class="text-soft"><?= Yii::$app->formatter->asDate($r->created_at) ?></td>
                            <td><a href="<?= Url::to(['/admin/user/view', 'id' => $r->referrer_id]) ?>" class="text-decoration-none"><?= Html::encode($r->referrer->name ?? $r->referrer->email ?? '—') ?></a></td>
                            <td><a href="<?= Url::to(['/admin/user/view', 'id' => $r->referred_id]) ?>" class="text-decoration-none"><?= Html::encode($r->referred->name ?? $r->referred->email ?? '—') ?></a></td>
                            <td><span class="badge bg-<?= ReferralReward::statusBadge($r->status) ?>"><?= ReferralReward::statusLabel($r->status) ?></span></td>
                            <td class="text-end fw-bold"><?= Yii::$app->formatter->asCurrency($r->amount_referrer + $r->amount_referred) ?></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
                <?php if ($rewardsProvider->getPagination()->pageCount > 1): ?>
                    <div class="card-footer d-flex justify-content-end">
                        <?= LinkPager::widget(['pagination' => $rewardsProvider->getPagination(), 'options' => ['class' => 'pagination pagination-sm m-0']]) ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
