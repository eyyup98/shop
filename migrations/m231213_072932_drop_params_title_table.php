<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%params_title}}`.
 */
class m231213_072932_drop_params_title_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('params_title_catalog_id', '{{%params_title}}');
        $this->dropForeignKey('params_title_group_id', '{{%params_title}}');
        $this->dropForeignKey('params_title_id', '{{%params}}');

        $this->dropTable('{{%params_title}}');

        $this->dropColumn('{{%params}}', 'title_id');

        $this->addColumn('{{%params}}', 'catalog_id', $this->integer()->notNull()->after('id'));
        $this->addColumn('{{%params}}', 'group_id', $this->integer()->notNull()->after('catalog_id'));

        $this->addForeignKey(
            'params_catalog_id',
            '{{%params}}',
            'catalog_id',
            '{{%catalogs}}',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'params_group_id',
            '{{%params}}',
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
        $this->createTable('{{%params_title}}', [
            'id' => $this->primaryKey(),
        ]);
    }
}
