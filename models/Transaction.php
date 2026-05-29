<?php

namespace app\models;

use yii\db\ActiveRecord;

class Transaction extends ActiveRecord
{
    const TYPE_TOPUP = 'topup';
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_RENTAL_CHARGE = 'rental_charge';
    const TYPE_DEPOSIT_HOLD = 'deposit_hold';
    const TYPE_DEPOSIT_RELEASE = 'deposit_release';
    const TYPE_REFUND = 'refund';
    const TYPE_PENALTY = 'penalty';
    const TYPE_CORRECTION = 'correction';
    const TYPE_BONUS = 'bonus';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    public static function tableName()
    {
        return '{{%transaction}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'type', 'amount', 'created_at'], 'required'],
            [['user_id', 'booking_id'], 'integer'],
            [['amount', 'balance_after'], 'number'],
            [['external_id'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 500],
        ];
    }

    public static function typeLabel($type)
    {
        return [
            self::TYPE_TOPUP => 'Пополнение',
            self::TYPE_WITHDRAW => 'Вывод',
            self::TYPE_RENTAL_CHARGE => 'Списание за аренду',
            self::TYPE_DEPOSIT_HOLD => 'Заморозка депозита',
            self::TYPE_DEPOSIT_RELEASE => 'Возврат депозита',
            self::TYPE_REFUND => 'Возврат',
            self::TYPE_PENALTY => 'Штраф',
            self::TYPE_CORRECTION => 'Корректировка',
            self::TYPE_BONUS => 'Бонус',
        ][$type] ?? $type;
    }

    public static function typeIcon($type)
    {
        return [
            self::TYPE_TOPUP => 'fa-plus-circle text-success',
            self::TYPE_WITHDRAW => 'fa-minus-circle text-danger',
            self::TYPE_RENTAL_CHARGE => 'fa-car text-primary',
            self::TYPE_DEPOSIT_HOLD => 'fa-lock text-warning',
            self::TYPE_DEPOSIT_RELEASE => 'fa-unlock text-info',
            self::TYPE_REFUND => 'fa-undo text-info',
            self::TYPE_PENALTY => 'fa-exclamation-triangle text-danger',
            self::TYPE_CORRECTION => 'fa-balance-scale text-secondary',
            self::TYPE_BONUS => 'fa-gift text-success',
        ][$type] ?? 'fa-circle';
    }

    public static function statusLabel($status)
    {
        return [
            self::STATUS_PENDING => 'В обработке',
            self::STATUS_COMPLETED => 'Завершена',
            self::STATUS_FAILED => 'Ошибка',
            self::STATUS_CANCELLED => 'Отменена',
        ][$status] ?? $status;
    }

    public static function statusBadge($status)
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
        ][$status] ?? 'secondary';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
    }
}
