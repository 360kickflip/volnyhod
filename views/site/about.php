<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'О компании — Вольный Ход';
$this->params['meta_description'] = 'История, миссия и команда сервиса каршеринга «Вольный Ход»';
?>

<nav class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= Url::home() ?>" class="text-decoration-none">Главная</a></li>
        <li class="breadcrumb-item active">О компании</li>
    </ol>
</nav>

<!-- HERO -->
<section class="vh-hero mt-3 mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <span class="badge badge-soft-primary mb-3" style="background: rgba(255,255,255,.12); color:#fff;">
                <i class="fa-solid fa-building me-1"></i> О компании
            </span>
            <h1 class="mb-3">Делаем городскую<br><span class="grad-text" style="-webkit-text-fill-color: transparent;">мобильность доступной</span></h1>
            <p class="lead mb-0" style="color: rgba(255,255,255,.85)">
                «Вольный Ход» — это не просто каршеринг. Это движение за свободу передвижения, за то, чтобы городская жизнь была проще, дешевле и удобнее для каждого.
            </p>
        </div>
        <div class="col-lg-6 d-none d-lg-block">
            <img src="<?= Yii::getAlias('@web/img/about/hero.svg') ?>" alt="Вольный Ход" class="img-fluid rounded-2xl"
                 style="max-height: 360px; object-fit: cover;"
                 onerror="this.style.display='none'">
        </div>
    </div>
</section>

<!-- CIFRY -->
<section class="my-5">
    <div class="row g-3 text-center">
        <?php
        $stats = [
            ['icon' => 'fa-calendar-day', 'color' => '#00c896', 'value' => '2020', 'label' => 'год основания'],
            ['icon' => 'fa-users',        'color' => '#0d6efd', 'value' => '50K+', 'label' => 'активных клиентов'],
            ['icon' => 'fa-car-side',     'color' => '#f59e0b', 'value' => '500+', 'label' => 'автомобилей в парке'],
            ['icon' => 'fa-route',        'color' => '#ef4444', 'value' => '2M+',  'label' => 'километров проехали'],
        ];
        foreach ($stats as $s): ?>
            <div class="col-md-3 col-6">
                <div class="vh-stat h-100 align-items-center">
                    <div class="vh-stat__icon mx-auto" style="background: <?= $s['color'] ?>20; color: <?= $s['color'] ?>;">
                        <i class="fa-solid <?= $s['icon'] ?>"></i>
                    </div>
                    <div class="vh-stat__value mx-auto" style="font-size: 2.25rem;"><?= $s['value'] ?></div>
                    <div class="vh-stat__label" style="text-align:center;"><?= $s['label'] ?></div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>

<!-- МИССИЯ И ЦЕННОСТИ -->
<section class="my-5">
    <div class="row g-4 align-items-center">
        <div class="col-lg-5">
            <span class="badge badge-soft-primary mb-2">Наша миссия</span>
            <h2 class="section-title m-0 mb-3">Свобода для каждого, в каждом городе</h2>
            <p class="text-soft">
                Мы верим: автомобиль не должен быть роскошью или обузой. Мы делаем владение машиной
                ненужным — взяли, доехали, оставили. Никаких страховок, парковок, ТО и кредитов.
                Только свобода передвижения по доступной цене.
            </p>
        </div>
        <div class="col-lg-7">
            <div class="row g-3">
                <?php
                $values = [
                    ['fa-handshake',     '#00c896', 'Доверие',          'Всегда говорим правду — про тарифы, штрафы и условия.'],
                    ['fa-bolt',          '#0d6efd', 'Скорость',         'Решаем вопросы в течение минут, а не дней.'],
                    ['fa-leaf',          '#10b981', 'Ответственность',  'К людям, к авто, к городу и к окружающей среде.'],
                    ['fa-star',          '#f59e0b', 'Качество',         'Лучшие авто, лучший сервис, лучшая поддержка.'],
                ];
                foreach ($values as [$icon, $color, $title, $text]): ?>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-inline-flex align-items-center justify-content-center mb-2" style="width:44px;height:44px;border-radius:12px;background: <?= $color ?>15; color: <?= $color ?>;">
                                    <i class="fa-solid <?= $icon ?>"></i>
                                </div>
                                <h6 class="mb-1"><?= $title ?></h6>
                                <p class="small text-soft mb-0"><?= $text ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</section>

