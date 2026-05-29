<?php

namespace app\models;

use yii\db\ActiveRecord;

class EmailTemplate extends ActiveRecord
{
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';

    public static function tableName()
    {
        return '{{%email_template}}';
    }

    public function rules()
    {
        return [
            [['code', 'body'], 'required'],
            [['code'], 'unique'],
            [['code'], 'string', 'max' => 100],
            [['subject', 'description'], 'string', 'max' => 500],
            [['body'], 'string'],
            [['channel'], 'in', 'range' => [self::CHANNEL_EMAIL, self::CHANNEL_SMS]],
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => 'Код',
            'channel' => 'Канал',
            'subject' => 'Тема',
            'body' => 'Тело шаблона',
            'is_active' => 'Активен',
        ];
    }

    public function render(array $vars = [])
    {
        $body = $this->body;
        $subject = $this->subject;
        foreach ($vars as $k => $v) {
            $body = str_replace('{' . $k . '}', $v, $body);
            $subject = str_replace('{' . $k . '}', $v, (string)$subject);
        }
        return ['subject' => $subject, 'body' => $body];
    }
}
