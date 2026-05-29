<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Response;
use app\models\Transaction;
use app\models\Booking;

class FinanceController extends BaseController
{
    public function actionIndex()
    {
        $from = Yii::$app->request->get('from', date('Y-m-d', strtotime('-30 days')));
        $to = Yii::$app->request->get('to', date('Y-m-d'));

        // По дням
        $byDay = Yii::$app->db->createCommand("
            SELECT DATE(created_at) d, SUM(ABS(amount)) v
            FROM {{%transaction}}
            WHERE type = :t AND status = :s AND created_at BETWEEN :from AND :to
            GROUP BY DATE(created_at)
            ORDER BY d
        ", [':t' => Transaction::TYPE_RENTAL_CHARGE, ':s' => Transaction::STATUS_COMPLETED, ':from' => $from . ' 00:00:00', ':to' => $to . ' 23:59:59'])->queryAll();

        $labels = []; $values = [];
        foreach ($byDay as $row) { $labels[] = date('d.m', strtotime($row['d'])); $values[] = (float)$row['v']; }

        $totalIncome = array_sum($values);

        // По тарифам
        $byTariff = Yii::$app->db->createCommand("
            SELECT t.name, t.color, COUNT(b.id) cnt, COALESCE(SUM(b.final_cost), 0) revenue
            FROM {{%booking}} b
            LEFT JOIN {{%tariff}} t ON t.id = b.tariff_id
            WHERE b.status = :s AND b.ended_at BETWEEN :from AND :to
            GROUP BY t.id, t.name, t.color
            ORDER BY revenue DESC
        ", [':s' => Booking::STATUS_COMPLETED, ':from' => $from . ' 00:00:00', ':to' => $to . ' 23:59:59'])->queryAll();

        // По авто
        $byCar = Yii::$app->db->createCommand("
            SELECT c.id, c.brand, c.model, c.license_plate, COUNT(b.id) cnt, COALESCE(SUM(b.final_cost), 0) revenue
            FROM {{%booking}} b
            LEFT JOIN {{%car}} c ON c.id = b.car_id
            WHERE b.status = :s AND b.ended_at BETWEEN :from AND :to
            GROUP BY c.id, c.brand, c.model, c.license_plate
            ORDER BY revenue DESC
            LIMIT 10
        ", [':s' => Booking::STATUS_COMPLETED, ':from' => $from . ' 00:00:00', ':to' => $to . ' 23:59:59'])->queryAll();

        return $this->render('index', compact('from', 'to', 'labels', 'values', 'totalIncome', 'byTariff', 'byCar'));
    }

    public function actionExport()
    {
        $from = Yii::$app->request->get('from', date('Y-m-d', strtotime('-30 days')));
        $to = Yii::$app->request->get('to', date('Y-m-d'));

        $rows = Yii::$app->db->createCommand("
            SELECT b.number, b.created_at, b.ended_at, b.final_cost, b.status, t.name tariff, c.brand, c.model, c.license_plate, u.email
            FROM {{%booking}} b
            LEFT JOIN {{%car}} c ON c.id = b.car_id
            LEFT JOIN {{%tariff}} t ON t.id = b.tariff_id
            LEFT JOIN {{%user}} u ON u.id = b.user_id
            WHERE b.created_at BETWEEN :from AND :to
            ORDER BY b.created_at DESC
        ", [':from' => $from . ' 00:00:00', ':to' => $to . ' 23:59:59'])->queryAll();

        // CSV
        $response = Yii::$app->response;
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="finance_' . $from . '_' . $to . '.csv"');
        $response->format = Response::FORMAT_RAW;

        $out = "\xEF\xBB\xBF"; // BOM для Excel
        $out .= "Номер;Создано;Завершено;Сумма;Статус;Тариф;Авто;Гос.номер;Email\n";
        foreach ($rows as $r) {
            $out .= implode(';', [
                $r['number'], $r['created_at'], $r['ended_at'], $r['final_cost'], $r['status'],
                $r['tariff'], $r['brand'] . ' ' . $r['model'], $r['license_plate'], $r['email']
            ]) . "\n";
        }
        return $out;
    }
}
