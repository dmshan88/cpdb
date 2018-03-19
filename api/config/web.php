<?php

$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);
$db = array_merge(
    require __DIR__ . '/db.php',
    require __DIR__ . '/db-local.php'
);


$config = [
    'id' => 'yiicpdbapi',
    'basePath' => dirname(__DIR__),
    'vendorPath'=>__DIR__.'/../../vendor',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            // 'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'cookieValidationKey' => 'yiicpdbapi',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            /*'on beforeSend' => function ($event) {
                $response = $event->sender;
                if (!$response->isSuccessful) {
                    $response->data = [
                        'errcode' => 104,
                        'errmsg' => 'unknown error',
                    ];
                    $response->statusCode = 200;
                }
            },*/
        ],
        'defaultRoute' => 'cpdb',
        'db' => $db,
     ], 
    // 'defaultRoute' => 'fapan', 
    // 'defaultController' => 'index' 
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'sourceLanguage' => 'en-US',
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
