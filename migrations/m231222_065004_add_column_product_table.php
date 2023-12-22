<?php

use yii\db\Migration;

/**
 * Class m231222_065004_add_column_product_table
 */
class m231222_065004_add_column_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%products}}', 'description', $this->text()->after('discount')->comment('Описание товара'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%products}}', 'description');
    }
}
