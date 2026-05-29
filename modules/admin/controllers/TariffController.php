<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Tariff;

class TariffController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['post'], 'toggle' => ['post'], 'sort' => ['post']],
            ],
        ]);
    }

    public function actionIndex()
    {
        $tariffs = Tariff::find()->orderBy(['sort_order' => SORT_ASC])->all();
        return $this->render('index', ['tariffs' => $tariffs]);
    }

    public function actionCreate()
    {
        $model = new Tariff(['is_active' => true, 'sort_order' => Tariff::find()->max('sort_order') + 1, 'color' => '#0d6efd']);
        return $this->save($model);
    }

    public function actionUpdate($id)
    {
        $model = Tariff::findOne($id);
        if (!$model) throw new NotFoundHttpException();
        return $this->save($model);
    }

    private function save(Tariff $model)
    {
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Тариф сохранён.');
            return $this->redirect(['index']);
        }
        return $this->render('form', ['model' => $model]);
    }

    public function actionToggle($id)
    {
        $t = Tariff::findOne($id);
        if (!$t) throw new NotFoundHttpException();
        $t->is_active = !$t->is_active;
        $t->save(false);
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $t = Tariff::findOne($id);
        if (!$t) throw new NotFoundHttpException();
        try {
            $t->delete();
            Yii::$app->session->setFlash('success', 'Тариф удалён.');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', 'Тариф используется автомобилями — удалить нельзя.');
        }
        return $this->redirect(['index']);
    }

    public function actionSort()
    {
        $order = (array)Yii::$app->request->post('order', []);
        foreach ($order as $i => $id) {
            Tariff::updateAll(['sort_order' => $i + 1], ['id' => (int)$id]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['ok' => true];
    }
}
