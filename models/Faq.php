<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Faq extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%faq}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => function () { return date('Y-m-d H:i:s'); },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['question', 'answer'], 'required'],
            [['category_id', 'sort_order'], 'integer'],
            [['answer'], 'string'],
            [['question'], 'string', 'max' => 500],
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'category_id' => 'Категория',
            'question' => 'Вопрос',
            'answer' => 'Ответ',
            'sort_order' => 'Порядок',
            'is_active' => 'Активен',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(FaqCategory::class, ['id' => 'category_id']);
    }
}
