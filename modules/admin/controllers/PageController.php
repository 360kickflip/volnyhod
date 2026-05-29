<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Page;

class PageController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => ['class' => VerbFilter::class, 'actions' => ['delete' => ['post']]],
        ]);
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(['query' => Page::find()->orderBy(['updated_at' => SORT_DESC])]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionCreate()
    {
        $model = new Page(['is_active' => true]);
        return $this->save($model);
    }

    public function actionUpdate($id)
    {
        $model = Page::findOne($id);
        if (!$model) throw new NotFoundHttpException();
        return $this->save($model);
    }

    public function actionDelete($id)
    {
        $p = Page::findOne($id);
        if ($p) $p->delete();
        return $this->redirect(['index']);
    }

    private function save(Page $model)
    {
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Сохранено.');
            return $this->redirect(['index']);
        }
        return $this->render('form', ['model' => $model]);
    }
}
