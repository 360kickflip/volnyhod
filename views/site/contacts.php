<?php
/** @var yii\web\View $this */
/** @var app\models\forms\SupportTicketForm|null $form */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use app\models\SupportTicket;

$this->title = 'Контакты — Вольный Ход';
$this->params['meta_description'] = 'Контакты сервиса каршеринга Вольный Ход — телефон, email, адрес, форма обратной связи';

$apiKey = Yii::$app->params['yandexMapsApiKey'] ?? '';
$ymUrl = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU' . ($apiKey ? '&apikey=' . urlencode($apiKey) : '');
$this->registerJsFile($ymUrl, ['position' => \yii\web\View::POS_END]);

$officeLat = 55.7558;
$officeLng = 37.6173;
$officeAddress = 'Москва, ул. Тверская, 1';
?>

<nav class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= Url::home() ?>" class="text-decoration-none">Главная</a></li>
        <li class="breadcrumb-item active">Контакты</li>
    </ol>
</nav>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="m-0">Контакты</h1>
        <p class="text-soft m-0 mt-1">Мы всегда на связи 24/7</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 text-center">
            <div class="card-body p-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;border-radius:50%;background: var(--vh-primary-light); color: var(--vh-primary-dark);">
                    <i class="fa-solid fa-phone fa-xl"></i>
                </div>
                <h5 class="mb-1">Позвоните</h5>
                <p class="text-soft small mb-3">Поддержка круглосуточно</p>
                <a href="tel:<?= preg_replace('/[^+\d]/', '', Yii::$app->params['supportPhone']) ?>" class="h5 d-block text-decoration-none">
                    <?= Yii::$app->params['supportPhone'] ?>
                </a>
                <small class="text-soft">бесплатно по России</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 text-center">
            <div class="card-body p-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;border-radius:50%;background: #dbeafe; color: #1d4ed8;">
                    <i class="fa-solid fa-envelope fa-xl"></i>
                </div>
                <h5 class="mb-1">Напишите</h5>
                <p class="text-soft small mb-3">Ответим в течение часа</p>
                <a href="mailto:<?= Yii::$app->params['supportEmail'] ?>" class="h5 d-block text-decoration-none">
                    <?= Yii::$app->params['supportEmail'] ?>
                </a>
                <small class="text-soft">по любым вопросам</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 text-center">
            <div class="card-body p-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;border-radius:50%;background: #fef3c7; color: #b45309;">
                    <i class="fa-solid fa-comments fa-xl"></i>
                </div>
                <h5 class="mb-1">Чат поддержки</h5>
                <p class="text-soft small mb-3">В личном кабинете</p>
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Url::to(['/site/login']) ?>" class="btn btn-soft">Войти и написать</a>
                <?php else: ?>
                    <a href="<?= Url::to(['/support/create']) ?>" class="btn btn-soft">Создать обращение</a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Карта -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-location-dot me-2 text-primary"></i>Главный офис</div>
            <div class="card-body p-0">
                <div id="contacts-map" style="height: 420px;"></div>
                <div class="p-4 border-top">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-soft small mb-1">Адрес</div>
                            <div class="fw-semibold"><?= Html::encode($officeAddress) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-soft small mb-1">Метро</div>
                            <div class="fw-semibold"><i class="fa-solid fa-circle text-danger me-1" style="font-size:.6rem;"></i> Охотный ряд (3 мин пешком)</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-soft small mb-1">Часы работы офиса</div>
                            <div class="fw-semibold">Пн–Пт: 9:00 — 20:00</div>
                            <div class="fw-semibold">Сб–Вс: 10:00 — 18:00</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-soft small mb-1">Поддержка по телефону</div>
                            <div class="fw-semibold text-success"><i class="fa-solid fa-circle me-1" style="font-size:.6rem;"></i> 24/7 без выходных</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Форма обратной связи -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-paper-plane me-2 text-primary"></i>Написать нам</div>
            <div class="card-body p-4">
                <?php if (Yii::$app->user->isGuest): ?>
                    <p class="text-soft">Чтобы создать обращение в поддержку, войдите в личный кабинет.</p>
                    <a href="<?= Url::to(['/site/login']) ?>" class="btn btn-primary w-100 mb-2"><i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Войти</a>
                    <a href="<?= Url::to(['/site/signup']) ?>" class="btn btn-soft w-100">Зарегистрироваться</a>
                    <hr class="my-4">
                    <p class="small text-soft mb-2">Или напишите напрямую:</p>
                    <a href="mailto:<?= Yii::$app->params['supportEmail'] ?>" class="d-block">
                        <i class="fa-solid fa-envelope me-1"></i> <?= Yii::$app->params['supportEmail'] ?>
                    </a>
                    <a href="tel:<?= preg_replace('/[^+\d]/', '', Yii::$app->params['supportPhone']) ?>" class="d-block mt-2">
                        <i class="fa-solid fa-phone me-1"></i> <?= Yii::$app->params['supportPhone'] ?>
                    </a>
                <?php else: ?>
                    <?php $f = ActiveForm::begin([
                        'action' => Url::to(['/site/contacts']),
                        'fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']],
                    ]) ?>
                        <?= $f->field($form, 'category')->dropDownList(SupportTicket::categoryDropdown(), ['prompt' => '— категория —']) ?>
                        <?= $f->field($form, 'subject')->textInput(['placeholder' => 'Кратко о проблеме']) ?>
                        <?= $f->field($form, 'message')->textarea(['rows' => 5, 'placeholder' => 'Опишите подробно']) ?>
                        <?= Html::submitButton('<i class="fa-solid fa-paper-plane me-1"></i> Отправить', ['class' => 'btn btn-primary w-100']) ?>
                    <?php ActiveForm::end() ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<!-- Социальные сети + реквизиты -->
