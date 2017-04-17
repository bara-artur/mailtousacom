<?php

namespace app\modules\additional_services\models;

use Yii;
use app\modules\user\models\User;
use app\modules\payment\models\PaymentInclude;

/**
 * This is the model class for table "additional_services".
 *
 * @property integer $id
 * @property integer $type
 * @property string $parcel_id_lst
 * @property integer $client_id
 * @property integer $user_id
 * @property string $detail
 * @property string $status_pay
 * @property integer $quantity
 * @property double $price
 * @property double $gst
 * @property double $qst
 */
class AdditionalServices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'additional_services';
    }

    public static function getStatusList(){
      return [
        0=>"Created",
        1=>"Sent to customer",
        2=>"Paid",
        3=>"Refunded",
        4=>"Cancelled"
      ];
    }
    public static function getTypeList(){
      return [
        1=>"Track number invoice",
      ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type', 'client_id', 'user_id', 'quantity', 'create','status_pay'], 'integer'],
            [['price', 'gst', 'qst','dop_price', 'dop_gst', 'dop_qst','kurs'], 'number'],
            [['parcel_id_lst', 'detail'], 'string', 'max' => 255],
        ];
    }

  public function getUser(){
    return $this->hasOne(User::className(), ['id' => 'user_id']);
  }

  public function getClient(){
    return $this->hasOne(User::className(), ['id' => 'client_id']);
  }

  public function getPaySuccessful(){
    $payments=PaymentInclude::find()
      ->select([
        'element_id',
        'sum(price) as already_price',
        'sum(qst) as already_qst',
        'sum(gst) as already_gst'
      ])
      ->where([
        'element_type'=>1,
        'element_id'=>$this->id,
        'status'=>1
      ])
      ->groupBy(['element_id'])
      ->asArray()
      ->all();

    return $payments;
  }

  public function getTextStatus(){
    $st=AdditionalServices::getStatusList();
    return isset($st[$this->status_pay])?$st[$this->status_pay]:'-';
  }
  public function getTextType(){
    $st=AdditionalServices::getTypeList();
    return isset($st[$this->type])?$st[$this->type]:'-';
  }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'parcel_id_lst' => 'Parcel Id Lst',
            'client_id' => 'Client',
            'user_id' => 'Admin',
            'detail' => 'Detail',
            'status_pay' => 'Status Pay',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'dop_price' => 'External price',
            'gst' => 'Gst',
            'qst' => 'Qst',
        ];
    }
}
