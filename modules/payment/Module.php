<?php

namespace app\modules\payment;

use Yii;


/**
 * payment module definition class
 */
class Module extends \yii\base\Module
{

  /**
   * @inheritdoc
   */
  public $controllerNamespace = 'app\modules\payment\controllers';

  /**
   * @setConfig
   * _apiContext in init() method
   */
  public function init()
  {

  }
}
