<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products_params}}`.
 */
class m231125_170035_create_products_params_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%products_params}}', [
            'id' => $this->primaryKey(),

            'param_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'name' => $this->string(),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'products_params_param_id',
            '{{%products_params}}',
            'param_id',
            '{{%params}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'products_params_product_id',
            '{{%products_params}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%products_params}}');
    }
}
