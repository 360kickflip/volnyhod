<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Setting;

class SettingController extends BaseController
{
    public function actionIndex()
    {
        $group = Yii::$app->request->get('group', 'general');

        if (Yii::$app->request->isPost) {
            $values = (array)Yii::$app->request->post('Setting', []);
            foreach ($values as $key => $val) {
                Setting::set($key, $val);
            }
            Yii::$app->session->setFlash('success', 'Настройки сохранены.');
            return $this->refresh();
        }

        $settings = Setting::byGroup($group);
        $allGroups = Setting::find()->select('group')->distinct()->column();

        return $this->render('index', [
            'settings' => $settings,
            'allGroups' => $allGroups,
            'currentGroup' => $group,
        ]);
    }
}
