<?php

namespace app\api\modules\v1\models\params;

use app\api\modules\v1\base\BaseActiveRecord;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "params".
 *
 * @property int $id
 * @property int $title_id
 * @property string|null $name
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ParamsTitle $title
 */
class Params extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title_id'], 'required'],
            [['title_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['title_id'], 'exist', 'skipOnError' => true, 'targetClass' => ParamsTitle::class, 'targetAttribute' => ['title_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title_id' => 'Title ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Title]].
     *
     * @return ActiveQuery
     */
    public function getTitle(): ActiveQuery
    {
        return $this->hasOne(ParamsTitle::class, ['id' => 'title_id']);
    }
}
