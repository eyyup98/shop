<?php

namespace app\api\modules\v1\controllers\client;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\products\Products;
use app\api\modules\v1\models\products\ProductsImg;
use Yii;

class ClientProductsController extends BaseApiController
{
    public $modelClass = Products::class;
    public bool $needCheckToken = false;

    function actionIndex($id = null)
    {
        if (!empty($id)) {
            $products = Products::find()->where(['id' => $id])->where(['active' => 1])->asArray()->one();

            if (empty($products))
                return self::createResponse(400, 'Объект не найден');

            $products['img'] = ProductsImg::find()->where(['product_id' => $id])->all();
        } else {
            $get = Yii::$app->request->get();

            $products = Products::find()->select(['id', 'name', 'price', 'discount'])->where(['active' => 1]);

            if (!empty($get['catalog_id']))
                $products->where(['catalog_id' => $get['catalog_id']]);

            if (!empty($get['group_id']))
                $products->where(['group_id' => $get['group_id']]);

            if (!empty($get['search'])) {
                $products->where("name like '%{$get['search']}%'");
            }

            $products = $products->asArray()->all();

            foreach ($products as &$product) {
                $product['img'] = ProductsImg::findOne(['product_id' => $product['id']])->src ?? null;
            }
        }
        return $products;
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
}