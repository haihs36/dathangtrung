<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'customer/bootstrap/css/bootstrap.min.css',
        'customer/css/font-awesome.min.css',
        'customer/css/login.css',
    ];

    public $js = [
         'customer/bootstrap/js/bootstrap.min.js',
         'customer/js/login.js',
    ];
    public $depends = [
//        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];

}