<div class="row g-3">
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="fa-solid fa-share-nodes me-2 text-primary"></i>Мы в социальных сетях</h5>
                <p class="text-soft small mb-3">Подписывайтесь на наши каналы — там акции, новости и истории клиентов</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="https://vk.com/" target="_blank" rel="noopener" class="btn btn-soft"><i class="fa-brands fa-vk me-2"></i>VKontakte</a>
                    <a href="https://t.me/" target="_blank" rel="noopener" class="btn btn-soft"><i class="fa-brands fa-telegram me-2"></i>Telegram</a>
                    <a href="https://youtube.com/" target="_blank" rel="noopener" class="btn btn-soft"><i class="fa-brands fa-youtube me-2"></i>YouTube</a>
                    <a href="https://dzen.ru/" target="_blank" rel="noopener" class="btn btn-soft"><i class="fa-solid fa-blog me-2"></i>Дзен</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="fa-solid fa-file-contract me-2 text-primary"></i>Реквизиты</h5>
                <dl class="row small mb-0">
                    <dt class="col-sm-5 text-soft">Юр. лицо</dt><dd class="col-sm-7">ООО «Вольный Ход»</dd>
                    <dt class="col-sm-5 text-soft">ИНН</dt><dd class="col-sm-7">7700000000</dd>
                    <dt class="col-sm-5 text-soft">ОГРН</dt><dd class="col-sm-7">1117700000000</dd>
                    <dt class="col-sm-5 text-soft">КПП</dt><dd class="col-sm-7">770000000</dd>
                </dl>
                <hr>
                <a href="<?= Url::to(['/page/view', 'slug' => 'user-agreement']) ?>" class="small text-decoration-none">Пользовательское соглашение →</a>
            </div>
        </div>
    </div>
</div>

<?php
$address = addslashes(Html::encode($officeAddress));
$this->registerJs(<<<JS
ymaps.ready(function () {
    const cmap = new ymaps.Map('contacts-map', {
        center: [$officeLat, $officeLng],
        zoom: 15,
        controls: ['zoomControl', 'geolocationControl', 'typeSelector']
    });
    const placemark = new ymaps.Placemark([$officeLat, $officeLng], {
        balloonContentHeader: '<strong>Вольный Ход</strong>',
        balloonContentBody: '$address<br><a href="tel:+78005553535">+7 (800) 555-35-35</a>',
        hintContent: '$address'
    }, { preset: 'islands#greenAutoIcon' });
    cmap.geoObjects.add(placemark);
});
JS);
?>
