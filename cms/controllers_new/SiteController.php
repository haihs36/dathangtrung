<?php

namespace cms\controllers;

use common\components\CaptchaAction;
use common\components\Controller;
use common\models\TbCustomers;
use common\models\TbOrders;
use common\models\TbOrdersDetail;
use common\models\TbProduct;
use Yii;


class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
     

        $role = \Yii::$app->user->identity->role;

        switch ($role){
            case WAREHOUSE:
            case WAREHOUSETQ:
                return $this->redirect(['shipping/barcode']);
                break;
            case CLERK:
                return $this->redirect(['chart/index']);
                break;
            default:
                return $this->render('index');
                break;

        }
    }



    public function actionExport()
    {
        set_time_limit(100);
        //lấy quyền

        $offset = 0;
        $limit = 10000;

        //find all custommer
        $sql = 'SELECT c.id,c.username,c.fullname,c.email,c.phone,count(o.orderID) as total FROM tb_customers c 
                    INNER JOIN tb_orders o ON c.id = o.customerID
                    GROUP BY o.customerID
                    ORDER BY total DESC';
        $customer = TbCustomers::findBySql($sql)->asArray()->all();
        if(!empty($customer)){
            foreach ($customer as $k=>$info){
                $orders = TbOrders::find()->select('orderID')->where(['customerID'=>$info['id']])->asArray()->all();
                if(!empty($orders)){
                    $orderIds = array_column($orders,'orderID');
                    $orderDetail = TbOrdersDetail::find()->select('productID')->where(['orderID'=>$orderIds])->asArray()->all();
                    if(!empty($orderDetail)) {
                        $productIds= array_column($orderDetail, 'productID');
                        $productIds = array_unique($productIds);
                        $products = TbProduct::find()->select('productID,shopID,sourceName,name,quantity,unitPrice,image,link,color,size')->where(['productID'=>$productIds])->limit(10)->asArray()->all();

                        $customer[$k]['product'] = $products;
                    }
                }
            }
        }

        return $this->renderPartial('export',[
            'customer'=>$customer
        ]);


    }

}
