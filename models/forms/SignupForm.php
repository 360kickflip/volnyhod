<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;
use app\models\Notification;
use app\models\Setting;
use app\components\ReferralBootstrap;

class SignupForm extends Model
{
    public $name;
    public $email;
    public $phone;
    public $password;
    public $password_repeat;
    public $birthdate;
    public $agreement;
    public $referral_code;

    /** @var User|null */
    public $referrer;

    public function rules()
    {
        return [
            [['name', 'email', 'phone', 'password', 'password_repeat', 'birthdate', 'agreement'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass' => User::class, 'message' => 'Этот email уже зарегистрирован.'],
            [['phone'], 'match', 'pattern' => '/^\+?[0-9\s\-\(\)]{10,20}$/', 'message' => 'Введите корректный номер телефона.'],
            [['phone'], 'unique', 'targetClass' => User::class, 'message' => 'Этот телефон уже зарегистрирован.'],
            [['name'], 'string', 'max' => 150],
            [['name'], 'match', 'pattern' => '/^[А-Яа-яЁёA-Za-z\s\-]+$/u', 'message' => 'Только буквы, пробел и тире.'],
            [['password'], 'string', 'min' => 6, 'max' => 64],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают.'],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d'],
            [['birthdate'], 'validateAge'],
            [['agreement'], 'boolean'],
            [['agreement'], 'compare', 'compareValue' => true, 'message' => 'Необходимо согласиться с условиями.'],
            [['referral_code'], 'string', 'max' => 20],
            [['referral_code'], 'validateReferralCode'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'ФИО',
            'email' => 'Email',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'password_repeat' => 'Подтверждение пароля',
            'birthdate' => 'Дата рождения',
            'agreement' => 'Согласие с условиями',
            'referral_code' => 'Реферальный код',
        ];
    }

    public function init()
    {
        parent::init();
        // Если код не задан, пробуем взять из cookie
        if (!$this->referral_code) {
            $cookieCode = ReferralBootstrap::getCookieCode();
            if ($cookieCode) {
                $this->referral_code = $cookieCode;
            }
        }
    }

    public function validateAge($attribute)
    {
        if ($this->birthdate) {
            $age = (int)((time() - strtotime($this->birthdate)) / (365.25 * 24 * 3600));
            if ($age < 21) $this->addError($attribute, 'Минимальный возраст для регистрации — 21 год.');
            if ($age > 100) $this->addError($attribute, 'Проверьте корректность даты.');
        }
    }

    public function validateReferralCode($attribute)
    {
        if (!$this->referral_code) return;
        $this->referrer = User::findByReferralCode($this->referral_code);
        if (!$this->referrer) {
            $this->addError($attribute, 'Реферальный код не найден.');
        }
    }

    public function signup()
    {
        if (!$this->validate()) return null;

        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->birthdate = $this->birthdate;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateReferralCode();
        $user->verification_status = User::VERIFICATION_NONE;
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_USER;
        $user->balance = 0;
        if ($this->referrer) {
            $user->referred_by_user_id = $this->referrer->id;
        }

        if (!$user->save()) {
            return null;
        }

        // Уведомления
        Notification::send($user->id, Notification::TYPE_SUCCESS, 'Добро пожаловать в Вольный Ход!', 'Загрузите документы в личном кабинете для начала поездок.', '/profile/documents', 'fa-handshake');

        // Создание награды + промо-уведомление о бонусе
        if ($this->referrer && Setting::get('referral_program_active', '1')) {
            $bonusReferrer = (float)Setting::get('referral_bonus_referrer', 300);
            $bonusReferred = (float)Setting::get('referral_bonus_referred', 300);

            $reward = new \app\models\ReferralReward([
                'referrer_id' => $this->referrer->id,
                'referred_id' => $user->id,
                'amount_referrer' => $bonusReferrer,
                'amount_referred' => $bonusReferred,
                'status' => \app\models\ReferralReward::STATUS_PENDING,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $reward->save(false);

            // Уведомление новому пользователю
            Notification::send($user->id, Notification::TYPE_PROMO,
                'Бонус от друга — ' . Yii::$app->formatter->asCurrency($bonusReferred),
                $this->referrer->name . ' пригласил вас в Вольный Ход. Совершите первую поездку — и бонус начислится автоматически.',
                '/profile/referrals', 'fa-gift');

            // Уведомление пригласившему
            Notification::send($this->referrer->id, Notification::TYPE_INFO,
                'Новый друг по приглашению!',
                $user->name . ' зарегистрировался по вашей ссылке. Бонус начислится после его первой поездки.',
                '/profile/referrals', 'fa-user-plus');

            ReferralBootstrap::clearCookie();
        } else {
            // Промо-уведомление о реферальной программе
            Notification::send($user->id, Notification::TYPE_PROMO,
                'Приглашайте друзей и зарабатывайте',
                'У вас уже есть личный реферальный код — делитесь и получайте бонусы за каждую первую поездку друга.',
                '/profile/referrals', 'fa-gift');
        }

        return $user;
    }
}
