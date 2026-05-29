<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Car;
use app\models\CarPhoto;
use app\models\Tariff;

class CarController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['post'], 'delete-photo' => ['post']],
            ],
        ]);
    }

    public function actionIndex()
    {
        $query = Car::find()->with(['tariff']);
        $status = Yii::$app->request->get('status');
        $search = Yii::$app->request->get('q');
        if ($status) $query->andWhere(['status' => $status]);
        if ($search) $query->andWhere(['or',
            ['like', 'brand', $search],
            ['like', 'model', $search],
            ['like', 'license_plate', $search],
        ]);
        $query->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider, 'status' => $status, 'search' => $search]);
    }

    public function actionCreate()
    {
        $model = new Car();
        $model->status = Car::STATUS_AVAILABLE;
        return $this->saveCar($model);
    }

    public function actionUpdate($id)
    {
        $model = Car::findOne($id);
        if (!$model) throw new NotFoundHttpException();
        return $this->saveCar($model);
    }

    private function saveCar(Car $model)
    {
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Загрузка фото
            $photos = UploadedFile::getInstancesByName('Car[photos]');
            if ($photos) {
                $dir = Yii::getAlias('@webroot/uploads/cars');
                if (!is_dir($dir)) @mkdir($dir, 0775, true);
                foreach ($photos as $f) {
                    $name = 'car_' . $model->id . '_' . uniqid() . '.' . $f->extension;
                    if ($f->saveAs($dir . '/' . $name)) {
                        $cp = new CarPhoto([
                            'car_id' => $model->id,
                            'file_path' => $name,
                            'is_main' => CarPhoto::find()->where(['car_id' => $model->id])->count() === 0,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                        $cp->save(false);
                    }
                }
            }
            Yii::$app->session->setFlash('success', 'Автомобиль сохранён.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('form', ['model' => $model, 'tariffs' => Tariff::find()->orderBy(['sort_order' => SORT_ASC])->all()]);
    }

    public function actionView($id)
    {
        $car = Car::findOne($id);
        if (!$car) throw new NotFoundHttpException();
        $bookings = $car->getBookings()->orderBy(['created_at' => SORT_DESC])->limit(20)->all();
        return $this->render('view', ['car' => $car, 'bookings' => $bookings]);
    }

    public function actionDelete($id)
    {
        $car = Car::findOne($id);
        if (!$car) throw new NotFoundHttpException();
        try {
            $car->delete();
            Yii::$app->session->setFlash('success', 'Автомобиль удалён.');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', 'Не удалось удалить (есть связанные бронирования).');
        }
        return $this->redirect(['index']);
    }

    public function actionDeletePhoto($id)
    {
        $photo = CarPhoto::findOne($id);
        if (!$photo) throw new NotFoundHttpException();
        $carId = $photo->car_id;
        @unlink(Yii::getAlias('@webroot/uploads/cars/' . $photo->file_path));
        $photo->delete();
        return $this->redirect(['view', 'id' => $carId]);
    }
}
