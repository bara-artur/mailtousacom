<?php

namespace app\modules\order\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $billing_address_id
 * @property int $order_type
 * @property int $user_id
 * @property int $user_id_750
 * @property int $order_status
 * @property int $created_at
 * @property int $transport_data
 * @property int $agreement
 * @property int $payment_type
 * @property int $payment_state
 * @property double $price
 * @property double $qst
 * @property double $gst
 */
class OrderFilterForm extends Order
{
    public $created_at_to;
    public $transport_data_to;
    /**
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','transport_data','order_status',
                'transport_data','transport_data_to','created_at','created_at_to',
              'order_type','payment_state','payment_type'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'billing_address_id' => 'Billing Address ID',
            'order_type' => 'Order Type',
            'user_id' => 'User ID',
            'user_id_750' => 'User Id 750',
            'order_status' => 'Order Status',
            'created_at' => 'Created At From',
            'transport_data' => 'Transport Data From',
            'agreement' => 'Agreement',
            'payment_type' => 'Payment Type',
            'payment_state' => 'Payment State',
            'price' => 'Price',
            'qst' => 'Qst',
            'gst' => 'Gst',
        ];
    }
}
