<?php
/** @var yii\web\View $this */
/** @var app\models\Page $page */
use yii\helpers\HtmlPurifier;
$this->title = $page->title;
$this->params['meta_description'] = $page->meta_description;
?>
<div class="row">
    <div class="col-lg-9 mx-auto">
        <h1 class="mb-4"><?= htmlspecialchars($page->title) ?></h1>
        <div class="card">
            <div class="card-body p-4 p-md-5 fs-6">
                <?= HtmlPurifier::process($page->content) ?>
            </div>
        </div>
        <div class="text-soft small mt-3">Обновлено: <?= Yii::$app->formatter->asDate($page->updated_at) ?></div>
    </div>
</div>
