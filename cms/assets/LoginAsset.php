<?php

namespace cms\assets;

use yii\web\AssetBundle;

class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'bootstrap/css/bootstrap.min.css',
        'font-awesome/css/font-awesome.min.css',
        'css/login.css',
    ];

    public $js = [
        'bootstrap/js/bootstrap.min.js',
        'js/users.js',
    ];
    public $depends = [
//        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];

    /* public function init()
     {
         parent::init();
         // resetting BootstrapAsset to not load own css files
         \Yii::$app->assetManager->bundles['yii\\bootstrap\\BootstrapAsset'] = [
             'css' => []
         ];
     }*/
}
