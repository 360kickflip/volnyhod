<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Booking extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    public static function tableName()
    {
        return '{{%booking}}';
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
            [['user_id', 'car_id', 'tariff_id'], 'required'],
            [['user_id', 'car_id', 'tariff_id', 'promo_code_id', 'planned_minutes', 'start_mileage', 'end_mileage', 'start_fuel', 'end_fuel'], 'integer'],
            [['deposit', 'estimated_cost', 'base_cost', 'extra_km_cost', 'overdue_cost', 'penalty_cost', 'discount_amount', 'final_cost'], 'number'],
            [['start_lat', 'start_lng', 'end_lat', 'end_lng'], 'number'],
            [['start_address', 'end_address'], 'string', 'max' => 255],
            [['cancel_reason'], 'string', 'max' => 500],
            [['status'], 'in', 'range' => self::statuses()],
            [['number'], 'string', 'max' => 20],
            [['number'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'number' => 'Номер',
            'user_id' => 'Пользователь',
            'car_id' => 'Автомобиль',
            'tariff_id' => 'Тариф',
            'status' => 'Статус',
            'planned_minutes' => 'Планируемое время (мин)',
            'started_at' => 'Начало',
            'ended_at' => 'Окончание',
            'planned_end_at' => 'Планируемое окончание',
            'start_mileage' => 'Пробег начало',
            'end_mileage' => 'Пробег конец',
            'start_fuel' => 'Топливо начало (%)',
            'end_fuel' => 'Топливо конец (%)',
            'deposit' => 'Депозит',
            'estimated_cost' => 'Оценка',
            'final_cost' => 'Итого',
            'discount_amount' => 'Скидка',
            'cancel_reason' => 'Причина отмены',
            'created_at' => 'Создано',
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ACTIVE,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_EXPIRED,
        ];
    }

    public static function statusLabels()
    {
        return [
            self::STATUS_PENDING => 'Ожидание',
            self::STATUS_ACTIVE => 'Активна',
            self::STATUS_COMPLETED => 'Завершена',
            self::STATUS_CANCELLED => 'Отменена',
            self::STATUS_EXPIRED => 'Просрочена',
        ];
    }

    public static function statusLabel($status)
    {
        return self::statusLabels()[$status] ?? $status;
    }

    public static function statusBadge($status)
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_ACTIVE => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_EXPIRED => 'danger',
        ][$status] ?? 'secondary';
    }

    public static function generateNumber()
    {
        do {
            $num = 'BR-' . strtoupper(substr(uniqid(), -6));
        } while (static::findOne(['number' => $num]));
        return $num;
    }

    /**
     * Длительность аренды в секундах (для активной — до текущего момента)
     */
    public function getDurationSeconds()
    {
        if (!$this->started_at) return 0;
        $start = strtotime($this->started_at);
        $end = $this->ended_at ? strtotime($this->ended_at) : time();
        return max(0, $end - $start);
    }

    public function getDurationMinutes()
    {
        return (int)round($this->getDurationSeconds() / 60);
    }

    public function getFormattedDuration()
    {
        $sec = $this->getDurationSeconds();
        $h = floor($sec / 3600);
        $m = floor(($sec % 3600) / 60);
        $s = $sec % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    /**
     * Текущая стоимость (live для активной аренды)
     */
    public function calculateCurrentCost()
    {
        if (!$this->tariff) return 0;
        $minutes = $this->getDurationMinutes();
        $km = max(0, ($this->end_mileage ?? ($this->car->mileage ?? $this->start_mileage)) - ($this->start_mileage ?? 0));
        $cost = $this->tariff->calculateCost($minutes, $km);
        return $cost['total'];
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function canReview()
    {
        if ($this->status !== self::STATUS_COMPLETED) return false;
        return !Review::findOne(['booking_id' => $this->id]);
    }

    // ===== Relations =====
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getCar()
    {
        return $this->hasOne(Car::class, ['id' => 'car_id']);
    }

    public function getTariff()
    {
        return $this->hasOne(Tariff::class, ['id' => 'tariff_id']);
    }

    public function getPromoCode()
    {
        return $this->hasOne(PromoCode::class, ['id' => 'promo_code_id']);
    }

    public function getCharges()
    {
        return $this->hasMany(BookingCharge::class, ['booking_id' => 'id']);
    }

    public function getReview()
    {
        return $this->hasOne(Review::class, ['booking_id' => 'id']);
    }

    public function getDamageReports()
    {
        return $this->hasMany(DamageReport::class, ['booking_id' => 'id']);
    }

    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['booking_id' => 'id']);
    }

    public function getLocationHistory()
    {
        return $this->hasMany(CarLocationHistory::class, ['booking_id' => 'id'])->orderBy(['recorded_at' => SORT_ASC]);
    }
}
