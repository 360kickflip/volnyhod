<?php

namespace app\models;

use yii\db\ActiveRecord;

class DamageReport extends ActiveRecord
{
    const SEVERITY_MINOR = 'minor';
    const SEVERITY_MODERATE = 'moderate';
    const SEVERITY_SEVERE = 'severe';

    const STATUS_REPORTED = 'reported';
    const STATUS_REVIEWING = 'reviewing';
    const STATUS_USER_LIABLE = 'user_liable';
    const STATUS_NOT_LIABLE = 'not_liable';
    const STATUS_RESOLVED = 'resolved';

    public static function tableName()
    {
        return '{{%damage_report}}';
    }

    public function rules()
    {
        return [
            [['car_id', 'description', 'created_at'], 'required'],
            [['booking_id', 'car_id', 'user_id', 'reviewer_id'], 'integer'],
            [['description', 'photos', 'reviewer_comment'], 'string'],
            [['repair_cost'], 'number', 'min' => 0],
            [['severity'], 'in', 'range' => [self::SEVERITY_MINOR, self::SEVERITY_MODERATE, self::SEVERITY_SEVERE]],
            [['status'], 'in', 'range' => [self::STATUS_REPORTED, self::STATUS_REVIEWING, self::STATUS_USER_LIABLE, self::STATUS_NOT_LIABLE, self::STATUS_RESOLVED]],
        ];
    }

    public static function severityLabel($s)
    {
        return [
            self::SEVERITY_MINOR => 'Незначительное',
            self::SEVERITY_MODERATE => 'Среднее',
            self::SEVERITY_SEVERE => 'Серьёзное',
        ][$s] ?? $s;
    }

    public static function severityBadge($s)
    {
        return [
            self::SEVERITY_MINOR => 'success',
            self::SEVERITY_MODERATE => 'warning',
            self::SEVERITY_SEVERE => 'danger',
        ][$s] ?? 'secondary';
    }

    public static function statusLabel($s)
    {
        return [
            self::STATUS_REPORTED => 'Сообщено',
            self::STATUS_REVIEWING => 'На рассмотрении',
            self::STATUS_USER_LIABLE => 'Виновен пользователь',
            self::STATUS_NOT_LIABLE => 'Не виновен',
            self::STATUS_RESOLVED => 'Решено',
        ][$s] ?? $s;
    }

    public function getPhotosArray()
    {
        if (empty($this->photos)) return [];
        $decoded = json_decode($this->photos, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getCar() { return $this->hasOne(Car::class, ['id' => 'car_id']); }
    public function getUser() { return $this->hasOne(User::class, ['id' => 'user_id']); }
    public function getBooking() { return $this->hasOne(Booking::class, ['id' => 'booking_id']); }
    public function getReviewer() { return $this->hasOne(User::class, ['id' => 'reviewer_id']); }
}
