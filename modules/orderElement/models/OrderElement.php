<?php

namespace app\modules\orderElement\models;

use Yii;
use app\modules\orderInclude\models\OrderInclude;
use yii\data\ActiveDataProvider;
use app\modules\user\models\User;
use app\modules\logs\models\Log;
use app\modules\receiving_points\models\ReceivingPoints;
use app\modules\additional_services\models\AdditionalServices;
use app\modules\payment\models\PaymentInclude;

/**
 * This is the model class for table "order_element".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $company_name
 * @property string $adress_1
 * @property string $adress_2
 * @property string $city
 * @property string $zip
 * @property string $phone
 * @property string $state
 */
class OrderElement extends \yii\db\ActiveRecord
{
    public $includes_packs;
    public $sub_total;
    /**
     * @inheritdoc
     */
  public static function getTextStatus(){
    return array(
      ''=>'All',
      '0'=>'Draft',
      '1'=>'Awaiting at MailtoUSA facility',
      '2'=>'Received at MailtoUSA facility ZZZ',
      '3'=>'On route to USA border',
      '4'=>'Transferred to XXX faclitity',
      '5'=>'YYY status',
      '6'=>'Returned at MailtoUSA facility',
    );
  }

  public static function elementStatusText($param)
  {
    $textForStatus =  OrderElement::getTextStatus();
    if ($param < (count($textForStatus)-1)) return  $textForStatus[$param];
    else return 'Unknown status';
  }


  //Получение полного статуса прописью в зависимости от текущего статуса и доп поля
  public function getFullTextStatus()
  {
    $textForStatus =  OrderElement::getTextStatus();
    $txt=$textForStatus[$this->status];
    if($this->status==2){
      $point=ReceivingPoints::findOne($this->status_dop);
      if ($point) $txt=str_replace('ZZZ',$point->name,$txt);
    }
    return $txt;
  }

    public static function tableName()
    {
        return 'order_element';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name','company_name', 'adress_1','city', 'zip', 'state'], 'required'],
            [['first_name', 'last_name', 'city', 'zip', 'phone', 'state'], 'string', 'max' => 60],
            [['company_name'], 'string', 'max' => 128],
            [['track_number'], 'string'],
            [['price','qst','gst'],'double'],
            [['weight'], 'double'],
            [['track_number_type','status_dop','status','payment_state'], 'integer'],
            [['address_type','weight','track_number','track_number_type'], 'safe'],
            [['adress_1', 'adress_2'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id'=>"User",
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'company_name' => 'Company Name',
            'adress_1' => 'Adress 1',
            'adress_2' => 'Adress 2',
            'city' => 'City',
            'zip' => 'Zip',
            'phone' => 'Phone',
            'state' => 'State',
            'qst' => 'PST',
            'gst' => 'GST/HST',
        ];
    }

  public function getUser(){
    return $this->hasOne(User::className(), ['id' => 'user_id']);
  }

  public function getOrderInclude(){
      return $this->hasMany(OrderInclude::className(),['order_id' => 'id']);
  }

  public function getPaySuccessful(){
    $payments=PaymentInclude::find()
      ->select([
        'element_id',
        'sum(price) as price',
        'sum(qst) as qst',
        'sum(gst) as gst',
        'sum(price+qst+gst) as sum'
      ])
      ->where([
        'element_type'=>0,
        'element_id'=>$this->id,
        'status'=>1
      ])
      ->groupBy(['element_id'])
      ->asArray()
      ->all();

    return $payments;
  }

  public function getTrackInvoice(){
    $el=AdditionalServices::find()->where(['parcel_id_lst'=>$this->id,'type'=>1])->one();
    if(!$el){
      $el=NEW AdditionalServices;
      $el->type=1;
      $el->client_id=$this->user_id;
      $el->user_id=Yii::$app->user->id;
      $el->parcel_id_lst=(string)$this->id;
      $el->price=0;
      $el->kurs=Yii::$app->config->get('USD_CAD');
      $el->create=time();
    };
    return $el;
  }

  public function getWeight_lb(){
    return floor($this->weight);
  }

  public function getWeight_oz(){
    return floor(($this->weight-floor($this->weight))*16);
  }

  public function getIncludesSearch(){
    $query = OrderInclude::find();
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);
    $query->andFilterWhere([
      'order_id'=>$this->id
    ]);
    return $dataProvider;

  }

