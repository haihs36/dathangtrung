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
            'captcha' => [
                'class' => CaptchaAction::className(),
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
     

       /* $url= 'https://thietkewebos.com/thong-bao.html';
       $ch = curl_init($url);      
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
        curl_setopt($ch, CURLOPT_GSSAPI_DELEGATION, CURLGSSAPI_DELEGATION_FLAG);    
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_GSSNEGOTIATE);    
        curl_setopt($ch, CURLOPT_USERPWD, ":");  
        $result = curl_exec($ch);  
        $json = json_decode($result, true);  
        curl_close($ch);  
      


        pr($json);die;*/


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
