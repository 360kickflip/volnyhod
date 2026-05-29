<?php

namespace app\components;

use Yii;
use app\models\Booking;
use app\models\BookingCharge;
use app\models\Car;
use app\models\Notification;
use app\models\Transaction;
use app\models\User;

/**
 * Сервис управления арендой: завершение, отмена, расчёт.
 */
class BookingService
{
    /**
     * Завершить активную аренду
     */
    public static function finish(Booking $booking, ?User $user = null)
    {
        if ($booking->status !== Booking::STATUS_ACTIVE) {
            return [false, 'Аренда уже завершена.'];
        }
        $user = $user ?? User::findOne($booking->user_id);
        $car = $booking->car;
        if (!$car || !$user) {
            return [false, 'Не найдены связанные сущности.'];
        }

        $tr = Yii::$app->db->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $booking->ended_at = $now;
            $booking->end_mileage = $car->mileage + rand(5, 50); // эмуляция нового пробега
            $booking->end_fuel = max(10, $car->fuel_level - rand(2, 15));
            $booking->end_lat = $car->lat;
            $booking->end_lng = $car->lng;
            $booking->end_address = $car->address;

            $minutes = $booking->getDurationMinutes();
            $km = max(0, $booking->end_mileage - ($booking->start_mileage ?? 0));
            $tariff = $booking->tariff;
            $cost = $tariff->calculateCost($minutes, $km);

            $overdueMinutes = 0;
            if ($booking->planned_end_at && strtotime($now) > strtotime($booking->planned_end_at)) {
                $overdueMinutes = (int)round((strtotime($now) - strtotime($booking->planned_end_at)) / 60);
                $cost = $tariff->calculateCost($minutes, $km, $overdueMinutes);
            }

            $discount = (float)$booking->discount_amount;
            $finalCost = max(0, round($cost['total'] - $discount, 2));

            $booking->base_cost = $cost['base'];
            $booking->extra_km_cost = $cost['extra_km'];
            $booking->overdue_cost = $cost['overdue'];
            $booking->final_cost = $finalCost;
            $booking->status = Booking::STATUS_COMPLETED;
            $booking->save(false);

            // Детализация
            self::charge($booking, BookingCharge::TYPE_BASE, 'Базовая стоимость (' . $minutes . ' мин)', $cost['base']);
            if ($cost['extra_km'] > 0) self::charge($booking, BookingCharge::TYPE_EXTRA_KM, 'Доп. километры (' . $km . ' км)', $cost['extra_km']);
            if ($cost['overdue'] > 0) self::charge($booking, BookingCharge::TYPE_OVERDUE, 'Просрочка (' . $overdueMinutes . ' мин)', $cost['overdue']);
            if ($discount > 0) self::charge($booking, BookingCharge::TYPE_DISCOUNT, 'Скидка по промокоду', -$discount);

            // Возврат депозита
            $user->locked_balance = max(0, (float)$user->locked_balance - (float)$booking->deposit);
            // Списание стоимости с баланса
            $user->balance = max(0, (float)$user->balance - $finalCost);
            $user->save(false);

            // Транзакции
            $depTx = new Transaction([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'type' => Transaction::TYPE_DEPOSIT_RELEASE,
                'amount' => (float)$booking->deposit,
                'balance_after' => $user->balance,
                'status' => Transaction::STATUS_COMPLETED,
                'payment_method' => 'wallet',
                'description' => 'Возврат депозита по аренде #' . $booking->number,
                'created_at' => $now,
                'completed_at' => $now,
            ]);
            $depTx->save(false);

            if ($finalCost > 0) {
                $rentTx = new Transaction([
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'type' => Transaction::TYPE_RENTAL_CHARGE,
                    'amount' => -$finalCost,
                    'balance_after' => $user->balance,
                    'status' => Transaction::STATUS_COMPLETED,
                    'payment_method' => 'wallet',
                    'description' => 'Оплата аренды #' . $booking->number,
                    'created_at' => $now,
                    'completed_at' => $now,
                ]);
                $rentTx->save(false);
            }

            // Возвращаем авто на линию
            $car->status = Car::STATUS_AVAILABLE;
            $car->mileage = $booking->end_mileage;
            $car->fuel_level = $booking->end_fuel;
            $car->save(false);

            Notification::send($user->id, Notification::TYPE_SUCCESS, 'Аренда завершена',
                $car->getFullName() . ' • ' . Yii::$app->formatter->asCurrency($finalCost),
                '/trips/' . $booking->id, 'fa-check-circle');

            $tr->commit();
            return [true, $booking];
        } catch (\Throwable $e) {
            $tr->rollBack();
            return [false, $e->getMessage()];
        }
    }

    public static function cancel(Booking $booking, $reason = '')
    {
        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_ACTIVE])) {
            return [false, 'Невозможно отменить эту аренду.'];
        }

        $tr = Yii::$app->db->beginTransaction();
        try {
            $user = User::findOne($booking->user_id);
            $car = $booking->car;

            $booking->status = Booking::STATUS_CANCELLED;
            $booking->cancelled_at = date('Y-m-d H:i:s');
            $booking->cancel_reason = $reason;
            $booking->save(false);

            // Возврат депозита
            if ($user && $booking->deposit > 0) {
                $user->locked_balance = max(0, (float)$user->locked_balance - (float)$booking->deposit);
                $user->save(false);

                $tx = new Transaction([
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'type' => Transaction::TYPE_DEPOSIT_RELEASE,
                    'amount' => (float)$booking->deposit,
                    'balance_after' => $user->balance,
                    'status' => Transaction::STATUS_COMPLETED,
                    'payment_method' => 'wallet',
                    'description' => 'Возврат депозита (отмена аренды #' . $booking->number . ')',
                    'created_at' => date('Y-m-d H:i:s'),
                    'completed_at' => date('Y-m-d H:i:s'),
                ]);
                $tx->save(false);
            }

            if ($car) {
                $car->status = Car::STATUS_AVAILABLE;
                $car->save(false);
            }

            if ($user) {
                Notification::send($user->id, Notification::TYPE_WARNING, 'Аренда отменена',
                    $reason ?: 'Депозит возвращён на баланс.', '/trips', 'fa-times-circle');
            }

            $tr->commit();
            return [true, $booking];
        } catch (\Throwable $e) {
            $tr->rollBack();
            return [false, $e->getMessage()];
        }
    }

    private static function charge(Booking $booking, $type, $desc, $amount)
    {
        $c = new BookingCharge();
        $c->booking_id = $booking->id;
        $c->type = $type;
        $c->description = $desc;
        $c->amount = $amount;
        $c->created_at = date('Y-m-d H:i:s');
        return $c->save(false);
    }
}
