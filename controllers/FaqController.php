<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\FaqCategory;
use app\models\Faq;

class FaqController extends Controller
{
    public function actionIndex()
    {
        $categories = FaqCategory::find()->with(['faqs'])->orderBy(['sort_order' => SORT_ASC])->all();
        // Если категорий нет — все вопросы списком
        $orphan = [];
        if (!$categories) {
            $orphan = Faq::find()->where(['is_active' => true])->orderBy(['sort_order' => SORT_ASC])->all();
        }
        return $this->render('index', ['categories' => $categories, 'orphan' => $orphan]);
    }
}
