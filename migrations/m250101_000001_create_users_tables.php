<?php

use yii\db\Migration;

/**
 * Создание таблиц пользователей и документов
 */
class m250101_000001_create_users_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // Пользователи
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(190)->notNull()->unique(),
            'phone' => $this->string(20)->null(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(64)->notNull(),
            'password_reset_token' => $this->string(255)->null()->unique(),
            'verification_token' => $this->string(255)->null(),
            'email_verified_at' => $this->dateTime()->null(),
            'name' => $this->string(150)->null(),
            'birthdate' => $this->date()->null(),
            'avatar' => $this->string(255)->null(),
            'balance' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'locked_balance' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'verification_status' => "ENUM('none','pending','verified','rejected') NOT NULL DEFAULT 'none'",
            'status' => "ENUM('active','blocked','deleted') NOT NULL DEFAULT 'active'",
            'role' => "ENUM('user','admin','manager') NOT NULL DEFAULT 'user'",
            'block_reason' => $this->string(500)->null(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-user-phone', '{{%user}}', 'phone');
        $this->createIndex('idx-user-status', '{{%user}}', 'status');
        $this->createIndex('idx-user-role', '{{%user}}', 'role');

        // Документы пользователей
        $this->createTable('{{%user_document}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => "ENUM('license_front','license_back','passport_main','passport_registration','selfie') NOT NULL",
            'file_path' => $this->string(255)->notNull(),
            'original_name' => $this->string(255)->null(),
            'status' => "ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'",
            'comment' => $this->text()->null(),
            'reviewer_id' => $this->integer()->null(),
            'uploaded_at' => $this->dateTime()->notNull(),
            'reviewed_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx-user_document-user_id', '{{%user_document}}', 'user_id');
        $this->createIndex('idx-user_document-status', '{{%user_document}}', 'status');
        $this->addForeignKey('fk-user_document-user_id', '{{%user_document}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_document}}');
        $this->dropTable('{{%user}}');
    }
}
