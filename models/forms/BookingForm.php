<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Car;
use app\models\Booking;
use app\models\BookingCharge;
use app\models\Transaction;
use app\models\PromoCode;
use app\models\User;
use app\models\Notification;

class BookingForm extends Model
{
    public $car_id;
    public $planned_minutes = 60;
    public $promo_code;
    public $agreement;

    /** Computed */
    public $appliedPromo;
    public $discount = 0;
    public $estimated = 0;
    public $deposit = 0;
    public $total = 0;
    public $promoMessage = '';

    public function rules()
    {
        return [
            [['car_id', 'planned_minutes'], 'required'],
            [['car_id'], 'integer'],
            [['planned_minutes'], 'integer', 'min' => 15, 'max' => 1440],
            [['promo_code'], 'string', 'max' => 50],
            [['agreement'], 'compare', 'compareValue' => true, 'message' => 'Необходимо согласиться с условиями.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'planned_minutes' => 'Длительность (мин)',
            'promo_code' => 'Промокод',
            'agreement' => 'Согласие с условиями',
        ];
    }

    /**
     * Расчёт стоимости. Не сохраняет ничего в БД, только калькулирует.
     */
    public function calculate(User $user)
    {
        $car = Car::findOne($this->car_id);
        if (!$car || !$car->tariff) {
            $this->addError('car_id', 'Автомобиль не найден.');
            return false;
        }
        $tariff = $car->tariff;
        $minutes = max(15, (int)$this->planned_minutes);

        $cost = $tariff->calculateCost($minutes, 0);
        $this->estimated = $cost['total'];
        $this->deposit = (float)$tariff->deposit;

        // Промокод
        if (!empty($this->promo_code)) {
            $promo = PromoCode::findOne(['code' => strtoupper(trim($this->promo_code))]);
            if (!$promo) {
                $this->promoMessage = 'Промокод не найден';
            } else {
                [$ok, $msg, $discount] = $promo->tryApply($this->estimated, $user->id);
                $this->promoMessage = $msg;
                if ($ok) {
                    $this->discount = $discount;
                    $this->appliedPromo = $promo;
                }
            }
        }

        $this->total = round($this->deposit + $this->estimated - $this->discount, 2);
        return true;
    }

    /**
     * Создаёт бронирование, замораживает депозит.
     */
    public function book(User $user)
    {
        if (!$this->validate()) return null;
        if (!$this->calculate($user)) return null;

        $car = Car::findOne($this->car_id);
        if (!$car || !$car->isAvailableForBooking()) {
            $this->addError('car_id', 'Автомобиль уже забронирован.');
            return null;
        }
        if ($user->getAvailableBalance() < $this->deposit) {
            $this->addError('car_id', 'Недостаточно средств на балансе для депозита (' . $this->deposit . ' ₽). Пополните баланс.');
            return null;
        }
        if ($user->getActiveBooking()) {
            $this->addError('car_id', 'У вас уже есть активная аренда.');
            return null;
        }

        $tr = Yii::$app->db->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $booking = new Booking();
            $booking->number = Booking::generateNumber();
            $booking->user_id = $user->id;
            $booking->car_id = $car->id;
            $booking->tariff_id = $car->tariff_id;
            $booking->status = Booking::STATUS_ACTIVE;
            $booking->planned_minutes = $this->planned_minutes;
            $booking->started_at = $now;
            $booking->planned_end_at = date('Y-m-d H:i:s', time() + $this->planned_minutes * 60);
            $booking->start_mileage = $car->mileage;
            $booking->start_fuel = $car->fuel_level;
            $booking->start_lat = $car->lat;
            $booking->start_lng = $car->lng;
            $booking->start_address = $car->address;
            $booking->deposit = $this->deposit;
            $booking->estimated_cost = $this->estimated;
            $booking->discount_amount = $this->discount;

            if ($this->appliedPromo) {
                $booking->promo_code_id = $this->appliedPromo->id;
                $this->appliedPromo->updateCounters(['usage_count' => 1]);
            }

            $booking->save(false);

            // Заморозка депозита
            $user->locked_balance = (float)$user->locked_balance + $this->deposit;
            $user->save(false);

            $tx = new Transaction();
            $tx->user_id = $user->id;
            $tx->booking_id = $booking->id;
            $tx->type = Transaction::TYPE_DEPOSIT_HOLD;
            $tx->amount = -$this->deposit;
            $tx->balance_after = $user->balance;
            $tx->status = Transaction::STATUS_COMPLETED;
            $tx->payment_method = 'wallet';
            $tx->description = 'Заморозка депозита для аренды #' . $booking->number;
            $tx->created_at = $now;
            $tx->completed_at = $now;
            $tx->save(false);

            // Меняем статус авто
            $car->status = Car::STATUS_RENTED;
            $car->save(false);

            // Уведомление
            Notification::send($user->id, Notification::TYPE_BOOKING, 'Аренда началась',
                $car->getFullName() . ' — ' . $car->license_plate, '/booking/active', 'fa-key');

            $tr->commit();
            return $booking;
        } catch (\Throwable $e) {
            $tr->rollBack();
            $this->addError('car_id', 'Не удалось создать бронирование: ' . $e->getMessage());
            return null;
        }
    }
}
