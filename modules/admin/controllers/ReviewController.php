<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Review;

class ReviewController extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['approve' => ['post'], 'reject' => ['post'], 'delete' => ['post']],
            ],
        ]);
    }

    public function actionIndex()
    {
        $query = Review::find()->with(['user', 'car']);
        $status = Yii::$app->request->get('status', 'pending');
        if ($status) $query->andWhere(['status' => $status]);
        $query->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);
        return $this->render('index', ['dataProvider' => $dataProvider, 'status' => $status]);
    }

    public function actionApprove($id)
    {
        $r = $this->find($id);
        $r->status = Review::STATUS_APPROVED;
        $r->moderator_id = Yii::$app->user->id;
        $r->moderated_at = date('Y-m-d H:i:s');
        $r->save(false);
        return $this->redirect(['index']);
    }

    public function actionReject($id)
    {
        $r = $this->find($id);
        $r->status = Review::STATUS_REJECTED;
        $r->moderator_id = Yii::$app->user->id;
        $r->moderator_comment = Yii::$app->request->post('comment', '');
        $r->moderated_at = date('Y-m-d H:i:s');
        $r->save(false);
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $r = $this->find($id);
        $r->delete();
        return $this->redirect(['index']);
    }

    private function find($id) { $r = Review::findOne($id); if (!$r) throw new NotFoundHttpException(); return $r; }
}
