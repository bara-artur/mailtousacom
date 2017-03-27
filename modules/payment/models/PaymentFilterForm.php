<?php

namespace app\modules\payment\models;

use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property int $id
 * @property int $type
 * @property int $order_id
 * @property int $client_id
 * @property double $price
 * @property double $qst
 * @property double $gst
 * @property string $code
 * @property int $status
 * @property int $pay_time
 * @property int $create_time
 */
class PaymentFilterForm extends PaymentsList
{
    public $pay_time_to;
    public $user_input;
    /**
     * @inheritdoc
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type','client_id','status', 'pay_time', 'pay_time_to', 'user_input'], 'safe'],
        ];
    }
}
