<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class FaqCategory extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%faq_category}}';
    }

    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['name', 'slug'], 'string', 'max' => 150],
            [['icon'], 'string', 'max' => 50],
            [['sort_order'], 'integer'],
            [['slug'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return ['name' => 'Название', 'slug' => 'Slug', 'icon' => 'Иконка', 'sort_order' => 'Порядок'];
    }

    public function getFaqs()
    {
        return $this->hasMany(Faq::class, ['category_id' => 'id'])
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public static function dropdown()
    {
        return ArrayHelper::map(static::find()->orderBy(['sort_order' => SORT_ASC])->all(), 'id', 'name');
    }
}
