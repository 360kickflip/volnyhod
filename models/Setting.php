<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Setting extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%setting}}';
    }

    public function rules()
    {
        return [
            [['key'], 'required'],
            [['key'], 'unique'],
            [['key'], 'string', 'max' => 100],
            [['value', 'description'], 'string'],
            [['group', 'label'], 'string', 'max' => 255],
            [['type'], 'in', 'range' => ['string', 'int', 'float', 'bool', 'text', 'json']],
            [['sort_order'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'key' => 'Ключ',
            'value' => 'Значение',
            'group' => 'Группа',
            'type' => 'Тип',
            'label' => 'Название',
            'description' => 'Описание',
        ];
    }

    public static function get($key, $default = null)
    {
        $row = static::findOne(['key' => $key]);
        if (!$row) return $default;
        return self::cast($row->value, $row->type);
    }

    public static function set($key, $value)
    {
        $row = static::findOne(['key' => $key]);
        if (!$row) {
            $row = new self(['key' => $key, 'group' => 'general', 'type' => 'string']);
        }
        $row->value = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string)$value;
        return $row->save();
    }

    public static function byGroup($group)
    {
        return static::find()->where(['group' => $group])->orderBy(['sort_order' => SORT_ASC])->all();
    }

    private static function cast($value, $type)
    {
        return match ($type) {
            'int' => (int)$value,
            'float' => (float)$value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOL),
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
