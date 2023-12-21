<?php

namespace app\api\modules\v1\controllers\client;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\catalogs\Catalogs;
use app\api\modules\v1\models\groups\Groups;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\db\IntegrityException;

class ClientCatalogsController extends BaseApiController
{
    public $modelClass = Catalogs::class;
    public bool $needCheckToken = false;

    function actionIndex($id = null)
    {
        return Catalogs::find('forClients')->where(['active' => true])->all();
    }
}
