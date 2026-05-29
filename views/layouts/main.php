<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use app\models\Notification;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? 'Каршеринг нового поколения — Вольный Ход']);
$this->registerMetaTag(['name' => 'theme-color', 'content' => '#00c896']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/svg+xml', 'href' => Yii::getAlias('@web/img/logo.svg')]);

$user = Yii::$app->user->isGuest ? null : Yii::$app->user->identity;
$unread = $user ? $user->getUnreadNotificationsCount() : 0;
$activeBooking = $user ? $user->getActiveBooking()->one() : null;

$navItems = [
    ['label' => 'Каталог', 'url' => ['/car/index'], 'icon' => 'fa-list', 'active' => Yii::$app->controller->id === 'car'],
    ['label' => 'Карта', 'url' => ['/map/index'], 'icon' => 'fa-map-location-dot', 'active' => Yii::$app->controller->id === 'map'],
    ['label' => 'Тарифы', 'url' => ['/tariff/index'], 'icon' => 'fa-tags', 'active' => Yii::$app->controller->id === 'tariff'],
    ['label' => 'Мои поездки', 'url' => ['/trip/index'], 'icon' => 'fa-route', 'active' => Yii::$app->controller->id === 'trip'],
];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title ?: Yii::$app->name) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?php $this->beginBody() ?>

<header class="vh-header">
    <div class="container-xl">
        <div class="vh-header__inner">
            <a href="<?= Url::home() ?>" class="vh-brand me-3">
                <span class="vh-brand__logo"><i class="fa-solid fa-road"></i></span>
                <span class="d-none d-sm-block">
                    <span class="vh-brand__name">Вольный Ход</span>
                    <div class="vh-brand__sub d-none d-md-block">каршеринг</div>
                </span>
            </a>

            <?php if ($user): ?>
                <nav class="vh-nav d-none d-lg-flex flex-grow-1">
                    <?php foreach ($navItems as $item): ?>
                        <a href="<?= Url::toRoute($item['url']) ?>" class="vh-nav__link <?= $item['active'] ? 'is-active' : '' ?>">
                            <i class="fa-solid <?= $item['icon'] ?>"></i><?= $item['label'] ?>
                        </a>
                    <?php endforeach ?>
                </nav>

                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a href="<?= Url::to(['/balance/index']) ?>" class="vh-balance-pill" title="Баланс">
                        <i class="fa-solid fa-wallet"></i>
                        <span><?= Yii::$app->formatter->asCurrency($user->balance) ?></span>
                    </a>

                    <a href="<?= Url::to(['/notification/index']) ?>" class="vh-bell" title="Уведомления">
                        <i class="fa-solid fa-bell"></i>
                        <?php if ($unread > 0): ?>
                            <span class="badge"><?= $unread > 99 ? '99+' : $unread ?></span>
                        <?php endif ?>
                    </a>

                    <div class="dropdown">
                        <div class="vh-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if ($user->avatar): ?>
                                <img src="<?= $user->getAvatarUrl() ?>" alt="">
                            <?php else: ?>
                                <?= Html::encode($user->getInitials()) ?>
                            <?php endif ?>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end vh-dropdown">
                            <li class="px-3 py-2">
                                <div class="fw-bold"><?= Html::encode($user->name ?: $user->email) ?></div>
                                <div class="small text-muted"><?= Html::encode($user->email) ?></div>
                            </li>
                            <li><hr class="dropdown-divider my-2"></li>
                            <li><a class="dropdown-item" href="<?= Url::to(['/profile/index']) ?>"><i class="fa-solid fa-user"></i>Профиль</a></li>
                            <li><a class="dropdown-item" href="<?= Url::to(['/balance/index']) ?>"><i class="fa-solid fa-wallet"></i>Баланс</a></li>
                            <li><a class="dropdown-item" href="<?= Url::to(['/referral/index']) ?>"><i class="fa-solid fa-gift text-warning"></i>Пригласить друзей</a></li>
                            <li><a class="dropdown-item" href="<?= Url::to(['/support/index']) ?>"><i class="fa-solid fa-life-ring"></i>Поддержка</a></li>
                            <li><a class="dropdown-item" href="<?= Url::to(['/faq/index']) ?>"><i class="fa-solid fa-circle-question"></i>FAQ</a></li>
                            <?php if ($user->isAdmin()): ?>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li><a class="dropdown-item" href="<?= Url::to(['/admin/default/index']) ?>"><i class="fa-solid fa-gauge-high"></i>Админ-панель</a></li>
                            <?php endif ?>
                            <li><hr class="dropdown-divider my-2"></li>
                            <li>
                                <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline w-100']) ?>
                                <?= Html::submitButton('<i class="fa-solid fa-right-from-bracket"></i>Выйти', ['class' => 'dropdown-item text-danger border-0 bg-transparent w-100 text-start']) ?>
                                <?= Html::endForm() ?>
                            </li>
                        </ul>
                    </div>

                    <button class="vh-mobile-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav">
                        <i class="fa-solid fa-bars fa-lg"></i>
                    </button>
                </div>
            <?php else: ?>
                <nav class="vh-nav d-none d-md-flex flex-grow-1">
                    <a href="<?= Url::to(['/car/index']) ?>" class="vh-nav__link <?= Yii::$app->controller->id === 'car' ? 'is-active' : '' ?>"><i class="fa-solid fa-list"></i>Каталог</a>
                    <a href="<?= Url::to(['/map/index']) ?>" class="vh-nav__link <?= Yii::$app->controller->id === 'map' ? 'is-active' : '' ?>"><i class="fa-solid fa-map-location-dot"></i>Карта</a>
                    <a href="<?= Url::to(['/tariff/index']) ?>" class="vh-nav__link <?= Yii::$app->controller->id === 'tariff' ? 'is-active' : '' ?>"><i class="fa-solid fa-tags"></i>Тарифы</a>
                    <a href="<?= Url::to(['/faq/index']) ?>" class="vh-nav__link <?= Yii::$app->controller->id === 'faq' ? 'is-active' : '' ?>"><i class="fa-solid fa-circle-question"></i>FAQ</a>
                </nav>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a href="<?= Url::to(['/site/login']) ?>" class="btn btn-soft d-none d-sm-inline-flex">Войти</a>
                    <a href="<?= Url::to(['/site/signup']) ?>" class="btn btn-primary">Регистрация</a>
                </div>
            <?php endif ?>
        </div>
    </div>
