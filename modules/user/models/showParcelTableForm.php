<?php

namespace app\modules\user\models;

use Yii;


class ShowParcelTableForm extends User
{
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
      'showQst' => 'Qst',
      'showGst' => 'Gst',
      'showTotal' => 'Total',

    ];
  }

}
