<?php

namespace app\api\modules\v1\controllers\products;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use app\api\modules\v1\models\params\Params;
use app\api\modules\v1\models\params\ParamsTitle;
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
            $products = Products::findOne($id)->toArray();

            if (empty($products))
                return self::createResponse(400, 'Объект не найден');

            $products['img'] = [];

            $subgroup = Groups::findOne($products['group_id']) ?? null;
            $products['subgroup_id'] = null;

            if (!empty($subgroup->parent_id)) {
                $products['group_id'] = $subgroup->parent_id;
                $products['subgroup_id'] = $subgroup->id;
            }

            $products['catalog_name'] = Catalogs::findOne($products['catalog_id'])->name;
            $products['group_name'] = Groups::findOne($products['group_id'])->name;
            $products['subgroup_name'] = Groups::findOne($products['subgroup_id'])->name ?? null;
//print_r('catalog_id' . $products['catalog_id']);
//            die();

            $title = ParamsTitle::find()->select(['id', 'name'])
                ->where(['catalog_id' => $products['catalog_id']])
            ->asArray()->all();
//            print_r($title);
//            die();

            foreach ($title as $item) {
                $return = [];
                $return['name'] = $item['name'];
                $return['catalog_id'] = $products['catalog_id'];
                $return['group_id'] = $products['group_id'];
//                print_r($return);
                $params = Params::find()->select(['id', 'name'])
                    ->where(['title_id' => $item['id']])->asArray()->all();
//                print_r($params);

                foreach ($params as $param) {
                    $return['params'][] = array_merge(
                        $param,
                        [
                            'value' => ProductsParams::findOne(['param_id' => $param['id'],
                                'product_id' => $products['id']])->name ?? ''
                        ]
                    );
                }
//                print_r($return['params'] ?? '');

//                print_r($return);

                if (!empty($return['params']))
                    $products['params'][] = $return;
            }
//            print_r($products['params']);

        } else {
            $products = Products::find()->select(['id', 'name', 'price', 'discount'])->asArray()->all();

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
        $params = $rawBody['params'];

        if (!empty($id)) {
            $products= Products::findOne($id);

            if (empty($products)) {
                return self::createResponse(400, 'Объект не найден');
            }
        } else {
            $products = new Products();
            $products->catalog_id = $params['catalog_id'];
            $products->group_id = !empty($params['subgroup_id']) ? $params['subgroup_id'] : $params['group_id'];
        }

        $products->name = $params['name'];
        $products->price = $params['price'];
        $products->discount = $params['discount'];
        $products->active = $params['active'];

        if (!$products->save()) {
            return self::createResponse(400, json_encode($products->errors));
        }

        foreach ($params['params'] as $param) {
            $title_id = Params::findOne(['id' => $param['param_id']])->title_id;
            $paramTitle = ParamsTitle::findOne(['id' => $title_id]);

            if ($products->catalog_id != $paramTitle->catalog_id)
                continue;

            $productParam = ProductsParams::findOne(['param_id' => $param['param_id'], 'product_id' => $products->id]);

            if (empty($productParam)) {
                $productParam = new ProductsParams();
                $productParam->param_id = $param['param_id'];
                $productParam->product_id = $products->id;
            }

            $productParam->name = $param['name'];

            if (empty($param['name']) && !empty($productParam->id)) {
                $productParam->delete();
            } elseif (!empty($param['name']))
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
