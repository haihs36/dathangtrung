<?php
    /**
     * Created by PhpStorm.
     * User: HAIHS
     * Date: 3/17/2018
     * Time: 12:49 AM
     */

    namespace frontend\widgets;


    use common\components\CommonLib;
    use yii\base\Widget;

    class Sidebar extends Widget
    {
        public $category_id;

        public function run()
        {
            parent::run(); // TODO: Change the autogenerated stub
            $menuList  = \common\components\CommonLib::getAllMenu();
            if(is_null($this->category_id)){
                $this->category_id = 68;
            }
            if ($this->category_id && isset($menuList['data'][$this->category_id]) && $menuList['data'][$this->category_id]) {
                $submenu = $menuList['data'][$this->category_id];
            }

            //get top news
            $articleNewMost = \cms\models\TbNews::getArticleNewmost(5);

            return $this->render('_sidebar',[
                'submenu' => isset($submenu) ? $submenu: null,
                'articleNewMost' => $articleNewMost,
            ]);
        }
    }