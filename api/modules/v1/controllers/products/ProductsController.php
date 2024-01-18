<?php

namespace app\api\modules\v1\controllers\products;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\params\Params;
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
        if (!empty($id)) {
            $products = Products::find()->where(['id' => $id])->one();

            if (empty($products))
                return self::createResponse(400, 'Объект не найден');
        } else {
            $get = Yii::$app->request->get();

            $products = Products::find()->select(['id', 'name', 'price', 'discount']);

            if (!empty($get['catalog_id']))
                $products->where(['catalog_id' => $get['catalog_id']]);

            if (!empty($get['group_id']))
                $products->where(['group_id' => $get['group_id']]);

            if (!empty($get['product_id'])) {
                $products->where(['id' => $get['product_id']]);
            } elseif (!empty($get['search'])) {
                $products->where("name like '%{$get['search']}%'");
            }

            $products = $products->asArray()->all();

            foreach ($products as &$product) {
                $product['img'] = ProductsImg::findOne(['product_id' => $product['id']])->src ?? null;
            }
        }
        return $products;
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    function actionCreate($id = null)
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);
        $product = $rawBody['product'];

        if (!empty($id)) {
            $productBD = Products::findOne($id);

            if (empty($productBD)) {
                return self::createResponse(400, 'Объект не найден');
            }
        } else {
            $productBD = new Products();
            $productBD->catalog_id = $product['catalog_id'];
            $productBD->group_id = $product['group_id'];
        }

        $productBD->name = $product['name'];
        $productBD->price = $product['price'];
        $productBD->discount = $product['discount'];
        $productBD->active = $product['active'];
        $productBD->description = $product['description'];

        if (!$productBD->save()) {
            return self::createResponse(400, json_encode($productBD->errors));
        }

        foreach ($product['params'] as $param) {
            $paramBD = Params::findOne(['id' => $param['id']]);

            if ($productBD->catalog_id != $paramBD->catalog_id || $productBD->group_id != $paramBD->group_id)
                continue;

            $productParam = ProductsParams::findOne(['param_id' => $param['id'], 'product_id' => $productBD->id]);

            if (empty($productParam)) {
                $productParam = new ProductsParams();
                $productParam->param_id = $param['id'];
                $productParam->product_id = $productBD->id;
            }

            $productParam->name = $param['product_param_name'];

            if (empty($param['product_param_name']) && !empty($productParam->id)) {
                $productParam->delete();
            } elseif (!empty($param['product_param_name']))
                if (!$productParam->save())
                    return self::createResponse(400, json_encode($productBD->errors));
        }

        return ['product_id' => $productBD->id];
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

    function actionSearch()
    {
        $get = Yii::$app->request->get();

        $list = Products::find('forSearch')->select(['id', 'name']);

        if (!empty($get['catalog_id']))
            $list->andWhere(['catalog_id' => $get['catalog_id']]);

        if (!empty($get['group_id']))
            $list->andWhere(['group_id' => $get['group_id']]);

        $list->andWhere("name like '%{$get['search']}%'");

        return $list->all() ?? [];
    }

    function actionForCart()
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);
        $params = $rawBody['params'];

        if (!empty($params['productsList'])) {
            $ids = implode(',', $params['productsList']);

            $products = Products::find()->select(['id', 'name', 'price', 'discount'])
                ->where("id in ($ids)")->asArray()->all();

            foreach ($products as &$product) {
                $product['img'] = ProductsImg::findOne(['product_id' => $product['id']])->src ?? null;
            }
        }

        return $products ?? [];
    }
}
