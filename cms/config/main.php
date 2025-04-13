<?php
$params     = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);
$urlManager = require(__DIR__ . '/urlManager.php');
use \yii\web\Request;

$baseUrl = str_replace('/web', '', (new Request)->getBaseUrl());
return [
    'id'                  => 'app-cms',
    'name'                => 'HsPanel',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'cms\controllers',
    //'bootstrap'           => ['log'],
    'modules'             => [
        'gridview' => [
            'class'          => 'kartik\grid\Module',
            'downloadAction' => 'gridview/export/download',
        ]
    ],
    'components'          => [
        /*'assetManager' => [
            'bundles' => [
                // you can override AssetBundle configs here
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => []
                ],
            ],
        ],*/
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset'                => [
                    'js' => []
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => []
                ],
                'yii\bootstrap\BootstrapAsset'       => [
//                        'css' => [],
                ],

            ],
        ],
        'urlManager'   => $urlManager,
        'user'         => [
            'identityClass'   => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie'  => [
                'name' => '_backendUser', // unique for backend
            ]
        ],
        'session'      => [
            'name'     => 'PHPBACKSESSID',
            'savePath' => sys_get_temp_dir(),
        ],
        /*'request'=>[
            'class' => 'common\components\Request',
            'web'=> '/cms/web',
            'adminUrl' => '/admin',
            'cookieValidationKey' => 'Q_4bzypHNGsQ0cgMe2KMkkp__b-Iqfv4',
            'csrfParam' => '_backendCSRF',
        ],*/

        'request' => [
            'baseUrl' => $baseUrl,
        ],
        /* 'log'          => [
             'traceLevel' => YII_DEBUG ? 3 : 0,
             'targets'    => [
                 [
                     'class'  => 'yii\log\FileTarget',
                     'levels' => ['error', 'warning'],
                 ],
             ],
         ],*/

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'as beforeRequest'    => [
        'class'        => 'yii\filters\AccessControl',
        'rules'        => [
            [
                'allow'   => true,
                'actions' => ['in'],
            ],
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
        'denyCallback' => function () {
            return Yii::$app->response->redirect(['sign/in']);
        },
    ],
    'params'              => $params,
     'modules' => [
        'gridview' => [
            'class'          => 'kartik\grid\Module',
            'downloadAction' => 'gridview/export/download',
        ],
        'coupons' => [
            'class' => 'cms\modules\coupons\Module',
        ],
    ],

];

