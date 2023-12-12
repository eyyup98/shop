<?php

namespace app\api\modules\v1\controllers\groups;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use app\api\modules\v1\models\params\ParamsTitle;
use Throwable;
use Yii;
use yii\db\IntegrityException;
use yii\db\StaleObjectException;

class GroupsController extends BaseApiController
{
    public $modelClass = Groups::class;

//    function actionIndex($id = null)
//    {
//        if (!empty($id))
//            return Groups::findOne($id);
//        else
//            return Groups::find()->all();
//    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    function actionCreate()
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);
        $params = $rawBody['params'];
        $delete = $rawBody['delete'];

        $catalog = Catalogs::findOne($params['catalog_id']);

        if (empty($catalog))
            return self::createResponse(400, 'Каталог не найден');

        foreach ($params['groups'] as $group) {
            if (!empty($group['id']))
                $groupBD = Groups::findOne($group['id']);
            else
                $groupBD = new Groups();

            $groupBD->catalog_id = $catalog->id;

            if (empty($group['name']))
                return self::createResponse(400, 'Название группы не должено быть пустым');

            $groupBD->name = $group['name'];

            if (!$groupBD->save())
                return self::createResponse(400, json_encode($groupBD->errors));
        }

        if (!empty($delete)) {
            foreach ($delete as $item) {
                try {
                    if (!Groups::findOne($item)->delete())
                        return self::createResponse(400, json_encode($delete->errors));
                } catch (IntegrityException $e) {
                    if ($e->getCode() == 23000) {
                        return self::createResponse(400, 'У этой группы есть параметры или продукты. Сперва удалите их');
                    }
                }
            }
        }

        return self::createResponse(204);
    }
}
