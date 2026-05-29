<?php
/** @var app\models\DamageReport $report */
use app\models\DamageReport;
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Повреждение #' . $report->id;
?>
<a href="<?= Url::to(['index']) ?>" class="text-decoration-none small text-soft mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> К списку</a>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <h3>Повреждение #<?= $report->id ?></h3>
                    <span class="badge bg-<?= DamageReport::severityBadge($report->severity) ?>"><?= DamageReport::severityLabel($report->severity) ?></span>
                </div>
                <div class="text-soft small mb-3"><?= Yii::$app->formatter->asDatetime($report->created_at) ?></div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <h6>Автомобиль</h6>
                        <a href="<?= Url::to(['/admin/car/view', 'id' => $report->car_id]) ?>" class="text-decoration-none"><?= Html::encode($report->car->getFullName() ?? '—') ?></a>
                    </div>
                    <div class="col-md-6">
                        <h6>Пользователь</h6>
                        <?php if ($report->user_id): ?>
                            <a href="<?= Url::to(['/admin/user/view', 'id' => $report->user_id]) ?>" class="text-decoration-none"><?= Html::encode($report->user->name ?? $report->user->email ?? '—') ?></a>
                        <?php else: ?>—<?php endif ?>
                    </div>
                </div>

                <h6>Описание</h6>
                <p><?= nl2br(Html::encode($report->description)) ?></p>

                <?php $photos = $report->getPhotosArray(); if ($photos): ?>
                    <h6 class="mt-4">Фото</h6>
                    <div class="row g-2">
                        <?php foreach ($photos as $p): ?>
                            <div class="col-md-3 col-6">
                                <a href="<?= Yii::getAlias('@web/uploads/damages/' . $p) ?>" target="_blank">
                                    <img src="<?= Yii::getAlias('@web/uploads/damages/' . $p) ?>" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;">
                                </a>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body p-4">
                <h6>Решение</h6>
                <?= Html::beginForm(['save', 'id' => $report->id]) ?>
                    <div class="mb-3">
                        <label class="form-label">Статус</label>
                        <select name="status" class="form-select">
                            <?php foreach (['reported' => 'Сообщено', 'reviewing' => 'На рассмотрении', 'user_liable' => 'Виновен пользователь', 'not_liable' => 'Не виновен', 'resolved' => 'Решено'] as $k => $v): ?>
                                <option value="<?= $k ?>" <?= $report->status === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Стоимость ремонта (₽)</label>
                        <input type="number" name="repair_cost" step="0.01" min="0" value="<?= $report->repair_cost ?>" class="form-control">
                        <small class="text-muted">Если статус «Виновен пользователь», эта сумма автоматически добавится в счёт по бронированию.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Комментарий</label>
                        <textarea name="reviewer_comment" rows="3" class="form-control"><?= Html::encode($report->reviewer_comment) ?></textarea>
                    </div>
                    <button class="btn btn-primary w-100"><i class="fa-solid fa-floppy-disk me-1"></i> Сохранить</button>
                <?= Html::endForm() ?>
            </div>
        </div>

        <?php if ($report->booking_id): ?>
            <a href="<?= Url::to(['/admin/booking/view', 'id' => $report->booking_id]) ?>" class="btn btn-soft w-100 mt-3">К бронированию</a>
        <?php endif ?>
    </div>
</div>
