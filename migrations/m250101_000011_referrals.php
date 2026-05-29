<?php

use yii\db\Migration;

/**
 * Реферальная программа: коды, связи, награды
 */
class m250101_000011_referrals extends Migration
{
    public function safeUp()
    {
        $tableOptions = $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB' : null;

        // Расширение user
        $this->addColumn('{{%user}}', 'referral_code', $this->string(20)->null()->after('role'));
        $this->addColumn('{{%user}}', 'referred_by_user_id', $this->integer()->null()->after('referral_code'));
        $this->addColumn('{{%user}}', 'referral_bonus_paid', $this->boolean()->notNull()->defaultValue(false)->after('referred_by_user_id'));

        $this->createIndex('idx-user-referral_code', '{{%user}}', 'referral_code', true);
        $this->createIndex('idx-user-referred_by', '{{%user}}', 'referred_by_user_id');
        $this->addForeignKey('fk-user-referred_by', '{{%user}}', 'referred_by_user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');

        // Таблица наград
        $this->createTable('{{%referral_reward}}', [
            'id' => $this->primaryKey(),
            'referrer_id' => $this->integer()->notNull(),
            'referred_id' => $this->integer()->notNull(),
            'booking_id' => $this->integer()->null(),
            'amount_referrer' => $this->decimal(10, 2)->notNull(),
            'amount_referred' => $this->decimal(10, 2)->notNull(),
            'status' => "ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending'",
            'created_at' => $this->dateTime()->notNull(),
            'paid_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx-referral_reward-referrer', '{{%referral_reward}}', 'referrer_id');
        $this->createIndex('idx-referral_reward-referred', '{{%referral_reward}}', 'referred_id', true);
        $this->createIndex('idx-referral_reward-status', '{{%referral_reward}}', 'status');
        $this->addForeignKey('fk-referral_reward-referrer', '{{%referral_reward}}', 'referrer_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-referral_reward-referred', '{{%referral_reward}}', 'referred_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-referral_reward-booking', '{{%referral_reward}}', 'booking_id', '{{%booking}}', 'id', 'SET NULL', 'CASCADE');

        // Сгенерировать коды для существующих пользователей
        $users = $this->db->createCommand("SELECT id, name, email FROM {{%user}} WHERE referral_code IS NULL")->queryAll();
        foreach ($users as $u) {
            $code = self::generateUniqueCode($this->db, $u);
            $this->update('{{%user}}', ['referral_code' => $code], ['id' => $u['id']]);
        }

        // Настройки реф-программы
        $this->batchInsert('{{%setting}}',
            ['key', 'value', 'group', 'type', 'label', 'description', 'sort_order'],
            [
                ['referral_program_active', '1',          'business', 'bool',   'Реферальная программа активна', 'Включает/выключает программу',          10],
                ['referral_bonus_referrer', '300',        'business', 'float',  'Бонус приглашающему (₽)',       'Сколько получит тот, кто пригласил',   11],
                ['referral_bonus_referred', '300',        'business', 'float',  'Бонус приглашённому (₽)',       'Сколько получит новый пользователь',   12],
                ['referral_bonus_trigger',  'first_trip', 'business', 'string', 'Триггер выплаты',               'first_trip / signup / verification',   13],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => ['referral_program_active', 'referral_bonus_referrer', 'referral_bonus_referred', 'referral_bonus_trigger']]);
        $this->dropTable('{{%referral_reward}}');
        $this->dropForeignKey('fk-user-referred_by', '{{%user}}');
        $this->dropIndex('idx-user-referred_by', '{{%user}}');
        $this->dropIndex('idx-user-referral_code', '{{%user}}');
        $this->dropColumn('{{%user}}', 'referral_bonus_paid');
        $this->dropColumn('{{%user}}', 'referred_by_user_id');
        $this->dropColumn('{{%user}}', 'referral_code');
    }

    private static function generateUniqueCode($db, $user): string
    {
        $base = '';
        if (!empty($user['name'])) {
            $name = preg_replace('/[^A-Za-z]/u', '', self::translit(explode(' ', $user['name'])[0] ?? ''));
            $base = mb_strtoupper(mb_substr($name, 0, 5));
        }
        if (!$base) $base = 'VH';
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        for ($i = 0; $i < 50; $i++) {
            $suffix = '';
            for ($j = 0; $j < 4; $j++) $suffix .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            $code = $base . '-' . $suffix;
            $exists = $db->createCommand("SELECT 1 FROM {{%user}} WHERE referral_code = :c", [':c' => $code])->queryScalar();
            if (!$exists) return $code;
        }
        return 'VH-' . substr(md5(uniqid()), 0, 8);
    }

    private static function translit(string $s): string
    {
        $map = [
            'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'i','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sh','ы'=>'y','э'=>'e','ю'=>'u','я'=>'ya','ъ'=>'','ь'=>'',
            'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'E','Ж'=>'Zh','З'=>'Z','И'=>'I','Й'=>'I','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'C','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Sh','Ы'=>'Y','Э'=>'E','Ю'=>'U','Я'=>'Ya','Ъ'=>'','Ь'=>'',
        ];
        return strtr($s, $map);
    }
}
