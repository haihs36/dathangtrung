<?php
    namespace frontend\controllers;

    use cms\models\TbNews;
    use common\components\CategoryModel;
    use common\components\CController;
    use common\components\CommonLib;
    use yii\helpers\Url;

    class ArticlesController extends CController
    {
        const LIMIT = 10;

        public function actionIndex()
        {

        }

        public function actionCatenews()
        {

            $slug = \Yii::$app->request->get('slug','tin-tuc');
            $catlist = CommonLib::getAllCate();
            $current_cat = (!empty($slug) && isset($catlist['all_slug'][$slug])) ? $catlist['all_slug'][$slug] : NULL;

            if (empty($current_cat))
                return CommonLib::redirectError();

            $calistID = [$current_cat['category_id']];
            if(isset($catlist['parent_id'][$current_cat['category_id']]) && $catlist['parent_id'][$current_cat['category_id']]) {
                $calistID = array_merge($calistID,$catlist['parent_id'][$current_cat['category_id']]);
            }

            $page = (int)\Yii::$app->request->get('page');
            if (!$page) $page = 1;

            $cache = \Yii::$app->cache;
            $key = 'Article-News-' . $slug . $page;
            $result = $cache->get($key);
            if ($result === false) {
                $result = TbNews::getArticleByCateId($calistID, self::LIMIT);
                $cache->set($key, $result, \Yii::$app->params['CACHE_TIME']['HOUR']);
            }

            return $this->render('cate-news', [
                'current_cat' => $current_cat,
                'data'        => $result['data'],
                'pages'       => $result['pages'],
                'slug'        => $slug
            ]);
        }


        public function actionDetail()
        {
            $slug = \Yii::$app->request->get('slug');
            $id = \Yii::$app->request->get('id');
            if (!$id)
                return CommonLib::redirectError();

            $data = TbNews::getArticleDetail($id);
            if (!$data)
                return CommonLib::redirectError();

            //Neu link sai dá 301 ve link dúng
            $mainUrl = Url::toRoute(['articles/detail', 'slug' => $data->slug, 'id' => $data->news_id]);
            $currentUrl = \Yii::$app->request->url;
            if ($mainUrl !== $currentUrl) {
                return $this->redirect($mainUrl, 301);
            }

            $ortherNews = TbNews::getNewsRelated($id, $data['category_id'], 6);

            return $this->render('detail', [
                'data'       => $data,
                'ortherNews' => $ortherNews,
                'slug'       => $slug,
            ]);

        }
    }