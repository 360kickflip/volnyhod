<?php

namespace app\modules\admin;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';
    public $defaultRoute = 'default/index';

    public function init()
    {
        parent::init();
        $this->layout = 'admin';
    }
}
