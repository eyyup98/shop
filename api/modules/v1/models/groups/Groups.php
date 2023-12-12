<?php

namespace app\api\modules\v1\models\groups;

use app\api\modules\v1\base\BaseActiveRecord;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\params\ParamsTitle;
use app\api\modules\v1\models\products\Products;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "groups".
 *
 * @property int $id
 * @property int $catalog_id
 * @property string|null $name
 * @property int|null $active
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Catalogs $catalog
 * @property ParamsTitle[] $paramsTitles
 * @property Products[] $products
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
            [['catalog_id'/*, 'active'*/], 'integer'],
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

    /**
     * Gets query for [[ParamsTitles]].
     *
     * @return ActiveQuery
     */
    public function getParamsTitles(): ActiveQuery
    {
        return $this->hasMany(ParamsTitle::class, ['group_id' => 'id']);
    }

    /**
     * Gets query for [[Products]].
     *
     * @return ActiveQuery
     */
    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(Products::class, ['group_id' => 'id']);
    }

    public function fields($method = null)
    {
        if (self::$method != 'forGroups') {
            self::$method = !empty(self::$method) ? self::$method : $method;

            if (!empty(self::$method))
                return $this->{self::$method}();
        }

        $fields = parent::fields();

        unset($fields['created_at']);
        unset($fields['updated_at']);

        return $fields;
    }

    function forParams(): array
    {
        return [
            'id',
            'catalog_id',
            'name',
            'active',
            'view_params_title' => function() { return true; },
            'params_title' => function($model) {
                return ParamsTitle::find()->where(['catalog_id' => $model->catalog_id, 'group_id' => $model->id])->all();
            },
        ];
    }
}