  public function getIncludes(){
    $query = OrderInclude::find()->where(['order_id'=>$this->id])->asArray()->all();
    return $query;
  }

  public function afterSave($insert, $changedAttributes)
  {
    //if($changedAttributes['agreement'])return true;
    //d($insert);
    //ddd($changedAttributes);
    parent::afterSave($insert, $changedAttributes);
    if ($insert) {
      // Да это новая запись (insert)
      if($this->source){
        Log::addLog($this->id,1,$this->source);
      }else{
        Log::addLog($this->id,0);
      }
    } else {
      // Нет, старая (update)
      if($this->status>0 AND isset($changedAttributes['weight'])){
        Log::addLog($this->id,2,[$changedAttributes['weight'],$this->weight]);
      }

      if(!isset($changedAttributes)){
        return true;
      }
      if(
        isset($changedAttributes['status'])||
        isset($changedAttributes['status_dop'])
      ){
        Log::addLog($this->id,['text'=>'Change status to "'.$this->getFullTextStatus().'"'],false,$this->status);
      }
    }
  }

  public function clearParcels($userID,$stringGroup){
    $query = OrderElement::find();
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    $query->andFilterWhere(['user_id' => $userID]);
    $el_group=explode(',',$stringGroup);
    $query->andFilterWhere(['in', 'id', $el_group]);

    $arr = array();
    foreach ($dataProvider->models as $parcel){
      $arr[] = $parcel->id;
    }
    $string = implode(',',$arr);
    return $string;
  }

  public function getTrackingNumberPostLink($ShippingCarrier, $TrackingNumber) {
    $usps = 'https://tools.usps.com/go/TrackConfirmAction!execute.action?formattedLabel=';
    $fedex = 'https://www.fedex.com/fedextrack/?action=track&tracknumbers=';
    $fedex_english = 'https://www.fedex.com/fedextrack/?action=track&cntry_code=english&tracknumbers=';
    $ups = 'http://wwwapps.ups.com/WebTracking/processInputRequest?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&InquiryNumber1=';
    $ups_Requester = 'http://wwwapps.ups.com/WebTracking/processPOD?Requester=&refNumbers=&loc=en_US&tracknum=';
    $canadapost = 'http://trackingshipment.net/index.php?co=canada_post&nomer_pos=';

    $ShippingCarrierURL = '';
    if (strtolower($ShippingCarrier) == "usps") {
      $ShippingCarrierURL = $usps;
    }
    else if (strtolower($ShippingCarrier) == "fedex") {
      $ShippingCarrierURL = $fedex;
    }
    else if (strtolower($ShippingCarrier) == "ups") {
      $ShippingCarrierURL = $ups;
    }
    else if (strtolower($ShippingCarrier) == "canadapost") {
      $ShippingCarrierURL = $canadapost;
    }
    else if (strlen($TrackingNumber) == 13) {
      $txt=substr($TrackingNumber, 0, 1);
      if (is_numeric($txt) == false) {
        $ShippingCarrierURL = $usps;
      }
    }
    $TrackingNumberPostLink = '';
    if (isset($TrackingNumber)) {
      if ($TrackingNumber != "") {
        if ($ShippingCarrierURL != "") {
          $TrackingNumberPostLink = "<a href='$ShippingCarrierURL$TrackingNumber' target='_blank' style='color:dodgerblue;font-weight:bold;text-decoration:none'>".$TrackingNumber."</a>";
        } else {
          $TrackingNumberPostLink = $TrackingNumber;
        }
      }
      else {
        $TrackingNumberPostLink = "";
      }
    }

    return $TrackingNumberPostLink;
  }

