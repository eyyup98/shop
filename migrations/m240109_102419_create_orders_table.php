<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orders}}`.
 */
class m240109_102419_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%orders}}', [
            'id' => $this->primaryKey(),

            'buyer_name' => $this->string()->comment('Имя покупателя'),
            'buyer_address' => $this->string(500)->comment('Адрес покупателя'),
            'buyer_phone' => $this->string(15)->comment('Телефонный номер покупателя'),
            'buyer_comment' => $this->text()->comment('Комментарий к заказу от покупателя'),
            'status' => $this->integer()->notNull()->defaultValue(0)->comment('Статус заказа. 0 - не подтверждено, 1 - подтверждено, 2 - завершено, 3 - отменено, 9 - удалено'),
            'other_info' => $this->text()->comment('Поля для заметок продовца'),
            'order_info' => $this->text()->comment('Сам заказ будет помещён сюда в сериалилизованном виде'),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%orders}}');
    }
}
