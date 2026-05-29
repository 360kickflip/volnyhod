<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;

/**
 * @property int $id
 * @property string $email
 * @property string|null $phone
 * @property string $password_hash
 * @property string $auth_key
 * @property string|null $password_reset_token
 * @property string|null $verification_token
 * @property string|null $email_verified_at
 * @property string|null $name
 * @property string|null $birthdate
 * @property string|null $avatar
 * @property float $balance
 * @property float $locked_balance
 * @property string $verification_status
 * @property string $status
 * @property string $role
 * @property string|null $block_reason
 * @property string $created_at
 * @property string $updated_at
 *
 * @property UserDocument[] $documents
 * @property Booking[] $bookings
 * @property Notification[] $notifications
 * @property Transaction[] $transactions
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_DELETED = 'deleted';

    const VERIFICATION_NONE = 'none';
    const VERIFICATION_PENDING = 'pending';
    const VERIFICATION_VERIFIED = 'verified';
    const VERIFICATION_REJECTED = 'rejected';

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';

    public static function tableName()
    {
        return '{{%user}}';
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
            [['email', 'password_hash', 'auth_key'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['phone'], 'string', 'max' => 20],
            [['phone'], 'unique', 'when' => function ($model) { return !empty($model->phone); }],
            [['name'], 'string', 'max' => 150],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d'],
            [['balance', 'locked_balance'], 'number'],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_BLOCKED, self::STATUS_DELETED]],
            [['role'], 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_MANAGER]],
            [['verification_status'], 'in', 'range' => [self::VERIFICATION_NONE, self::VERIFICATION_PENDING, self::VERIFICATION_VERIFIED, self::VERIFICATION_REJECTED]],
            [['block_reason'], 'string', 'max' => 500],
            [['avatar'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'phone' => 'Телефон',
            'name' => 'ФИО',
            'birthdate' => 'Дата рождения',
            'avatar' => 'Аватар',
            'balance' => 'Баланс',
            'locked_balance' => 'Заблокированные средства',
            'verification_status' => 'Статус верификации',
            'status' => 'Статус',
            'role' => 'Роль',
            'created_at' => 'Зарегистрирован',
            'updated_at' => 'Обновлён',
        ];
    }

    // ===== IdentityInterface =====
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    // ===== Helpers =====
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne(['password_reset_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) return false;
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = 3600 * 24; // 24 часа
        return $timestamp + $expire >= time();
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    public function isVerified()
    {
        return $this->verification_status === self::VERIFICATION_VERIFIED;
    }

    public function getAvailableBalance()
    {
        return (float)$this->balance - (float)$this->locked_balance;
    }

    public function getInitials()
    {
        if (!$this->name) return mb_strtoupper(mb_substr($this->email, 0, 1));
        $parts = preg_split('/\s+/', trim($this->name));
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $second = mb_substr($parts[1] ?? '', 0, 1);
        return mb_strtoupper($first . $second);
    }

    public function getAvatarUrl()
    {
        if ($this->avatar) {
            return Yii::getAlias('@web/uploads/avatars/' . $this->avatar);
        }
        return null;
    }

    public static function verificationStatusLabel($status)
    {
        return [
            self::VERIFICATION_NONE => 'Не загружено',
            self::VERIFICATION_PENDING => 'На проверке',
            self::VERIFICATION_VERIFIED => 'Верифицирован',
            self::VERIFICATION_REJECTED => 'Отклонено',
        ][$status] ?? $status;
    }

    public static function verificationStatusBadge($status)
    {
        return [
            self::VERIFICATION_NONE => 'secondary',
            self::VERIFICATION_PENDING => 'warning',
            self::VERIFICATION_VERIFIED => 'success',
            self::VERIFICATION_REJECTED => 'danger',
        ][$status] ?? 'secondary';
    }

    // ===== Relations =====
    public function getDocuments()
    {
        return $this->hasMany(UserDocument::class, ['user_id' => 'id']);
    }

    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['user_id' => 'id']);
    }

    public function getActiveBooking()
    {
        return $this->hasOne(Booking::class, ['user_id' => 'id'])->where(['status' => Booking::STATUS_ACTIVE]);
    }

    public function getNotifications()
    {
        return $this->hasMany(Notification::class, ['user_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function getUnreadNotificationsCount()
    {
        return (int)Notification::find()->where(['user_id' => $this->id, 'is_read' => false])->count();
    }

    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['user_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function getSupportTickets()
    {
        return $this->hasMany(SupportTicket::class, ['user_id' => 'id'])->orderBy(['updated_at' => SORT_DESC]);
    }
}
