<?php

use yii\db\Migration;

/**
 * FAQ, статичные страницы, настройки
 */
class m250101_000008_create_faq_pages_settings extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // FAQ категории
        $this->createTable('{{%faq_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(150)->notNull(),
            'slug' => $this->string(150)->notNull()->unique(),
            'icon' => $this->string(50)->null(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        // FAQ
        $this->createTable('{{%faq}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->null(),
            'question' => $this->string(500)->notNull(),
            'answer' => $this->text()->notNull(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-faq-category_id', '{{%faq}}', 'category_id');
        $this->addForeignKey('fk-faq-category_id', '{{%faq}}', 'category_id', '{{%faq_category}}', 'id', 'SET NULL', 'CASCADE');

        // Статичные страницы
        $this->createTable('{{%page}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(190)->notNull()->unique(),
            'title' => $this->string(255)->notNull(),
            'content' => $this->text()->null(),
            'meta_description' => $this->string(500)->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        // Настройки
        $this->createTable('{{%setting}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(100)->notNull()->unique(),
            'value' => $this->text()->null(),
            'group' => $this->string(50)->notNull()->defaultValue('general'),
            'type' => "ENUM('string','int','float','bool','text','json') NOT NULL DEFAULT 'string'",
            'label' => $this->string(255)->null(),
            'description' => $this->string(500)->null(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx-setting-group', '{{%setting}}', 'group');

        // Email/SMS шаблоны
        $this->createTable('{{%email_template}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(100)->notNull()->unique(),
            'channel' => "ENUM('email','sms') NOT NULL DEFAULT 'email'",
            'subject' => $this->string(255)->null(),
            'body' => $this->text()->notNull(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'description' => $this->string(500)->null(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%email_template}}');
        $this->dropTable('{{%setting}}');
        $this->dropTable('{{%page}}');
        $this->dropTable('{{%faq}}');
        $this->dropTable('{{%faq_category}}');
    }
}
