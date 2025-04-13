<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        ['home/css/layout.css', 'media' => 'all'],
      


    ];

    public $js = [
    
         'home/js/layout.js',

    ];
    public $depends = [
        /* 'yii\web\YiiAsset',
         'yii\bootstrap\BootstrapAsset',*/
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
