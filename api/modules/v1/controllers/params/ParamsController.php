<?php

namespace app\api\modules\v1\controllers\params;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\params\Params;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class ParamsController extends BaseApiController
{
    public $modelClass = Params::class;

    function actionIndex($id = null)
    {
        if (!empty($id))
            return Params::findOne($id);
        else
            return Params::find()->all();
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    function actionCreate()
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);
        $params = $rawBody['params'];
        $delete = $rawBody['delete'];

        foreach ($params as $param) {
            if (!empty($param['id'])) {
                $paramsDb= Params::findOne($param['id']);

                if (empty($paramsDb)) {
                    return self::createResponse(400, 'Параметр не найден');
                }
            } else {
                $paramsDb = new Params();
                $paramsDb->catalog_id = $rawBody['catalog_id'];
                $paramsDb->group_id = $rawBody['group_id'];
            }

            $paramsDb->name = $param['name'];

            if (
                !empty(Params::findOne(['catalog_id' => $paramsDb->catalog_id,
                    'group_id' => $paramsDb->group_id,'name' => $paramsDb->name]))
            ) {
                return self::createResponse(400, 'Такой параметр уже существует');
            }

            if (!$paramsDb->save()) {
                return self::createResponse(400, json_encode($paramsDb->errors));
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $item) {
                if (!Params::findOne($item)->delete()) {
                    return self::createResponse(400, 'Ошибка при удалении');
                }
            }
        }

        return self::createResponse(204);
    }
}
