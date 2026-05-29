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

            // Реферальная награда — после первой завершённой поездки
            self::tryPayReferralReward($user, $booking);

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

    /**
     * Выплачивает реф-бонус приглашающему и приглашённому, если это первая
     * успешная поездка и бонус ещё не выдан.
     */
    private static function tryPayReferralReward(User $user, Booking $booking): void
    {
        if ($user->referral_bonus_paid) return;
        if (!$user->referred_by_user_id) return;

        $reward = \app\models\ReferralReward::findOne([
            'referred_id' => $user->id,
            'status' => \app\models\ReferralReward::STATUS_PENDING,
        ]);
        if (!$reward) return;

        $referrer = User::findOne($reward->referrer_id);
        if (!$referrer || $referrer->status !== User::STATUS_ACTIVE) {
            $reward->status = \app\models\ReferralReward::STATUS_CANCELLED;
            $reward->save(false);
            $user->referral_bonus_paid = 1;
            $user->save(false);
            return;
        }

        $now = date('Y-m-d H:i:s');

        // Бонус приглашённому
        if ($reward->amount_referred > 0) {
            $user->balance = (float)$user->balance + (float)$reward->amount_referred;
            $tx = new Transaction([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'type' => Transaction::TYPE_BONUS,
                'amount' => $reward->amount_referred,
                'balance_after' => $user->balance,
                'status' => Transaction::STATUS_COMPLETED,
                'payment_method' => 'bonus',
                'description' => 'Реферальный бонус (приглашён ' . ($referrer->name ?: $referrer->email) . ')',
                'created_at' => $now,
                'completed_at' => $now,
            ]);
            $tx->save(false);
        }

        // Бонус приглашающему
        if ($reward->amount_referrer > 0) {
            $referrer->balance = (float)$referrer->balance + (float)$reward->amount_referrer;
            $referrer->save(false);
            $tx = new Transaction([
                'user_id' => $referrer->id,
                'type' => Transaction::TYPE_BONUS,
                'amount' => $reward->amount_referrer,
                'balance_after' => $referrer->balance,
                'status' => Transaction::STATUS_COMPLETED,
                'payment_method' => 'bonus',
                'description' => 'Реферальный бонус (друг ' . ($user->name ?: $user->email) . ' совершил первую поездку)',
                'created_at' => $now,
                'completed_at' => $now,
            ]);
            $tx->save(false);

            Notification::send($referrer->id, Notification::TYPE_PROMO,
                'Друг совершил первую поездку!',
                'Вам начислено ' . Yii::$app->formatter->asCurrency($reward->amount_referrer) . ' за приглашение ' . ($user->name ?: 'друга'),
                '/profile/referrals', 'fa-gift');
        }

        Notification::send($user->id, Notification::TYPE_PROMO,
            'Бонус начислен',
            'Спасибо за первую поездку! На баланс зачислено ' . Yii::$app->formatter->asCurrency($reward->amount_referred),
            '/balance', 'fa-gift');

        $reward->status = \app\models\ReferralReward::STATUS_PAID;
        $reward->booking_id = $booking->id;
        $reward->paid_at = $now;
        $reward->save(false);

        $user->balance = $user->balance; // already updated
        $user->referral_bonus_paid = 1;
        $user->save(false);
    }
}
