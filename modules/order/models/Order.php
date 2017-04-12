<?php

namespace app\modules\order\models;

use Yii;
use app\modules\orderElement\models\OrderElement;
use app\modules\user\models\User;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property string $el_group
 * @property integer $user_id
 * @property string $created_at
 */
class Order extends \yii\db\ActiveRecord
{
    public static function getTextStatus(){
        return array(
            ''=>'All',
            '0'=>'Draft',
            '1'=>'Awaiting at MailtoUSA facility',
            '2'=>'Received at MailtoUSA facility',
            '3'=>'On route to USA border',
            '4'=>'Transferred to XXX faclitity',
            '5'=>'YYY status',
            '6'=>'Returned at MailtoUSA facility',
        );
    }

    public static function orderStatusText($param)
    {
        $textForStatus =  Order::getTextStatus();
        if ($param < (count($textForStatus)-1)) return  $textForStatus[$param];
        else return 'Unknown status';
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    public function getUser()
    {
      return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getOrderElement()
    {
        return $this->hasMany(OrderElement::className(),['id' =>explode(',',$this->el_group)]);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [[ 'user_id'], 'integer'],
            [[ 'el_group'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Order ID',
            'user_id' => 'User ID',
            'el_group' => 'User ID',
            'created_at' => 'Created At',

        ];
    }

    public function setData($data){
      /*Yii::$app->db->createCommand()
      ->update('order_element', $data, ['id' => explode(',',$this->el_group)])
      ->execute();*/
      $numbers = explode(',',$this->el_group);
      $parcels = OrderElement::find()->where(['id' => $numbers])->all();
      foreach($parcels as &$parcel){
        $parcel->attributes=$data;
        $parcel->save();
      }
    }

  public function setStatus($status,$send_mail){
    $numbers = explode(',',$this->el_group);
    $parcels = OrderElement::find()->where(['id' => $numbers])->all();

    $users=[];
    $users_parcel=[];
    $total=[
      'weight'=>0,
      'weight_by_user'=>[]
    ];

    foreach($parcels as &$parcel){
      if(!$users_parcel[$parcel->user_id]){
        $users_parcel[$parcel->user_id]=[];
        $total['weight_by_user'][$parcel->user_id]=0;
        $users[]=$parcel->user_id;
      }
      $total['weight']+=$parcel->weight;
      $total['weight_by_user'][$parcel->user_id]+=$parcel->weight;
      $users_parcel[$parcel->user_id][]=$parcel;

      $parcel->status=$status;
      $parcel->status_dop='';
      $parcel->save();
    }

    \Yii::$app->getSession()->setFlash('success', 'Status of parcels updated.');

    if($send_mail){
      $users=User::find()->where(['id' => $users])->all();
      foreach ($users as $user) {
        \Yii::$app->mailer->compose('new_status', [
          'user' => $user,
          'parcels' => $users_parcel[$user->id],
          'total_weight'=>$total['weight_by_user'][$user->id]
        ])
          ->setFrom(\Yii::$app->params['adminEmail'])
          ->setTo($user->email)
          ->setSubject('Status of parcels updated')
          ->send();
      }
    }
    //$users=User::find()->where(['id' => $users])->all();
  }
}
