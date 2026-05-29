<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\User;
use app\models\UserDocument;
use app\models\Notification;
use app\models\Transaction;

class UserController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'block' => ['post'], 'unblock' => ['post'],
                    'verify' => ['post'], 'reject-verification' => ['post'],
                    'adjust-balance' => ['post'],
                    'review-document' => ['post'],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $query = User::find();

        $status = Yii::$app->request->get('status');
        $verification = Yii::$app->request->get('verification');
        $search = Yii::$app->request->get('q');

        if ($status) $query->andWhere(['status' => $status]);
        if ($verification) $query->andWhere(['verification_status' => $verification]);
        if ($search) $query->andWhere(['or',
            ['like', 'email', $search],
            ['like', 'phone', $search],
            ['like', 'name', $search],
        ]);
        $query->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 25],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'filters' => compact('status', 'verification', 'search'),
        ]);
    }

    public function actionView($id)
    {
        $user = User::findOne($id);
        if (!$user) throw new NotFoundHttpException();

        return $this->render('view', [
            'user' => $user,
            'documents' => $user->documents,
            'bookings' => $user->getBookings()->orderBy(['created_at' => SORT_DESC])->limit(10)->all(),
            'transactions' => $user->getTransactions()->limit(10)->all(),
        ]);
    }

    public function actionBlock($id)
    {
        $user = $this->find($id);
        $reason = Yii::$app->request->post('reason', '');
        $user->status = User::STATUS_BLOCKED;
        $user->block_reason = $reason;
        $user->save(false);
        Notification::send($user->id, 'danger', 'Аккаунт заблокирован', $reason ?: 'Свяжитесь с поддержкой.', '/support', 'fa-ban');
        Yii::$app->session->setFlash('success', 'Пользователь заблокирован.');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionUnblock($id)
    {
        $user = $this->find($id);
        $user->status = User::STATUS_ACTIVE;
        $user->block_reason = null;
        $user->save(false);
        Notification::send($user->id, 'success', 'Аккаунт разблокирован', 'Доступ восстановлен.', '/profile', 'fa-circle-check');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionVerify($id)
    {
        $user = $this->find($id);
        $user->verification_status = User::VERIFICATION_VERIFIED;
        $user->save(false);
        UserDocument::updateAll(['status' => UserDocument::STATUS_APPROVED, 'reviewer_id' => Yii::$app->user->id, 'reviewed_at' => date('Y-m-d H:i:s')], ['user_id' => $user->id, 'status' => UserDocument::STATUS_PENDING]);
        Notification::send($user->id, 'success', 'Верификация пройдена', 'Документы проверены, можно арендовать авто.', '/profile/documents', 'fa-circle-check');
        Yii::$app->session->setFlash('success', 'Пользователь верифицирован.');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionRejectVerification($id)
    {
        $user = $this->find($id);
        $reason = Yii::$app->request->post('reason', '');
        $user->verification_status = User::VERIFICATION_REJECTED;
        $user->save(false);
        Notification::send($user->id, 'danger', 'Верификация отклонена', $reason ?: 'Перезагрузите документы.', '/profile/documents', 'fa-circle-xmark');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionReviewDocument($id)
    {
        $doc = UserDocument::findOne($id);
        if (!$doc) throw new NotFoundHttpException();
        $action = Yii::$app->request->post('action');
        $comment = Yii::$app->request->post('comment', '');
        if ($action === 'approve') {
            $doc->status = UserDocument::STATUS_APPROVED;
        } elseif ($action === 'reject') {
            $doc->status = UserDocument::STATUS_REJECTED;
            $doc->comment = $comment;
        }
        $doc->reviewer_id = Yii::$app->user->id;
        $doc->reviewed_at = date('Y-m-d H:i:s');
        $doc->save(false);
        return $this->redirect(['view', 'id' => $doc->user_id]);
    }

    public function actionAdjustBalance($id)
    {
        $user = $this->find($id);
        $amount = (float)Yii::$app->request->post('amount', 0);
        $note = trim(Yii::$app->request->post('note', 'Корректировка баланса'));
        if ($amount === 0.0) {
            Yii::$app->session->setFlash('error', 'Сумма не может быть нулевой.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $tx = new Transaction([
            'user_id' => $user->id,
            'type' => Transaction::TYPE_CORRECTION,
            'amount' => $amount,
            'status' => Transaction::STATUS_COMPLETED,
            'payment_method' => 'manual',
            'description' => $note,
            'created_at' => date('Y-m-d H:i:s'),
            'completed_at' => date('Y-m-d H:i:s'),
        ]);
        $user->balance = max(0, (float)$user->balance + $amount);
        $tx->balance_after = $user->balance;
        $user->save(false);
        $tx->save(false);

        Notification::send($user->id, 'payment', 'Баланс скорректирован', $note . ': ' . Yii::$app->formatter->asCurrency($amount), '/balance', 'fa-balance-scale');
        Yii::$app->session->setFlash('success', 'Баланс скорректирован.');
        return $this->redirect(['view', 'id' => $id]);
    }

    private function find($id)
    {
        $u = User::findOne($id);
        if (!$u) throw new NotFoundHttpException();
        return $u;
    }
}
