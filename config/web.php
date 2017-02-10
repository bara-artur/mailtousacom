<?php

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
          'cache' => 'yii\caching\FileCache',
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                //закрываем пямой доступ к /user/user
                'user/default/<action>'=>'404',
                'user/user/<action>'=>'404',
                'user/user/<action>/<action2>'=>'404',
                //получение города по стране
                'city/get/<id:\d+>' => 'city/get',
                //Взаимодействия с пользователем на сайте
                '<action:(online|registration|logout|confirm|reset|resetpassword)>' => 'user/user/<action>',

                //закрываем прямой доступ к базовому контроллеру
                'site/<action>'=>'404',
                'site/<action>/<action2>'=>'404',
                //базовые страницы в основном контроллере
                '<action:(top|shop|about|blog|legends|mans|competitions|onlinehelp)>' => 'site/<action>',
                //Страница пользователя
                '<action:(user)>/<id:\d+>' => 'site/user/',
                '<action:(profile)>' => 'user/default/<action>',
                'address/<action>' => 'address/default/<action>'
            ],
        ],
    ],
    'params' => $params,
    'modules' => [
      'rbac' =>  [
        'class' => 'johnitvn\rbacplus\Module',
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
      'tariff' => [
        'class' => 'app\modules\tariff\Module',
      ],
      'state' => [
        'class' => 'app\modules\state\Module',
      ],
      'gridview' =>  [
        'class' => '\kartik\grid\Module'
        // enter optional module parameters below - only if you need to
        // use your own export download action or custom translation
        // message source
        // 'downloadAction' => 'gridview/export/download',
        // 'i18n' => []
      ]
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

return $config;
