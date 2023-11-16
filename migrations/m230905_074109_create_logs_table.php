<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%logs}}`.
 */
class m230905_074109_create_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%logs}}', [
            'id' => $this->bigPrimaryKey()->comment('Идентификатор записи'),
            'log_type' => $this->integer()->comment('Тип логирования из таблицы log_types'),
            'table_name' => $this->string()->comment('Название таблицы операции'),
            'event_type' => $this->integer()->comment('Идентификатор события из таблицы sql_events'),
            'object_info' => $this->string()->comment('Информация идентификатора объекта с которым произведены действия'),
            'text' => $this->text()->comment('Текст лога'),
            'created_at' => $this->dateTime()->comment('Дата создания записи'),
            'updated_at' => $this->dateTime()->comment('Дата обновления записи'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%logs}}');
    }
}
