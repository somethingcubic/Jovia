<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'product/index',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2OqGCMgTLto6E4exqYEEveJMwtcQw9-I',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
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
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com',
                'username' => '568452193@qq.com',
                'password' => 'awcfaywsmngebdgi',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
        'Ecom' => [
            'class' => 'app\common\components\Ecom',
            'client_id' => 'AW-cCf7iW-WuVJX_O_RmvPwnGi5yjga1kcr9eK85fbbOsNy3fdQGhSQpoYnfycmwbtNc15cOaoGCz6Nr',
            'client_secret' => 'EE43JSGI6oqsJWz91XDSmlFue01WZ4Nvo0h-SCpw4SRjQQA5BzGNdKN27Acsn6KDxP8S5ERV2FmVaImQ',
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
        'assetManager' => [
            'linkAssets' => true,
        ],
        /*
          'urlManager' => [
          'enablePrettyUrl' => true,
          'showScriptName' => false,
          'rules' => [
          ],
          ],
         */
    ],
    'params' => $params,
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
    ];
    $config['modules']['admin'] = [
        'class' => 'app\modules\admin',
    ];
}

return $config;
