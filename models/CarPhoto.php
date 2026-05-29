<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class CarPhoto extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%car_photo}}';
    }

    public function rules()
    {
        return [
            [['car_id', 'file_path', 'created_at'], 'required'],
            [['car_id', 'sort_order'], 'integer'],
            [['is_main'], 'boolean'],
            [['file_path'], 'string', 'max' => 255],
        ];
    }

    public function getUrl()
    {
        return Yii::getAlias('@web/uploads/cars/' . $this->file_path);
    }

    public function getCar()
    {
        return $this->hasOne(Car::class, ['id' => 'car_id']);
    }
}
