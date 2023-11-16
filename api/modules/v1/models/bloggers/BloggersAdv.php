<?php

namespace app\api\modules\v1\models\bloggers;

use app\api\modules\v1\base\BaseActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "bloggers_adv".
 *
 * @property int $id Идентификатор записи таблицы
 * @property int $user_id Идентификатор пользователя
 * @property int $user_shop_id Идентификатор юр. лица
 * @property int|null $blogger_id Идентификатор блогера таблица bloggers
 * @property string $blogger_name Имя блогера
 * @property string|null $nmid Артикул ВБ
 * @property string|null $nmid_supplier Артикул поставщика
 * @property string|null $adv_start Дата и время старта рекламы
 * @property string|null $adv_finish Дата и время окончания рекламы
 * @property float|null $price_release Цена товара на момент выхода рекламы
 * @property string|null $feedback_wb Отзыв на ВБ
 * @property string|null $feedback_date Дата отзыва
 * @property string|null $adv_type Тип рекламы
 * @property float|null $pay_product Оплата товара
 * @property float|null $pay_adv Оплата рекламы
 * @property float|null $price_adv_product Цена рекламы с учетом товара
 * @property int|null $all_subs Подписчиков всего
 * @property int|null $people_subs Подписчиков живых душ
 * @property int|null $coverage_plan Охваты план - должны рассчитываться как % от Живых душ. Нужна кнопка вверху для
 * ввода % плановых охватов
 * @property int|null $coverage_fact Охваты факт
 * @property int|null $cpm_plan СРМ план. Считается как "цена рекламы с учетом товара" / "охваты план" * 1000
 * @property int|null $cpm_fact СРМ факт. Считается как "цена рекламы с учетом товара" / "охваты факт" * 1000
 * @property int|null $click_plan Клики план - считается как % от охваты план. Нужна кнопка вверху для ввода % от
 * охватов план
 * @property int|null $click_fact Клики факт
 * @property float|null $ctr CTR - считается как клики факт/охваты факт
 * @property int|null $order_plan Заказы план - считается как % от кликов. Нужна кнопка вверху для ввода % от кликов
 * @property int|null $order_fact Заказы факт
 * @property int|null $order_adv_time Заказы за время рекламы
 * @property float|null $cr CR - считается как "заказы за время рекламы " разделить на "клики факт"
 * @property float|null $profit_adv Выручка с рекламы, руб. (в заказах) - Заказы за время рекламы*Цена товара на момент
 * выхода рекламы
 * @property float|null $romi ROMI (окупаемость средств) - (Заказы за время рекламы*Цена товара на момент выхода рекламы
 * - Оплата товара- Оплата рекламы)/(Оплата товара+Оплата рекламы)
 * @property float|null $cpl CPL (стоимость лида или заказа) - считается как Цена рекламы с учетом товара / Заказы за
 * время рекламы
 * @property string|null $created_at Дата создания записи
 * @property string|null $updated_at Дата обновления записи
 *
 * @property Bloggers $blogger
 */
