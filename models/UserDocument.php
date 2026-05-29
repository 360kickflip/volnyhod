<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class UserDocument extends ActiveRecord
{
    const TYPE_LICENSE_FRONT = 'license_front';
    const TYPE_LICENSE_BACK = 'license_back';
    const TYPE_PASSPORT_MAIN = 'passport_main';
    const TYPE_PASSPORT_REGISTRATION = 'passport_registration';
    const TYPE_SELFIE = 'selfie';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function tableName()
    {
        return '{{%user_document}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'type', 'file_path', 'uploaded_at'], 'required'],
            [['user_id', 'reviewer_id'], 'integer'],
            [['comment'], 'string'],
            [['file_path', 'original_name'], 'string', 'max' => 255],
            [['type'], 'in', 'range' => self::types()],
            [['status'], 'in', 'range' => self::statuses()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => 'Тип документа',
            'file_path' => 'Файл',
            'status' => 'Статус',
            'comment' => 'Комментарий',
            'uploaded_at' => 'Загружен',
            'reviewed_at' => 'Проверен',
        ];
    }

    public static function types()
    {
        return [self::TYPE_LICENSE_FRONT, self::TYPE_LICENSE_BACK, self::TYPE_PASSPORT_MAIN, self::TYPE_PASSPORT_REGISTRATION, self::TYPE_SELFIE];
    }

    public static function statuses()
    {
        return [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED];
    }

    public static function typeLabel($type)
    {
        return [
            self::TYPE_LICENSE_FRONT => 'ВУ (лицевая)',
            self::TYPE_LICENSE_BACK => 'ВУ (оборотная)',
            self::TYPE_PASSPORT_MAIN => 'Паспорт (главная)',
            self::TYPE_PASSPORT_REGISTRATION => 'Паспорт (прописка)',
            self::TYPE_SELFIE => 'Селфи',
        ][$type] ?? $type;
    }

    public static function statusLabel($status)
    {
        return [
            self::STATUS_PENDING => 'На проверке',
            self::STATUS_APPROVED => 'Одобрен',
            self::STATUS_REJECTED => 'Отклонён',
        ][$status] ?? $status;
    }

    public function getUrl()
    {
        return Yii::getAlias('@web/uploads/documents/' . $this->file_path);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
