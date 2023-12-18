<?php

namespace app\api\modules\v1\models\products;

use app\api\modules\v1\base\BaseActiveRecord;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use app\api\modules\v1\models\params\Params;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property int $catalog_id
 * @property int $group_id
 * @property string $name
 * @property float|null $price
 * @property float|null $discount
 * @property int|null $active
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Catalogs $catalog
 * @property Groups $group
 * @property ProductsImg[] $productsImgs
 * @property ProductsParams[] $productsParams
 */
class Products extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catalog_id', 'group_id', 'name'], 'required'],
            [['catalog_id', 'group_id'/*, 'active'*/], 'integer'],
            [['price', 'discount'], 'number'],
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
            'price' => 'Price',
            'discount' => 'Discount',
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
     * Gets query for [[Group]].
     *
     * @return ActiveQuery
     */
    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(Groups::class, ['id' => 'group_id']);
    }

    /**
     * Gets query for [[ProductsImgs]].
     *
     * @return ActiveQuery
     */
    public function getProductsImgs(): ActiveQuery
    {
        return $this->hasMany(ProductsImg::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[ProductsParams]].
     *
     * @return ActiveQuery
     */
    public function getProductsParams(): ActiveQuery
    {
        return $this->hasMany(ProductsParams::class, ['product_id' => 'id']);
    }

    public function fields($method = null)
    {
        self::$method = !empty(self::$method) ? self::$method : $method;

        if (!empty(self::$method))
            return $this->{self::$method}();

        return array_merge(
            parent::fields(),
            [
                'catalog_name' => function($model) {
                    return Catalogs::findOne($model->catalog_id)->name;
                },
                'group_name' => function($model) {
                    return Groups::findOne($model->group_id)->name;
                },
                'img' => function($model) {
                    return ProductsImg::find()->where(['product_id' => $model->id])->all();
                },
                'params' => function($model) {
                    $result = [];
                    $params = Params::find()->where(['catalog_id' => $model->catalog_id,
                        'group_id' => $model->group_id])->asArray()->all();

                    foreach ($params as $param) {
                        $productParams = ProductsParams::find()->where(['param_id' => $param['id'],
                            'product_id' => $model->id])->asArray()->one();

                        $result[] = array_merge(
                            $param,
                            [
                                'product_param_id' => $productParams['id'] ?? null,
                                'product_param_name' => $productParams['name'] ?? ''
                            ]
                        );
                    }

                    return $result;
                },
            ]
        );
    }

    function forSearch()
    {
        return [
            'id',
            'name'
        ];
    }
}
