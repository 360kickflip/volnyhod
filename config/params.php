<?php

return [
    'siteName' => 'Вольный Ход',
    'siteSlogan' => 'Каршеринг нового поколения',
    'adminEmail' => 'admin@volnyhod.ru',
    'senderEmail' => 'noreply@volnyhod.ru',
    'senderName' => 'Вольный Ход',
    'supportPhone' => '+7 (800) 555-35-35',
    'supportEmail' => 'support@volnyhod.ru',

    // Параметры по умолчанию
    'defaultDeposit' => 2000.00, // ₽ депозит при бронировании
    'defaultCity' => 'Москва',
    'defaultCenter' => ['lat' => 55.7558, 'lng' => 37.6173], // Москва

    // Настройки загрузки
    'uploadPath' => '@webroot/uploads',
    'uploadUrl' => '@web/uploads',
    'maxPhotoSize' => 5 * 1024 * 1024, // 5MB
    'allowedPhotoExt' => ['jpg', 'jpeg', 'png', 'webp'],

    // Постраничный вывод
    'pageSize' => 15,

    // Юкасса/Робокасса (заглушки)
    'paymentGateway' => 'demo',
    'yookassaShopId' => '',
    'yookassaSecretKey' => '',

    // Яндекс карты (для прода — поставить ключ)
    'yandexMapsApiKey' => '',
];
