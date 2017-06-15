<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            //修改user主键路径
            'identityClass' => 'backend\models\User',
            'loginUrl'=>['user/login'],
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

        'qiniu'=>[
            'class'=>\backend\components\Qiniu::className(),
            'up_host' => 'http://up.qiniu.com',
            'accessKey'=>'CDoTHCw-z8LSDyj6Rek7bBTeOcaWs71_xDoN7LU0',
            'secretKey'=>'BtHUrYN_lCFJ90ZZvZf1NdbFhKpoV55hDdUsermA',
            'bucket'=>'yii2',
            'domain'=>'http://or9rwgf8b.bkt.clouddn.com',
        ],

    ],
    'params' => $params,
];
