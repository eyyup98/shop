<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%groups}}`.
 */
class m231211_210528_drop_column_groups_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%groups}}', 'parent_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%groups}}', 'parent_id', $this->integer()->after('catalog_id'));
    }
}
