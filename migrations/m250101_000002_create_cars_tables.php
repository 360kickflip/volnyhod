<?php

use yii\db\Migration;

/**
 * Тарифы, автомобили, фото, история локаций
 */
class m250101_000002_create_cars_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // Тарифы
        $this->createTable('{{%tariff}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'description' => $this->text()->null(),
            'price_per_minute' => $this->decimal(8, 2)->notNull()->defaultValue(0),
            'price_per_km' => $this->decimal(8, 2)->notNull()->defaultValue(0),
            'price_per_hour' => $this->decimal(8, 2)->null(),
            'price_per_day' => $this->decimal(10, 2)->null(),
            'free_km' => $this->integer()->null()->defaultValue(0),
            'deposit' => $this->decimal(10, 2)->notNull()->defaultValue(2000),
            'overdue_per_minute' => $this->decimal(8, 2)->notNull()->defaultValue(0),
            'color' => $this->string(20)->null()->defaultValue('#0d6efd'),
            'icon' => $this->string(50)->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        // Автомобили
        $this->createTable('{{%car}}', [
            'id' => $this->primaryKey(),
            'tariff_id' => $this->integer()->notNull(),
            'brand' => $this->string(80)->notNull(),
            'model' => $this->string(80)->notNull(),
            'year' => $this->smallInteger()->null(),
            'color' => $this->string(50)->null(),
            'license_plate' => $this->string(20)->notNull()->unique(),
            'vin' => $this->string(17)->null(),
            'transmission' => "ENUM('manual','auto','robot','variator') NOT NULL DEFAULT 'auto'",
            'body_type' => $this->string(40)->null(),
            'seats' => $this->tinyInteger()->null()->defaultValue(5),
            'fuel_type' => "ENUM('petrol','diesel','hybrid','electric','gas') NOT NULL DEFAULT 'petrol'",
            'mileage' => $this->integer()->notNull()->defaultValue(0),
            'fuel_level' => $this->tinyInteger()->notNull()->defaultValue(100),
            'battery_level' => $this->tinyInteger()->null(),
            'features' => $this->text()->null(),
            'description' => $this->text()->null(),
            'lat' => $this->decimal(10, 7)->null(),
            'lng' => $this->decimal(10, 7)->null(),
            'address' => $this->string(255)->null(),
            'status' => "ENUM('available','rented','reserved','maintenance','blocked','offline') NOT NULL DEFAULT 'available'",
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-car-tariff_id', '{{%car}}', 'tariff_id');
        $this->createIndex('idx-car-status', '{{%car}}', 'status');
        $this->createIndex('idx-car-brand_model', '{{%car}}', ['brand', 'model']);
        $this->addForeignKey('fk-car-tariff_id', '{{%car}}', 'tariff_id', '{{%tariff}}', 'id', 'RESTRICT', 'CASCADE');

        // Фото автомобилей
        $this->createTable('{{%car_photo}}', [
            'id' => $this->primaryKey(),
            'car_id' => $this->integer()->notNull(),
            'file_path' => $this->string(255)->notNull(),
            'is_main' => $this->boolean()->notNull()->defaultValue(false),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-car_photo-car_id', '{{%car_photo}}', 'car_id');
        $this->addForeignKey('fk-car_photo-car_id', '{{%car_photo}}', 'car_id', '{{%car}}', 'id', 'CASCADE', 'CASCADE');

        // История локаций
        $this->createTable('{{%car_location_history}}', [
            'id' => $this->primaryKey(),
            'car_id' => $this->integer()->notNull(),
            'booking_id' => $this->integer()->null(),
            'lat' => $this->decimal(10, 7)->notNull(),
            'lng' => $this->decimal(10, 7)->notNull(),
            'speed' => $this->smallInteger()->null(),
            'mileage' => $this->integer()->null(),
            'address' => $this->string(255)->null(),
            'recorded_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-car_location_history-car_id', '{{%car_location_history}}', 'car_id');
        $this->createIndex('idx-car_location_history-booking_id', '{{%car_location_history}}', 'booking_id');
        $this->createIndex('idx-car_location_history-recorded_at', '{{%car_location_history}}', 'recorded_at');
        $this->addForeignKey('fk-car_location_history-car_id', '{{%car_location_history}}', 'car_id', '{{%car}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%car_location_history}}');
        $this->dropTable('{{%car_photo}}');
        $this->dropTable('{{%car}}');
        $this->dropTable('{{%tariff}}');
    }
}
