<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Car;
use app\models\Tariff;

class MapController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            'tariffs' => Tariff::listActive(),
        ]);
    }

    public function actionCars()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cars = Car::find()
            ->with('tariff')
            ->where(['status' => Car::STATUS_AVAILABLE])
            ->andWhere(['is not', 'lat', null])
            ->andWhere(['is not', 'lng', null])
            ->all();
        $result = [];
        foreach ($cars as $car) {
            $result[] = [
                'id' => $car->id,
                'lat' => (float)$car->lat,
                'lng' => (float)$car->lng,
                'brand' => $car->brand,
                'model' => $car->model,
                'plate' => $car->license_plate,
                'tariff' => $car->tariff->name ?? '',
                'tariffColor' => $car->tariff->color ?? '#0d6efd',
                'pricePerMinute' => (float)($car->tariff->price_per_minute ?? 0),
                'pricePerKm' => (float)($car->tariff->price_per_km ?? 0),
                'fuelLevel' => $car->fuel_level,
                'photo' => $car->getMainPhotoUrl(),
                'address' => $car->address,
                'url' => \yii\helpers\Url::to(['/car/view', 'id' => $car->id]),
            ];
        }
        return $result;
    }
}
