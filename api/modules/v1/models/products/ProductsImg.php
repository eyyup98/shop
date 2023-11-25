<?php

namespace app\api\modules\v1\models\products;

use app\api\modules\v1\base\BaseActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "products_img".
 *
 * @property int $id
 * @property int $product_id
 * @property string|null $img_src
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Products $product
 */
class ProductsImg extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_img';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['img_src'], 'string', 'max' => 255],
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
            'product_id' => 'Product ID',
            'img_src' => 'Img Src',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
