<?php
    return [
        'components' => [
            'db'     => require(__DIR__ . '/db.php'),
            'mailer' => [
                'class'            => 'yii\swiftmailer\Mailer',
                'transport'        => [
                    'class'      => 'Swift_SmtpTransport',
                    'host'       => 'smtp.gmail.com',
                    'username'   => 'testmail.online24h@gmail.com',
                    'password'   => 'dvg@2017',
                    'port'       => '587',
                    'encryption' => 'tls',
                ],
                'viewPath'         => '@common/mail',
                // send all mails to a file by default. You have to set
                // 'useFileTransport' to false and configure a transport
                // for the mailer to send real emails.
                'useFileTransport' => false,
            ],
        ],

    ];