  function GetShippingSummary($TrackingNumber,$ShippingCarrier){
    $ShippingSummary='';

    if ($ShippingCarrier=='canadapost') {
      $Carrier='canada_post';
    } else {
      $Carrier=$ShippingCarrier;
    }

    $url = 'http://trackingshipment.net';
    $apicall = $url . "/" . $Carrier . "/" . $TrackingNumber;

    ////$myString = print_r($resp, TRUE);

    $myString=strip_tags(file_get_contents($apicall));

    $findme1 = 'request N';
    $pos1 = strpos($myString, $findme1);
    if ($pos1 !== false) {
      $findme2 = 'Summary:';
      $pos2 = strpos($myString, $findme2, $pos1);
    }
//echo "pos1=" . $pos1 . " pos2=" . $pos2 . " pos3=" . $pos3;echo "<br>";
    if ($pos1 !== false && $pos2 !== false) {
      $TrackingNumber1 = trim(substr($myString,$pos1+strlen($findme1),$pos2-($pos1+strlen($findme1))));
      //echo "TrackingNumber=" . $TrackingNumber1;echo "<br>";
      if ($TrackingNumber1 != $TrackingNumber) {
        goto exit_here;
      }
    }

//exit;
    $findme1 = 'Summary:';
    $pos1 = strpos($myString, $findme1);
    if ($pos1 !== false) {
      $findme2 = 'Details:';
      $pos2 = strpos($myString, $findme2, $pos1);
    }
    if ($pos2 === false) {
      $myString = str_replace('U.S. ', '', $myString);
      $findme2 = '.';
      $pos2 = strpos($myString, $findme2, $pos1);
    }
    if ($pos1 !== false && $pos2 !== false) {
      //echo "pos1=" . $pos1 . " pos2=" . $pos2 . " pos3=" . $pos3;echo "<br>";
      $Summary = trim(substr($myString,$pos1+strlen($findme1),$pos2-($pos1+strlen($findme1))));
      $Summary = str_replace('[nbsp;', '', $Summary);
      //$Summary = str_replace('at] => ', '', $Summary);
      $Summary = str_replace(',] =>', '', $Summary);
      $Summary = str_replace('] =>', '', $Summary);
      //$Summary = str_replace(',] =>', '', $Summary);
      $Summary = str_replace('_', '', $Summary);
      $Summary = str_replace('ByEndofDay', '', $Summary);
      $Summary = trim($Summary);
    }

    $ShippingSummary=$Summary;
    ////}
    exit_here:
    return $ShippingSummary;
  }

  function GetShippingCarrier($TrackingNumber) //синхронизировать с finditem.php
  {
    $ShippingCarrier='';
    if (strlen($TrackingNumber)==26) {
      $ShippingCarrier='ups';
    } else if (strlen($TrackingNumber)==22 && substr($TrackingNumber,0,2)!='96') { //96 -FedEx
      $ShippingCarrier='usps';
    } else if (strlen($TrackingNumber)==22 && substr($TrackingNumber,0,2)=='96') { //96 -FedEx 9612800 087985515428589
      $ShippingCarrier='fedex';
    } else if (strlen($TrackingNumber)==20 && substr($TrackingNumber,0,2)=='61') {
      $ShippingCarrier='fedex';
    } else if (strlen($TrackingNumber)==20 && substr($TrackingNumber,0,2)=='13') {
      $ShippingCarrier='usps';
    } else if (strlen($TrackingNumber)==20 && substr($TrackingNumber,0,2)=='23') {
      $ShippingCarrier='usps';
    } else if (strlen($TrackingNumber)==18 && strtoupper(substr($TrackingNumber,0,2))=='1Z') {
      $ShippingCarrier='ups';
    } else if (strlen($TrackingNumber)==16 && substr($TrackingNumber,0,1)=='7') {
      $ShippingCarrier='canadapost';
    } else if (strlen($TrackingNumber)==15 || strlen($TrackingNumber)==12) {
      $ShippingCarrier='fedex';
    } else if (strlen($TrackingNumber)==13) {
      $txt=substr($TrackingNumber,0,1);
      if (is_numeric($txt)==false)  {
        $ShippingCarrier='usps';
      }
    }

    return $ShippingCarrier;
  }

}
