<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12/15/15
 * Time: 8:35 AM
 */

namespace common\components;


use yii\web\UrlRule;

class LanguageUrlRule extends UrlRule
{
    public function init()
    {
        if ($this->pattern !== null) {
            $this->pattern = '<language>/' . $this->pattern;
            // for subdomain it should be:
            // $this->pattern =  'http://<language>.example.com/' . $this->pattern,
        }
        $this->defaults['language'] = \Yii::$app->language;
        parent::init();
    }
}