<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products_img}}`.
 */
class m231125_082518_create_products_img_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%products_img}}', [
            'id' => $this->primaryKey(),

            'product_id' => $this->integer()->notNull(),
            'src' => $this->string(),
            'name' => $this->string(),
            'main_img' => $this->boolean()->defaultValue(false),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'products_img_product_id',
            '{{%products_img}}',
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
        $this->dropTable('{{%products_img}}');
    }
}
