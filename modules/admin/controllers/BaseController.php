<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use app\models\User;

/**
 * Базовый контроллер админки. Доступ только для admin/manager.
 */
class BaseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $u = Yii::$app->user->identity;
                            return $u && $u->isManager();
                        },
                    ],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->getResponse()->redirect(['/site/login']);
                    }
                    throw new ForbiddenHttpException('Доступ запрещён.');
                },
            ],
        ];
    }
}
