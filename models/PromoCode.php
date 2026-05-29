<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class PromoCode extends ActiveRecord
{
    const TYPE_PERCENT = 'percent';
    const TYPE_FIXED = 'fixed';

    public static function tableName()
    {
        return '{{%promo_code}}';
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
            [['code', 'type', 'value'], 'required'],
            [['code'], 'unique'],
            [['code'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['type'], 'in', 'range' => [self::TYPE_PERCENT, self::TYPE_FIXED]],
            [['value', 'min_amount', 'max_discount'], 'number', 'min' => 0],
            [['usage_limit', 'usage_count', 'per_user_limit'], 'integer', 'min' => 0],
            [['valid_from', 'valid_to'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => 'Код',
            'description' => 'Описание',
            'type' => 'Тип',
            'value' => 'Значение',
            'min_amount' => 'Мин. сумма',
            'max_discount' => 'Макс. скидка',
            'usage_limit' => 'Общий лимит',
            'usage_count' => 'Использовано',
            'per_user_limit' => 'На пользователя',
            'valid_from' => 'Действует с',
            'valid_to' => 'Действует до',
            'is_active' => 'Активен',
        ];
    }

    public static function generateCode($length = 8)
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (static::findOne(['code' => $code]));
        return $code;
    }

    /**
     * Возвращает [success, message, discount]
     */
    public function tryApply($amount, $userId)
    {
        if (!$this->is_active) {
            return [false, 'Промокод не активен', 0];
        }
        $now = date('Y-m-d H:i:s');
        if ($this->valid_from && $now < $this->valid_from) {
            return [false, 'Промокод ещё не действителен', 0];
        }
        if ($this->valid_to && $now > $this->valid_to) {
            return [false, 'Срок действия промокода истёк', 0];
        }
        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return [false, 'Лимит использований исчерпан', 0];
        }
        if ($this->min_amount && $amount < $this->min_amount) {
            return [false, 'Минимальная сумма для применения: ' . $this->min_amount . ' ₽', 0];
        }
        $userUsage = (int)Booking::find()->where(['promo_code_id' => $this->id, 'user_id' => $userId])->count();
        if ($this->per_user_limit && $userUsage >= $this->per_user_limit) {
            return [false, 'Вы уже использовали этот промокод', 0];
        }

        if ($this->type === self::TYPE_PERCENT) {
            $discount = $amount * $this->value / 100;
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
        } else {
            $discount = $this->value;
        }
        $discount = min($discount, $amount);
        return [true, 'Промокод применён', round($discount, 2)];
    }
}