</header>

<?php if ($user): ?>
<!-- Mobile offcanvas nav -->
<div class="offcanvas offcanvas-end" id="mobileNav" tabindex="-1">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold">Меню</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-flex flex-column gap-2">
            <?php foreach ($navItems as $item): ?>
                <a href="<?= Url::toRoute($item['url']) ?>" class="vh-nav__link <?= $item['active'] ? 'is-active' : '' ?>">
                    <i class="fa-solid <?= $item['icon'] ?>"></i><?= $item['label'] ?>
                </a>
            <?php endforeach ?>
            <a href="<?= Url::to(['/balance/index']) ?>" class="vh-nav__link"><i class="fa-solid fa-wallet"></i>Баланс</a>
            <a href="<?= Url::to(['/referral/index']) ?>" class="vh-nav__link"><i class="fa-solid fa-gift"></i>Пригласить друзей</a>
            <a href="<?= Url::to(['/notification/index']) ?>" class="vh-nav__link"><i class="fa-solid fa-bell"></i>Уведомления <?php if ($unread): ?><span class="badge bg-danger ms-auto"><?= $unread ?></span><?php endif ?></a>
            <a href="<?= Url::to(['/support/index']) ?>" class="vh-nav__link"><i class="fa-solid fa-life-ring"></i>Поддержка</a>
            <a href="<?= Url::to(['/profile/index']) ?>" class="vh-nav__link"><i class="fa-solid fa-user"></i>Профиль</a>
            <a href="<?= Url::to(['/faq/index']) ?>" class="vh-nav__link"><i class="fa-solid fa-circle-question"></i>FAQ</a>
        </div>
    </div>
</div>
<?php endif ?>

<main role="main" class="<?= ($activeBooking && Yii::$app->controller->id !== 'booking') ? '' : '' ?>">
    <?php if ($activeBooking && !in_array(Yii::$app->controller->id . '/' . Yii::$app->controller->action->id, ['booking/active', 'site/login', 'site/signup'])): ?>
        <div class="container-xl mb-3 mt-3">
            <div class="alert alert-info d-flex align-items-center justify-content-between gap-3 mb-0">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid fa-circle-info fa-lg"></i>
                    <div>
                        <strong>Активная аренда:</strong>
                        <?= Html::encode($activeBooking->car->getFullName()) ?> · <?= Html::encode($activeBooking->car->license_plate) ?>
                    </div>
                </div>
                <a href="<?= Url::to(['/booking/active']) ?>" class="btn btn-primary btn-sm">Перейти к аренде</a>
            </div>
        </div>
    <?php endif ?>

    <div class="container-xl">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<?= $this->render('@app/views/_partials/_cookie_consent') ?>

