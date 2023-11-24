<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products}}`.
 */
class m231124_124334_create_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),

            'catalog_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'code' => $this->integer()->notNull()->check('code > 1000000'),
            'price' => $this->decimal(10, 2),
            'discount' => $this->decimal(10, 2),
            'active' => $this->boolean()->defaultValue(true),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'products_catalog_id',
            '{{%products}}',
            'catalog_id',
            '{{%catalogs}}',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'products_groups_id',
            '{{%products}}',
            'group_id',
            '{{%groups}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%products}}');
    }
}
