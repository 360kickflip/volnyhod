<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\SupportTicket;
use app\models\SupportMessage;
use app\models\Notification;
use app\models\User;

class SupportController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['reply' => ['post'], 'assign' => ['post'], 'status' => ['post']],
            ],
        ]);
    }

    public function actionIndex()
    {
        $query = SupportTicket::find()->with('user');
        $status = Yii::$app->request->get('status');
        $category = Yii::$app->request->get('category');
        $priority = Yii::$app->request->get('priority');
        if ($status) $query->andWhere(['status' => $status]);
        if ($category) $query->andWhere(['category' => $category]);
        if ($priority) $query->andWhere(['priority' => $priority]);
        $query->orderBy(['updated_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 25]]);
        return $this->render('index', ['dataProvider' => $dataProvider, 'filters' => compact('status', 'category', 'priority')]);
    }

    public function actionView($id)
    {
        $ticket = SupportTicket::findOne($id);
        if (!$ticket) throw new NotFoundHttpException();
        $admins = User::find()->where(['role' => [User::ROLE_ADMIN, User::ROLE_MANAGER]])->all();
        return $this->render('view', ['ticket' => $ticket, 'admins' => $admins]);
    }

    public function actionReply($id)
    {
        $ticket = SupportTicket::findOne($id);
        if (!$ticket) throw new NotFoundHttpException();
        $message = trim(Yii::$app->request->post('message', ''));
        $newStatus = Yii::$app->request->post('status', null);
        if (!$message) {
            Yii::$app->session->setFlash('error', 'Введите сообщение.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $msg = new SupportMessage([
            'ticket_id' => $ticket->id,
            'author_id' => Yii::$app->user->id,
            'author_role' => SupportMessage::ROLE_ADMIN,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $msg->save(false);

        if ($newStatus && in_array($newStatus, SupportTicket::statuses())) {
            $ticket->status = $newStatus;
        } else {
            $ticket->status = SupportTicket::STATUS_WAITING_USER;
        }
        if (!$ticket->assigned_admin_id) $ticket->assigned_admin_id = Yii::$app->user->id;
        $ticket->save(false);

        Notification::send($ticket->user_id, 'support', 'Ответ от поддержки',
            mb_strimwidth($message, 0, 100, '…'),
            '/support/view/' . $ticket->id, 'fa-headset');

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAssign($id)
    {
        $ticket = SupportTicket::findOne($id);
        if (!$ticket) throw new NotFoundHttpException();
        $ticket->assigned_admin_id = (int)Yii::$app->request->post('admin_id') ?: null;
        $ticket->save(false);
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionStatus($id)
    {
        $ticket = SupportTicket::findOne($id);
        if (!$ticket) throw new NotFoundHttpException();
        $status = Yii::$app->request->post('status');
        if (in_array($status, SupportTicket::statuses())) {
            $ticket->status = $status;
            if ($status === SupportTicket::STATUS_CLOSED) $ticket->closed_at = date('Y-m-d H:i:s');
            $ticket->save(false);
        }
        return $this->redirect(['view', 'id' => $id]);
    }
}
