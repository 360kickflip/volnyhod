<?php

use yii\db\Migration;

/**
 * Поддержка: обращения и сообщения
 */
class m250101_000007_create_support_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%support_ticket}}', [
            'id' => $this->primaryKey(),
            'number' => $this->string(20)->notNull()->unique(),
            'user_id' => $this->integer()->notNull(),
            'assigned_admin_id' => $this->integer()->null(),
            'category' => "ENUM('account','payment','rental','car','technical','other') NOT NULL DEFAULT 'other'",
            'subject' => $this->string(255)->notNull(),
            'priority' => "ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal'",
            'status' => "ENUM('open','in_progress','waiting_user','resolved','closed') NOT NULL DEFAULT 'open'",
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'closed_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx-support_ticket-user_id', '{{%support_ticket}}', 'user_id');
        $this->createIndex('idx-support_ticket-status', '{{%support_ticket}}', 'status');
        $this->createIndex('idx-support_ticket-category', '{{%support_ticket}}', 'category');
        $this->addForeignKey('fk-support_ticket-user_id', '{{%support_ticket}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-support_ticket-assigned_admin_id', '{{%support_ticket}}', 'assigned_admin_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');

        $this->createTable('{{%support_message}}', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'author_role' => "ENUM('user','admin','system') NOT NULL DEFAULT 'user'",
            'message' => $this->text()->notNull(),
            'attachments' => $this->text()->null(),
            'created_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-support_message-ticket_id', '{{%support_message}}', 'ticket_id');
        $this->createIndex('idx-support_message-author_id', '{{%support_message}}', 'author_id');
        $this->addForeignKey('fk-support_message-ticket_id', '{{%support_message}}', 'ticket_id', '{{%support_ticket}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-support_message-author_id', '{{%support_message}}', 'author_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%support_message}}');
        $this->dropTable('{{%support_ticket}}');
    }
}
