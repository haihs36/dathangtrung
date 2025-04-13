<?php
namespace frontend\widgets;

use cms\models\TbCarousel;
use yii\base\Widget;

class Slider extends Widget
{
    public $data = [];

    public function init()
    {
        parent::init();

       /* $cache = \Yii::$app->cache;
        $key = 'Slider-All';
        if(($this->data = $cache->get($key)) === false) {
            $this->data   = $this->data = TbCarousel::find()->select(['title','image','link'])->status(TbCarousel::STATUS_ON)->sort()->asArray()->all();
            $cache->set($key, $this->data, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }*/
    }

    public function run()
    {
        return $this->render('_slider', [
//            'data' => $this->data
        ]);
    }

}