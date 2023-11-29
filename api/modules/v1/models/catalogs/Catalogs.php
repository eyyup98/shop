<?php

namespace app\api\modules\v1\models\catalogs;

use app\api\modules\v1\base\BaseActiveRecord;
use app\api\modules\v1\models\groups\Groups;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

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
//            [['active'], 'integer'],
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

    public function fields($method = null)
    {
        self::$method = !empty(self::$method) ? self::$method : $method;

        if (!empty(self::$method))
            return $this->{self::$method}();

        $fields = parent::fields();

        unset($fields['created_at']);
        unset($fields['updated_at']);

        return $fields;
    }

    function forGroups(): array
    {
        return [
            'id',
            'name',
            'active',
            'view_groups' => function() { return true; },
            'groups' => function($model) {
                return Groups::find()->where(['catalog_id' => $model->id, 'parent_id' => null])->all();
            },
        ];
    }
}
