<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%params_title}}`.
 */
class m231120_191656_create_params_title_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%params_title}}', [
            'id' => $this->primaryKey(),
            'catalog_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'params_title_catalog_id',
            '{{%params_title}}',
            'catalog_id',
            '{{%catalogs}}',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'params_title_group_id',
            '{{%params_title}}',
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
        $this->dropTable('{{%params_title}}');
    }
}
