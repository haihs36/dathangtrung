<?php
    return [
        'timeZone'   => 'Asia/Ho_Chi_Minh',
        'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
        'aliases' => [
            '@bower' => '@vendor/bower-asset',
            '@npm' => '@vendor/npm-asset',
        ],
        'components' => [
            'menuHelper' => [
                'class' => 'common\components\MenuHelper',
            ],
            'cache'      => [
                'class' => 'yii\caching\FileCache',
            ],
            'i18n'       => [
                'translations' => [
                    'frontend*' => [
                        'class'    => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@common/messages',
                    ],
                    'cms*'      => [
                        'class'    => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@common/messages',
                    ],
                ],
            ],

        ],
        //    'as beforeRequest' =>[
        //        'class' => 'common\behaviors\CheckLanguage'
        //    ],
        //set default language
        //    'language' => 'vi' //console: yii message/extract @common/config/i18n.php
    ];
