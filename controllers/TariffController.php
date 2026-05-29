<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Tariff;
use app\models\Car;

class TariffController extends Controller
{
    public function actionIndex()
    {
        $tariffs = Tariff::find()->where(['is_active' => true])->orderBy(['sort_order' => SORT_ASC])->all();

        // Подсчёт авто на каждый тариф
        $carCounts = [];
        foreach ($tariffs as $t) {
            $carCounts[$t->id] = (int)Car::find()->where(['tariff_id' => $t->id])->count();
        }

        return $this->render('index', [
            'tariffs' => $tariffs,
            'carCounts' => $carCounts,
        ]);
    }
}
