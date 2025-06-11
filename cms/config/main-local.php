<?php
    $config = [
        'components' => [
            'request' => [
                'cookieValidationKey' => 'Q_4bzypHNGsQ0cgMe2KMkkp__b-Iqfv4',
            ],
        ],
    ];

   
    if (!YII_ENV_TEST) {
        // configuration adjustments for 'dev' environment
        $config['bootstrap'][]      = 'debug';
        $config['modules']['debug'] = [
            'class' => 'yii\debug\Module',
           'allowedIPs' => ['180.148.142.233'],
        ];
        $config['bootstrap'][]    = 'gii';
        $config['modules']['gii'] = 'yii\gii\Module';
    }


    return $config;
