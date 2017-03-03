<?php

namespace app\modules\ebay;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * payment module definition class
 */
class Module extends \yii\base\Module
{

  public $config;
  public $mode;

  public $controllerNamespace = 'app\modules\ebay\controllers';

  /**
   * @setConfig
   * _apiContext in init() method
   */
  public function init()
  {
    if($this->mode=='sandbox') {
      $config = [
        'tradeUrl'=>'https://api.sandbox.ebay.com/ws/api.dll',
        'signinUrl' => 'https://signin.sandbox.ebay.com/ws/eBayISAPI.dll?SignIn&',
      ];
    }else{
      $config = [
        'tradeUrl'=>'https://api.ebay.com/ws/api.dll',
        'signinUrl' => 'https://signin.ebay.com/ws/eBayISAPI.dll?SignIn&',
      ];
    }
    $this->config=ArrayHelper::merge($config,$this->config[\Yii::$app->user->identity->ebay_account]);

  }
}