class BloggersAdv extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bloggers_adv';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'user_shop_id', 'blogger_name'], 'required'],
            [['user_id', 'user_shop_id', 'blogger_id', 'all_subs', 'people_subs', 'coverage_plan', 'coverage_fact', 'cpm_plan', 'cpm_fact', 'click_plan', 'click_fact', 'order_plan', 'order_fact', 'order_adv_time'], 'integer'],
            [['adv_start', 'adv_finish', 'feedback_date', 'created_at', 'updated_at'], 'safe'],
            [['price_release', 'pay_product', 'pay_adv', 'price_adv_product', 'ctr', 'cr', 'profit_adv', 'romi', 'cpl'], 'number'],
            [['feedback_wb'/*, 'adv_type'*/], 'string'],
            [['blogger_name'], 'string', 'max' => 255],
            [['nmid'], 'string', 'max' => 30],
            [['nmid_supplier'], 'string', 'max' => 100],
            [['blogger_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bloggers::class, 'targetAttribute' => ['blogger_id' => 'blogger_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'user_shop_id' => 'User Shop ID',
            'blogger_id' => 'Blogger ID',
            'blogger_name' => 'Blogger Name',
            'nmid' => 'Nmid',
            'nmid_supplier' => 'Nmid Supplier',
            'adv_start' => 'Adv Start',
            'adv_finish' => 'Adv Finish',
            'price_release' => 'Price Release',
            'feedback_wb' => 'Feedback Wb',
            'feedback_date' => 'Feedback Date',
            'adv_type' => 'Adv Type',
            'pay_product' => 'Pay Product',
            'pay_adv' => 'Pay Adv',
            'price_adv_product' => 'Price Adv Product',
            'all_subs' => 'All Subs',
            'people_subs' => 'People Subs',
            'coverage_plan' => 'Coverage Plan',
            'coverage_fact' => 'Coverage Fact',
            'cpm_plan' => 'Cpm Plan',
            'cpm_fact' => 'Cpm Fact',
            'click_plan' => 'Click Plan',
            'click_fact' => 'Click Fact',
            'ctr' => 'Ctr',
            'order_plan' => 'Order Plan',
            'order_fact' => 'Order Fact',
            'order_adv_time' => 'Order Adv Time',
            'cr' => 'Cr',
            'profit_adv' => 'Profit Adv',
            'romi' => 'Romi',
            'cpl' => 'Cpl',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Blogger]].
     *
     * @return ActiveQuery
     */
    public function getBlogger(): ActiveQuery
    {
        return $this->hasOne(Bloggers::class, ['blogger_id' => 'blogger_id']);
    }

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setCpmPlan'], 'cpm_plan');
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setCpmPlan'], 'cpm_plan');

        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setCpmFact'], 'cpm_fact');
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setCpmFact'], 'cpm_fact');

        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setCtr'], 'ctr');
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setCtr'], 'ctr');

        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setCr'], 'cr');
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setCr'], 'cr');

        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setProfitAdv'], 'profit_adv');
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setProfitAdv'], 'profit_adv');

        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setRomi'], 'romi');
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setRomi'], 'romi');

        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setCpl'], 'cpl');
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setCpl'], 'cpl');

        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setSerializeArray'], 'adv_type');
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setSerializeArray'], 'adv_type');
        $this->on(self::EVENT_AFTER_FIND, [$this, 'getUnserializeArray'], 'adv_type');

//        $this->on(
//            self::EVENT_AFTER_FIND,
//            function() {
//                if (!empty($this->adv_start))
//                    $this->adv_start =  date('d.m.Y', strtotime($this->adv_start));
//
//                if (!empty($this->adv_finish))
//                    $this->adv_finish =  date('d.m.Y', strtotime($this->adv_finish));
//
//                if (!empty($this->feedback_date))
//                    $this->feedback_date =  date('d.m.Y', strtotime($this->feedback_date));
//            }
//        );
    }

    public function setCpmPlan()
    {
        if (!empty($this->coverage_plan) && $this->price_adv_product != null) {
            $this->cpm_plan = $this->price_adv_product / $this->coverage_plan * 1000;
        }
    }

    public function setCpmFact()
    {
        if (!empty($this->coverage_fact) && $this->price_adv_product != null) {
            $this->cpm_fact = $this->price_adv_product / $this->coverage_fact * 1000;
        }
    }

    public function setCtr()
    {
        if (!empty($this->coverage_fact) && $this->click_fact != null) {
            $this->ctr = $this->click_fact / $this->coverage_fact;
        }
    }

    public function setCr()
    {
        if (!empty($this->click_fact) && $this->order_adv_time != null) {
            $this->cr = $this->order_adv_time / $this->click_fact;
        }
    }

    public function setProfitAdv()
    {
        if ($this->order_adv_time != null && $this->price_release != null) {
            $this->profit_adv = $this->order_adv_time * $this->price_release;
        }
    }

    public function setRomi()
    {
        if (
            !empty($this->pay_product + $this->pay_adv) &&
            $this->order_adv_time != null &&
            $this->price_release != null &&
            $this->pay_product != null &&
            $this->pay_adv != null
        ) {
            $this->romi = ($this->order_adv_time * $this->price_release - $this->pay_product - $this->pay_adv) /
                ($this->pay_product + $this->pay_adv);
        }
    }

    public function setCpl()
    {
        if (!empty($this->order_adv_time) && $this->price_adv_product != null) {
            $this->cpl = $this->price_adv_product / $this->order_adv_time;
        }
    }
}
