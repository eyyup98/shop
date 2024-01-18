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

    function actionIndex()
    {
        $get = Yii::$app->request->get();

        $count = Orders::find()->count();

        $orders = Orders::find()->orderBy(['id' => SORT_DESC])
            ->limit(10)
            ->offset(($get['pagination'] - 1) * 5)
            ->asArray()->all();

        foreach ($orders as &$order) {
            $order['created_at'] = date("d.m.Y H:i:s", strtotime($order['created_at']));
            $order['updated_at'] = date("d.m.Y H:i:s", strtotime($order['updated_at']));

            $order['status_name'] = match ($order['status']) {
                0 => 'Не обработан',
                1 => 'Принят',
                2 => 'Завершён',
                3 => 'Отменён'
            };

            $order['order_info'] = unserialize($order['order_info']);
        }

        return ['data' => $orders, 'count' => $count];
    }

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

        $order->other_info = $rawBody['params']['other_info'];
        $order->status = $rawBody['params']['status'];

        if (!$order->save())
            return self::createResponse(400, json_encode($order->errors));

        return self::createResponse(204);
    }
}
