<?php

namespace app\modules\orderInclude\models;

use Yii;
use app\modules\orderInclude\models\OrderInclude;
/**
 * This is the model class for table "order_include".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $name
 * @property double $price
 * @property integer $weight
 * @property integer $quantity
 */
class OrderAddItems extends OrderInclude
{
  public function rules()
  {
    $rules = parent::rules();
    $rules[] = [['first_name', 'last_name','company_name','adress_1','city','zip','phone','state'], 'required'];
    return $rules;
  }

}