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
        else
            return Groups::find()->where(['not', ['parent_id' => null]])->all();
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    function actionCreate($id = null)
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);
        $params = $rawBody['params'];
        $delete = $rawBody['delete'];

        $catalog = Catalogs::findOne($params['catalog_id']);

        if (empty($catalog))
            return self::createResponse(400, 'Каталог не найден');

        if (!empty($id)) {
            $groups = Groups::findOne($id);

            if (empty($groups)) {
                return self::createResponse(400, 'Группа не найдена');
            }
        } else {
            $groups = new Groups();
        }

        $groups->catalog_id = $catalog->id;
        $groups->parent_id = $params['parent_id'];
        $groups->name = $params['name'];
        $groups->active = $params['active'];

        if ($catalog->active == false && $catalog->active != $groups->active)
            return self::createResponse(400, 'Сперва измените активность в каталоге');

        if (!$groups->save()) {
            return self::createResponse(400, json_encode($groups->errors));
        }

        foreach ($params['subgroups'] as $item) {
            if (!empty($item['id'])) {
                $subgroup = Groups::findOne($item['id']);
                if (empty($subgroup)) {
                    return self::createResponse(400, 'Подгруппа не найдена');
                }
            } else
                $subgroup = new Groups();

            $subgroup->catalog_id = $catalog->id;
            $subgroup->name = $item['name'];
            $subgroup->parent_id = $groups->id;
            $subgroup->active = $groups->active;

            if (!$subgroup->save()) {
                return self::createResponse(400, json_encode($subgroup->errors));
            }
        }

        Groups::updateAll(['active' => $groups->active], ['parent_id' => $groups->id]);

        if (!empty($delete)) {
            foreach ($delete as $item) {
                if (!Groups::findOne($item)->delete()) {
                    return self::createResponse(400, 'Ошибка при удалении');
                }
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
