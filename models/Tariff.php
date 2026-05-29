<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Tariff extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%tariff}}';
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
            [['name', 'price_per_minute', 'price_per_km', 'deposit'], 'required'],
            [['description'], 'string'],
            [['price_per_minute', 'price_per_km', 'price_per_hour', 'price_per_day', 'deposit', 'overdue_per_minute'], 'number', 'min' => 0],
            [['free_km', 'sort_order'], 'integer'],
            [['is_active'], 'boolean'],
            [['name'], 'string', 'max' => 100],
            [['color', 'icon'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'description' => 'Описание',
            'price_per_minute' => 'Цена за минуту',
            'price_per_km' => 'Цена за км',
            'price_per_hour' => 'Цена за час',
            'price_per_day' => 'Цена за сутки',
            'deposit' => 'Депозит',
            'overdue_per_minute' => 'Цена просрочки/мин',
            'free_km' => 'Включено км',
            'color' => 'Цвет',
            'icon' => 'Иконка (FontAwesome)',
            'is_active' => 'Активен',
            'sort_order' => 'Порядок',
        ];
    }

    public static function listActive()
    {
        return static::find()->where(['is_active' => true])->orderBy(['sort_order' => SORT_ASC])->all();
    }

    public static function dropdown()
    {
        return \yii\helpers\ArrayHelper::map(static::find()->orderBy(['sort_order' => SORT_ASC])->all(), 'id', 'name');
    }

    public function getCars()
    {
        return $this->hasMany(Car::class, ['tariff_id' => 'id']);
    }

    /**
     * Расчёт стоимости поездки по тарифу
     */
    public function calculateCost($minutes, $km = 0, $overdueMinutes = 0)
    {
        $base = $minutes * $this->price_per_minute;
        $extraKm = max(0, $km - $this->free_km) * $this->price_per_km;
        $overdue = $overdueMinutes * $this->overdue_per_minute;
        return [
            'base' => round($base, 2),
            'extra_km' => round($extraKm, 2),
            'overdue' => round($overdue, 2),
            'total' => round($base + $extraKm + $overdue, 2),
        ];
    }
}
