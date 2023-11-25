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

//    function actionIndex($id = null)
//    {
//        if (!empty($id))
//            return Products::findOne($id);
//        else
//            return Products::find()->all();
//    }

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

        foreach ($_FILES as $FILE) {
            $format = explode('.', $FILE['name']);
            $name = uniqid() . '.' . end($format);
            $path = $path . $name;
            $savePath['src'][] = $name;
            move_uploaded_file($FILE['tmp_name'], $path);
        }

        foreach ($savePath['src'] as $item) {
            $productImg = new ProductsImg();
            $productImg->product_id = $id;
            $productImg->img_src = $savePath['root'] . $item;
            if (!$productImg->save())
                return self::createResponse(400, json_encode($productImg->errors));
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
