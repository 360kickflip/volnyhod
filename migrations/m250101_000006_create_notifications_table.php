<?php

use yii\db\Migration;

/**
 * Уведомления пользователей
 */
class m250101_000006_create_notifications_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => "ENUM('info','success','warning','danger','booking','payment','support','promo','system') NOT NULL DEFAULT 'info'",
            'title' => $this->string(255)->notNull(),
            'message' => $this->text()->null(),
            'icon' => $this->string(50)->null(),
            'url' => $this->string(255)->null(),
            'is_read' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->dateTime()->notNull(),
            'read_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx-notification-user_id', '{{%notification}}', 'user_id');
        $this->createIndex('idx-notification-is_read', '{{%notification}}', 'is_read');
        $this->createIndex('idx-notification-created_at', '{{%notification}}', 'created_at');
        $this->addForeignKey('fk-notification-user_id', '{{%notification}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%notification}}');
    }
}
