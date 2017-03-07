<?php
use kartik\mpdf\Pdf;
use yii\helpers\Url;

$params = require(__DIR__ . '/params.php');
$personal = require(__DIR__ . '/personal.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
          'baseUrl' => '',
          'cookieValidationKey' => 'grejhthdxrthxdr',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/'],
            'on afterLogin' => function($event) {
                app\modules\user\models\User::afterLogin($event->identity->id);
            }
        ],
        'authManager' => [
          'class' => 'yii\rbac\DbManager',
         // 'cache' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
          'class' => 'yii\swiftmailer\Mailer',
          // send all mails to a file by default. You have to set
          // 'useFileTransport' to false and configure a transport
          // for the mailer to send real emails.
          'useFileTransport' => false,
          'transport' => $personal['MailTransport'],
          'messageConfig' => [
            //'from' => ['admin@website.com' => 'Admin'], // this is needed for sending emails
            'charset' => 'UTF-8',
          ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'pdf' => [
          'mode' => Pdf::MODE_UTF8,
          'class' => Pdf::classname(),
          'format' => Pdf::FORMAT_A4,
          'orientation' => Pdf::ORIENT_PORTRAIT,
          'destination' => Pdf::DEST_BROWSER,
          // refer settings section for all configuration options
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                //закрываем пямой доступ к /user/user
                'user/default/<action>'=>'404',
                'user/user/<action>'=>'404',
                'user/user/<action>/<action2>'=>'404',
                'ebay/default/<action>/<action2>'=>'404',
                'ebay/default/<action>'=>'404',
                '/site/<action>'=>'404',
                '/'=>'/site/index',
                //получение города по стране
                'city/get/<id:\d+>' => 'city/get',
                //Взаимодействия с пользователем на сайте
                '<action:(online|registration|logout|confirm|reset|resetpassword)>' => 'user/user/<action>',

                //закрываем прямой доступ к базовому контроллеру
                //'site/<action>'=>'404',
                'site/<action>/<action2>'=>'404',
                //базовые страницы в основном контроллере
                '<action:(top|shop|about|blog|legends|mans|competitions|onlinehelp)>' => 'site/<action>',
                //Страница пользователя
                '<action:(user)>/<id:\d+>' => 'site/user/',
                '<action:(profile)>' => 'user/default/<action>',

                'address/<action>'=>'address/default/<action>',
                'order/<action>'=>'order/default/<action>',

                'orderElement/<action>'=>'orderElement/default/<action>',
                'orderElement/create/<id:\d+>'=>'orderElement/default/create',

                'orderInclude/<action>'=>'orderInclude/default/<action>',
                'orderInclude/create-order/<id:\d+>'=>'orderInclude/default/create-order2/',
                'orderInclude/<action:border-form|border-form-pdf>/<id:\d+>'=>'orderInclude/default/<action>/',


                'payment/<action:order>/<id:\d+>'=>'payment/default/<action>/',
                'payment/<action:finish>'=>'payment/default/<action>/',

                'ebay/<action:get-order|connection>/<id:\d+>'=>'ebay/default/<action>/',
                'ebay/<action:callback>'=>'ebay/default/<action>/',
            ],
        ],
    ],
    'params' => $params,
    'modules' => [
      'rbac' =>  [
        'class' => 'johnitv\rbacplus\Module',
        'userModelClassName'=>null,
        'userModelIdField'=>'id',
        'userModelLoginField'=>'username',
        'userModelLoginFieldLabel'=>null,
        'userModelExtraDataColumls'=>null,
        'beforeCreateController'=>function($route){
          return Yii::$app->user->can('rbac');
        },
        'beforeAction'=>null
      ],
      'user' => [
            'class' => 'app\modules\user\Module',
        ],
      'address' => [
            'class' => 'app\modules\address\Module',
      ],
      'payment' => [
        'class' => 'app\modules\payment\Module',
        'clientId'     => $personal['paypal_client_id'],
        'clientSecret' => $personal['paypal_client_secret'],
        'baseUrl' => $personal['site_url'].'payment/finish',
        //'isProduction' => false,
        // This is config file for the PayPal system
        'config'       => [
          'currency'=>"USD",
          'http.ConnectionTimeOut' => 30,
          'http.Retry'             => 1,
          'mode'                   => 'sandbox', // development (sandbox) or production (live) mode
          'log.LogEnabled'         => YII_DEBUG ? 1 : 0,
          'log.FileName'           => '@runtime/logs/paypal.log',
          'log.LogLevel'           => 'FINE', // 'FINE','INFO','WARN','ERROR';
        ]
      ],
      'ebay' => [
        'class'        => 'app\modules\ebay\Module',
        'mode'         =>'sandbox',
        'config'       => $personal['ebay']
      ],
      'tariff' => [
        'class' => 'app\modules\tariff\Module',
      ],
      'state' => [
        'class' => 'app\modules\state\Module',
      ],
      'order' => [
            'class' => 'app\modules\order\Module',
        ],
      'orderElement' => [
            'class' => 'app\modules\orderElement\Module',
        ],
      'orderInclude' => [
            'class' => 'app\modules\orderInclude\Module',
        ],
      'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
      'logs' => [
            'class' => 'app\modules\logs\Module',
        ],
    ]
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.*', '::1', '192.168.0.*', '192.168.1.*','31.202.224.*'],

    ];
}

\Yii::$container->set('skinka\widgets\gritter\AlertGritterWidget', ['enableIcon' => false]);

return $config;
