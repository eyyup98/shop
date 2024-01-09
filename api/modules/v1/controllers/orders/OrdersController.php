<?php

namespace app\api\modules\v1\controllers\orders;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\orders\Orders;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class OrdersController extends BaseApiController
{
    public $modelClass = Orders::class;

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    function actionCreate($id = null)
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);

        $order = Orders::findOne($id);

        if (empty($order))
            return self::createResponse(400, 'Объект не найден');

        $order->other_info = $rawBody['params']['seller_comment'];
        $order->status = $rawBody['params']['status'];

        if (!$order->save())
            return self::createResponse(400, json_encode($order->errors));

        return self::createResponse(204);
    }
}
