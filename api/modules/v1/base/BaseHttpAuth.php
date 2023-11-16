<?php


namespace app\api\modules\v1\base;


use yii\filters\auth\AuthMethod;
use yii\web\HttpException;

class BaseHttpAuth extends AuthMethod
{
    /**
     * @var string the HTTP authentication realm
     */
    public string $realm = 'api';

    /**
     * @var callable|null a PHP callable that will authenticate the user with the HTTP basic auth information.
     * The callable receives a username and a password as its parameters. It should return an identity object
     * that matches the username and password. Null should be returned if there is no such identity.
     * The callable will be called only if current user is not authenticated.
     *
     * The following code is a typical implementation of this callable:
     *
     * ```php
     * function ($username, $password) {
     *     return \app\models\User::findOne([
     *         'username' => $username,
     *         'password' => $password,
     *     ]);
     * }
     * ```
     *
     * If this property is not set, the username information will be considered as an access token
     * while the password information will be ignored. The [[\yii\web\User::loginByAccessToken()]]
     * method will be called to authenticate and login the user.
     */
    public $auth;

    public function authenticate($user, $request, $response)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function challenge($response)
    {
        $response->getHeaders()->set('WWW-Authenticate', "Basic realm=\"$this->realm\"");
    }

    /**
     * @throws HttpException
     */
    public function beforeAction($action)
    {
        throw new HttpException(401, '401 Unauthorized', 401);
    }
}