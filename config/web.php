<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'volnyhod',
    'name' => 'Вольный Ход',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
            'layout' => 'admin',
        ],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'CCmPbB9M0pdipltpIUkjcMhqBc_MxuAO',
            'enableCsrfValidation' => true,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'charset' => 'UTF-8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'cookieParams' => [
                'httponly' => true,
                'lifetime' => 86400 * 7, // 7 дней
            ],
            'timeout' => 86400 * 7,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '' => 'site/index',
                'login' => 'site/login',
                'signup' => 'site/signup',
                'logout' => 'site/logout',
                'catalog' => 'car/index',
                'map' => 'map/index',
                'trips' => 'trip/index',
                'trips/<id:\d+>' => 'trip/view',
                'profile' => 'profile/index',
                'profile/<action:[\w-]+>' => 'profile/<action>',
                'balance' => 'balance/index',
                'notifications' => 'notification/index',
                'support' => 'support/index',
                'support/<action:[\w-]+>' => 'support/<action>',
                'support/<action:[\w-]+>/<id:\d+>' => 'support/<action>',
                'faq' => 'faq/index',
                'page/<slug:[\w-]+>' => 'page/view',
                'admin' => 'admin/default/index',
                '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>',
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'ru-RU',
            'currencyCode' => 'RUB',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:d.m.Y H:i',
            'timeFormat' => 'php:H:i',
            'thousandSeparator' => ' ',
            'decimalSeparator' => ',',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];
}

return $config;
