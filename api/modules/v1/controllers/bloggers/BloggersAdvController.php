<?php

namespace app\api\modules\v1\controllers\bloggers;

use app\api\modules\v1\base\BaseApiController;
use app\api\modules\v1\models\bloggers\Bloggers;
use app\api\modules\v1\models\bloggers\BloggersAdv;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class BloggersAdvController extends BaseApiController
{
    public $modelClass = BloggersAdv::class;

    public function actionIndex($id = null)
    {
        if (empty($id))
            return BloggersAdv::find()->where(['user_shop_id' => $this->user_shop_id])->all();
        else
            return BloggersAdv::find()->where(['user_shop_id' => $this->user_shop_id, 'id' => $id])->all();
    }


    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionCreate($id = null)
    {
        $rawBody = json_decode(Yii::$app->request->rawBody, true);

        if (empty($id)) {
            $blogger = new BloggersAdv();
            $blogger->user_id = $this->user_id;
            $blogger->user_shop_id = $this->user_shop_id;
//            $blogger->adv_start = date("Y-m-d H:i:s");
        } else {
            $blogger = BloggersAdv::findOne(['user_shop_id' => $this->user_shop_id, 'id' => $id]);

            if (empty($blogger)) {
                return self::createResponse(400, 'Объект не найден');
            }
        }

        $blogger->blogger_id = $rawBody['params']['blogger_id'] ?? $blogger->id;
        $blg = Bloggers::findOne(['blogger_id' => $blogger->blogger_id]);
        if (!empty($blg)) {
            $blogger->blogger_name = $blg->blogger_name;
        } else {
            return self::createResponse(400, 'Блогер не найден');
        }
//        $blogger->blogger_name = $rawBody['params']['blogger_name'] ?? $blogger->blogger_name;

        $blogger->nmid = $rawBody['params']['nmid'] ?? $blogger->nmid;
        $blogger->nmid_supplier = $rawBody['params']['nmid_supplier'] ?? $blogger->nmid_supplier;
        $blogger->price_release = $rawBody['params']['price_release'] ?? $blogger->price_release;
        $blogger->feedback_wb = $rawBody['params']['feedback_wb'] ?? $blogger->feedback_wb;
        $blogger->feedback_date = $rawBody['params']['feedback_date'] ?? $blogger->feedback_date;
        $blogger->adv_type = $rawBody['params']['adv_type'] ?? $blogger->adv_type;
        $blogger->pay_product = $rawBody['params']['pay_product'] ?? $blogger->pay_product;
        $blogger->pay_adv = $rawBody['params']['pay_adv'] ?? $blogger->pay_adv;
        $blogger->price_adv_product = $rawBody['params']['price_adv_product'] ?? $blogger->price_adv_product;
        $blogger->all_subs = $rawBody['params']['all_subs'] ?? $blogger->all_subs;
        $blogger->people_subs = $rawBody['params']['all_subs'] ?? $blogger->people_subs;
        $blogger->coverage_plan = $rawBody['params']['coverage_plan'] ?? $blogger->coverage_plan;
        $blogger->coverage_fact = $rawBody['params']['coverage_fact'] ?? $blogger->coverage_fact;
        $blogger->click_plan = $rawBody['params']['click_plan'] ?? $blogger->click_plan;
        $blogger->click_fact = $rawBody['params']['click_fact'] ?? $blogger->click_fact;
        $blogger->order_plan = $rawBody['params']['order_plan'] ?? $blogger->order_plan;
        $blogger->order_fact = $rawBody['params']['order_fact'] ?? $blogger->order_fact;
        $blogger->order_adv_time = $rawBody['params']['order_adv_time'] ?? $blogger->order_adv_time;
        $blogger->adv_start = $rawBody['params']['adv_start'] ?? $blogger->adv_start;
        $blogger->adv_finish = $rawBody['params']['adv_finish'] ?? $blogger->adv_finish;

        if (!$blogger->save()) {
            return self::createResponse(400, json_encode($blogger->errors));
        }

        return self::createResponse(200, 'Объект сохранён');
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete($id = null)
    {
        $delete = BloggersAdv::findOne(['user_shop_id' => $this->user_shop_id, 'id' => $id]);

        if (empty($delete))
            return self::createResponse(400, 'Объект не найден');

        $delete->delete();
        return self::createResponse(200, 'Объект удалён');
    }
}
