<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\SupportTicket;
use app\models\SupportMessage;
use app\models\forms\SupportTicketForm;

class SupportController extends Controller
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
                'actions' => ['create' => ['post', 'get'], 'reply' => ['post'], 'close' => ['post']],
            ],
        ];
    }

    public function actionIndex()
    {
        $tickets = SupportTicket::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();
        return $this->render('index', ['tickets' => $tickets]);
    }

    public function actionCreate()
    {
        $form = new SupportTicketForm();
        if ($form->load(Yii::$app->request->post())) {
            $ticket = $form->create(Yii::$app->user->identity);
            if ($ticket) {
                Yii::$app->session->setFlash('success', 'Обращение создано: ' . $ticket->number);
                return $this->redirect(['view', 'id' => $ticket->id]);
            }
        }
        return $this->render('create', ['form' => $form]);
    }

    public function actionView($id)
    {
        $ticket = $this->findTicket($id);
        return $this->render('view', ['ticket' => $ticket]);
    }

    public function actionReply($id)
    {
        $ticket = $this->findTicket($id);
        $message = trim(Yii::$app->request->post('message', ''));
        if (!$message) {
            Yii::$app->session->setFlash('error', 'Введите сообщение.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $msg = new SupportMessage();
        $msg->ticket_id = $ticket->id;
        $msg->author_id = Yii::$app->user->id;
        $msg->author_role = SupportMessage::ROLE_USER;
        $msg->message = $message;
        $msg->created_at = date('Y-m-d H:i:s');
        $msg->save(false);

        if ($ticket->status === SupportTicket::STATUS_WAITING_USER) {
            $ticket->status = SupportTicket::STATUS_IN_PROGRESS;
        } elseif ($ticket->status === SupportTicket::STATUS_RESOLVED || $ticket->status === SupportTicket::STATUS_CLOSED) {
            $ticket->status = SupportTicket::STATUS_OPEN;
        }
        $ticket->save(false);

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionClose($id)
    {
        $ticket = $this->findTicket($id);
        $ticket->status = SupportTicket::STATUS_CLOSED;
        $ticket->closed_at = date('Y-m-d H:i:s');
        $ticket->save(false);
        Yii::$app->session->setFlash('success', 'Обращение закрыто.');
        return $this->redirect(['index']);
    }

    private function findTicket($id)
    {
        $ticket = SupportTicket::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$ticket) throw new NotFoundHttpException();
        return $ticket;
    }
}
