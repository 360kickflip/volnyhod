<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Booking;
use app\components\BookingService;

class BookingController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['cancel' => ['post'], 'finish' => ['post']],
            ],
        ]);
    }

    public function actionIndex()
    {
        $query = Booking::find()->with(['user', 'car', 'tariff']);

        $status = Yii::$app->request->get('status');
        $userId = Yii::$app->request->get('user_id');
        $carId = Yii::$app->request->get('car_id');
        $from = Yii::$app->request->get('from');
        $to = Yii::$app->request->get('to');
        $search = Yii::$app->request->get('q');

        if ($status) $query->andWhere(['status' => $status]);
        if ($userId) $query->andWhere(['user_id' => $userId]);
        if ($carId) $query->andWhere(['car_id' => $carId]);
        if ($from) $query->andWhere(['>=', 'created_at', $from . ' 00:00:00']);
        if ($to) $query->andWhere(['<=', 'created_at', $to . ' 23:59:59']);
        if ($search) $query->andWhere(['like', 'number', $search]);

        $query->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 25],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'filters' => compact('status', 'userId', 'carId', 'from', 'to', 'search'),
        ]);
    }

    public function actionView($id)
    {
        $booking = Booking::find()->with(['user', 'car', 'tariff', 'charges', 'review', 'damageReports', 'transactions'])->where(['id' => $id])->one();
        if (!$booking) throw new NotFoundHttpException();
        return $this->render('view', ['booking' => $booking]);
    }

    public function actionCancel($id)
    {
        $booking = Booking::findOne($id);
        if (!$booking) throw new NotFoundHttpException();
        $reason = Yii::$app->request->post('reason', 'Отменено администратором');
        [$ok, $msg] = BookingService::cancel($booking, $reason);
        Yii::$app->session->setFlash($ok ? 'success' : 'error', $ok ? 'Аренда отменена.' : $msg);
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionFinish($id)
    {
        $booking = Booking::findOne($id);
        if (!$booking) throw new NotFoundHttpException();
        [$ok, $msg] = BookingService::finish($booking);
        Yii::$app->session->setFlash($ok ? 'success' : 'error', $ok ? 'Аренда завершена.' : $msg);
        return $this->redirect(['view', 'id' => $id]);
    }
}
