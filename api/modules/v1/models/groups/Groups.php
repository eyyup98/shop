<?php

namespace app\api\modules\v1\models\groups;

use app\api\modules\v1\base\BaseActiveRecord;
use app\api\modules\v1\models\catalogs\Catalogs;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "groups".
 *
 * @property int $id
 * @property int $catalog_id
 * @property int|null $parent_id
 * @property string|null $name
 * @property int|null $active
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Catalogs $catalog
 */
class Groups extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catalog_id'], 'required'],
            [['catalog_id', 'parent_id'/*, 'active'*/], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['catalog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogs::class, 'targetAttribute' => ['catalog_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'catalog_id' => 'Catalog ID',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Catalog]].
     *
     * @return ActiveQuery
     */
    public function getCatalog(): ActiveQuery
    {
        return $this->hasOne(Catalogs::class, ['id' => 'catalog_id']);
    }

    public function fields()
    {
        return array_merge(
            parent::fields(),
            [
                'view_subgroups' => function() {return false;},
                'subgroups' => function($model) {
                    return Groups::find()->select(['active', 'catalog_id', 'id', 'name', 'parent_id'])
                        ->where(['parent_id' => $model->id])->asArray()->all() ?? [];
                },
                'catalog_name' => function($model) {
                    return Catalogs::findOne($model->catalog_id)->name;
                }

            ]
        );
    }
}
