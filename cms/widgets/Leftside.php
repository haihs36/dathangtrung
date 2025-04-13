<?php
namespace cms\widgets;
use common\components\CommonLib;
use yii\base\Widget;

class Leftside extends Widget
{
    public $menus;
    public $menuAll;


    public function run()
    {
        $menuAll  = \common\components\CommonLib::getAllMenu();
        $menuAdmin = $menuAll['data'][5];

        $roleID = \Yii::$app->user->identity->role;
        $arrExtent = [];

        switch ($roleID){
            case WAREHOUSETQ: //nhan vien kho tq
                $arrExtent = [
                    'message/index','menu/index','payment/index','lo/index','customer/index','shipper/index','history/index',
                    'orders/index','ordercomplain/index','chart/index','system/index','news/index','complain/index','carousel/index',
                    'bank/index','complain-type/index','user/index', 'category/index','hotlink/index', 'setting/index','support/index',
                    'language/index','supplier/index','user/index','transfercode/index','account-transaction/index','vdt',
                    'kg','deposit','service','province','check',
                ];

                break;

            case WAREHOUSE: //nhan vien kho ;$k = 15 don hang
                $arrExtent = [
                    'message/index','menu/index','customer/index','orders/index','shipper/index','ordercomplain/index','orders/approve','chart/index','system/index','news/index','complain/index','carousel/index','bank/index','complain-type/index',
                    'user/index', 'category/index','hotlink/index', 'setting/index','support/index', 'language/index',
                    'supplier/index','user/index',
                    'kg','deposit','service','province','check'
                ];

                /*if ($menuAdmin) {
                    foreach ($menuAdmin as $k => $item) {
                        if (in_array($item['redirect'], $arrExtent))
                            continue;
                        $menus[$k] = $item;
                    }
                }*/
                break;
            case STAFFS: //nhan vien dat hang
                $arrExtent = [
                    'message/index','lo/index','menu/index','customer/index','shipper/index','ordercomplain/index','chart/index','system/index','news/index','carousel/index','bank/index','complain-type/index',
                    'user/index', 'category/index','hotlink/index', 'setting/index','support/index', 'language/index',
                    'supplier/index','user/index','shipping/barcode','payment/index','kho','account-transaction/index','vdt',
                    'kg','deposit','service','province','check'
                ];
                break;

            case BUSINESS: //nhan vien kd
                $arrExtent = [
                    'user','shipping/barcode','payment/index','menu/index','shipping','system/index','news/index','carousel/index','bank/index',
                    'user/index', 'category/index','hotlink/index', 'setting/index','support/index', 'language/index',
                    'supplier/index','user/index','kg','deposit','service','province','check'
                ];


                break;
        }

        //get menu parent and child
        $menus = [];
        if ($menuAdmin) {


            foreach ($menuAdmin as $k => $item) {
                if ($item['status'] == 0) continue;

                if (in_array(trim($item['redirect'],'/'), $arrExtent) || in_array($item['control'],$arrExtent))
                    continue;


                $menus[$k] = $item;
                if(isset($menuAll['data'][$item['category_id']])){
                    $chilMenu = $menuAll['data'][$item['category_id']];
                    $submenu = [];
                    foreach ($chilMenu as $key=> $value){
                        if (in_array(trim($value['redirect'],'/'), $arrExtent) || in_array($item['control'],$arrExtent))
                            continue;

                        $submenu[] = $value;
                    }

                    $menus[$k]['child'] = $submenu;
                }
            }
        }

        return $this->render('menu_left', [
            'menus'    => isset($menus) ? $menus : $menuAdmin,
            'menuAll' => $menuAll,
        ]);

    }

}