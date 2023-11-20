<?php

namespace app\api\modules\v1\controllers\groups;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class GroupsController extends BaseApiController
{
    public $modelClass = Groups::class;

    function actionParents($id = null)
    {
        if (!empty($id))
            return Groups::find()->where(['id' => $id, 'parent_id' => null])->one();
        else
            return Groups::find()->with('catalog')->where(['parent_id' => null])->all();
    }

    function actionChilds($id = null)
    {
        if (!empty($id))
            return Groups::find()->where(['id' => $id])->andWhere(['not', ['parent_id' => null]])->one();
        else {
            $groups = Groups::find()->where(['not', ['parent_id' => null]])->all();
            $newGroups = [];

            foreach ($groups as $group) {
                $newGroups[$group['parent_id']][] = $group;
            }

            return $newGroups;
        }
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    function actionCreate($id = null)
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);
        $params = $rawBody['params'];

        $catalog_id = Catalogs::findOne($params['catalog_id']);

        if (empty($catalog_id))
            return self::createResponse(400, 'Каталог не найден');

        if (!empty($id)) {
            $groups = Groups::findOne($id);

            if (empty($groups)) {
                return self::createResponse(400, 'Группа не найдена');
            }
        } else {
            if (
                Groups::findOne(['catalog_id' => $catalog_id->id,
                    'parent_id' => $params['parent_id'], 'name' => $params['name']])
            )
                return self::createResponse(400, 'Группа уже существует');

            $groups = new Groups();
        }

        $groups->catalog_id = $catalog_id->id;
        $groups->parent_id = $params['parent_id'];
        $groups->name = $params['name'];
        $groups->active = $params['active'];

        if (!$groups->save()) {
            return self::createResponse(400, json_encode($groups->errors));
        }

//        if (!empty($id)) {
        return self::createResponse(204);
//        } else {
//            return $groups;
//        }
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    function actionDelete($id = null)
    {
        if (empty($id))
            return self::createResponse(400, 'Необходимо указать объект');

        $delete = Groups::findOne($id);

        $child = Groups::find()->where(['parent_id' => $id])->asArray()->all();

        if (count($child) > 0) {
            return self::createResponse(400, 'У этой группы есть подгруппы. Сперва удалите их');
        }

        if (empty($delete))
            return self::createResponse(400, 'Объект не найден');

        if (!$delete->delete())
            return self::createResponse(400, json_encode($delete->errors));

        return self::createResponse(204);
    }
}
