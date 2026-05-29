<?php
/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $name;
?>
<div class="error-page">
    <div class="error-page__code grad-text"><?= Html::encode(preg_replace('/[^0-9]/', '', $name) ?: '!') ?></div>
    <h2 class="mb-3"><?= Html::encode($name) ?></h2>
    <p class="text-soft mb-4 lead-thin"><?= nl2br(Html::encode($message)) ?></p>
    <a href="<?= Url::home() ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-house me-2"></i>На главную</a>
</div>