<!-- ИСТОРИЯ -->
<section class="my-5">
    <div class="text-center mb-4">
        <span class="badge badge-soft-primary mb-2"><i class="fa-solid fa-clock-rotate-left me-1"></i> История</span>
        <h2 class="section-title m-0">Наш путь</h2>
    </div>
    <div class="vh-timeline">
        <?php
        $milestones = [
            ['2020', 'Запуск',           'Открыли первые 30 автомобилей в Москве. Команда из 5 энтузиастов.'],
            ['2021', 'Расширение',       'Парк вырос до 200 авто. Запустили мобильное приложение и API.'],
            ['2022', 'Новые города',     'Открыли филиалы в Санкт-Петербурге, Казани и Екатеринбурге.'],
            ['2023', '50 000 клиентов',  'Преодолели рубеж в 50 тысяч активных пользователей. Топ-3 каршеринг России.'],
            ['2024', 'Электромобили',    'Добавили электромобили в парк. Курс на экологичный транспорт.'],
            ['2025', 'Премиум-сегмент',  'Запустили премиум-парк (BMW, Mercedes, Audi). Выход в Краснодар.'],
        ];
        foreach ($milestones as [$year, $title, $text]): ?>
            <div class="vh-timeline__item">
                <div class="vh-timeline__year"><?= $year ?></div>
                <div class="vh-timeline__content">
                    <h6 class="mb-1"><?= $title ?></h6>
                    <p class="small text-soft m-0"><?= $text ?></p>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>

<!-- КОМАНДА -->
<section class="my-5">
    <div class="text-center mb-4">
        <span class="badge badge-soft-primary mb-2"><i class="fa-solid fa-users me-1"></i> Команда</span>
        <h2 class="section-title m-0">Люди за сервисом</h2>
        <p class="text-soft mb-0">Замените фото и данные на реальную команду в <code>web/img/team/</code></p>
    </div>
    <div class="row g-3">
        <?php
        $team = [
            ['name' => 'Алексей Петров',    'role' => 'CEO, основатель',      'photo' => 'team/ceo.jpg'],
            ['name' => 'Мария Иванова',     'role' => 'CTO',                  'photo' => 'team/cto.jpg'],
            ['name' => 'Иван Сидоров',      'role' => 'Операционный директор','photo' => 'team/coo.jpg'],
            ['name' => 'Ольга Козлова',     'role' => 'Руководитель поддержки','photo' => 'team/support.jpg'],
        ];
        foreach ($team as $m): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center h-100">
                    <img src="<?= Yii::getAlias('@web/img/' . $m['photo']) ?>"
                         onerror="this.src='<?= Yii::getAlias('@web/img/team/placeholder.svg') ?>';"
                         class="card-img-top" style="aspect-ratio:1;object-fit:cover;border-radius:var(--vh-radius) var(--vh-radius) 0 0;" alt="<?= Html::encode($m['name']) ?>">
                    <div class="card-body">
                        <h6 class="mb-1"><?= Html::encode($m['name']) ?></h6>
                        <div class="text-soft small"><?= Html::encode($m['role']) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>

<!-- CTA -->
<section class="my-5 p-4 p-md-5 rounded-2xl text-center" style="background: linear-gradient(135deg, var(--vh-primary-light), #fff);">
    <h2 class="mb-3">Присоединяйтесь к нам</h2>
    <p class="lead text-soft mb-4">Станьте частью сообщества из 50 000+ свободных водителей.</p>
    <?php if (Yii::$app->user->isGuest): ?>
        <a href="<?= Url::to(['/site/signup']) ?>" class="btn btn-primary btn-lg me-2"><i class="fa-solid fa-rocket me-2"></i>Попробовать</a>
    <?php endif ?>
    <a href="<?= Url::to(['/site/contacts']) ?>" class="btn btn-soft btn-lg"><i class="fa-solid fa-envelope me-2"></i>Связаться с нами</a>
</section>
