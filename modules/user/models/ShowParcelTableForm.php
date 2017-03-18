<?php

namespace app\modules\user\models;

use Yii;


class ShowParcelTableForm extends User
{
  public $parcelTableSerial= 0x0001;
  public $parcelTableID= 0x0002;
  public $parcelTableStatus= 0x0004;
  public $parcelTableCreatedAt= 0x0008;
  public $parcelTablePaymentState= 0x0010;
  public $parcelTablePaymentType= 0x0020;
  public $parcelTablePrice= 0x0040;
  public $parcelTableQst= 0x0080;
  public $parcelTableGst= 0x0100;
  public $parcelTableTotal= 0x0200;

  public $showSerial;
  public $showID;
  public $showStatus;
  public $showCreatedAt;
  public $showPaymentState;
  public $showPaymentType;
  public $showPrice;
  public $showQst;
  public $showGst;
  public $showTotal;

 public function getAllFlags(){
   $allFlags = 0;
   if ($this->showSerial) $allFlags|=$this->parcelTableSerial;
   if ($this->showID) $allFlags|=$this->parcelTableID;
   if ($this->showStatus) $allFlags|=$this->parcelTableStatus;
   if ($this->showCreatedAt) $allFlags|=$this->parcelTableCreatedAt;
   if ($this->showPaymentState) $allFlags|=$this->parcelTablePaymentState;
   if ($this->showPaymentType) $allFlags|=$this->parcelTablePaymentType;
   if ($this->showPrice) $allFlags|=$this->parcelTablePrice;
   if ($this->showQst) $allFlags|=$this->parcelTableQst;
   if ($this->showGst) $allFlags|=$this->parcelTableGst;
   if ($this->showTotal) $allFlags|=$this->parcelTableTotal;
   return $allFlags;
 }

  public function __construct($parcelTableOptions = 0xffff)
  {
    if ($parcelTableOptions == -1) {
      $this->showSerial = null;
    } else {
      $this->showSerial = $parcelTableOptions & $this->parcelTableSerial;
      $this->showID = (($parcelTableOptions & $this->parcelTableID) != 0);
      $this->showStatus = (($parcelTableOptions & $this->parcelTableStatus) != 0);
      $this->showCreatedAt = (($parcelTableOptions & $this->parcelTableCreatedAt) != 0);
      $this->showPaymentState = (($parcelTableOptions & $this->parcelTablePaymentState) != 0);
      $this->showPaymentType = (($parcelTableOptions & $this->parcelTablePaymentType) != 0);
      $this->showPrice = (($parcelTableOptions & $this->parcelTablePrice) != 0);
      $this->showQst = (($parcelTableOptions & $this->parcelTableQst) != 0);
      $this->showGst = (($parcelTableOptions & $this->parcelTableGst) != 0);
      $this->showTotal = (($parcelTableOptions & $this->parcelTableTotal) != 0);
    }
  }
  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['showSerial','showID','showStatus','showCreatedAt','showPaymentState','showPaymentType',
        'showPrice','showQst','showGst','showTotal'], 'safe']
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
      'showStatus' => 'Status',
      'showCreatedAt' => 'Created At',
      'showPaymentState' => 'Payment State',
      'showPaymentType' => 'Payment Type',
      'showPrice' => 'Price',
      'showQst' => 'PST',
      'showGst' => 'GST/HST',
      'showTotal' => 'Total',

    ];
  }

}
