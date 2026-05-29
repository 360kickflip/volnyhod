<?php

namespace app\models;

use yii\db\ActiveRecord;

class BookingCharge extends ActiveRecord
{
    const TYPE_BASE = 'base';
    const TYPE_EXTRA_KM = 'extra_km';
    const TYPE_OVERDUE = 'overdue';
    const TYPE_PENALTY = 'penalty';
    const TYPE_DAMAGE = 'damage';
    const TYPE_DISCOUNT = 'discount';
    const TYPE_MANUAL = 'manual';

    public static function tableName()
    {
        return '{{%booking_charge}}';
    }

    public function rules()
    {
        return [
            [['booking_id', 'type', 'amount', 'created_at'], 'required'],
            [['booking_id'], 'integer'],
            [['amount'], 'number'],
            [['description'], 'string', 'max' => 255],
        ];
    }

    public static function typeLabel($type)
    {
        return [
            self::TYPE_BASE => 'Базовая стоимость',
            self::TYPE_EXTRA_KM => 'Доп. километры',
            self::TYPE_OVERDUE => 'Просрочка',
            self::TYPE_PENALTY => 'Штраф',
            self::TYPE_DAMAGE => 'Повреждения',
            self::TYPE_DISCOUNT => 'Скидка',
            self::TYPE_MANUAL => 'Прочее',
        ][$type] ?? $type;
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
    }
}
