<?php

namespace app\api\modules\v1\models\orders;

use app\api\modules\v1\base\BaseActiveRecord;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property string|null $buyer_name Имя покупателя
 * @property string|null $buyer_address Адрес покупателя
 * @property string|null $buyer_phone Телефонный номер покупателя
 * @property string|null $buyer_comment Комментарий к заказу от покупателя
 * @property int $status Статус заказа. 0 - не подтверждено, 1 - подтверждено, 2 - завершено, 3 - отменено, 9 - удалено
 * @property string|null $other_info Поля для заметок продовца
 * @property string|null $order_info Сам заказ будет помещён сюда в сериалилизованном виде
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Orders extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['buyer_comment', 'other_info'/*, 'order_info'*/], 'string'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['buyer_name'], 'string', 'max' => 255],
            [['buyer_address'], 'string', 'max' => 500],
            [['buyer_phone'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'buyer_name' => 'Buyer Name',
            'buyer_address' => 'Buyer Address',
            'buyer_phone' => 'Buyer Phone',
            'buyer_comment' => 'Buyer Comment',
            'status' => 'Status',
            'other_info' => 'Other Info',
            'order_info' => 'Order Info',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_FIND, [$this, 'getUnserializeArray'], 'order_info');
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setSerializeArray'], 'order_info');
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setSerializeArray'], 'order_info');
    }

    public function fields()
    {
        return array_merge(
            parent::fields(),
            [
                'created_at',
                'updated_at'
            ]
        );
    }
}
