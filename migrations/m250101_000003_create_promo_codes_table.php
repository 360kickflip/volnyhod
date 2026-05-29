<?php

use yii\db\Migration;

/**
 * Промокоды и история использования
 */
class m250101_000003_create_promo_codes_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%promo_code}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(50)->notNull()->unique(),
            'description' => $this->string(255)->null(),
            'type' => "ENUM('percent','fixed') NOT NULL DEFAULT 'percent'",
            'value' => $this->decimal(10, 2)->notNull(),
            'min_amount' => $this->decimal(10, 2)->null(),
            'max_discount' => $this->decimal(10, 2)->null(),
            'usage_limit' => $this->integer()->null(),
            'usage_count' => $this->integer()->notNull()->defaultValue(0),
            'per_user_limit' => $this->integer()->notNull()->defaultValue(1),
            'valid_from' => $this->dateTime()->null(),
            'valid_to' => $this->dateTime()->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-promo_code-is_active', '{{%promo_code}}', 'is_active');
    }

    public function safeDown()
    {
        $this->dropTable('{{%promo_code}}');
    }
}
