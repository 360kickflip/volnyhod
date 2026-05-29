<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Page;

class PageController extends Controller
{
    public function actionView($slug)
    {
        $page = Page::findOne(['slug' => $slug, 'is_active' => true]);
        if (!$page) throw new NotFoundHttpException('Страница не найдена.');
        return $this->render('view', ['page' => $page]);
    }
}
