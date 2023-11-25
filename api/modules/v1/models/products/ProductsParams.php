<?php

namespace app\api\modules\v1\models\products;

use app\api\modules\v1\base\BaseActiveRecord;
use app\api\modules\v1\models\params\Params;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "products_params".
 *
 * @property int $id
 * @property int $param_id
 * @property int $product_id
 * @property string|null $name
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Params $param
 * @property Products $product
 */
class ProductsParams extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['param_id', 'product_id'], 'required'],
            [['param_id', 'product_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['param_id'], 'exist', 'skipOnError' => true, 'targetClass' => Params::class, 'targetAttribute' => ['param_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'param_id' => 'Param ID',
            'product_id' => 'Product ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Param]].
     *
     * @return ActiveQuery
     */
    public function getParam(): ActiveQuery
    {
        return $this->hasOne(Params::class, ['id' => 'param_id']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Products::class, ['id' => 'product_id']);
    }
}
