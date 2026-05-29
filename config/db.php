<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=volnyhod',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'tablePrefix' => '',
    'attributes' => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'",
    ],
];
