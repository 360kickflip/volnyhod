<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Notification;

class NotificationController extends Controller
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
                'actions' => ['read' => ['post'], 'mark-all-read' => ['post']],
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $notifications = Notification::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(200)
            ->all();

        // Группируем по дням
        $grouped = [];
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        foreach ($notifications as $n) {
            $day = date('Y-m-d', strtotime($n->created_at));
            if ($day === $today) $key = 'Сегодня';
            elseif ($day === $yesterday) $key = 'Вчера';
            else $key = Yii::$app->formatter->asDate($n->created_at, 'long');
            $grouped[$key][] = $n;
        }

        return $this->render('index', ['grouped' => $grouped]);
    }

    public function actionRead($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $notif = Notification::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$notif) return ['ok' => false];
        $notif->markRead();
        return ['ok' => true];
    }

    public function actionMarkAllRead()
    {
        Notification::updateAll(
            ['is_read' => true, 'read_at' => date('Y-m-d H:i:s')],
            ['user_id' => Yii::$app->user->id, 'is_read' => false]
        );
        Yii::$app->session->setFlash('success', 'Все уведомления отмечены прочитанными.');
        return $this->redirect(['index']);
    }
}
