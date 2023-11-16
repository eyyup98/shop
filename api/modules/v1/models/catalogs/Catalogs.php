<?php

namespace app\api\modules\v1\models\catalogs;

use app\api\modules\v1\base\BaseActiveRecord;

/**
 * This is the model class for table "catalogs".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $active
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Catalogs extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catalogs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
