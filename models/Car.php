<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

class Car extends ActiveRecord
{
    const STATUS_AVAILABLE = 'available';
    const STATUS_RENTED = 'rented';
    const STATUS_RESERVED = 'reserved';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_OFFLINE = 'offline';

    public static function tableName()
    {
        return '{{%car}}';
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
            [['tariff_id', 'brand', 'model', 'license_plate'], 'required'],
            [['tariff_id', 'year', 'seats', 'mileage', 'fuel_level', 'battery_level'], 'integer'],
            [['lat', 'lng'], 'number'],
            [['features', 'description'], 'string'],
            [['brand', 'model'], 'string', 'max' => 80],
            [['license_plate'], 'string', 'max' => 20],
            [['license_plate'], 'unique'],
            [['vin'], 'string', 'max' => 17],
            [['color', 'body_type'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => self::statuses()],
            [['fuel_level', 'battery_level'], 'integer', 'min' => 0, 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'tariff_id' => 'Тариф',
            'brand' => 'Марка',
            'model' => 'Модель',
            'year' => 'Год',
            'color' => 'Цвет',
            'license_plate' => 'Гос. номер',
            'vin' => 'VIN',
            'transmission' => 'КПП',
            'body_type' => 'Тип кузова',
            'seats' => 'Мест',
            'fuel_type' => 'Топливо',
            'mileage' => 'Пробег (км)',
            'fuel_level' => 'Топливо (%)',
            'battery_level' => 'Заряд (%)',
            'features' => 'Опции',
            'description' => 'Описание',
            'lat' => 'Широта',
            'lng' => 'Долгота',
            'address' => 'Адрес',
            'status' => 'Статус',
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_AVAILABLE,
            self::STATUS_RENTED,
            self::STATUS_RESERVED,
            self::STATUS_MAINTENANCE,
            self::STATUS_BLOCKED,
            self::STATUS_OFFLINE,
        ];
    }

    public static function statusLabels()
    {
        return [
            self::STATUS_AVAILABLE => 'Доступен',
            self::STATUS_RENTED => 'В аренде',
            self::STATUS_RESERVED => 'Забронирован',
            self::STATUS_MAINTENANCE => 'Обслуживание',
            self::STATUS_BLOCKED => 'Заблокирован',
            self::STATUS_OFFLINE => 'Не на связи',
        ];
    }

    public static function statusLabel($status)
    {
        return self::statusLabels()[$status] ?? $status;
    }

    public static function statusBadge($status)
    {
        return [
            self::STATUS_AVAILABLE => 'success',
            self::STATUS_RENTED => 'primary',
            self::STATUS_RESERVED => 'info',
            self::STATUS_MAINTENANCE => 'warning',
            self::STATUS_BLOCKED => 'danger',
            self::STATUS_OFFLINE => 'secondary',
        ][$status] ?? 'secondary';
    }

    public function getFullName()
    {
        return trim($this->brand . ' ' . $this->model);
    }

    public function getFeaturesArray()
    {
        if (empty($this->features)) return [];
        if (is_array($this->features)) return $this->features;
        $decoded = json_decode($this->features, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getMainPhotoUrl()
    {
        $photo = $this->mainPhoto ?: ($this->photos[0] ?? null);
        if ($photo && $photo->file_path) {
            return Yii::getAlias('@web/uploads/cars/' . $photo->file_path);
        }
        // Заглушка SVG
        return Yii::getAlias('@web/img/car-placeholder.svg');
    }

    public function getPhotoUrls()
    {
        $urls = [];
        foreach ($this->photos as $p) {
            $urls[] = Yii::getAlias('@web/uploads/cars/' . $p->file_path);
        }
        if (empty($urls)) {
            $urls[] = Yii::getAlias('@web/img/car-placeholder.svg');
        }
        return $urls;
    }

    public function isAvailableForBooking()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    // ===== Relations =====
    public function getTariff()
    {
        return $this->hasOne(Tariff::class, ['id' => 'tariff_id']);
    }

    public function getPhotos()
    {
        return $this->hasMany(CarPhoto::class, ['car_id' => 'id'])->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public function getMainPhoto()
    {
        return $this->hasOne(CarPhoto::class, ['car_id' => 'id'])->where(['is_main' => true]);
    }

    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['car_id' => 'id']);
    }

    public function getActiveBooking()
    {
        return $this->hasOne(Booking::class, ['car_id' => 'id'])->where(['status' => Booking::STATUS_ACTIVE]);
    }

    public function getReviews()
    {
        return $this->hasMany(Review::class, ['car_id' => 'id'])->where(['status' => Review::STATUS_APPROVED]);
    }

    public function getAverageRating()
    {
        return round((float)Review::find()
            ->where(['car_id' => $this->id, 'status' => Review::STATUS_APPROVED])
            ->average('rating'), 1);
    }

    public function getLocationHistory()
    {
        return $this->hasMany(CarLocationHistory::class, ['car_id' => 'id'])->orderBy(['recorded_at' => SORT_DESC]);
    }
}
