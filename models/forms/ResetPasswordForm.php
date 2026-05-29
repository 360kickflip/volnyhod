<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\base\InvalidArgumentException;
use app\models\User;

class ResetPasswordForm extends Model
{
    public $password;
    public $password_repeat;
    private $_user;

    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Токен сброса пароля не может быть пустым.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidArgumentException('Неверный токен сброса пароля.');
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            [['password'], 'string', 'min' => 6, 'max' => 64],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают.'],
        ];
    }

    public function attributeLabels()
    {
        return ['password' => 'Новый пароль', 'password_repeat' => 'Подтверждение'];
    }

    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        $user->generateAuthKey();
        return $user->save(false);
    }
}
