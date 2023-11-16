<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%catalogs}}`.
 */
class m231116_162615_create_catalogs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%catalogs}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%catalogs}}');
    }
}
