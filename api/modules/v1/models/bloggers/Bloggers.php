<?php

namespace app\api\modules\v1\models\bloggers;

use app\api\modules\v1\base\BaseActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "bloggers".
 *
 * @property int $blogger_id Идентификатор блогера
 * @property int $user_id Идентификатор пользователя
 * @property int $user_shop_id Идентификатор юр. лица
 * @property string $blogger_name Имя блогера
 * @property string|null $created_at Дата создания записи
 * @property string|null $updated_at Дата обновления записи
 *
 * @property BloggersAdv[] $bloggersAdv
 */
class Bloggers extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bloggers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'user_shop_id', 'blogger_name'], 'required'],
            [['user_id', 'user_shop_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['blogger_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'blogger_id' => 'Blogger ID',
            'user_id' => 'User ID',
            'user_shop_id' => 'User Shop ID',
            'blogger_name' => 'Blogger Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[BloggersAdv]].
     *
     * @return ActiveQuery
     */
    public function getBloggersAdv(): ActiveQuery
    {
        return $this->hasMany(BloggersAdv::class, ['blogger_id' => 'blogger_id']);
    }
}
