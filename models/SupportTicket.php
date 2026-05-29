<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class SupportTicket extends ActiveRecord
{
    const CATEGORY_ACCOUNT = 'account';
    const CATEGORY_PAYMENT = 'payment';
    const CATEGORY_RENTAL = 'rental';
    const CATEGORY_CAR = 'car';
    const CATEGORY_TECHNICAL = 'technical';
    const CATEGORY_OTHER = 'other';

    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_WAITING_USER = 'waiting_user';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    public static function tableName()
    {
        return '{{%support_ticket}}';
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
            [['user_id', 'subject', 'category'], 'required'],
            [['user_id', 'assigned_admin_id'], 'integer'],
            [['subject'], 'string', 'max' => 255],
            [['number'], 'string', 'max' => 20],
            [['number'], 'unique'],
            [['category'], 'in', 'range' => self::categories()],
            [['priority'], 'in', 'range' => self::priorities()],
            [['status'], 'in', 'range' => self::statuses()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'number' => 'Номер',
            'category' => 'Категория',
            'subject' => 'Тема',
            'priority' => 'Приоритет',
            'status' => 'Статус',
        ];
    }

    public static function categories() { return [self::CATEGORY_ACCOUNT, self::CATEGORY_PAYMENT, self::CATEGORY_RENTAL, self::CATEGORY_CAR, self::CATEGORY_TECHNICAL, self::CATEGORY_OTHER]; }
    public static function priorities() { return [self::PRIORITY_LOW, self::PRIORITY_NORMAL, self::PRIORITY_HIGH, self::PRIORITY_URGENT]; }
    public static function statuses() { return [self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_WAITING_USER, self::STATUS_RESOLVED, self::STATUS_CLOSED]; }

    public static function categoryLabel($c)
    {
        return [
            self::CATEGORY_ACCOUNT => 'Аккаунт',
            self::CATEGORY_PAYMENT => 'Оплата',
            self::CATEGORY_RENTAL => 'Аренда',
            self::CATEGORY_CAR => 'Автомобиль',
            self::CATEGORY_TECHNICAL => 'Техническое',
            self::CATEGORY_OTHER => 'Другое',
        ][$c] ?? $c;
    }

    public static function categoryDropdown()
    {
        return [
            self::CATEGORY_ACCOUNT => 'Аккаунт',
            self::CATEGORY_PAYMENT => 'Оплата',
            self::CATEGORY_RENTAL => 'Аренда',
            self::CATEGORY_CAR => 'Автомобиль',
            self::CATEGORY_TECHNICAL => 'Техническое',
            self::CATEGORY_OTHER => 'Другое',
        ];
    }

    public static function priorityLabel($p)
    {
        return [
            self::PRIORITY_LOW => 'Низкий',
            self::PRIORITY_NORMAL => 'Обычный',
            self::PRIORITY_HIGH => 'Высокий',
            self::PRIORITY_URGENT => 'Срочный',
        ][$p] ?? $p;
    }

    public static function priorityBadge($p)
    {
        return [
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_NORMAL => 'info',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_URGENT => 'danger',
        ][$p] ?? 'secondary';
    }

    public static function statusLabel($s)
    {
        return [
            self::STATUS_OPEN => 'Открыто',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_WAITING_USER => 'Ждём ответа',
            self::STATUS_RESOLVED => 'Решено',
            self::STATUS_CLOSED => 'Закрыто',
        ][$s] ?? $s;
    }

    public static function statusBadge($s)
    {
        return [
            self::STATUS_OPEN => 'primary',
            self::STATUS_IN_PROGRESS => 'info',
            self::STATUS_WAITING_USER => 'warning',
            self::STATUS_RESOLVED => 'success',
            self::STATUS_CLOSED => 'secondary',
        ][$s] ?? 'secondary';
    }

    public static function generateNumber()
    {
        do {
            $num = 'TK-' . strtoupper(substr(uniqid(), -6));
        } while (static::findOne(['number' => $num]));
        return $num;
    }

    public function getUser() { return $this->hasOne(User::class, ['id' => 'user_id']); }
    public function getAssignedAdmin() { return $this->hasOne(User::class, ['id' => 'assigned_admin_id']); }
    public function getMessages() { return $this->hasMany(SupportMessage::class, ['ticket_id' => 'id'])->orderBy(['created_at' => SORT_ASC]); }
    public function getLastMessage() { return $this->hasOne(SupportMessage::class, ['ticket_id' => 'id'])->orderBy(['created_at' => SORT_DESC]); }
}
