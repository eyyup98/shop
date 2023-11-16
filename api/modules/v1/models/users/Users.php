<?php

namespace app\api\modules\v1\models\users;

use app\api\modules\v1\base\BaseActiveRecord;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string|null $name
 * @property string $login
 * @property string $password
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Users extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'login', 'password'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'login' => 'Login',
            'password' => 'Password',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
