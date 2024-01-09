<?php

namespace app\api\modules\v1\controllers\client;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\orders\Orders;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class ClientOrdersController extends BaseApiController
{
    public $modelClass = Orders::class;
    public bool $needCheckToken = false;

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    function actionCreate()
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);

        $user = $rawBody['params']['user'];

        $order = new Orders();

        $order->buyer_name = $user['name'];
        $order->buyer_address = $user['address'];
        $order->buyer_phone = '+993' . $user['phone'];
        $order->buyer_comment = $user['comment'];
        $order->order_info = $rawBody['params']['orders'];

        if (!$order->save())
            return self::createResponse(400, json_encode($order->errors));

        return self::createResponse(204);
    }
}
