<?php

namespace app\api\modules\v1\controllers\params;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use app\api\modules\v1\models\params\Params;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\db\IntegrityException;

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
    function actionCreate($id = null)
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);
        $params = $rawBody['params'];

        foreach ($params as $param) {
            if (!empty($param['id'])) {
                $paramsDb= Params::findOne($param['id']);

                if (empty($paramsDb)) {
                    return self::createResponse(400, 'Параметр не найден');
                }
            } else {
                $paramsDb = new Params();
                $paramsDb->title_id = $param['title_id'];
            }

            $paramsDb->name = $param['name'];

            if (!$paramsDb->save()) {
                return self::createResponse(400, json_encode($paramsDb->errors));
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

        $delete = Params::findOne($id);

        if (empty($delete))
            return self::createResponse(400, 'Объект не найден');

        try {
            if (!$delete->delete())
                return self::createResponse(400, json_encode($delete->errors));
        } catch (IntegrityException $e) {
            if ($e->getCode() == 23000) {
                return self::createResponse(400, 'У этого каталога есть группы. Сперва удалите их');
            }
        }

        return self::createResponse(204);
    }
}
