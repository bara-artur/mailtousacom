<?php

namespace app\modules\orderElement\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $billing_address_id
 * @property int $order_type
 * @property int $user_id
 * @property int $user_input
 * @property int $user_id_750
 * @property int $order_status
 * @property int $created_at
 * @property int $transport_data
 * @property int $agreement
 * @property int $payment_state
 * @property double $price
 * @property double $qst
 * @property double $gst
 */
class ElementFilterForm extends OrderElement
{
  public $created_at_to;
  public $transport_data_to;
  public $user_input;
  public $price_end;
  /**
   * @inheritdoc
   */
  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['id','user_id','track_number','price','price_end',
        'transport_data','transport_data_to','created_at','created_at_to',
        'status','group_index','payment_state','user_input'], 'safe']
    ];
  }


}
