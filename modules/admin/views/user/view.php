<?php
/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\UserDocument[] $documents */
/** @var app\models\Booking[] $bookings */
/** @var app\models\Transaction[] $transactions */

use app\models\User;
use app\models\UserDocument;
use app\models\Booking;
use app\models\Transaction;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $user->name ?: $user->email;
?>

<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body p-4 text-center">
                <span class="vh-avatar mx-auto mb-3" style="width:80px;height:80px;font-size:24px;"><?= Html::encode($user->getInitials()) ?></span>
                <h4><?= Html::encode($user->name ?: '—') ?></h4>
                <div class="text-soft small mb-3">#<?= $user->id ?> · <?= Html::encode($user->email) ?></div>

                <div class="d-flex flex-column gap-2 text-start mt-3">
                    <div class="d-flex justify-content-between"><span class="text-soft"><i class="fa-solid fa-phone me-1"></i> Телефон</span><span><?= Html::encode($user->phone ?: '—') ?></span></div>
                    <div class="d-flex justify-content-between"><span class="text-soft"><i class="fa-solid fa-cake-candles me-1"></i> Дата рождения</span><span><?= $user->birthdate ? Yii::$app->formatter->asDate($user->birthdate) : '—' ?></span></div>
                    <div class="d-flex justify-content-between"><span class="text-soft"><i class="fa-solid fa-user-shield me-1"></i> Роль</span><span class="badge bg-<?= $user->role === 'admin' ? 'warning' : 'secondary' ?>"><?= ucfirst($user->role) ?></span></div>
                    <div class="d-flex justify-content-between"><span class="text-soft"><i class="fa-solid fa-calendar me-1"></i> Регистрация</span><span><?= Yii::$app->formatter->asDate($user->created_at) ?></span></div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body p-4">
                <h6 class="mb-3">Баланс</h6>
                <div class="text-soft small">Доступно</div>
                <div class="h3 m-0"><?= Yii::$app->formatter->asCurrency($user->getAvailableBalance()) ?></div>
                <div class="small text-soft mt-1">Всего: <?= Yii::$app->formatter->asCurrency($user->balance) ?> · Заморожено: <?= Yii::$app->formatter->asCurrency($user->locked_balance) ?></div>

                <hr>
                <?= Html::beginForm(['adjust-balance', 'id' => $user->id]) ?>
                    <label class="form-label small">Корректировка</label>
                    <div class="input-group input-group-sm mb-2">
                        <input type="number" name="amount" step="0.01" class="form-control" placeholder="Сумма (+ или -)">
                        <span class="input-group-text">₽</span>
                    </div>
                    <input type="text" name="note" class="form-control form-control-sm mb-2" placeholder="Причина">
                    <button class="btn btn-soft btn-sm w-100">Применить</button>
                <?= Html::endForm() ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body p-4">
                <h6 class="mb-3">Действия</h6>

                <?php if ($user->verification_status !== User::VERIFICATION_VERIFIED): ?>
                    <?= Html::beginForm(['verify', 'id' => $user->id]) ?>
                        <button class="btn btn-success w-100 mb-2" onclick="return confirm('Верифицировать?')"><i class="fa-solid fa-check me-1"></i> Верифицировать</button>
                    <?= Html::endForm() ?>
                <?php endif ?>

                <?php if ($user->verification_status !== User::VERIFICATION_REJECTED): ?>
                    <?= Html::beginForm(['reject-verification', 'id' => $user->id]) ?>
                        <input type="text" name="reason" class="form-control form-control-sm mb-2" placeholder="Причина отказа">
                        <button class="btn btn-soft btn-sm w-100 text-danger mb-2"><i class="fa-solid fa-xmark me-1"></i> Отклонить верификацию</button>
                    <?= Html::endForm() ?>
                <?php endif ?>

                <?php if ($user->status === 'active'): ?>
                    <?= Html::beginForm(['block', 'id' => $user->id]) ?>
                        <input type="text" name="reason" class="form-control form-control-sm mb-2" placeholder="Причина блокировки">
                        <button class="btn btn-danger w-100" onclick="return confirm('Заблокировать пользователя?')"><i class="fa-solid fa-ban me-1"></i> Заблокировать</button>
                    <?= Html::endForm() ?>
                <?php else: ?>
                    <?= Html::beginForm(['unblock', 'id' => $user->id]) ?>
                        <button class="btn btn-success w-100"><i class="fa-solid fa-unlock me-1"></i> Разблокировать</button>
                    <?= Html::endForm() ?>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <span><i class="fa-solid fa-id-card me-2"></i>Документы</span>
                <span class="ms-2 badge bg-<?= User::verificationStatusBadge($user->verification_status) ?>"><?= User::verificationStatusLabel($user->verification_status) ?></span>
            </div>
            <div class="card-body">
                <?php if (!$documents): ?>
                    <p class="text-soft m-0">Документы не загружены.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($documents as $doc): ?>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="fw-semibold"><?= UserDocument::typeLabel($doc->type) ?></div>
                                        <span class="badge bg-<?= match($doc->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' } ?>"><?= UserDocument::statusLabel($doc->status) ?></span>
                                    </div>
                                    <a href="<?= $doc->getUrl() ?>" target="_blank" class="d-block mb-2">
                                        <img src="<?= $doc->getUrl() ?>" style="width:100%;aspect-ratio:3/2;object-fit:cover;border-radius:8px;background:#f1f5f9;" onerror="this.style.display='none'">
                                    </a>
                                    <div class="small text-soft mb-2"><?= Yii::$app->formatter->asDatetime($doc->uploaded_at) ?></div>
                                    <?php if ($doc->status === UserDocument::STATUS_PENDING): ?>
                                        <?= Html::beginForm(['review-document', 'id' => $doc->id]) ?>
                                            <input type="text" name="comment" class="form-control form-control-sm mb-2" placeholder="Комментарий (для отказа)">
                                            <div class="d-flex gap-1">
                                                <button name="action" value="approve" class="btn btn-success btn-sm flex-grow-1"><i class="fa-solid fa-check"></i></button>
                                                <button name="action" value="reject" class="btn btn-soft btn-sm text-danger flex-grow-1"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                        <?= Html::endForm() ?>
                                    <?php elseif ($doc->comment): ?>
                                        <div class="alert alert-warning small mb-0"><?= Html::encode($doc->comment) ?></div>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><i class="fa-solid fa-key me-2"></i>Последние бронирования</div>
            <div class="card-body p-0">
                <?php if ($bookings): ?>
                    <table class="table mb-0">
                        <thead><tr><th>Номер</th><th>Авто</th><th>Дата</th><th>Статус</th><th>Сумма</th></tr></thead>
                        <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><a href="<?= Url::to(['/admin/booking/view', 'id' => $b->id]) ?>" class="text-decoration-none fw-semibold"><?= $b->number ?></a></td>
                                <td class="small"><?= Html::encode($b->car->getFullName() ?? '—') ?></td>
                                <td class="small text-soft"><?= Yii::$app->formatter->asDate($b->created_at) ?></td>
                                <td><span class="badge bg-<?= Booking::statusBadge($b->status) ?>"><?= Booking::statusLabel($b->status) ?></span></td>
                                <td class="fw-bold"><?= Yii::$app->formatter->asCurrency($b->final_cost) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty"><h6>Бронирований нет</h6></div>
                <?php endif ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fa-solid fa-receipt me-2"></i>Последние транзакции</div>
            <div class="card-body p-0">
                <?php if ($transactions): ?>
                    <table class="table mb-0">
                        <thead><tr><th>Дата</th><th>Тип</th><th>Описание</th><th class="text-end">Сумма</th></tr></thead>
                        <tbody>
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td class="small"><?= Yii::$app->formatter->asDatetime($tx->created_at) ?></td>
                                <td><i class="fa-solid <?= Transaction::typeIcon($tx->type) ?> me-1"></i><?= Transaction::typeLabel($tx->type) ?></td>
                                <td class="small text-soft"><?= Html::encode($tx->description) ?></td>
                                <td class="text-end fw-bold <?= $tx->amount > 0 ? 'text-success' : 'text-danger' ?>"><?= ($tx->amount > 0 ? '+' : '') . Yii::$app->formatter->asCurrency($tx->amount) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty"><h6>Транзакций нет</h6></div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
