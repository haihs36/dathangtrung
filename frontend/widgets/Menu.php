<?php
    namespace frontend\widgets;


    use common\components\CommonLib;
    use yii\base\Widget;

    class Menu extends Widget
    {

        public function init()
        {
            parent::init();
        }

        public function run()
        {
          /*  $menuList = \Yii::$app->controller->menus;
            $data    = $menuList['data'][6];*/
            return $this->render('_menu', [
                /*'data' => $data,
                'menuList' => $menuList,*/
            ]);
        }
    }