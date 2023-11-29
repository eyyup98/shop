<?php

namespace app\api\modules\v1\controllers\products;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\products\Products;
use app\api\modules\v1\models\products\ProductsImg;
use Exception;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class ProductsImgController extends BaseApiController
{
    public $modelClass = ProductsImg::class;

    function actionIndex($id = null)
    {
        if (!empty($id))
            return ProductsImg::findOne($id);
        else {
            $get = Yii::$app->request->get();

            return ProductsImg::find()->where(['product_id' => $get['product_id']])->all();
        }
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    function actionCreate($id = null)
    {
        if (empty($id))
            return self::createResponse(400, 'Необходимо указать id продукта');

        $savePath = [];
        $path = $_SERVER['DOCUMENT_ROOT'] . "/../storage" . ($savePath['root'] = "/images/products/" . date("dmY") . '/');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if (!empty($_FILES)) {
            foreach ($_FILES as $FILE) {
                $format = explode('.', $FILE['name']);
                $name = uniqid() . '.' . end($format);
                move_uploaded_file($FILE['tmp_name'], $path . $name);
                $savePath['src'][] = $name;
            }

            foreach ($savePath['src'] as $item) {
                $productImg = new ProductsImg();
                $productImg->product_id = $id;
                $productImg->src = $savePath['root'] . $item;
                $productImg->name = $item;
                if (!$productImg->save())
                    return self::createResponse(400, json_encode($productImg->errors));
            }
        }

        $get = Yii::$app->request->get();

        if (!empty($get['delete'])) {
            foreach ($get['delete'] as $item) {
                $deleteImg = ProductsImg::findOne($item);
                unlink($_SERVER['DOCUMENT_ROOT'] . "/../storage" . $deleteImg->src);
                $deleteImg->delete();
            }
        }

        return self::createResponse(204);
    }

//    /**
//     * @throws StaleObjectException
//     * @throws Throwable
//     */
//    function actionDelete($id = null)
//    {
//        $get = Yii::$app->request->get();
//        return $get;
//
//        if (empty($id))
//            return self::createResponse(400, 'Необходимо указать объект');
//
//        $delete = Products::findOne($id);
//
//        if (empty($delete))
//            return self::createResponse(400, 'Объект не найден');
//
//        try {
//            if (!$delete->delete())
//                return self::createResponse(400, json_encode($delete->errors));
//        } catch (Exception $e) {
//            return self::createResponse(400, $e->getMessage());
//        }
//
//        return self::createResponse(204);
//    }
}
