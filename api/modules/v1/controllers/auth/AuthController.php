<?php

namespace app\api\modules\v1\controllers\auth;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\users\AccessToken;
use app\api\modules\v1\models\users\Users;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class AuthController extends BaseApiController
{
    public $modelClass = Users::class;

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionAuthentication()
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);

        $user = Users::findOne(['login' => $rawBody['login'], 'password' => $rawBody['password']]);

        if (empty($user)) {
            return self::createResponse(401, 'Не верный логин или пароль');
        }

        $accessToken = AccessToken::findOne(['user_id' => $user->id]);

        if (empty($accessToken)) {
            $accessToken = new AccessToken();
            $accessToken->user_id = $user->id;
        }

        $accessToken->token = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );

        if (!$accessToken->save()) {
            return self::createResponse(401, json_encode($accessToken->errors));
        }

        return ['token' => $accessToken->token];
    }
}
