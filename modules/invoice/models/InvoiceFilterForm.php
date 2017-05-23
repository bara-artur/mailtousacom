<?php

namespace app\modules\invoice\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $user_id
 * @property int $create
 * @property int $pay_status
 * @property double $price
 */
class InvoiceFilterForm extends Invoice
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
      [['id','user_id','price','price_end',
        'create','created_at_to',
        'pay_status','user_input'], 'safe'],
    ];
  }


}
