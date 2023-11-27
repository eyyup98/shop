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
        $params = Params::find()->select(['id', 'name', 'title_id'])->where(['title_id' => $this->id])->asArray()->all();

        foreach ($params as &$param) {
            $param['value'] = '';
        }

        $groupChild = Groups::find()->where(['id' => $this->group_id])->andWhere(['not', ['parent_id' => null]])->one();
        $groupParent = Groups::findOne(['id' => ($groupChild->parent_id ?? $this->group_id)]);
        $catalog = Catalogs::findOne($groupParent->catalog_id);
        $parent = parent::fields();
        $groupActive = $groupChild->active ?? $groupParent->active;
        return array_merge(
            $parent,
            [
                'catalog_id' => function() use ($catalog) { return $catalog->id; },
                'catalog_name' => function() use ($catalog) { return $catalog->name; },
                'group_parent_id' => function() use ($groupParent) { return $groupParent->id; },
                'group_parent_name' => function() use ($groupParent) { return $groupParent->name; },
                'group_child_id' => function() use ($groupChild) { return $groupChild->id ?? null; },
                'group_child_name' => function() use ($groupChild) { return $groupChild->name ?? null; },
                'params' => function() use ($params) { return $params ?? []; },
                'group_active' => function() use ($groupActive) { return $groupActive; },
                'view_params' => function() { return false; }
            ]
        );
    }
}
