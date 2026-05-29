<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\Transaction;
use app\models\forms\TopUpForm;

class BalanceController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $form = new TopUpForm();

        if ($form->load(Yii::$app->request->post()) && $form->process($user)) {
            Yii::$app->session->setFlash('success', 'Баланс успешно пополнен на ' . Yii::$app->formatter->asCurrency($form->amount));
            return $this->refresh();
        }

        $type = Yii::$app->request->get('type');
        $query = Transaction::find()->where(['user_id' => $user->id]);
        if ($type) {
            $query->andWhere(['type' => $type]);
        }
        $query->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
        ]);

        return $this->render('index', [
            'user' => $user,
            'form' => $form,
            'dataProvider' => $dataProvider,
            'currentType' => $type,
        ]);
    }
}
