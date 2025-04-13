<?php
return [
    'class' => 'yii\web\UrlManager',
    'showScriptName' => false, // Disable index.php
    'enablePrettyUrl' => true, // Disable r= routes
    'suffix' => '',
    'rules' => [
        [
            'pattern' => '/',
            'route' => 'site/index',
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => [
                'api/app'
            ],
        ],

        //api
        'get-config' => 'api/app/config',
        'dat-hang/add' => 'api/app/add-cart',
        'insert-cart' => 'api/app/insert-cart',
        'list-cart' => 'api/app/updatde-cart',
        'api/orders/list-cart' => 'api/orders/list-cart',

        'add-cart' => 'api/app/add-cart',
        'checking' => 'api/app/checking',

        // bo sung
        'api/refresh'          => 'api/app/refresh',
        'api/search'          => 'api/app/search',
        'api/init'          => 'api/app/init',

        'getdata' => 'api/getdata',
        'applogin' => 'api/applogin',
        'update-sms' => 'ajax/updatesms',
        'xac-nhan-dat-coc' => 'ajax/deposit',
        'lien-he' => 'site/contact',
        /*ajax*/
        'tim-kiem' => 'site/search',
        'tim-kiem-ma' => 'site/searchcode',
        //'get-config'                          => 'api/config',
        'get-info' => 'api/get-info',
        'get-cart' => 'site/get-cart',
        'dat-hang-nhanh' => 'ordersfast/create',
        /*chat*/
        'up-file' => 'ajax/upfile',
        'chat-count' => 'ajax/chat-count',
        'chat-status' => 'ajax/chat-status',
        'update-chat' => 'ajax/chat-message',
        'get-currency' => 'ajax/get-currency',
        'remove-image/<id:\d+>' => 'shipper/clear-image',
        /*end*/
        'get-message-<id:\d+>' => 'ajax/messages',
        'sms-all' => 'sms/index',
        'sms-view-<id:\d+>' => 'sms/view-messages',
        'import-excel' => 'ajax/importexcel',
        'get-district' => 'ajax/district',
        'phi-kiem-dem' => 'ajax/free-counting',
        'ajax-check' => 'ajax/checkform',
        'deposit-all' => 'ajax/deposit-all',
        'get-exchange-rate' => 'ajax/get-exchange-rate',
        'delete-order-item' => 'ajax/delete-order-item',
        'delete-product-<id:\d+>' => 'orders-detail/delete',
        'shop-cart-delete' => 'ajax/shop-cart-delete',
        'clear-image/<id:\d+>' => 'orders/clear-image',
        /*khieu nai*/
        'danh-sach-khieu-nai-<status:\d+>' => 'complain/index', //get data by status
        'danh-sach-khieu-nai' => 'complain/index',
        'chi-tiet-khieu-nai-<id:\d+>' => 'complain/view',
        'khieu-nai-<id:\d+>' => 'complain/create',
        /*thanh toan ho*/
        'danh-sach-thanh-toan-ho' => 'paymentsupport/index',
        'thanh-toan-ho-<id:\d+>' => 'paymentsupport/view',
        'tao-thanh-toan-ho' => 'paymentsupport/create',
        'huy-thanh-toan-ho' => 'paymentsupport/disabled',
        'xu-ly-thanh-toan-ho' => 'paymentsupport/payment',
        /*don hang*/
        'don-hang/export-<id:\d+>' => 'orders/export',
        'don-hang-<status:\d+>' => 'orders/index',
        'don-hang' => 'orders/index',
        'thong-bao' => 'site/notifycation',
        'kien-hang/da-ship' => 'orders/shipped',
        'kien-hang/dang-cho-ship' => 'orders/shipping',
        'don-hang/kien-hang' => 'orders/package',
        'don-hang-view-<id:\d+>' => 'orders/view',
        'don-hang-edit-<id:\d+>' => 'orders/edit',
        'xoa-don-hang-<id:\d+>' => 'orders/delete',
        'gio-hang' => 'orders/cart',
        'dat-hang' => 'orders/create',
        'update-cart' => 'ajax/update-cart',
        'tracking' => 'orders/tracking',
        'update-cart-shop' => 'ajax/update-cart-shop',
        'tao-don-hang-excel' => 'orders/fileexcel',
        // 'dat-hang/add'                        => 'api/add-cart',
        'don-ki-gui' => 'shipper/index',
        'chinh-sua-san-pham-<id:\d+>' => 'orders-detail/update',
        'tao-don-ky-gui' => 'shipper/create',
        /*nguoi dung*/
        'thong-tin-ca-nhan' => 'user/info',
        'dashboard' => 'user/dashboard',
        'rut-tien' => 'account-transaction/desc-acount',
        'lich-su-giao-dich-<type:\d+>' => 'account-transaction/history',
        'lich-su-giao-dich' => 'account-transaction/history',
        'thanh-vien/dia-chi-giao-hang' => 'addressshipping/index',
        //'dat-hang'                       => 'user/order',
        'login' => 'site/login',
        'logout' => 'site/logout',
        'login/<authclient:facebook|google+>' => 'site/auth',
        'register' => 'site/signup',
        'forgot-password' => 'site/forgot-password',
        'reset-password' => 'site/reset-password',
        'active/acount/<token:[\w-]+>' => 'site/activate',
        'thay-doi-mat-khau' => 'user/change-password',
        /*detail*/
        '<slug:[\w-]+>-<id:\d+>' => 'articles/detail',
        '<slug:[\w-]+>/trang-<page:\d+>' => 'articles/catenews',
        '<slug:[\w-]+>' => 'articles/catenews',

        '<controller:\w+>/<id:\d+>' => '<controller>/view',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
        'module/<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
    ],
];