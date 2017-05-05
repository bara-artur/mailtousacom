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
use yii\web\UploadedFile;
use app\modules\additional_services\models\AdditionalServicesList;
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
  public $files;

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
      ///'5'=>'YYY status',
      '5'=>'In transit',
      '6'=>'Delivered',
      '7'=>'Returned at MailtoUSA facility',
    );
  }

  public function getStateText(){
    if(!$this->state){
      return '';
    }
    if(isset(Yii::$app->params['states'][$this->state])){
      return Yii::$app->params['states'][$this->state];
    }
    return $this->state;
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
    if($this->status==4){
      $point=$this->GetShippingCarrierName(true);
      if ($point) $txt=str_replace('YYY',$point,$txt);
    }
    if($this->status==5){
      $point=$this->GetShippingCarrierName(true);
      if ($point) $txt=$point.": ".$this->status_dop;
    }
    return $txt;
  }

  public static function tableName(){
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
          [['zip'], 'string', 'min' => 5],
          [['track_number'], 'string'],
          [['price','qst','gst'],'double'],
          [['weight'], 'double'],
          [['track_number_type','status','payment_state'], 'integer'],
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

  /*
   * Возвращает данные о сервисах к данной посылке. Выводит только услуги для этой посылки.
   * $show_track_invoice - выводить/нет данные о инвойсе трек номера
   */
  public function getAdditionalServiceList($show_track_invoice=true){
    $els=AdditionalServices::find()->where(['parcel_id_lst'=>$this->id]);
    if(!$show_track_invoice){
      $els=$els->andWhere(['!=','type',1]);
    }
    return $els->all();
  }

  //добавляем услугу к посылки. Проверяем существование услуги
  public function addAdditionalService($service,$show_flash_message=true){
    if($service==1){

      return;
    }

    $tpl=AdditionalServicesList::find()->where(['id'=>$service,'active'=>1])->one();
    if(!$tpl){
      if($show_flash_message) {
        Yii::$app
          ->getSession()
          ->setFlash(
            'error',
            'Service not found.'
          );
      };
      return false;
    }

    if($tpl->only_one==1){
      $service_item=$this->getAdditionalService($service);
      if($service!=1){
        $service_item=$service_item[0];
      }

      if(!$service_item->isNewRecord){
        if($show_flash_message) {
          Yii::$app
            ->getSession()
            ->setFlash(
              'error',
              'This service can be only one for each package.'
            );
        };
        return false;
      };
    }

    $el=NEW AdditionalServices;
    $el->type=$service;
    $el->client_id=$this->user_id;
    $el->user_id=Yii::$app->user->id;
    $el->parcel_id_lst=(string)$this->id;
    $el->price=$tpl->base_price;
    $el->kurs=Yii::$app->config->get('USD_CAD');
    $el->create=time();

    return $el->save();
  }

  //Получить запись о конкретном сервисе или создать для него стартовую модель
  public function getAdditionalService($service,$save_on_create=false){
    $el=AdditionalServices::find()->where(['parcel_id_lst'=>$this->id,'type'=>$service]);
    if($service==1){
      $el=$el->one();
    }else{
      $el=$el->all();
    };
    if(!$el || count($el)==0){
      $tpl=AdditionalServicesList::find()->where(['id'=>$service,'active'=>1])->one();
      if(!$tpl)return false;

      $el=NEW AdditionalServices;
      $el->type=$service;
      $el->client_id=$this->user_id;
      $el->user_id=Yii::$app->user->id;
      $el->parcel_id_lst=(string)$this->id;
      $el->price=$tpl->base_price;
      $el->kurs=Yii::$app->config->get('USD_CAD');
      $el->create=time();

      if($save_on_create){
        $el->save();
      }

      if($service!=1){
        $el=[$el];
      }
    }
    return $el;
  }

  //Получем данные трек инвойса
  public function getTrackInvoice(){
    if($this->track_number_type!=1){
      return false;
    }
    return $this->getAdditionalService(1);
  }

  public function getRecipientData(){
    if($this->first_name=='[default]'){
      return "<div>By scanner</div>";
    };

    return '<div>'.$this->first_name.' '.$this->last_name.'</div>'.
            '<div>'.$this->company_name.'</div>'.
            '<div>'.$this->getStateText().'</div>'.
            '<div>'.$this->city.'</div>';
            '<div>'.$this->zip.'</div>';
  }

  //Возвращает знаение веса в Lb(только целое число)
  public function getWeight_lb(){
    return floor($this->weight);
  }

  //Возвращает дробную часть веса в Oz(только целое число)
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

  public function getPath($test=true){
    $path='order_docs/'.floor($this->id/100).'/'.($this->id % 100).'/';
    if($test && !is_readable($path)){
      mkdir($path,0777,true);
    }
    return $path;
  }

  public function getDocsCount(){
    $path=$this->getPath(false);
    if(!is_readable($path)){
      return 0;
    }
    $dh  = scandir(realpath($path));
    return count($dh)-2;
  }

  public function delFile($key){
    $path=$this->getPath();
    if(is_readable($path.$key)){
      return unlink($path.$key);
    }
    return false;
  }

  public function fileOutArray($url_arr){
    $p1=array();
    $p2=array();

    for ($i = 0; $i < count($url_arr); $i++) {
      $file_name=explode('/',$url_arr[$i]);
      $file_name=strtolower($file_name[count($file_name)-1]);
      $key = $file_name;
      $url = str_replace('//','/','/'.$url_arr[$i]);
      $type=false;
      $p1[] = $url; // sends the data
      $data = [
        'caption' => $key,
        'size' => filesize($url_arr[$i]),
        //'url' => $url,
        'key' => $key
      ];
      if(strpos($file_name,'.pdf'))$type='pdf';
      if(strpos($file_name,'.doc'))$type='object';
      if(strpos($file_name,'.rtf'))$type='object';
      if($type){
        $data['type']=$type;
      }
      $p2[]=$data;
    };

    return [
      'initialPreview' => $p1,
      'initialPreviewConfig' => $p2,
      'append' => true // whether to append these configurations to initialPreview.
      // if set to false it will overwrite initial preview
      // if set to true it will append to initial preview
      // if this propery not set or passed, it will default to true.
    ];
  }

  public function fileList(){
    $url_arr=array();
    $path=$this->getPath();

    $dh  = opendir(realpath($path));
    while (false !== ($filename = readdir($dh))) {
      if(strlen($filename)<5)continue;
      $url_arr[] = $path.$filename;
    }
    return $this->fileOutArray($url_arr);
  }

  public function loadDoc($files){
    $path=$this->getPath();
    $url_arr=array();

    $count_now=count(scandir($path));
    $err=false;
    if($count_now-2>=5){
      $err='Maximum number of files is 5.';
    }else {
      foreach ($files as $file) {
        $file_name = ($path . date('Ymd_His_') . 'id' . $this->user_id . '_order' . $this->id . '.' . $file->extension);
        $file->saveAs($file_name);
        $url_arr[] = $file_name;
      }
    }
    $files=$this->fileList();
    if($err){
      $files["error"]=$err;
    }
    return json_encode($files);
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
    return true;
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

  public function GetShippingCarrierName($short=false){
    $index=$this->GetShippingCarrier($this->track_number);
    if($short){
      $ShippingCarrier=Yii::$app->params['ShippingCarrierShort'];
    }else{
      $ShippingCarrier=Yii::$app->params['ShippingCarrier'];
    }
    if(isset($ShippingCarrier[$index])){
      return $ShippingCarrier[$index];
    }else{
      return "N/A";
    }
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
    if (strlen($TrackingNumber)==26) {
      return 'ups';
    }
    if (strlen($TrackingNumber)==22 && substr($TrackingNumber,0,2)!='96') { //96 -FedEx
      return 'usps';
    }
    if (strlen($TrackingNumber)==22 && substr($TrackingNumber,0,2)=='96') { //96 -FedEx 9612800 087985515428589
      return 'fedex';
    }
    if (strlen($TrackingNumber)==20 && substr($TrackingNumber,0,2)=='61') {
      return 'fedex';
    }
    if (strlen($TrackingNumber)==20 && substr($TrackingNumber,0,2)=='13') {
      return 'usps';
    }
    if (strlen($TrackingNumber)==20 && substr($TrackingNumber,0,2)=='23') {
      return 'usps';
    }
    if (strlen($TrackingNumber)==18 && strtoupper(substr($TrackingNumber,0,2))=='1Z') {
      return 'ups';
    }
    if (strlen($TrackingNumber)==16 && substr($TrackingNumber,0,1)=='7') {
      return 'canadapost';
    }
    if (strlen($TrackingNumber)==15 || strlen($TrackingNumber)==12) {
      return 'fedex';
    }
    if (strlen($TrackingNumber)==13) {
      $txt=substr($TrackingNumber,0,1);
      if (is_numeric($txt)==false)  {
        return 'usps';
      }
    }

    return "";
  }
}
