<?php
    namespace cms\assets;

    class FrontendAsset extends \yii\web\AssetBundle
    {
        public $sourcePath = '@cms/media';
        public $css
                           = [
                'css/frontend.css',
            ];
        public $js
                           = [
                'js/frontend.js'
            ];
        public $depends
                           = [
                /*  'yii\web\JqueryAsset',
                  'yii\bootstrap\BootstrapAsset',
                  'cms\assets\SwitcherAsset'*/
            ];
    }
