<?php

use yii\db\Migration;

/**
 * Бронирования, детализация счёта, отзывы, повреждения
 */
class m250101_000004_create_bookings_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%booking}}', [
            'id' => $this->primaryKey(),
            'number' => $this->string(20)->notNull()->unique(),
            'user_id' => $this->integer()->notNull(),
            'car_id' => $this->integer()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'promo_code_id' => $this->integer()->null(),
            'status' => "ENUM('pending','active','completed','cancelled','expired') NOT NULL DEFAULT 'pending'",

            'planned_minutes' => $this->integer()->null(),
            'started_at' => $this->dateTime()->null(),
            'planned_end_at' => $this->dateTime()->null(),
            'ended_at' => $this->dateTime()->null(),

            'start_mileage' => $this->integer()->null(),
            'end_mileage' => $this->integer()->null(),
            'start_fuel' => $this->tinyInteger()->null(),
            'end_fuel' => $this->tinyInteger()->null(),

            'start_lat' => $this->decimal(10, 7)->null(),
            'start_lng' => $this->decimal(10, 7)->null(),
            'start_address' => $this->string(255)->null(),
            'end_lat' => $this->decimal(10, 7)->null(),
            'end_lng' => $this->decimal(10, 7)->null(),
            'end_address' => $this->string(255)->null(),

            'deposit' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'estimated_cost' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'base_cost' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'extra_km_cost' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'overdue_cost' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'penalty_cost' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'discount_amount' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'final_cost' => $this->decimal(10, 2)->notNull()->defaultValue(0),

            'cancel_reason' => $this->string(500)->null(),
            'cancelled_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-booking-user_id', '{{%booking}}', 'user_id');
        $this->createIndex('idx-booking-car_id', '{{%booking}}', 'car_id');
        $this->createIndex('idx-booking-status', '{{%booking}}', 'status');
        $this->createIndex('idx-booking-created_at', '{{%booking}}', 'created_at');
        $this->addForeignKey('fk-booking-user_id', '{{%booking}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-booking-car_id', '{{%booking}}', 'car_id', '{{%car}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-booking-tariff_id', '{{%booking}}', 'tariff_id', '{{%tariff}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-booking-promo_code_id', '{{%booking}}', 'promo_code_id', '{{%promo_code}}', 'id', 'SET NULL', 'CASCADE');

        // Детализация счёта
        $this->createTable('{{%booking_charge}}', [
            'id' => $this->primaryKey(),
            'booking_id' => $this->integer()->notNull(),
            'type' => "ENUM('base','extra_km','overdue','penalty','damage','discount','manual') NOT NULL",
            'description' => $this->string(255)->null(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-booking_charge-booking_id', '{{%booking_charge}}', 'booking_id');
        $this->addForeignKey('fk-booking_charge-booking_id', '{{%booking_charge}}', 'booking_id', '{{%booking}}', 'id', 'CASCADE', 'CASCADE');

        // Отзывы
        $this->createTable('{{%review}}', [
            'id' => $this->primaryKey(),
            'booking_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'car_id' => $this->integer()->notNull(),
            'rating' => $this->tinyInteger()->notNull(),
            'text' => $this->text()->null(),
            'photos' => $this->text()->null(),
            'status' => "ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'",
            'moderator_id' => $this->integer()->null(),
            'moderator_comment' => $this->string(500)->null(),
            'moderated_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-review-booking_id', '{{%review}}', 'booking_id', true);
        $this->createIndex('idx-review-user_id', '{{%review}}', 'user_id');
        $this->createIndex('idx-review-car_id', '{{%review}}', 'car_id');
        $this->createIndex('idx-review-status', '{{%review}}', 'status');
        $this->addForeignKey('fk-review-booking_id', '{{%review}}', 'booking_id', '{{%booking}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-review-user_id', '{{%review}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-review-car_id', '{{%review}}', 'car_id', '{{%car}}', 'id', 'CASCADE', 'CASCADE');

        // Повреждения / инциденты
        $this->createTable('{{%damage_report}}', [
            'id' => $this->primaryKey(),
            'booking_id' => $this->integer()->null(),
            'car_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->null(),
            'description' => $this->text()->notNull(),
            'severity' => "ENUM('minor','moderate','severe') NOT NULL DEFAULT 'minor'",
            'photos' => $this->text()->null(),
            'status' => "ENUM('reported','reviewing','user_liable','not_liable','resolved') NOT NULL DEFAULT 'reported'",
            'repair_cost' => $this->decimal(10, 2)->null(),
            'reviewer_id' => $this->integer()->null(),
            'reviewer_comment' => $this->text()->null(),
            'created_at' => $this->dateTime()->notNull(),
            'reviewed_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx-damage_report-booking_id', '{{%damage_report}}', 'booking_id');
        $this->createIndex('idx-damage_report-car_id', '{{%damage_report}}', 'car_id');
        $this->createIndex('idx-damage_report-status', '{{%damage_report}}', 'status');
        $this->addForeignKey('fk-damage_report-booking_id', '{{%damage_report}}', 'booking_id', '{{%booking}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-damage_report-car_id', '{{%damage_report}}', 'car_id', '{{%car}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-damage_report-user_id', '{{%damage_report}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%damage_report}}');
        $this->dropTable('{{%review}}');
        $this->dropTable('{{%booking_charge}}');
        $this->dropTable('{{%booking}}');
    }
}
