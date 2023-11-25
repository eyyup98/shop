<?php

namespace app\api\modules\v1\controllers\products;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\products\Products;
use app\api\modules\v1\models\products\ProductsImg;
use app\api\modules\v1\models\products\ProductsParams;
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
        else {
            $products = Products::find()->select(['id', 'name', 'price', 'discount'])->asArray()->all();

            foreach ($products as &$product) {
                $product['img'] = ProductsImg::findOne(['product_id' => $product['id']])->img_src ?? null;
            }

            return $products;
        }
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    function actionCreate($id = null)
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);
        $params = $rawBody['params'];

        if (!empty($id)) {
            $products= Products::findOne($id);

            if (empty($products)) {
                return self::createResponse(400, 'Объект не найден');
            }
        } else {
            $products = new Products();
        }

        $products->catalog_id = $params['catalog_id'];
        $products->group_id = !empty($params['subgroup_id']) ? $params['subgroup_id'] : $params['group_id'];
        $products->name = $params['name'];
        $products->price = $params['price'];
        $products->discount = $params['discount'];
        $products->active = $params['active'];

        if (!$products->save()) {
            return self::createResponse(400, json_encode($products->errors));
        }

        foreach ($params['params'] as $param) {
            $productParam = ProductsParams::findOne(['param_id' => $param['param_id'], 'product_id' => $products->id]);

            if (empty($productParam)) {
                $productParam = new ProductsParams();
                $productParam->param_id = $param['param_id'];
                $productParam->product_id = $products->id;
            }

            $productParam->name = $param['name'];
            if (!$productParam->save())
                return self::createResponse(400, json_encode($products->errors));
        }

        return ['product_id' => $products->id];
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
