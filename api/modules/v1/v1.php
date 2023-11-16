<?php

namespace app\api\modules\v1;

use yii\web\Response;
use Yii;
use yii\base\Module;
use yii\helpers\FileHelper;

/**
 * projects module definition class
 */
class v1 extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\api\modules\v1\controllers';

    /**
     * Метод пробегается по всем контроллерам директории '.../controllers/...' и
     * ищет необходимый из них ориентируясь на url
     */
    public function init()
    {
        parent::init();

        $pathInfo = explode("/", Yii::$app->request->pathInfo);

        $files = FileHelper::findFiles(__DIR__ . '/controllers');

        $needFolder = null;

        foreach ($files as $file) {
            $needFolder = $needFile = explode('\\', $file);

            $needFile = end($needFile);
            $needFile = str_replace('Controller.php', '', $needFile);
            $needFile = mb_strtolower($needFile);

            if ($needFile == str_replace('-', '', $pathInfo[1])) {
                end($needFolder);
                $needFolder = prev($needFolder);
                break;
            }

            $needFolder = null;
        }

        if (empty($needFolder)){
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $this->controllerNamespace .= '\\' . $needFolder;
    }
}
