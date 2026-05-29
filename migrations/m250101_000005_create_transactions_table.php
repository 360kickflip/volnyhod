<?php

use yii\db\Migration;

/**
 * Транзакции (баланс, депозиты, списания)
 */
class m250101_000005_create_transactions_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%transaction}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'booking_id' => $this->integer()->null(),
            'type' => "ENUM('topup','withdraw','rental_charge','deposit_hold','deposit_release','refund','penalty','correction','bonus') NOT NULL",
            'amount' => $this->decimal(12, 2)->notNull(),
            'balance_after' => $this->decimal(12, 2)->null(),
            'status' => "ENUM('pending','completed','failed','cancelled') NOT NULL DEFAULT 'pending'",
            'payment_method' => "ENUM('card','sbp','wallet','manual','bonus') NULL DEFAULT NULL",
            'external_id' => $this->string(100)->null(),
            'description' => $this->string(500)->null(),
            'created_at' => $this->dateTime()->notNull(),
            'completed_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx-transaction-user_id', '{{%transaction}}', 'user_id');
        $this->createIndex('idx-transaction-booking_id', '{{%transaction}}', 'booking_id');
        $this->createIndex('idx-transaction-type', '{{%transaction}}', 'type');
        $this->createIndex('idx-transaction-status', '{{%transaction}}', 'status');
        $this->createIndex('idx-transaction-created_at', '{{%transaction}}', 'created_at');
        $this->addForeignKey('fk-transaction-user_id', '{{%transaction}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-transaction-booking_id', '{{%transaction}}', 'booking_id', '{{%booking}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%transaction}}');
    }
}
