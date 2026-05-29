<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Notification extends ActiveRecord
{
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';
    const TYPE_BOOKING = 'booking';
    const TYPE_PAYMENT = 'payment';
    const TYPE_SUPPORT = 'support';
    const TYPE_PROMO = 'promo';
    const TYPE_SYSTEM = 'system';

    public static function tableName()
    {
        return '{{%notification}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'type', 'title', 'created_at'], 'required'],
            [['user_id'], 'integer'],
            [['message'], 'string'],
            [['title', 'url'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 50],
            [['is_read'], 'boolean'],
        ];
    }

    public static function typeIcon($type)
    {
        return [
            self::TYPE_INFO => 'fa-info-circle text-info',
            self::TYPE_SUCCESS => 'fa-check-circle text-success',
            self::TYPE_WARNING => 'fa-exclamation-circle text-warning',
            self::TYPE_DANGER => 'fa-times-circle text-danger',
            self::TYPE_BOOKING => 'fa-key text-primary',
            self::TYPE_PAYMENT => 'fa-credit-card text-success',
            self::TYPE_SUPPORT => 'fa-life-ring text-info',
            self::TYPE_PROMO => 'fa-gift text-warning',
            self::TYPE_SYSTEM => 'fa-cog text-secondary',
        ][$type] ?? 'fa-bell';
    }

    public static function typeColor($type)
    {
        return [
            self::TYPE_INFO => 'info',
            self::TYPE_SUCCESS => 'success',
            self::TYPE_WARNING => 'warning',
            self::TYPE_DANGER => 'danger',
            self::TYPE_BOOKING => 'primary',
            self::TYPE_PAYMENT => 'success',
            self::TYPE_SUPPORT => 'info',
            self::TYPE_PROMO => 'warning',
            self::TYPE_SYSTEM => 'secondary',
        ][$type] ?? 'secondary';
    }

    public static function send($userId, $type, $title, $message = '', $url = null, $icon = null)
    {
        $n = new self();
        $n->user_id = $userId;
        $n->type = $type;
        $n->title = $title;
        $n->message = $message;
        $n->url = $url;
        $n->icon = $icon;
        $n->is_read = false;
        $n->created_at = date('Y-m-d H:i:s');
        $n->save();
        return $n;
    }

    public function markRead()
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->read_at = date('Y-m-d H:i:s');
            $this->save(false);
        }
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
