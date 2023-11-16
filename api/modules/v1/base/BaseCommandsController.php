<?php


namespace app\api\modules\v1\base;


use app\api\modules\v1\helpers\ExceptionHelper;
use yii\console\Controller;

class BaseCommandsController extends Controller
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        date_default_timezone_set('Europe/Moscow');
    }

    public function __destruct()
    {
        ExceptionHelper::checkException();
    }
}