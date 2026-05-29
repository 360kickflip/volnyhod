<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\PromoCode;
use app\models\Booking;

class PromoCodeController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['post'], 'toggle' => ['post']],
            ],
        ]);
    }

    public function actionIndex()
    {
        $query = PromoCode::find()->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 25]]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionCreate()
    {
        $model = new PromoCode(['type' => 'percent', 'value' => 10, 'is_active' => true, 'per_user_limit' => 1]);
        return $this->save($model);
    }

    public function actionUpdate($id)
    {
        $model = PromoCode::findOne($id);
        if (!$model) throw new NotFoundHttpException();
        return $this->save($model);
    }

    private function save(PromoCode $model)
    {
        if ($model->load(Yii::$app->request->post())) {
            $model->code = strtoupper(trim($model->code));
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Промокод сохранён.');
                return $this->redirect(['index']);
            }
        }
        return $this->render('form', ['model' => $model]);
    }

    public function actionToggle($id)
    {
        $p = PromoCode::findOne($id);
        if (!$p) throw new NotFoundHttpException();
        $p->is_active = !$p->is_active;
        $p->save(false);
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $p = PromoCode::findOne($id);
        if (!$p) throw new NotFoundHttpException();
        $p->delete();
        Yii::$app->session->setFlash('success', 'Промокод удалён.');
        return $this->redirect(['index']);
    }

    public function actionGenerate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['code' => PromoCode::generateCode(8)];
    }
}
