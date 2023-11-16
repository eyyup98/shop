<?php


namespace app\api\modules\v1\helpers;


use app\api\modules\v1\models\Logs;
use Exception;
use Yii;

class ExceptionHelper
{
    public static function checkException()
    {
        $exception = Yii::$app->errorHandler->exception;

        if (!empty($exception)) {
            $log = new Logs();
            $log->log_type = 0;
            $log->text = 'PHP Error. File \'' . $exception->getFile() . '\' line ' .
                $exception->getLine() . ' with message ' . $exception->getMessage();
            $log->save();
        }
    }

    public static function exceptionLog(Exception $exception, $objectInfo = null, $objectName = null)
    {
        $log = new Logs();
        $log->log_type = 0;
        $log->text = $exception->getMessage();
        $log->object_info = $objectInfo;
        $log->table_name = $objectName;
        $log->save();
    }
}
