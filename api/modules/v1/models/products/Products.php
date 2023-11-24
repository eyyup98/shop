<?php

namespace app\api\modules\v1\models\products;

use app\api\modules\v1\base\BaseActiveRecord;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property int $catalog_id
 * @property int $group_id
 * @property string $name
 * @property int $code
 * @property float|null $price
 * @property float|null $discount
 * @property int|null $active
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Catalogs $catalog
 * @property Groups $group
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
            [['catalog_id', 'group_id', 'name', 'code'], 'required'],
            [['catalog_id', 'group_id', 'code'/*, 'active'*/], 'integer'],
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
            'code' => 'Code',
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
}
