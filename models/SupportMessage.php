<?php

namespace app\models;

use yii\db\ActiveRecord;

class SupportMessage extends ActiveRecord
{
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_SYSTEM = 'system';

    public static function tableName()
    {
        return '{{%support_message}}';
    }

    public function rules()
    {
        return [
            [['ticket_id', 'author_id', 'message', 'created_at'], 'required'],
            [['ticket_id', 'author_id'], 'integer'],
            [['message', 'attachments'], 'string'],
            [['author_role'], 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_SYSTEM]],
        ];
    }

    public function getAttachmentsArray()
    {
        if (empty($this->attachments)) return [];
        $decoded = json_decode($this->attachments, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getTicket() { return $this->hasOne(SupportTicket::class, ['id' => 'ticket_id']); }
    public function getAuthor() { return $this->hasOne(User::class, ['id' => 'author_id']); }
}
