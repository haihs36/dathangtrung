<?php
    $params = array_merge(
        require(__DIR__ . '/../../common/config/params.php'),
        require(__DIR__ . '/params.php')
    );
    return [
        'id' => 'app-frontend',
        'basePath' => dirname(__DIR__),
        //       122 'bootstrap'           => ['log'],
        'controllerNamespace' => 'frontend\controllers',
        'components' => [
            'request' => [
                'class' => 'common\components\Request',
                'web' => '/frontend/web',
                'cookieValidationKey' => '#$^(*$^&()5689@^^#9e4BlIUVKS8jH1mCIEsg9ZTk9EoUV%ffn&^%$&&%$gf',
                'parsers' => [
                    'application/json' => 'yii\web\JsonParser',
                ],
                'enableCsrfValidation' => false,
               // 'enableCsrfCookie' => false,
            ],
            'user' => [
                'identityClass' => 'common\models\Custommer',
                'enableAutoLogin' => true,
                'authTimeout' => 3600, // auth expire
            ],

            'session' => [
                'class'        => 'yii\web\Session',
                //'cookieParams' => ['lifetime' => 24 *60 * 60],//2 week
                'cookieParams' => [
                    'lifetime' => 24 * 60 * 60,
                    'httpOnly' => true,
                    'secure'   => true,
                    'path'     => '/;SameSite=None'
                ],
                'timeout'      => 3600 * 24, //session expire 30'
                'useCookies'   => true,
                'name'         => 'trungvf',
                // 'savePath' => sys_get_temp_dir(),
            ],

            'urlManager' => require(__DIR__ . '/urlManager.php'),
            'errorHandler' => [
                'errorAction' => 'site/error',
            ],
        ],

        'modules' => [
            'gridview' => [
                'class'          => 'kartik\grid\Module',
                'downloadAction' => 'gridview/export/download',
            ],
            'api' => [
                'class' => 'frontend\modules\api\Module'
            ],
           
        ],
        'params' => $params,
    ];
