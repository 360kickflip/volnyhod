<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Booking;
use app\models\Review;
use app\models\forms\ReviewForm;

class TripController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['review' => ['post']],
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $query = Booking::find()
            ->with(['car', 'tariff', 'review'])
            ->where(['user_id' => $userId])
            ->andWhere(['!=', 'status', Booking::STATUS_ACTIVE]);

        $from = Yii::$app->request->get('from');
        $to = Yii::$app->request->get('to');
        if ($from) $query->andWhere(['>=', 'created_at', $from . ' 00:00:00']);
        if ($to) $query->andWhere(['<=', 'created_at', $to . ' 23:59:59']);

        $query->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function actionView($id)
    {
        $booking = Booking::find()
            ->with(['car', 'tariff', 'charges', 'review', 'damageReports', 'transactions'])
            ->where(['id' => $id, 'user_id' => Yii::$app->user->id])
            ->one();
        if (!$booking) throw new NotFoundHttpException();

        $reviewForm = $booking->canReview() ? new ReviewForm() : null;

        return $this->render('view', [
            'booking' => $booking,
            'reviewForm' => $reviewForm,
        ]);
    }

    public function actionReview($id)
    {
        $booking = Booking::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$booking) throw new NotFoundHttpException();

        $form = new ReviewForm();
        $form->load(Yii::$app->request->post());
        if ($form->create($booking)) {
            Yii::$app->session->setFlash('success', 'Спасибо! Отзыв отправлен на модерацию.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось сохранить отзыв.');
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionReceipt($id)
    {
        $booking = Booking::find()
            ->with(['car', 'tariff', 'charges', 'user'])
            ->where(['id' => $id, 'user_id' => Yii::$app->user->id])
            ->one();
        if (!$booking) throw new NotFoundHttpException();

        // Генерация HTML-чека (печать в PDF через окно браузера)
        return $this->renderPartial('receipt', ['booking' => $booking]);
    }
}
