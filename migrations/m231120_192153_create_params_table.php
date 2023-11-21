<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%params}}`.
 */
class m231120_192153_create_params_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%params}}', [
            'id' => $this->primaryKey(),
            'title_id' => $this->integer()->notNull(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'params_title_id',
            '{{%params}}',
            'title_id',
            '{{%params_title}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%params}}');
    }
}
