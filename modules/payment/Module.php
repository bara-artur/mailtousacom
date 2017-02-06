<?php

namespace app\modules\payment;

/**
 * payment module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\payment\controllers';

    //region API settings
    public $clientId;
    public $clientSecret;
    public $isProduction = false;
    public $currency = 'USD';
    public $config = [];
    /** @var ApiContext */
    private $_apiContext = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
