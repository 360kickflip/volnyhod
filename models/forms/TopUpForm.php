<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Transaction;
use app\models\User;
use app\models\Notification;

class TopUpForm extends Model
{
    public $amount;
    public $payment_method = 'card';

    public function rules()
    {
        return [
            [['amount', 'payment_method'], 'required'],
            [['amount'], 'number', 'min' => 100, 'max' => 100000],
            [['payment_method'], 'in', 'range' => ['card', 'sbp']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => 'Сумма пополнения',
            'payment_method' => 'Способ оплаты',
        ];
    }

    public static function paymentMethods()
    {
        return [
            'card' => 'Банковская карта',
            'sbp' => 'СБП',
        ];
    }

    /**
     * Демо-обработка пополнения. В проде сюда подключается ЮKassa/Robokassa.
     */
    public function process(User $user)
    {
        if (!$this->validate()) return false;

        $tx = new Transaction();
        $tx->user_id = $user->id;
        $tx->type = Transaction::TYPE_TOPUP;
        $tx->amount = $this->amount;
        $tx->payment_method = $this->payment_method;
        $tx->status = Transaction::STATUS_COMPLETED; // Демо: сразу завершаем
        $tx->description = 'Пополнение баланса (' . self::paymentMethods()[$this->payment_method] . ')';
        $tx->created_at = date('Y-m-d H:i:s');
        $tx->completed_at = date('Y-m-d H:i:s');

        $user->balance = (float)$user->balance + (float)$this->amount;
        $tx->balance_after = $user->balance;

        $tx->save(false);
        $user->save(false);

        Notification::send($user->id, Notification::TYPE_PAYMENT, 'Баланс пополнен', 'Зачислено: ' . Yii::$app->formatter->asCurrency($this->amount), '/balance', 'fa-plus-circle');

        return $tx;
    }
}
