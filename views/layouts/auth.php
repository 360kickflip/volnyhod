<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/svg+xml', 'href' => Yii::getAlias('@web/img/logo.svg')]);

$themeScript = <<<'JS'
(function() {
    try {
        var t = localStorage.getItem('vh_theme');
        if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', t);
    } catch(e) {}
})();
JS;
$this->registerJs($themeScript, \yii\web\View::POS_HEAD);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title ?: Yii::$app->name) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="auth-wrap">
    <div class="auth-wrap__form">
        <div class="auth-card fade-up">
            <a href="<?= Url::home() ?>" class="vh-brand mb-4 d-inline-flex">
                <span class="vh-brand__logo"><i class="fa-solid fa-road"></i></span>
                <span class="vh-brand__name">Вольный Ход</span>
            </a>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>
    <div class="auth-wrap__visual">
        <div>
            <span class="badge badge-soft-primary" style="background: rgba(255,255,255,.15); color:#fff;">
                <i class="fa-solid fa-bolt me-1"></i> Каршеринг нового поколения
            </span>
            <h2 class="mt-4 mb-3">Свобода передвижения<br>в одно касание</h2>
            <p class="lead-thin" style="color: rgba(255,255,255,.75)">
                Сотни автомобилей по всему городу. Поминутная тарификация. Никаких скрытых платежей.
            </p>
        </div>
        <div class="row g-3">
            <div class="col-6">
                <div style="background: rgba(255,255,255,.08); border-radius: 14px; padding: 1rem;">
                    <i class="fa-solid fa-car-side text-success mb-2 fa-lg"></i>
                    <div style="font-size:1.5rem;font-weight:800;color:#fff;">500+</div>
                    <div style="color:rgba(255,255,255,.6);font-size:.85rem;">авто на линии</div>
                </div>
            </div>
            <div class="col-6">
                <div style="background: rgba(255,255,255,.08); border-radius: 14px; padding: 1rem;">
                    <i class="fa-solid fa-users text-warning mb-2 fa-lg"></i>
                    <div style="font-size:1.5rem;font-weight:800;color:#fff;">50K</div>
                    <div style="color:rgba(255,255,255,.6);font-size:.85rem;">довольных клиентов</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
