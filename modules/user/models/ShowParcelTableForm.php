<?php

namespace app\modules\user\models;

use Yii;


class ShowParcelTableForm extends User
{
  public $parcelTableSerial= 'serial';
  public $parcelTableID= 'user_id';
  public $parcelTableParcelID= 'parcel_id';
  public $parcelTableStatus= 'status';
  public $parcelTableItems= 'items';
  public $parcelTableCreatedAt= 'created_at';
  public $parcelTablePaymentState= 'payment_state';
  public $parcelTablePaymentType= 'payment_type';
  public $parcelTablePrice= 'price';
  public $parcelTableQst= 'qst';
  public $parcelTableGst= 'gst';
  public $parcelTableTotal= 'total';
  public $parcelTableTrackNumber= 'track_number';

  public $showSerial;
  public $showID;
  public $showParcelID;
  public $showStatus;
  public $showItems;
  public $showCreatedAt;
  public $showPaymentState;
  public $showPaymentType;
  public $showPrice;
  public $showQst;
  public $showGst;
  public $showTotal;
  public $showTrackNumber;

 public function getAllFlags(){
   $arr = [];
   if ($this->showSerial) $arr[]=$this->parcelTableSerial;
   if ($this->showID) $arr[]=$this->parcelTableID;
   if ($this->showParcelID) $arr[]=$this->parcelTableParcelID;
   if ($this->showStatus) $arr[]=$this->parcelTableStatus;
   if ($this->showItems) $arr[]=$this->parcelTableItems;
   if ($this->showCreatedAt) $arr[]=$this->parcelTableCreatedAt;
   if ($this->showPaymentState) $arr[]=$this->parcelTablePaymentState;
   if ($this->showPaymentType) $arr[]=$this->parcelTablePaymentType;
   if ($this->showPrice) $arr[]=$this->parcelTablePrice;
   if ($this->showQst) $arr[]=$this->parcelTableQst;
   if ($this->showGst) $arr[]=$this->parcelTableGst;
   if ($this->showTotal) $arr[]=$this->parcelTableTotal;
   if ($this->showTrackNumber) $arr[]=$this->parcelTableTrackNumber;
   if (count($arr)==0) {
     return  $this->parcelTableSerial.','.$this->parcelTableTrackNumber.','.
             $this->parcelTableTotal.','.$this->parcelTableGst.','.$this->parcelTableQst.','.
             $this->parcelTablePrice.','.$this->parcelTableID.','.$this->parcelTableParcelID.','.$this->parcelTableStatus.','.
             $this->parcelTableItems.','.$this->parcelTableCreatedAt.','.$this->parcelTablePaymentState.','.
             $this->parcelTablePaymentType;
   }
   return implode(',',$arr);
 }

  public function __construct($parcelTableOptions = 0xffff)
  {
    if ($parcelTableOptions == -1) {
      $this->showSerial = null;
    } else {
      $arr_showColumns=explode(',',$parcelTableOptions);
      $this->showSerial = in_array($this->parcelTableSerial,$arr_showColumns);
      $this->showID = in_array($this->parcelTableID,$arr_showColumns);
      $this->showParcelID = in_array($this->parcelTableParcelID,$arr_showColumns);
      $this->showStatus = in_array($this->parcelTableStatus,$arr_showColumns);
      $this->showItems = in_array($this->parcelTableItems,$arr_showColumns);
      $this->showCreatedAt = in_array($this->parcelTableCreatedAt,$arr_showColumns);
      $this->showPaymentState = in_array($this->parcelTablePaymentState,$arr_showColumns);
      $this->showPaymentType = in_array($this->parcelTablePaymentType,$arr_showColumns);
      $this->showPrice =in_array($this->parcelTablePrice,$arr_showColumns);
      $this->showQst = in_array($this->parcelTableQst,$arr_showColumns);
      $this->showGst = in_array($this->parcelTableGst,$arr_showColumns);
      $this->showTotal = in_array($this->parcelTableTotal,$arr_showColumns);
      $this->showTrackNumber = in_array($this->parcelTableTrackNumber,$arr_showColumns);
    }
  }
  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['showSerial','showID','showParcelID','showStatus','showItems','showCreatedAt','showPaymentState','showPaymentType',
        'showPrice','showQst','showGst','showTotal','showTrackNumber'], 'safe']
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return [
      'showSerial' => 'Serial',
      'showID' => 'ID',
      'showParcelID' => 'Parcel ID',
      'showStatus' => 'Status',
      'showItems' => 'Items',
      'showCreatedAt' => 'Created At',
      'showPaymentState' => 'Payment State',
      'showPaymentType' => 'Payment Type',
      'showPrice' => 'Price',
      'showQst' => 'PST',
      'showGst' => 'GST/HST',
      'showTotal' => 'Total',
      'showTrackNumber' => 'Track number',

    ];
  }

}
