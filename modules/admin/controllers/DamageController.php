<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\DamageReport;
use app\models\BookingCharge;
use app\models\Notification;

class DamageController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['save' => ['post']],
            ],
        ]);
    }

    public function actionIndex()
    {
        $query = DamageReport::find()->with(['car', 'user', 'booking']);
        $status = Yii::$app->request->get('status');
        $severity = Yii::$app->request->get('severity');
        if ($status) $query->andWhere(['status' => $status]);
        if ($severity) $query->andWhere(['severity' => $severity]);
        $query->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 25]]);
        return $this->render('index', ['dataProvider' => $dataProvider, 'status' => $status, 'severity' => $severity]);
    }

    public function actionView($id)
    {
        $report = DamageReport::findOne($id);
        if (!$report) throw new NotFoundHttpException();
        return $this->render('view', ['report' => $report]);
    }

    public function actionSave($id)
    {
        $report = DamageReport::findOne($id);
        if (!$report) throw new NotFoundHttpException();

        $oldStatus = $report->status;
        $report->status = Yii::$app->request->post('status', $report->status);
        $report->repair_cost = (float)Yii::$app->request->post('repair_cost', 0);
        $report->reviewer_comment = Yii::$app->request->post('reviewer_comment', '');
        $report->reviewer_id = Yii::$app->user->id;
        $report->reviewed_at = date('Y-m-d H:i:s');
        $report->save(false);

        // Если виноват пользователь — добавляем штраф к бронированию
        if ($report->status === DamageReport::STATUS_USER_LIABLE && $report->repair_cost > 0 && $report->booking_id && $oldStatus !== DamageReport::STATUS_USER_LIABLE) {
            $charge = new BookingCharge([
                'booking_id' => $report->booking_id,
                'type' => BookingCharge::TYPE_DAMAGE,
                'description' => 'Возмещение ущерба: ' . mb_strimwidth($report->description, 0, 80, '…'),
                'amount' => $report->repair_cost,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $charge->save(false);
            // Увеличиваем итог
            if ($report->booking) {
                $report->booking->penalty_cost = (float)$report->booking->penalty_cost + $report->repair_cost;
                $report->booking->final_cost = (float)$report->booking->final_cost + $report->repair_cost;
                $report->booking->save(false);
            }
            if ($report->user_id) {
                Notification::send($report->user_id, 'danger', 'Штраф за повреждение',
                    'Сумма: ' . Yii::$app->formatter->asCurrency($report->repair_cost),
                    '/trips/' . $report->booking_id, 'fa-triangle-exclamation');
            }
        }

        Yii::$app->session->setFlash('success', 'Обновлено.');
        return $this->redirect(['view', 'id' => $id]);
    }
}
