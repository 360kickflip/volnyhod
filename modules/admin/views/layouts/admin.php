<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use app\models\SupportTicket;
use app\models\DamageReport;
use app\models\Review;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/svg+xml', 'href' => Yii::getAlias('@web/img/logo.svg')]);

$user = Yii::$app->user->identity;
$ctrl = Yii::$app->controller->id;

// Бейджи для меню
$openTickets = SupportTicket::find()->where(['status' => [SupportTicket::STATUS_OPEN, SupportTicket::STATUS_IN_PROGRESS]])->count();
$pendingDamages = DamageReport::find()->where(['status' => [DamageReport::STATUS_REPORTED, DamageReport::STATUS_REVIEWING]])->count();
$pendingReviews = Review::find()->where(['status' => Review::STATUS_PENDING])->count();

$menu = [
    ['label' => 'Основное'],
    ['title' => 'Дашборд', 'icon' => 'fa-gauge-high', 'url' => ['/admin/default/index'], 'active' => $ctrl === 'default'],
    ['title' => 'Бронирования', 'icon' => 'fa-key', 'url' => ['/admin/booking/index'], 'active' => $ctrl === 'booking'],
    ['title' => 'Финансы', 'icon' => 'fa-chart-line', 'url' => ['/admin/finance/index'], 'active' => $ctrl === 'finance'],

    ['label' => 'Каталог'],
    ['title' => 'Автомобили', 'icon' => 'fa-car', 'url' => ['/admin/car/index'], 'active' => $ctrl === 'car'],
    ['title' => 'Тарифы', 'icon' => 'fa-tags', 'url' => ['/admin/tariff/index'], 'active' => $ctrl === 'tariff'],
    ['title' => 'Промокоды', 'icon' => 'fa-percent', 'url' => ['/admin/promo-code/index'], 'active' => $ctrl === 'promo-code'],

    ['label' => 'Клиенты'],
    ['title' => 'Пользователи', 'icon' => 'fa-users', 'url' => ['/admin/user/index'], 'active' => $ctrl === 'user'],
    ['title' => 'Поддержка', 'icon' => 'fa-life-ring', 'url' => ['/admin/support/index'], 'active' => $ctrl === 'support', 'badge' => $openTickets],
    ['title' => 'Отзывы', 'icon' => 'fa-star', 'url' => ['/admin/review/index'], 'active' => $ctrl === 'review', 'badge' => $pendingReviews],
    ['title' => 'Повреждения', 'icon' => 'fa-triangle-exclamation', 'url' => ['/admin/damage/index'], 'active' => $ctrl === 'damage', 'badge' => $pendingDamages],

    ['label' => 'Контент'],
    ['title' => 'FAQ', 'icon' => 'fa-circle-question', 'url' => ['/admin/faq/index'], 'active' => $ctrl === 'faq'],
    ['title' => 'Страницы', 'icon' => 'fa-file-lines', 'url' => ['/admin/page/index'], 'active' => $ctrl === 'page'],
    ['title' => 'История локаций', 'icon' => 'fa-map-location', 'url' => ['/admin/location/index'], 'active' => $ctrl === 'location'],

    ['label' => 'Система'],
    ['title' => 'Настройки', 'icon' => 'fa-cog', 'url' => ['/admin/setting/index'], 'active' => $ctrl === 'setting'],
];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title ? $this->title . ' · Админка' : 'Админка · Вольный Ход') ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="<?= Url::to(['/admin/default/index']) ?>" class="admin-sidebar__brand">
            <span class="vh-brand__logo"><i class="fa-solid fa-road"></i></span>
            <div>
                <div style="font-size:1.05rem;line-height:1;">Вольный Ход</div>
                <div style="font-size:.7rem;font-weight:500;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.04em;margin-top:3px;">Админка</div>
            </div>
        </a>
        <nav class="admin-sidebar__nav">
            <?php foreach ($menu as $item): ?>
                <?php if (isset($item['label'])): ?>
                    <div class="admin-sidebar__group"><?= Html::encode($item['label']) ?></div>
                <?php else: ?>
                    <a href="<?= Url::toRoute($item['url']) ?>" class="admin-sidebar__link <?= !empty($item['active']) ? 'is-active' : '' ?>">
                        <i class="fa-solid <?= Html::encode($item['icon']) ?>"></i>
                        <span><?= Html::encode($item['title']) ?></span>
                        <?php if (!empty($item['badge'])): ?>
                            <span class="badge bg-danger"><?= $item['badge'] ?></span>
                        <?php endif ?>
                    </a>
                <?php endif ?>
            <?php endforeach ?>
        </nav>

        <div class="p-3 small" style="border-top:1px solid rgba(255,255,255,.08); color:rgba(255,255,255,.5);">
            <a href="<?= Url::to(['/site/index']) ?>" class="admin-sidebar__link"><i class="fa-solid fa-arrow-left"></i><span>На сайт</span></a>
        </div>
    </aside>

    <div class="admin-content">
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-soft btn-icon d-lg-none" data-admin-sidebar-toggle><i class="fa-solid fa-bars"></i></button>
                <h2 class="admin-page-title"><?= Html::encode($this->title ?: 'Админ-панель') ?></h2>
            </div>

            <div class="dropdown">
                <button class="btn btn-soft d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <span class="vh-avatar" style="width:32px;height:32px;font-size:12px;"><?= Html::encode($user->getInitials()) ?></span>
                    <span class="d-none d-md-inline"><?= Html::encode($user->name ?: $user->email) ?></span>
                    <i class="fa-solid fa-chevron-down small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end vh-dropdown">
                    <li><a class="dropdown-item" href="<?= Url::to(['/profile/index']) ?>"><i class="fa-solid fa-user"></i>Мой профиль</a></li>
                    <li><a class="dropdown-item" href="<?= Url::to(['/site/index']) ?>"><i class="fa-solid fa-house"></i>На сайт</a></li>
                    <li><hr class="dropdown-divider my-2"></li>
                    <li>
                        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline w-100']) ?>
                            <?= Html::submitButton('<i class="fa-solid fa-right-from-bracket"></i>Выйти', ['class' => 'dropdown-item text-danger border-0 bg-transparent w-100 text-start']) ?>
                        <?= Html::endForm() ?>
                    </li>
                </ul>
            </div>
        </header>

        <main class="admin-main">
            <?= Alert::widget() ?>
            <?= $content ?>
        </main>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
