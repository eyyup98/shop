<?php

namespace app\api\modules\v1\controllers\params;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\products\Products;
use Exception;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class ProductsController extends BaseApiController
{
    public $modelClass = Products::class;

    function actionIndex($id = null)
    {
        if (!empty($id))
            return Products::findOne($id);
        else
            return Products::find()->all();
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    function actionCreate($id = null)
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);
        $params = $rawBody['params'];

        foreach ($params as $param) {
            if (!empty($id)) {
                $products= Products::findOne($id);

                if (empty($products)) {
                    return self::createResponse(400, 'Параметр не найден');
                }
            } else {
                $products = new Products();
            }
            $products->catalog_id = $param['catalog_id'];
            $products->group_id = $param['group_id'];
            $products->name = $param['name'];
            $products->price = $param['price'];
            $products->discount = $param['discount'];
            $products->active = $param['active'];

            if (!$products->save()) {
                return self::createResponse(400, json_encode($products->errors));
            }
        }

        return self::createResponse(204);
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    function actionDelete($id = null)
    {
        if (empty($id))
            return self::createResponse(400, 'Необходимо указать объект');

        $delete = Products::findOne($id);

        if (empty($delete))
            return self::createResponse(400, 'Объект не найден');

        try {
            if (!$delete->delete())
                return self::createResponse(400, json_encode($delete->errors));
        } catch (Exception $e) {
            return self::createResponse(400, $e->getMessage());
        }

        return self::createResponse(204);
    }
}
