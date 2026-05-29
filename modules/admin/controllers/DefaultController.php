<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\User;
use app\models\Car;
use app\models\Booking;
use app\models\Transaction;
use app\models\SupportTicket;

class DefaultController extends BaseController
{
    public function actionIndex()
    {
        $today = date('Y-m-d');
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        $monthAgo = date('Y-m-d', strtotime('-30 days'));

        // Доход
        $incomeToday = (float)Transaction::find()
            ->where(['type' => Transaction::TYPE_RENTAL_CHARGE, 'status' => Transaction::STATUS_COMPLETED])
            ->andWhere(['>=', 'created_at', $today . ' 00:00:00'])
            ->sum('amount');
        $incomeWeek = (float)Transaction::find()
            ->where(['type' => Transaction::TYPE_RENTAL_CHARGE, 'status' => Transaction::STATUS_COMPLETED])
            ->andWhere(['>=', 'created_at', $weekAgo . ' 00:00:00'])
            ->sum('amount');
        $incomeMonth = (float)Transaction::find()
            ->where(['type' => Transaction::TYPE_RENTAL_CHARGE, 'status' => Transaction::STATUS_COMPLETED])
            ->andWhere(['>=', 'created_at', $monthAgo . ' 00:00:00'])
            ->sum('amount');

        $stats = [
            'income_today' => abs($incomeToday),
            'income_week' => abs($incomeWeek),
            'income_month' => abs($incomeMonth),
            'active_rentals' => Booking::find()->where(['status' => Booking::STATUS_ACTIVE])->count(),
            'total_users' => User::find()->where(['role' => User::ROLE_USER])->count(),
            'new_users_week' => User::find()->where(['role' => User::ROLE_USER])->andWhere(['>=', 'created_at', $weekAgo . ' 00:00:00'])->count(),
            'total_cars' => Car::find()->count(),
            'available_cars' => Car::find()->where(['status' => Car::STATUS_AVAILABLE])->count(),
            'avg_duration_min' => (int)round((float)Booking::find()
                ->select(new \yii\db\Expression('AVG(TIMESTAMPDIFF(MINUTE, started_at, ended_at))'))
                ->where(['status' => Booking::STATUS_COMPLETED])
                ->scalar()),
        ];

        // Доход по дням за 14 дней
        $rows = Yii::$app->db->createCommand("
            SELECT DATE(created_at) as d, SUM(ABS(amount)) as v
            FROM {{%transaction}}
            WHERE type = :t AND status = :s AND created_at >= :from
            GROUP BY DATE(created_at)
            ORDER BY d
        ", [':t' => Transaction::TYPE_RENTAL_CHARGE, ':s' => Transaction::STATUS_COMPLETED, ':from' => date('Y-m-d', strtotime('-14 days'))])->queryAll();

        $chartLabels = [];
        $chartValues = [];
        $byDay = [];
        foreach ($rows as $r) $byDay[$r['d']] = (float)$r['v'];
        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $chartLabels[] = date('d.m', strtotime($d));
            $chartValues[] = $byDay[$d] ?? 0;
        }

        // Топ авто
        $topCars = Yii::$app->db->createCommand("
            SELECT c.id, c.brand, c.model, c.license_plate, COUNT(b.id) as cnt, COALESCE(SUM(b.final_cost), 0) as revenue
            FROM {{%car}} c
            LEFT JOIN {{%booking}} b ON b.car_id = c.id AND b.status = :status
            GROUP BY c.id, c.brand, c.model, c.license_plate
            ORDER BY cnt DESC
            LIMIT 5
        ", [':status' => Booking::STATUS_COMPLETED])->queryAll();

        // Последние 5 бронирований
        $recentBookings = Booking::find()->with(['user', 'car'])->orderBy(['created_at' => SORT_DESC])->limit(8)->all();

        // Новые тикеты
        $newTickets = SupportTicket::find()->with('user')->where(['status' => [SupportTicket::STATUS_OPEN]])->orderBy(['created_at' => SORT_DESC])->limit(5)->all();

        return $this->render('index', compact('stats', 'chartLabels', 'chartValues', 'topCars', 'recentBookings', 'newTickets'));
    }
}
