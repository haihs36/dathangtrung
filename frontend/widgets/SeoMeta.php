<?php
    namespace frontend\widgets;

    class SeoMeta extends \yii\base\Widget
    {

        public $seo;
        public $setting;

        public function init()
        {
            parent::init();
            $this->setting = \Yii::$app->controller->setting;

            if (empty($this->seo)) {
                $this->seo['title']          = isset($this->setting['seo_title']) ? $this->setting['seo_title'] : $this->setting['site_name'];
                $this->seo['description']    = isset($this->setting['seo_description']) ? $this->setting['seo_description'] : '';
                $this->seo['keywords']       = isset($this->setting['seo_keywords']) ? $this->setting['seo_keywords'] : '';
                $this->seo['og:title']       = isset($this->setting['seo_og_title']) ? $this->setting['seo_og_title'] : '';
                $this->seo['og:description'] = isset($this->setting['seo_og_description']) ? $this->setting['seo_og_description'] : '';
                $this->seo['og:site_name']   = isset($this->setting['seo_og_site_name']) ? $this->setting['seo_og_site_name'] : $this->setting['site_name'];
                $this->seo['og:image']       = ['itemprop'=>"thumbnailUrl",'content'=>$this->setting['seo_og_image']];
                $this->seo['og:image:width']  = 198;
                $this->seo['og:image:height'] = 40;
                $this->seo['author']      = $this->setting['site_name'];
                $this->seo['og:url']      = \Yii::$app->params['baseUrl'];
            }

            $this->seo['og:image']        = isset($this->seo['og:image']) ? $this->seo['og:image'] : '';
            $this->seo['fb:app_id']       = isset($this->seo['fb:app_id']) ? $this->seo['fb:app_id'] : $this->setting['fb:app_id'];
            $this->seo['og:type']         = isset($this->seo['og:type']) ? $this->seo['og:type'] : $this->setting['og:type'];
            $this->seo['og:locale']       = isset($this->setting['og:locale']) ? $this->setting['og:locale'] : 'vi_VN';
            $this->seo['copyright']       = isset($this->setting['seo_copyright']) ? $this->setting['seo_copyright'] : $this->setting['site_name'];

        }

        public function run()
        {
            if ($this->seo) {
                foreach ($this->seo as $key => $value) {
                    if (empty($value)) continue;
                    if ($key == 'title') {
                        // $siteName = ' - '.$this->setting['site_name'];
                        // if(\Yii::$app->requestedRoute =='site/index'){
                        //     $siteName = '';
                        // }
                        \Yii::$app->view->title = $value;
                    }elseif (in_array($key, self::argProperty())) { //property
                        $data_set = [];
                        $data_set['property'] = $key;
                        if (is_array($value)) {
                            $data_set = array_merge($data_set, $value);
                        } else {
                            $data_set['content'] = $value;
                        }
                        \Yii::$app->view->registerMetaTag($data_set);
                    }elseif (in_array($key, self::argLink())){
                        $data_set = [];
                        $data_set['rel'] = $key;
                        if (is_array($value)) {
                            $data_set = array_merge($data_set, $value);
                        }
                        \Yii::$app->view->registerLinkTag($data_set);
                    }
                    else {
                        $data_set = [];
                        $data_set['name'] = $key;
                        if (is_array($value)) {
                            $data_set = array_merge($data_set, $value);
                        } else {
                            $data_set['content'] = trim($value);
                        }
                        \Yii::$app->view->registerMetaTag($data_set);
                    }
                }

            }
            return true;
        }

        public static function argLink()
        {
            return [
               'canonical',
                'alternate'
            ];
        }

        public static function argProperty()
        {
            return [
                'og:url',
                'og:image',
                'og:title',
                'og:description',
                'og:type',
                'og:site_name',
                'article:tag',
                'fb:app_id',
                'og:locale',
            ];
        }
    }