<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Faq;
use app\models\FaqCategory;

class FaqController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => ['class' => VerbFilter::class, 'actions' => ['delete' => ['post']]],
        ]);
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Faq::find()->with('category')->orderBy(['category_id' => SORT_ASC, 'sort_order' => SORT_ASC]),
            'pagination' => ['pageSize' => 50],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionCreate()
    {
        $model = new Faq(['is_active' => true, 'sort_order' => 0]);
        return $this->save($model);
    }

    public function actionUpdate($id)
    {
        $model = Faq::findOne($id);
        if (!$model) throw new NotFoundHttpException();
        return $this->save($model);
    }

    private function save(Faq $model)
    {
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Сохранено.');
            return $this->redirect(['index']);
        }
        return $this->render('form', ['model' => $model, 'categories' => FaqCategory::dropdown()]);
    }

    public function actionDelete($id)
    {
        $f = Faq::findOne($id);
        if (!$f) throw new NotFoundHttpException();
        $f->delete();
        return $this->redirect(['index']);
    }
}
