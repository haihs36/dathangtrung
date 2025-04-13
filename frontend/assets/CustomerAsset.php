<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class CustomerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
      'customer/css/system.css',


    ];

    public $js = [
     'customer/js/system.js',


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
