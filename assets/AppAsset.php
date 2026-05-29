<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // Inter — современный шрифт от Rasmus Andersson
        ['https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap', 'rel' => 'stylesheet'],
        // FontAwesome 6 free
        ['https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', 'rel' => 'stylesheet'],
        'css/site.css',
    ];
    public $js = [
        'js/site.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
