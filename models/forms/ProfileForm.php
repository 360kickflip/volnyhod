<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;

class ProfileForm extends Model
{
    public $name;
    public $email;
    public $phone;
    public $birthdate;

    /** @var User */
    private $user;

    public function __construct(User $user, $config = [])
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->birthdate = $user->birthdate;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->user->id]],
            [['phone'], 'match', 'pattern' => '/^\+?[0-9\s\-\(\)]{10,20}$/'],
            [['phone'], 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->user->id], 'when' => fn() => !empty($this->phone)],
            [['name'], 'string', 'max' => 150],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'ФИО',
            'email' => 'Email',
            'phone' => 'Телефон',
            'birthdate' => 'Дата рождения',
        ];
    }

    public function save()
    {
        if (!$this->validate()) return false;
        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->phone = $this->phone;
        $this->user->birthdate = $this->birthdate ?: null;
        return $this->user->save(false);
    }
}
