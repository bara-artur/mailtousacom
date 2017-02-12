<?php

namespace app\modules\payment;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * payment module definition class
 */
class Module extends \yii\base\Module
{

  public $clientId;
  public $clientSecret;
  public $baseUrl;
  public $config;
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
    $this->config=ArrayHelper::merge(
      [
        'mode'                      => 'sandbox', // development (sandbox) or production (live) mode
        'http.ConnectionTimeOut'    => 30,
        'http.Retry'                => 1,
        'log.LogEnabled'            => YII_DEBUG ? 1 : 0,
        'log.FileName'              => Yii::getAlias('@runtime/logs/paypal.log'),
        'log.LogLevel'              => 'fine',
        'validation.level'          => 'log',
        'cache.enabled'             => 'true',
        'currency'                  =>  "USD",
      ],$this->config);

    if($this->config['mode']=='sandbox'){
      $this->clientId='AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS';
      $this->clientSecret='EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL';
    }

    // Set file name of the log if present
    if (isset($this->config['log.FileName'])
      && isset($this->config['log.LogEnabled'])
      && ((bool)$this->config['log.LogEnabled'] == true)
    ) {
      $logFileName = \Yii::getAlias($this->config['log.FileName']);
      if ($logFileName) {
        if (!file_exists($logFileName)) {
          if (!touch($logFileName)) {
            throw new ErrorException('Can\'t create paypal.log file at: ' . $logFileName);
          }
        }
      }
      $this->config['log.FileName'] = $logFileName;
    }
  }
}
