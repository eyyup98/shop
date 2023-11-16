<?php


namespace app\api\modules\v1\base;


use app\api\modules\v1\models\Logs;
use Throwable;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\StaleObjectException;

class BaseActiveRecord extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function update($runValidation = true, $attributeNames = null)
    {
        $oldValues = $this->getOldAttributes();

        parent::update($runValidation, $attributeNames);

        $newValues = $this->getAttributes();
        $flagChange = false;
        $log = new Logs();

        $log->log_type = 1;
        $log->table_name = $this->formName();
        $log->event_type = 3;
        $log->object_info .= '';
        $log->text .= '';

        foreach ($this->getPrimaryKey(true) as $key => $value) {
            $log->object_info .= '[' . $key . '] => [' . $value . ']; ';
        }

        foreach ($oldValues as $key => $value) {
            if ($value != $newValues[$key] && $key != 'updated_at') {
                $flagChange = true;
                $log->text .= $key . ' [\'' . $value . '\' => \'' . $newValues[$key] . '\']; ';
            }
        }

        if ($flagChange) {
            $log->save();
        } else {
            unset($log);
        }
    }

    public function insert($runValidation = true, $attributes = null)
    {
        $return = parent::insert($runValidation, $attributes);

        if ($this->formName() != 'Logs') {
            $log = new Logs();

            $log->log_type = 1;
            $log->table_name = $this->formName();
            $log->event_type = 1;
            $log->object_info .= '';

            foreach ($this->getPrimaryKey(true) as $key => $value) {
                $log->object_info .= '[' . $key . '] => [' . $value . ']; ';
            }

            $log->save();
        }
        return $return;
    }

    public function getUnserializeArray($column)
    {
        $column = $column->data;

        $conf = $this->$column;
        $this->$column = null;
        if (!empty($conf)) {
            $this->$column = unserialize($conf);
        }
    }

    public function setSerializeArray($column)
    {
        $column = $column->data;

        $conf = $this->$column;
        $this->$column = null;
        if (!empty($conf) && is_array($conf)) {
            $this->$column = serialize($conf);
        }
    }

    function setSerializeDate($column)
    {
        $column = $column->data;

        $conf = $this->$column;
        $this->$column = null;
        if (!empty($conf)) {
            $this->$column = date("Y-m-d", strtotime($conf));
        }
    }

    function getUnserializeDate($column)
    {
        $column = $column->data;

        $conf = $this->$column;
        $this->$column = null;
        if (!empty($conf)) {
            $this->$column = date("d.m.Y", strtotime($conf));
        }
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['created_at']);
        unset($fields['updated_at']);

        return $fields;
    }

    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[isNewRecord]] is `true`, or [[update()]]
     * when [[isNewRecord]] is `false`.
     *
     * For example, to save a customer record:
     *
     * ```php
     * $customer = new Customer; // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ```
     *
     * @param bool $runValidation whether to perform validation (calling [[validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param null $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return bool whether the saving succeeded (i.e. no validation errors occurred).
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->getIsNewRecord()) {
            $return = $this->insert($runValidation, $attributeNames);
        } else {
            $return = $this->update($runValidation, $attributeNames) !== false;
        }

        if (!empty($this->errors)) {
            Logs::createErrorLog($this->errors, $this->primaryKey, $this->formName(), 0);
        }

        return $return;
    }
}
