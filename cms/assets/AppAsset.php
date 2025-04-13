<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cms\assets;

use yii\web\AssetBundle;


class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
//    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $css = [
        'css/system.css'
//        'bootstrap/css/bootstrap.min.css',
//        'font-awesome/css/font-awesome.min.css',
//        'css/dataTables.bootstrap.min.css',
//        'plugins/sweets/sweetalert.css',
//        'plugins/select2/select2.css',
//        'plugins/iCheck/minimal/blue.css',
//        'plugins/datepicker/datepicker3.css',
//        'css/skins/_all-skins.min.css',
//        'css/bootstrap-toggle.css',
//        'css/layout.css',


    ];

    public $js = [

         'js/system.js',

//        'plugins/select2/select2.js',
//        'js/moment-with-locales.js',
//        'plugins/datepicker/bootstrap-datepicker.js',
////        'plugins/sweets/sweetalert.js',
//        'plugins/iCheck/icheck.min.js',
//        'js/bootstrap-toggle.js',
//        'js/app.min.js',
//        'js/plugins.js',
//        'js/notify.js',
//        'js/chatbox.js',
//        'js/simple.money.format.js',
//        'js/users.js',
    ];

    public $depends = [
//        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
//        'cms\assets\SwitcherAsset',
    ];
}
