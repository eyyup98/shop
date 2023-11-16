<?php

namespace app\api\modules\v1\controllers\bloggers;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\bloggers\Bloggers;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class BloggersController extends BaseApiController
{
    public $modelClass = Bloggers::class;

    public function actionTest(){
        return 'hello test';
    }

    public function actionIndex($id = null)
    {
        if (empty($id))
            return Bloggers::find()->where(['user_shop_id' => $this->user_shop_id])->all();
        else
            return Bloggers::find()->where(['user_shop_id' => $this->user_shop_id, 'blogger_id' => $id])->all();
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionCreate($id = null)
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);

        if (empty($id)) {
            $blogger = new Bloggers();
            $blogger->user_id = $this->user_id;
            $blogger->user_shop_id = $this->user_shop_id;
        } else {
            $blogger = Bloggers::findOne(['user_shop_id' => $this->user_shop_id, 'blogger_id' => $id]);

            if (empty($blogger)) {
                return self::createResponse(400, 'Объект не найден');
            }
        }

        $blogger->blogger_name = $rawBody['params']['blogger_name'];

        if (!$blogger->save()) {
            return self::createResponse(400, json_encode($blogger->errors));
        }

        return self::createResponse(200, 'Объект сохранён');
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete($id = null)
    {
        $delete = Bloggers::findOne(['user_shop_id' => $this->user_shop_id, 'blogger_id' => $id]);

        if (empty($delete))
            return self::createResponse(400, 'Объект не найден');

        $delete->delete();
        return self::createResponse(200, 'Объект удалён');
    }
}
