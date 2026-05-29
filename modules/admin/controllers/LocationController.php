<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\Car;
use app\models\CarLocationHistory;

class LocationController extends BaseController
{
    public function actionIndex()
    {
        $cars = Car::find()->orderBy(['brand' => SORT_ASC])->all();
        $carId = (int)Yii::$app->request->get('car_id');
        $from = Yii::$app->request->get('from');
        $to = Yii::$app->request->get('to');

        $history = [];
        $points = [];
        $car = null;

        if ($carId) {
            $car = Car::findOne($carId);
            $query = CarLocationHistory::find()->where(['car_id' => $carId]);
            if ($from) $query->andWhere(['>=', 'recorded_at', $from . ' 00:00:00']);
            if ($to) $query->andWhere(['<=', 'recorded_at', $to . ' 23:59:59']);
            $query->orderBy(['recorded_at' => SORT_ASC]);

            $dataProvider = new ActiveDataProvider(['query' => clone $query, 'pagination' => ['pageSize' => 30]]);
            foreach ($query->limit(500)->all() as $h) {
                $points[] = ['lat' => (float)$h->lat, 'lng' => (float)$h->lng, 'time' => $h->recorded_at, 'speed' => $h->speed];
            }
            return $this->render('index', compact('cars', 'car', 'carId', 'from', 'to', 'dataProvider', 'points'));
        }
        return $this->render('index', ['cars' => $cars, 'car' => null, 'carId' => null, 'from' => null, 'to' => null, 'dataProvider' => null, 'points' => []]);
    }
}
