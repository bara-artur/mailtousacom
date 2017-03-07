<?php

namespace app\modules\amazon;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * payment module definition class
 */
class Module extends \yii\base\Module
{

  public $config;
  public $mode;

  public $controllerNamespace = 'app\modules\amazon\controllers';

  /**
   * @setConfig
   * _apiContext in init() method
   */
  public function init()
  {
    $config = [
      'version' => 'latest',
      'region'  => 'us-east-1'
    ];
    $this->config=ArrayHelper::merge($config,$this->config);
  }
}
