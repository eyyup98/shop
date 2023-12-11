<?php

namespace app\api\modules\v1\models\params;

use app\api\modules\v1\base\BaseActiveRecord;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "params_title".
 *
 * @property int $id
 * @property int $catalog_id
 * @property int $group_id
 * @property string|null $name
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Catalogs $catalog
 * @property Groups $group
 * @property Params[] $params
 */
class ParamsTitle extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'params_title';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catalog_id', 'group_id'], 'required'],
            [['catalog_id', 'group_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['catalog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogs::class, 'targetAttribute' => ['catalog_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Groups::class, 'targetAttribute' => ['group_id' => 'id']],
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
            'group_id' => 'Group ID',
            'name' => 'Name',
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

    /**
     * Gets query for [[Group]].
     *
     * @return ActiveQuery
     */
    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(Groups::class, ['id' => 'group_id']);
    }

    /**
     * Gets query for [[Params]].
     *
     * @return ActiveQuery
     */
    public function getParams(): ActiveQuery
    {
        return $this->hasMany(Params::class, ['title_id' => 'id']);
    }

    public function fields()
    {
        return array_merge(
            parent::fields(),
            [
                'params' => function($model) {
                    return Params::find()->select(['id', 'name', 'title_id'])->where(['title_id' => $model->id])->asArray()->all() ?? [];
                }
            ]
        );
    }
}
