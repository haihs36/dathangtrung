<?php

return [
    'class' => 'yii\web\UrlManager',
    'showScriptName' => false, // Disable index.php
    'enablePrettyUrl' => true, // Disable r= routes
    'suffix' => '',
    'rules' => [
        '' => 'site/index',
        'login' => 'sign/in',
        'logout' => 'sign/logout',
        'get-ios' => 'ajax/getios',
        'shoot-barcode' => 'ajax/shoot-barcode',
        'update-barcode' => 'ajax/insert-barcode',
        'get-all-barcode' => 'ajax/get-consign-detail',
        'delete-consign-detail' => 'ajax/delete-consign-detail',
        'save-consign-detail' => 'ajax/save-consign-detail',
        'don-hang-view-<id:\d+>' => 'orders/view',
        'load-shop' => 'payment/load-shop',
        //'luu-ma-van-don'              => 'payment/insert-code',
        'save-ship-code' => 'transfercode/save-ship-code',
        'update-kg' => 'transfercode/updatekg',
        'del-code-ship' => 'transfercode/del-code-ship',
        'ajax-check'    => 'ajax/checkform',
        'upload-image' => 'ajax/upload-image',
        'update-bag' => 'ajax/updatebag',
        'up-file' => 'ajax/upfile',
        'chat-count' => 'ajax/chat-count',
        'chat-status' => 'ajax/chat-status',
        'update-chat' => 'ajax/chat-message',
        'gui-thong-bao-<id:\d+>' => 'ajax/send-sms',
        'thanh-toan' => 'payment/pay',
        'tra-hang-ky-gui' => 'payment/pay-ship',
        'phieu-xuat-<id:\d+>' => 'lo/update',
        'chi-tiet-phieu-<id:\d+>' => 'lo/view',

        
        '<controller:\w+>/<id:\d+>' => '<controller>/view',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    ],
];