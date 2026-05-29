<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use app\models\Car;
use app\models\Tariff;
use app\models\forms\BookingForm;

class CarController extends Controller
{
    public function actionIndex()
    {
        $query = Car::find()->with(['tariff', 'photos']);

        // Фильтры
        $tariffId = Yii::$app->request->get('tariff');
        $transmission = Yii::$app->request->get('transmission');
        $status = Yii::$app->request->get('status', 'available');
        $search = Yii::$app->request->get('q');

        if ($tariffId) $query->andWhere(['tariff_id' => $tariffId]);
        if ($transmission) $query->andWhere(['transmission' => $transmission]);
        if ($status === 'available') {
            $query->andWhere(['status' => Car::STATUS_AVAILABLE]);
        } elseif ($status === 'all') {
            // ничего
        } else {
            $query->andWhere(['status' => $status]);
        }
        if ($search) {
            $query->andWhere(['or',
                ['like', 'brand', $search],
                ['like', 'model', $search],
                ['like', 'license_plate', $search],
            ]);
        }
        $query->orderBy(['status' => SORT_ASC, 'brand' => SORT_ASC, 'model' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 12],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tariffs' => Tariff::listActive(),
            'filters' => compact('tariffId', 'transmission', 'status', 'search'),
        ]);
    }

    public function actionView($id)
    {
        $car = Car::findOne($id);
        if (!$car) throw new NotFoundHttpException('Автомобиль не найден.');

        $bookingForm = null;
        if (!Yii::$app->user->isGuest) {
            $bookingForm = new BookingForm(['car_id' => $car->id, 'planned_minutes' => 60]);
            $bookingForm->calculate(Yii::$app->user->identity);
        }

        return $this->render('view', [
            'car' => $car,
            'bookingForm' => $bookingForm,
            'reviews' => $car->reviews,
        ]);
    }
}