<footer class="vh-footer">
    <div class="container-xl">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <a href="<?= Url::home() ?>" class="vh-brand text-white mb-3 d-inline-flex">
                    <span class="vh-brand__logo"><i class="fa-solid fa-road"></i></span>
                    <span>Вольный Ход</span>
                </a>
                <p class="small mb-3" style="color: rgba(255,255,255,.65);">Каршеринг нового поколения. Свобода передвижения в каждом километре.</p>
                <div class="d-flex gap-2">
                    <a href="#" class="d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(255,255,255,.08);border-radius:10px;color:#fff;" title="VK"><i class="fa-brands fa-vk"></i></a>
                    <a href="#" class="d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(255,255,255,.08);border-radius:10px;color:#fff;" title="Telegram"><i class="fa-brands fa-telegram"></i></a>
                    <a href="#" class="d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(255,255,255,.08);border-radius:10px;color:#fff;" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-6">
                <h6>Сервис</h6>
                <ul>
                    <li><a href="<?= Url::to(['/car/index']) ?>">Каталог</a></li>
                    <li><a href="<?= Url::to(['/map/index']) ?>">Карта</a></li>
                    <li><a href="<?= Url::to(['/tariff/index']) ?>">Тарифы</a></li>
                    <li><a href="<?= Url::to(['/site/about']) ?>">О компании</a></li>
                    <li><a href="<?= Url::to(['/site/contacts']) ?>">Контакты</a></li>
                    <?php if (Yii::$app->user->isGuest): ?>
                        <li><a href="<?= Url::to(['/site/signup']) ?>">Регистрация</a></li>
                    <?php else: ?>
                        <li><a href="<?= Url::to(['/trip/index']) ?>">Мои поездки</a></li>
                    <?php endif ?>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 col-6">
                <h6>Помощь</h6>
                <ul>
                    <li><a href="<?= Url::to(['/faq/index']) ?>">FAQ</a></li>
                    <li><a href="<?= Url::to(['/support/index']) ?>">Поддержка</a></li>
                    <li><a href="<?= Url::to(['/page/view', 'slug' => 'terms']) ?>">Правила сервиса</a></li>
                    <li><a href="<?= Url::to(['/page/view', 'slug' => 'user-agreement']) ?>">Пользовательское соглашение</a></li>
                    <li><a href="<?= Url::to(['/page/view', 'slug' => 'privacy']) ?>">Политика конфиденциальности</a></li>
                    <li><a href="<?= Url::to(['/page/view', 'slug' => 'cookies']) ?>">Политика cookie</a></li>
                    <li><button type="button" class="vh-footer__cookie-btn" data-cookie-action="open-settings"><i class="fa-solid fa-cookie-bite me-1"></i> Настройки cookie</button></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6>Контакты</h6>
                <ul class="m-0">
                    <li class="d-flex align-items-start gap-2 mb-3">
                        <i class="fa-solid fa-phone mt-1" style="color: var(--vh-primary);"></i>
                        <div>
                            <a href="tel:<?= preg_replace('/[^+\d]/', '', Yii::$app->params['supportPhone']) ?>" class="d-block fw-semibold" style="color:#fff;"><?= Yii::$app->params['supportPhone'] ?></a>
                            <small style="color: rgba(255,255,255,.55);">Поддержка 24/7</small>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-2 mb-3">
                        <i class="fa-solid fa-envelope mt-1" style="color: var(--vh-primary);"></i>
                        <div>
                            <a href="mailto:<?= Yii::$app->params['supportEmail'] ?>" class="d-block fw-semibold" style="color:#fff;"><?= Yii::$app->params['supportEmail'] ?></a>
                            <small style="color: rgba(255,255,255,.55);">Ответим в течение часа</small>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-location-dot mt-1" style="color: var(--vh-primary);"></i>
                        <div>
                            <span class="fw-semibold" style="color:#fff;">Москва, ул. Тверская, 1</span><br>
                            <small style="color: rgba(255,255,255,.55);">Пн–Пт: 9:00–20:00</small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="vh-footer__bottom">
            <span>© <?= date('Y') ?> Вольный Ход. Все права защищены.</span>
            <span>Сделано с <i class="fa-solid fa-heart text-danger"></i> для свободы передвижения</span>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
