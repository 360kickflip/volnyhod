<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $referrer_id
 * @property int $referred_id
 * @property int|null $booking_id
 * @property float $amount_referrer
 * @property float $amount_referred
 * @property string $status
 * @property string $created_at
 * @property string|null $paid_at
 *
 * @property User $referrer
 * @property User $referred
 * @property Booking|null $booking
 */
class ReferralReward extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    public static function tableName()
    {
        return '{{%referral_reward}}';
    }

    public function rules()
    {
        return [
            [['referrer_id', 'referred_id', 'amount_referrer', 'amount_referred', 'created_at'], 'required'],
            [['referrer_id', 'referred_id', 'booking_id'], 'integer'],
            [['amount_referrer', 'amount_referred'], 'number', 'min' => 0],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_PAID, self::STATUS_CANCELLED]],
        ];
    }

    public static function statusLabel($s)
    {
        return [
            self::STATUS_PENDING => 'Ожидает первой поездки',
            self::STATUS_PAID => 'Бонус начислен',
            self::STATUS_CANCELLED => 'Отменена',
        ][$s] ?? $s;
    }

    public static function statusBadge($s)
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_PAID => 'success',
            self::STATUS_CANCELLED => 'secondary',
        ][$s] ?? 'secondary';
    }

    public function getReferrer()
    {
        return $this->hasOne(User::class, ['id' => 'referrer_id']);
    }

    public function getReferred()
    {
        return $this->hasOne(User::class, ['id' => 'referred_id']);
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
    }
}
