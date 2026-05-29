<?php
/** @var app\models\Booking $booking */
use yii\helpers\Html;
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Чек по аренде #<?= $booking->number ?></title>
    <style>
        @page { size: A4; margin: 16mm; }
        * { font-family: 'DejaVu Sans', Arial, sans-serif; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #1f2937; font-size: 13px; line-height: 1.5; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 16px; border-bottom: 2px solid #00c896; margin-bottom: 24px; }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-logo { width: 48px; height: 48px; background: linear-gradient(135deg, #00c896, #00a8a8); color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 800; font-size: 22px; }
        .brand-name { font-size: 20px; font-weight: 800; color: #0a1628; }
        .brand-sub { font-size: 11px; color: #6b7689; text-transform: uppercase; letter-spacing: .04em; }
        h2 { margin: 0; }
        .meta { text-align: right; color: #6b7689; font-size: 12px; }
        h3 { font-size: 14px; text-transform: uppercase; letter-spacing: .05em; color: #6b7689; border-bottom: 1px solid #e6eaf2; padding-bottom: 6px; margin: 24px 0 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { padding: 6px 8px; text-align: left; vertical-align: top; }
        .row { display: flex; gap: 24px; }
        .col { flex: 1; }
        .label { color: #6b7689; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
        .value { font-weight: 600; color: #0a1628; }
        .charge-table { margin-top: 8px; }
        .charge-table th { background: #f1f4f9; font-size: 11px; text-transform: uppercase; color: #6b7689; }
        .charge-table td, .charge-table th { border-bottom: 1px solid #e6eaf2; }
        .charge-table .amount { text-align: right; font-weight: 600; }
        .total { background: #d6fae9; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: #0a1628; margin-top: 12px; }
        .footer { margin-top: 40px; padding-top: 12px; border-top: 1px solid #e6eaf2; text-align: center; color: #6b7689; font-size: 11px; }
        .print-bar { background: #f8fafc; border: 1px solid #e6eaf2; padding: 12px; margin-bottom: 24px; border-radius: 8px; text-align: center; }
        .btn { background: #00c896; color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; }
        @media print { .print-bar { display: none; } body { font-size: 12px; } }
    </style>
</head>
<body>
<div class="print-bar">
    <span>Это печатная версия чека. Используйте «Печать» в браузере и выберите «Сохранить как PDF».</span>
    <button class="btn" onclick="window.print()">🖨 Печать / PDF</button>
</div>

<div class="header">
    <div class="brand">
        <div class="brand-logo">В</div>
        <div>
            <div class="brand-name">Вольный Ход</div>
            <div class="brand-sub">Каршеринг нового поколения</div>
        </div>
    </div>
    <div class="meta">
        <h2>Чек № <?= $booking->number ?></h2>
        <div><?= Yii::$app->formatter->asDatetime($booking->ended_at ?: $booking->created_at) ?></div>
        <div><?= Yii::$app->params['supportEmail'] ?> · <?= Yii::$app->params['supportPhone'] ?></div>
    </div>
</div>

<h3>Клиент</h3>
<div class="row">
    <div class="col">
        <div class="label">ФИО</div>
        <div class="value"><?= Html::encode($booking->user->name ?: 'Не указано') ?></div>
    </div>
    <div class="col">
        <div class="label">Email</div>
        <div class="value"><?= Html::encode($booking->user->email) ?></div>
    </div>
    <div class="col">
        <div class="label">Телефон</div>
        <div class="value"><?= Html::encode($booking->user->phone ?: '—') ?></div>
    </div>
</div>

<h3>Автомобиль</h3>
<div class="row">
    <div class="col">
        <div class="label">Марка/модель</div>
        <div class="value"><?= Html::encode($booking->car->getFullName()) ?> (<?= $booking->car->year ?>)</div>
    </div>
    <div class="col">
        <div class="label">Гос. номер</div>
        <div class="value"><?= Html::encode($booking->car->license_plate) ?></div>
    </div>
    <div class="col">
        <div class="label">Тариф</div>
        <div class="value"><?= Html::encode($booking->tariff->name) ?></div>
    </div>
</div>

<h3>Параметры поездки</h3>
<div class="row">
    <div class="col">
        <div class="label">Начало</div>
        <div class="value"><?= Yii::$app->formatter->asDatetime($booking->started_at) ?></div>
        <div class="label" style="margin-top:6px;"><?= Html::encode($booking->start_address) ?></div>
    </div>
    <div class="col">
        <div class="label">Окончание</div>
        <div class="value"><?= Yii::$app->formatter->asDatetime($booking->ended_at) ?></div>
        <div class="label" style="margin-top:6px;"><?= Html::encode($booking->end_address) ?></div>
    </div>
    <div class="col">
        <div class="label">Длительность / пробег</div>
        <div class="value"><?= floor($booking->getDurationMinutes() / 60) ?>ч <?= $booking->getDurationMinutes() % 60 ?>м · <?= max(0, ($booking->end_mileage ?: 0) - ($booking->start_mileage ?: 0)) ?> км</div>
    </div>
</div>

<h3>Детализация</h3>
<table class="charge-table">
    <thead>
    <tr><th>Статья</th><th>Описание</th><th class="amount">Сумма</th></tr>
    </thead>
    <tbody>
    <?php foreach ($booking->charges as $c): ?>
        <tr>
            <td><?= Html::encode(\app\models\BookingCharge::typeLabel($c->type)) ?></td>
            <td><?= Html::encode($c->description) ?></td>
            <td class="amount" style="color: <?= $c->amount < 0 ? '#10b981' : '#0a1628' ?>;"><?= ($c->amount > 0 ? '' : '') . Yii::$app->formatter->asCurrency($c->amount) ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>

<div class="total">
    <span>Итого к оплате</span>
    <span><?= Yii::$app->formatter->asCurrency($booking->final_cost) ?></span>
</div>

<div class="footer">
    Списано с баланса · Депозит <?= Yii::$app->formatter->asCurrency($booking->deposit) ?> возвращён<br>
    «Вольный Ход» · ИНН/КПП — демо · <?= Yii::$app->params['supportEmail'] ?>
</div>
</body>
</html>
