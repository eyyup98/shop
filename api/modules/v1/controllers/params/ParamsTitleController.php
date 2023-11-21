<?php

namespace app\api\modules\v1\controllers\params;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use app\api\modules\v1\models\params\Params;
use app\api\modules\v1\models\params\ParamsTitle;
use PhpParser\Node\Param;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\db\IntegrityException;

class ParamsTitleController extends BaseApiController
{
    public $modelClass = ParamsTitle::class;

    function actionIndex($id = null)
    {
        if (!empty($id))
            return ParamsTitle::findOne($id);
        else
            return ParamsTitle::find()->all();
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
            $paramsTitle = ParamsTitle::findOne($id);

            if (empty($paramsTitle)) {
                return self::createResponse(400, 'Каталог не найден');
            }
        } else {
            $paramsTitle = new ParamsTitle();
        }

        $paramsTitle->name = $params['name'];
        $paramsTitle->catalog_id = $params['catalog_id'];

        if (empty($params['group_child_id'])) {
            if (!empty(Groups::findOne(['parent_id' => $params['group_parent_id']])))
                return self::createResponse(400, 'У этой группы есть подгруппа. Необходимо выбрать подгруппу');
            $paramsTitle->group_id = $params['group_parent_id'];
        } else
            $paramsTitle->group_id = $params['group_child_id'];

        if (!$paramsTitle->save()) {
            return self::createResponse(400, json_encode($paramsTitle->errors));
        }

        return ['title_id' => $paramsTitle->id];
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    function actionDelete($id = null)
    {
        if (empty($id))
            return self::createResponse(400, 'Необходимо указать объект');

        $delete = ParamsTitle::findOne($id);

        if (empty($delete))
            return self::createResponse(400, 'Объект не найден');

        try {
            if (!$delete->delete())
                return self::createResponse(400, json_encode($delete->errors));
        } catch (IntegrityException $e) {
            if ($e->getCode() == 23000) {
                return self::createResponse(400, 'У этого загаловка есть параметры. Сперва удалите их');
            }
        }

        return self::createResponse(204);
    }
}
