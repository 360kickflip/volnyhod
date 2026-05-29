<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

class PasswordChangeForm extends Model
{
    public $current_password;
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            [['current_password', 'password', 'password_repeat'], 'required'],
            [['password'], 'string', 'min' => 6, 'max' => 64],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают.'],
            [['current_password'], 'validateCurrent'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'current_password' => 'Текущий пароль',
            'password' => 'Новый пароль',
            'password_repeat' => 'Подтверждение',
        ];
    }

    public function validateCurrent($attribute)
    {
        $user = Yii::$app->user->identity;
        if (!$user || !$user->validatePassword($this->current_password)) {
            $this->addError($attribute, 'Текущий пароль введён неверно.');
        }
    }

    public function change()
    {
        if (!$this->validate()) return false;
        $user = Yii::$app->user->identity;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        return $user->save(false);
    }
}
