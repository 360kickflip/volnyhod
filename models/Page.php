<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Page extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%page}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
                'updatedAtAttribute' => 'updated_at',
                'value' => function () { return date('Y-m-d H:i:s'); },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['slug', 'title'], 'required'],
            [['slug'], 'unique'],
            [['slug'], 'string', 'max' => 190],
            [['title'], 'string', 'max' => 255],
            [['content'], 'string'],
            [['meta_description'], 'string', 'max' => 500],
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'slug' => 'URL',
            'title' => 'Заголовок',
            'content' => 'Содержимое',
            'meta_description' => 'Meta description',
            'is_active' => 'Активна',
        ];
    }
}
