<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;
use app\models\Notification;

class SignupForm extends Model
{
    public $name;
    public $email;
    public $phone;
    public $password;
    public $password_repeat;
    public $birthdate;
    public $agreement;

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
        ];
    }

    public function validateAge($attribute)
    {
        if ($this->birthdate) {
            $age = (int)((time() - strtotime($this->birthdate)) / (365.25 * 24 * 3600));
            if ($age < 21) {
                $this->addError($attribute, 'Минимальный возраст для регистрации — 21 год.');
            }
            if ($age > 100) {
                $this->addError($attribute, 'Проверьте корректность даты.');
            }
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
        $user->verification_status = User::VERIFICATION_NONE;
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_USER;
        $user->balance = 0;

        if ($user->save()) {
            // Бонус-уведомление
            Notification::send($user->id, Notification::TYPE_SUCCESS, 'Добро пожаловать в Вольный Ход!', 'Загрузите документы в личном кабинете для начала поездок.', '/profile/documents', 'fa-handshake');
            Notification::send($user->id, Notification::TYPE_PROMO, 'Промокод WELCOME10', 'Используйте код WELCOME10 для скидки 10% на первую поездку.', null, 'fa-gift');
            return $user;
        }
        return null;
    }
}
