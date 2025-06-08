<?php

use yii\helpers\VarDumper;

if (!function_exists('dd')) {
    function dd($var, $depth = 10)
    {
        VarDumper::dump($var, $depth, true);
        exit;
    }
}

if (!function_exists('dump')) {
    function dump($var, $depth = 10)
    {
        VarDumper::dump($var, $depth, true);
    }
}
