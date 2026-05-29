<?php

namespace app\models;

use yii\db\ActiveRecord;

class CarLocationHistory extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%car_location_history}}';
    }

    public function rules()
    {
        return [
            [['car_id', 'lat', 'lng', 'recorded_at'], 'required'],
            [['car_id', 'booking_id', 'speed', 'mileage'], 'integer'],
            [['lat', 'lng'], 'number'],
            [['address'], 'string', 'max' => 255],
        ];
    }

    public function getCar()
    {
        return $this->hasOne(Car::class, ['id' => 'car_id']);
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
    }
}
