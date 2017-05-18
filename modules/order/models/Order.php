<?php

namespace app\modules\order\models;

use Yii;
use app\modules\orderElement\models\OrderElement;
use app\modules\user\models\User;
use yii\db\Query;
use app\modules\address\models\Address;
use app\modules\additional_services\models\AdditionalServicesList;
use app\modules\additional_services\models\AdditionalServices;
use app\components\ParcelPrice;

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

    public function addAdditionalService($service){
      $tpl=AdditionalServicesList::find()->where(['id'=>$service,'active'=>1])->one();

      if($tpl->type==1){
        $items=$this->getOrderElement();
        foreach ($items as $pac){
          $pac->addAdditionalService($service,false);
        }
        Yii::$app
          ->getSession()
          ->setFlash(
            'info',
            'The service has been added to the selection of parcels in the sample.'
          );
      }else{
        $el=NEW AdditionalServices;
        $el->type=$service;
        $el->client_id=$this->client_id;
        $el->user_id=Yii::$app->user->id;
        $el->parcel_id_lst=(string)$this->el_group;
        $el->price=$tpl->base_price;
        $el->group_id=1;
        $el->kurs=Yii::$app->config->get('USD_CAD');
        $el->create=time();
        $el->save();

        Yii::$app
          ->getSession()
          ->setFlash(
            'info',
            'The service is added to the parcel selection.'
          );
      }

      return true;
    }


    public function getAdditionalService(){
      $el=AdditionalServices::find();

      $els=explode(',',$this->el_group);
      foreach($els as $pac){
        $el->orWhere(['parcel_id_lst'=>$pac]);
        $el->orWhere(['like','parcel_id_lst','%,'.$pac.',%',false]);
        $el->orWhere(['like','parcel_id_lst',$pac.',%',false]);
        $el->orWhere(['like','parcel_id_lst','%,'.$pac,false]);
      }
      $el->andWhere(['group_id'=>1]);
      $el=$el->all();
      return $el;
    }   /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [[ 'user_id'], 'integer'],
            [[ 'client_id'], 'integer'],
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
            'client_id' => 'Client ID',
            'el_group' => 'Parcels ID',
            'created_at' => 'Created At',

        ];
    }

    //Собираем сумарные даные по заказу (в основном для PDF)
  public function getSumData($id,$test_data=false){
    $order_elements = $this->getOrderElement();

    if(!Yii::$app->user->isGuest && Yii::$app->user->can('changeTariff')){
      $test_data=false;
    };

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
        //d($pac->transport_data);
        //ddd($max_time);
        if ($pac->transport_data < $max_time) {
          $pac->transport_data = $max_time;
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

  public function getPaymentData($services_list=false,$parcels_list=false){
    $request = Yii::$app->request;
    $pay_array=array(); //для сохранения данных об оплате

    $el_group=$this->orderElement;

    $user_id=$el_group[0]->user_id;

    //Проверяем что б пользователю хватало прав на просмотр
    if(!Yii::$app->user->identity->isManager() && $user_id!=Yii::$app->user->identity->id){
      throw new NotFoundHttpException('You can pay only for your packages.');
    }

    if(Yii::$app->user->identity->isManager()){
      $user=User::find()->where(['id'=>$user_id])->one();
    }else{
      $user=Yii::$app->user->identity;
    }

    //узнаем налог пользователя
    $tax=$user->getTax();
    if(!$tax){
      Yii::$app->getSession()->setFlash('error', 'Missing billing address.');
      return $this->redirect(['/parcels']);
    }

    $total=array(
      'price'=>0,
      'gst'=>0,
      'qst'=>0,
      'service_price'=>0,
      'service_gst'=>0,
      'service_qst'=>0,
    );

    $pays_total=array(
      'price'=>0,
      'gst'=>0,
      'qst'=>0
    );

    //проверяем посылки на принадлежность пользователю и при необходимости делаем пересчет цены
    foreach ($el_group as &$pac) {
      $save_to_pay=!$request->post('agree_'.$pac->id);
      $sub_total=array(
        'price'=>0,
        'gst'=>0,
        'qst'=>0
      );

      //проверка принадлежности
      if ($user_id != $pac->user_id) {
        throw new NotFoundHttpException('You can not pay parcels for different users.');
      }

      //проверка необходимости пересчета
      if($pac->status<2 || $pac->price==0){
        $pac->price=(float)ParcelPrice::widget(['weight'=>$pac->weight,'user'=>$user_id]);
        $pac->qst=round($pac->price*$tax['qst']/100,2);
        $pac->gst=round($pac->price*$tax['gst']/100,2);
        $pac->save();
      };

      $ch=$this->id||in_array($pac->id,$parcels_list)||$request->post('ch_parcel_'.$pac->id);

      if($ch){
        $sub_total['price']+=round($pac->price,2);
        $sub_total['qst']+=round($pac->qst,2);
        $sub_total['gst']+=round($pac->gst,2);

        //получаем данные о уже осуществленных платежах
        $paySuccessful=$pac->paySuccessful;
        if($paySuccessful AND count($paySuccessful)>0){
          $sub_total['price']-=round($paySuccessful[0]['price'],2);
          $sub_total['qst']-=round($paySuccessful[0]['qst'],2);
          $sub_total['gst']-=round($paySuccessful[0]['gst'],2);
        };

        //усли есть сумма к оплате добовляем ее к глобальному массиву платежа
        if($sub_total['price']>0) {
          $pay_array[] = [
            'element_id' => $pac->id,
            'element_type' => 0,
            'status' => $save_to_pay ? 0 : -1,
            'comment' => $save_to_pay ? '' : $request->post('text_not_agree_' . $pac->id),
            'price' => round($sub_total['price'], 2),
            'qst' => round($sub_total['qst'], 2),
            'gst' => round($sub_total['gst'], 2),
          ];
          if ($save_to_pay) {
            $pays_total['price'] += $sub_total['price'];
            $pays_total['qst'] += $sub_total['qst'];
            $pays_total['gst'] += $sub_total['gst'];
          }
        }
      }

      //получаем данные о инвойсах
      $invoices=$pac->getAdditionalServiceList(true);
      foreach($invoices as $invoice){
        //для инвойса  храним все в промежуточном массиве
        $invoice_total=array();
        $invoice_total['price']=$invoice->price;
        $invoice_total['qst']=$invoice->qst;
        $invoice_total['gst']=$invoice->gst;

        $invoice_total['price']+=$invoice->dop_price;
        $invoice_total['qst']+=$invoice->dop_qst;
        $invoice_total['gst']+=$invoice->dop_gst;

        //получаем данные о уже осуществленных платежах
        $paySuccessful=$invoice->paySuccessful;
        if($paySuccessful AND count($paySuccessful)>0){
          $invoice_total['price']-=$paySuccessful[0]['price'];
          $invoice_total['qst']-=$paySuccessful[0]['qst'];
          $invoice_total['gst']-=$paySuccessful[0]['gst'];
        };

        $ch=$this->id||in_array($invoice->id,$services_list)||$request->post('v_ch_invoice_'.$invoice->id);
        if($ch) {
          //усли есть сумма к оплате добовляем ее к глобальному массиву платежа
          if ($invoice_total['price'] > 0) {
            $pay_array[] = [
              'element_id' => $invoice->id,
              'element_type' => 1,
              'status' => $save_to_pay ? 0 : -1,
              'comment' => $save_to_pay ? '' : $request->post('text_not_agree_' . $pac->id),
              'price' => $invoice_total['price'],
              'qst' => $invoice_total['qst'],
              'gst' => $invoice_total['gst'],
            ];

            if ($save_to_pay) {
              $pays_total['price'] += $invoice_total['price'];
              $pays_total['qst'] += $invoice_total['qst'];
              $pays_total['gst'] += $invoice_total['gst'];
            }
          }

          $sub_total['price'] += $invoice_total['price'];
          $sub_total['qst'] += $invoice_total['qst'];
          $sub_total['gst'] += $invoice_total['gst'];
        }
      }
      $sub_total=[
        'price'=>round($sub_total['price'],2),
        'qst'=>round($sub_total['qst'],2),
        'gst'=>round($sub_total['gst'],2),
      ];

      $sub_total['vat']=$sub_total['gst']+$sub_total['qst'];
      $sub_total['sum']=$sub_total['price']+$sub_total['vat'];

      $pac->sub_total=$sub_total;

      $total['price']+=$sub_total['price'];
      $total['qst']+=$sub_total['qst'];
      $total['gst']+=$sub_total['gst'];
    }

    $save_to_pay=!$request->post('agree_service');
    $order_service=$order_service=$this->getAdditionalService();
    foreach ($order_service as $as) {

      $ch=$this->id||in_array($as->id,$services_list)||$request->post('v_ch_invoice_'.$as->id);

      if($ch) {
        $service_price = $as['price'];
        $service_qst = $as['qst'];
        $service_gst = $as['gst'];
        $paySuccessful = $as->paySuccessful;
        if ($paySuccessful AND count($paySuccessful) > 0) {
          $service_price -= $paySuccessful[0]['price'];
          $service_qst -= $paySuccessful[0]['qst'];
          $service_gst -= $paySuccessful[0]['gst'];
        }

        $total['service_price'] += $service_price;
        $total['service_qst'] += $service_qst;
        $total['service_gst'] += $service_gst;

        //усли есть сумма к оплате добовляем ее к глобальному массиву платежа
        if ($service_price > 0) {
          $pay_array[] = [
            'element_id' => $as->id,
            'element_type' => 1,
            'status' => $save_to_pay ? 0 : -1,
            'comment' => $save_to_pay ? '' : $request->post('text_not_agree_service'),
            'price' => $service_price,
            'qst' => $service_qst,
            'gst' => $service_gst,
          ];

          if ($save_to_pay) {
            $pays_total['price'] += $service_price;
            $pays_total['qst'] += $service_qst;
            $pays_total['gst'] += $service_gst;
          }
        }
      }
    }

    $total['service_vat']=$total['service_qst']+$total['service_gst'];
    $total['service_sum']=$total['service_price']+$total['service_vat'];

    $total['price']+=$total['service_price'];
    $total['qst']+=$total['service_qst'];
    $total['gst']+=$total['service_gst'];

    $total['vat']=$total['qst']+$total['gst'];
    $total['sum']=$total['price']+$total['vat'];

    $total['pay_pal']=$total['sum']*(1+Yii::$app->config->get('paypal_commision_dolia')/100)+
      Yii::$app->config->get('paypal_commision_fixed');
    //ddd($pay_array);
    return [
      'order_id'=>$this->id,
      'paces'=>$el_group,
      'total'=>$total,
      'user'=>$user,
      'order_service'=>$order_service,
      'pay_array'=>$pay_array,
      'pays_total'=>$pays_total,
      'is_admin'=>Yii::$app->user->identity->isManager(),
      'parcels_list'=>$parcels_list,
      'services_list'=>$services_list,
    ];
  }

  public function beforeSave($insert)
  {
    if ($this->el_group) {   // если это не создание пустого заказа
      $arr = explode(',', $this->el_group);
      asort($arr);
      $admin = 1;
      if (!Yii::$app->user->identity->isManager()) {
        $admin = 0;
      }
      $user_id = null;
      $status = null;
      $flag = 0;
      $flag_dif_clients = 0;

      foreach ($arr as $id) {
        $parcel = OrderElement::findOne(['id' => $id]);
        if ($parcel) {
          if (($flag == 1) &&   //  если это уже не первая посылка из списка
            ((($user_id != $parcel->user_id) && ($admin == 0)) ||    // несовпадение юзерров у неАдмина
              (($status > 1) && ($parcel->status <= 1)) ||
              (($status <= 1) && ($parcel->status > 1)))
          ) {           // несовпадение статусов
            return false;
          }
          if (($user_id != $parcel->user_id) && ($flag == 1)) $flag_dif_clients = 1;
          $user_id = $parcel->user_id;
          $status = $parcel->status;
          $flag = 1;
        } else {
          return false; // нет посылки из списка
        }
      }

      if ($admin == 0) {
        $this->user_id = $user_id;
      } else {
        $this->user_id = Yii::$app->user->id;
      }
      $this->created_at = time();
      $this->client_id = $user_id;
      if ($flag_dif_clients == 1) $this->client_id = 0;
    }
    return parent::beforeSave($insert); // TODO: Change the autogenerated stub
  }

  public function getInvoiceData($invoice=false){

    if(isset($_COOKIE['invoice_check']) && strlen($_COOKIE['invoice_check'])>5){
      $invoice_check=json_decode($_COOKIE['invoice_check'],true);
    }else{
      $invoice_check=[];
    }

    $model = $this->getOrderElement();
    if($invoice){
      $data=json_decode($invoice->detail,true);
    }else {
      $data = [
        'invoice' => '',
        'ref_code' => '',
        'contract_number' => '',
      ];
    }

    $usluga=[
      'parcel'=>[],
      'many'=>[],
    ];
    $uslugaList=AdditionalServicesList::find()
      ->where(['active'=>1])
      //->andWhere(['!=', 'id', 1])
      ->asArray()
      ->all();
    foreach ($uslugaList as $item){
      if($item['id']==1){
        continue;
      }
      if($item['type']==1){
        $usluga['parcel'][]=$item;
      }else{
        $usluga['many'][]=$item;
      }
    };

    if($invoice){
      $services_list=explode(',',$invoice->services_list);
      $parcels_list=explode(',',$invoice->parcels_list);
      $order_service=$this->getAdditionalService();
      foreach ($order_service as $item){
        $invoice_check['ch_invoice_'.$item->id]=in_array($item->id,$services_list);
      }
      $pacs=$this->getOrderElement();
      foreach ($pacs as $item){
        $invoice_check['ch_parcel_'.$item->id]=in_array($item->id,$parcels_list);
        $order_service=$item->getAdditionalServiceList();
        foreach ($order_service as $serv){
          $invoice_check['ch_invoice_'.$serv->id]=in_array($serv->id,$services_list);
        }
      }
      setcookie(
        'invoice_check',
        json_encode($invoice_check)
      );
      //ddd(json_encode($invoice_check));
      //$_COOKIE['invoice_check']=json_encode($invoice_check);
    };

    return [
      'users_parcel'=>$model,
      'order_id'=>$this->id,
      'data'=>$data,
      'usluga'=>$usluga,
      'order_service'=>$this->getAdditionalService(),
      'session'=>Yii::$app->session,
      'invoice_id'=>$invoice?$invoice->id:false,
    ];
  }
}
