<?php

namespace app\api\modules\v1\controllers\auth;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\users\AccessToken;
use Throwable;
use Yii;

class VerificationController extends BaseApiController
{
    public $modelClass = AccessToken::class;

    /**
     * @throws Throwable
     */
    public function actionIndex()
    {
        $get = Yii::$app->request->get();

        $accessToken = AccessToken::findOne(['token' => $get['token']]);

        if (empty($accessToken)) {
            return self::createResponse(401, 'Не авторизован');
        }

        return true;
    }
}
