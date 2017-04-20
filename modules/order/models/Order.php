<?php

namespace app\modules\order\models;

use Yii;
use app\modules\orderElement\models\OrderElement;
use app\modules\user\models\User;
use yii\db\Query;
use app\modules\address\models\Address;

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
        return OrderElement::find()->where(['id' =>explode(',',$this->el_group)])->all();
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

  public function getSumData($id,$test_data=false){
    $order_elements = $this->getOrderElement();

    $total=array(
      'price'=>0,
      'weight'=>0,
      'quantity'=>0,
    );

    $query = new Query;
    $query->select('weight')
      ->from('tariffs')
      ->orderBy([
        'weight' => SORT_DESC
      ]);
    $row = $query->one();
    $max_weight=$row['weight'];

    foreach ($order_elements as &$pac) {
      //подумать как оптимизировать перебитие даты.
      if($test_data){
        $param_name='receive_max_time'.(Yii::$app->user->identity->isManager() ? '_admin' : '');
        $max_time =
          time() +
          (24-Yii::$app->config->get($param_name)) * 60 * 60;
        if ($pac->transport_data < $max_time) {
          $pac->transport_data = strtotime('+1 days');
        }
      }
      $sub_total=0;
      $pac->includes_packs = $pac->getIncludes();
      if (count($pac->includes_packs) == 0) {
        Yii::$app
          ->getSession()
          ->setFlash(
            'error',
            'The package must have at least one attachment.'
          );
        Yii::$app->response->redirect('/orderInclude/create-order/' . $id);
        return false;
      }
      foreach ($pac->includes_packs as $pack) {
        $sub_total+=$pack['price'] * $pack['quantity'];
        $total['price'] += $pack['price'] * $pack['quantity'];
        $total['quantity'] += $pack['quantity'];
      }
      $pac->sub_total=$sub_total;
      $this_weight=$pac->weight;
      $total['weight']+=$this_weight;
      if($this_weight>$max_weight){
        Yii::$app
          ->getSession()
          ->setFlash(
            'error',
            'Allowable weight of the parcel is '.$max_weight.'lb.'
          );
        Yii::$app->response->redirect('/orderInclude/create-order/' . $id);
        return false;
      }

    }

    $total['weight_lb']=floor($total['weight']);
    $total['weight_oz']=floor(($total['weight']-$total['weight_lb'])*16);
    $user = User::find()->where(['id'=>$pac->user_id])->one();
    $address=Address::findOne(['user_id' => $user->id]);

    return [
      'order_elements' => $order_elements,
      'transport_data'=>$pac->transport_data,
      'total'=>$total,
      'address'=>$address,
      'order_id'=>$id,
      'user'=>$user,
    ];
  }

    public function setData($data){
      /*Yii::$app->db->createCommand()
      ->update('order_element', $data, ['id' => explode(',',$this->el_group)])
      ->execute();*/
      $parcels = $this->getOrderElement();
      foreach($parcels as &$parcel){
        $parcel->attributes=$data;
        $parcel->save();
      }
    }

  public function setStatus($status,$send_mail){
    $parcels = $this->getOrderElement();

    $users=[];
    $users_parcel=[];
    $total=[
      'weight'=>0,
      'weight_by_user'=>[]
    ];

    foreach($parcels as &$parcel){
      if(!isset($users_parcel[$parcel->user_id])){
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
          ->setFrom(\Yii::$app->config->get('adminEmail'))
          ->setTo($user->email)
          ->setSubject('Status of parcels updated')
          ->send();
      }
    }
    //$users=User::find()->where(['id' => $users])->all();
  }
}
