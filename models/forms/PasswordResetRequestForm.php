<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;

class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'exist', 'targetClass' => User::class, 'filter' => ['status' => User::STATUS_ACTIVE], 'message' => 'Пользователь с таким email не найден.'],
        ];
    }

    public function attributeLabels()
    {
        return ['email' => 'Email'];
    }

    public function sendEmail()
    {
        $user = User::findByEmail($this->email);
        if (!$user) return false;
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) return false;
        }
        return Yii::$app->mailer->compose()
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($user->email)
            ->setSubject('Сброс пароля — Вольный Ход')
            ->setTextBody("Для сброса пароля перейдите по ссылке:\n" . Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]))
            ->send();
    }
}
