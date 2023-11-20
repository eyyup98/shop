<?php

namespace app\api\modules\v1\controllers\catalogs;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\catalogs\Catalogs;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class CatalogsController extends BaseApiController
{
    public $modelClass = Catalogs::class;

    function actionIndex($id = null)
    {
        return Catalogs::find()->all();
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
            $catalogs = Catalogs::findOne($id);

            if (empty($catalogs)) {
                return self::createResponse(400, 'Каталог не найден');
            }
        } else {
            $catalogs = new Catalogs();
        }

        $catalogs->name = $params['name'];
        $catalogs->active = $params['active'];

        if (!$catalogs->save()) {
            return self::createResponse(400, json_encode($catalogs->errors));
        } else {
            return self::createResponse(204);
        }
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    function actionDelete($id = null)
    {
        if (empty($id))
            return self::createResponse(400, 'Необходимо указать объект');

        $delete = Catalogs::findOne($id);

        if (empty($delete))
            return self::createResponse(400, 'Объект не найден');

        if (!$delete->delete())
            return self::createResponse(400, json_encode($delete->errors));

        return self::createResponse(204);
    }
}
