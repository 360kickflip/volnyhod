<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Review extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function tableName()
    {
        return '{{%review}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => function () { return date('Y-m-d H:i:s'); },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['booking_id', 'user_id', 'car_id', 'rating'], 'required'],
            [['booking_id', 'user_id', 'car_id', 'rating', 'moderator_id'], 'integer'],
            [['rating'], 'integer', 'min' => 1, 'max' => 5],
            [['text'], 'string'],
            [['photos'], 'string'],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED]],
            [['moderator_comment'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'rating' => 'Оценка',
            'text' => 'Отзыв',
            'photos' => 'Фотографии',
            'status' => 'Статус',
        ];
    }

    public static function statusLabel($status)
    {
        return [
            self::STATUS_PENDING => 'На модерации',
            self::STATUS_APPROVED => 'Одобрен',
            self::STATUS_REJECTED => 'Отклонён',
        ][$status] ?? $status;
    }

    public function getPhotosArray()
    {
        if (empty($this->photos)) return [];
        $decoded = json_decode($this->photos, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getCar()
    {
        return $this->hasOne(Car::class, ['id' => 'car_id']);
    }
}
