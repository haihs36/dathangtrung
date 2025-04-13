<?php
namespace common\behaviors;


use yii\base\Behavior;

class CheckLanguage extends Behavior {

    public function events(){
        return [
            \yii\web\Application::EVENT_BEFORE_REQUEST => 'checkLanguage'
        ];
    }

    public function checkLanguage(){
        if(\Yii::$app->getRequest()->getCookies()->has('lang')){
            \Yii::$app->language = \Yii::$app->getRequest()->getCookies()->getValue('lang');
        }
    }
} 