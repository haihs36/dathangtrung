<?php


function pr($str)
{
    echo "<pre>";
    print_r($str);
    echo "</pre>";
}


ini_set('display_errors', 1);
error_reporting(E_ALL);
 defined('YII_DEBUG') or define('YII_DEBUG', true);
 defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/aliases.php');
include_once __DIR__ . '/../../common/helpers/PHPExcel.php';
//include_once __DIR__ . '/../../common/helpers/recaptchalib.php';
include_once __DIR__ . '/../../common/config/constants.php';
$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);
$application = new yii\web\Application($config);
$application->run();
