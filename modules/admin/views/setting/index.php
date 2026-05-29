<?php
/** @var app\models\Setting[] $settings */
/** @var array $allGroups */
/** @var string $currentGroup */

use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Настройки';

$groupLabels = [
    'general' => 'Общие',
    'payment' => 'Платежи',
    'maps' => 'Карты',
    'business' => 'Бизнес',
    'email' => 'Email',
    'sms' => 'SMS',
];
?>
<ul class="nav vh-tabs mb-3">
    <?php foreach ($allGroups as $g): ?>
        <li class="nav-item"><a class="nav-link <?= $currentGroup === $g ? 'active' : '' ?>" href="<?= Url::to(['index', 'group' => $g]) ?>"><?= $groupLabels[$g] ?? ucfirst($g) ?></a></li>
    <?php endforeach ?>
</ul>

<form method="post" class="card">
    <div class="card-body p-4">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <?php foreach ($settings as $s): ?>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?= Html::encode($s->label ?: $s->key) ?></label>
                <div class="col-sm-9">
                    <?php if ($s->type === 'text' || $s->type === 'json' || strlen((string)$s->value) > 80): ?>
                        <textarea name="Setting[<?= $s->key ?>]" class="form-control" rows="3"><?= Html::encode($s->value) ?></textarea>
                    <?php elseif ($s->type === 'bool'): ?>
                        <select name="Setting[<?= $s->key ?>]" class="form-select"><option value="1" <?= $s->value ? 'selected' : '' ?>>Да</option><option value="0" <?= !$s->value ? 'selected' : '' ?>>Нет</option></select>
                    <?php elseif (in_array($s->type, ['int', 'float'])): ?>
                        <input type="number" step="<?= $s->type === 'float' ? '0.01' : '1' ?>" name="Setting[<?= $s->key ?>]" value="<?= Html::encode($s->value) ?>" class="form-control">
                    <?php else: ?>
                        <input type="text" name="Setting[<?= $s->key ?>]" value="<?= Html::encode($s->value) ?>" class="form-control">
                    <?php endif ?>
                    <?php if ($s->description): ?>
                        <small class="text-muted"><?= Html::encode($s->description) ?></small>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="card-footer text-end">
        <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Сохранить</button>
    </div>
</form>
