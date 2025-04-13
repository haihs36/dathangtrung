<?php
    namespace common\helpers;

    use common\components\CommonLib;
    use Yii;
    use yii\helpers\Inflector;
    use yii\helpers\StringHelper;

    class Data
    {
        public static function cache($key, $duration, $callable)
        {
            $cache = Yii::$app->cache;
            $data  = $cache->get($key);
            if (!$data) {
                $data = $callable();
                if ($data) {
                    $cache->set($key, $data, $duration);
                }
            }
            if ($cache->exists($key)) {
                $data = $cache->get($key);
            } else {
                $data = $callable();
                if ($data) {
                    $cache->set($key, $data, $duration);
                }
            }
            return $data;
        }

        public static function getLocale()
        {
            return strtolower(substr(Yii::$app->language, 0, 2));
        }
    }