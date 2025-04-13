<?php

    namespace frontend\widgets;


    use cms\models\TbNews;
    use common\components\CommonLib;
    use yii\base\Widget;

    class NewsHome extends Widget
    {

        const LIMIT = 5;

        public function init()
        {
            parent::init();
        }

        public function run()
        {
            parent::run();
            $data = \cms\models\TbNews::getArticleHome(self::LIMIT);

            return $this->render('_newsHome',[
                'data' => $data
            ]);
        }
    }