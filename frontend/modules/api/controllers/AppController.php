<?php



namespace frontend\modules\api\controllers;



use common\components\CommonLib;

use common\models\LoginMember;

use common\models\TbCustomers;

use common\models\TbOrdersSession;

use frontend\modules\api\resources\UserResource;

use Yii;

use yii\filters\auth\CompositeAuth;

use yii\filters\auth\HttpBasicAuth;

use yii\filters\auth\HttpBearerAuth;

use yii\filters\auth\QueryParamAuth;

use yii\filters\Cors;

use yii\filters\VerbFilter;

use yii\rest\Controller;

use yii\web\Response;


class AppController extends Controller
{
    public $modelClass = UserResource::class;

    public function actions()
    {
        $action = parent::actions();

        unset($action['index']);
        unset($action['create']);
        unset($action['update']);
        unset($action['delete']);
    }


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'except' => ['config', 'checking'], //,'add-cart'

            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];

        return $behaviors;
    }

    const DOMAIN = 'dathangtrung.vn';

    public function checkToken(){
        $statusCode = 0;

        if (isset($_SERVER['HTTP_ACCEPT_MAIN'])) {
            $encodedToken = $_SERVER['HTTP_ACCEPT_MAIN']; 
            $decodedToken = base64_decode($encodedToken);    


            if(md5(self::DOMAIN) === md5($decodedToken)){
                $statusCode = 1;
            }
        }

        // Thiết lập giá trị trong header của phản hồi
        header('X-Code: '.$statusCode);
    }


    public function actionConfig()
    {

        $this->checkToken();

        Yii::$app->response->statusCode = 200;
         $setting = CommonLib::getSettingByName(['hotline', 'CNY']);
             

        $data = [
            'hotline' => $setting['hotline'],
            "exchange_rate" => (int)$setting['CNY'],
        ];

        $uerLogin = Yii::$app->session->get('uerLogin');
        if (!empty($uerLogin) && Yii::$app->user->isGuest) {
            Yii::$app->user->login($uerLogin, LoginMember::EXPIRE_TIME);
        }else{
            $user = Yii::$app->user->identity;
            Yii::$app->session->set('uerLogin',$user);
        }

         

        if (!Yii::$app->user->isGuest) {
            $token = Yii::$app->user->identity->access_token;
            header('X-Token: '.$token);
        }

        return $data;
    }



    public function actionChecking()
    {
        
        try {
            
            if (\Yii::$app->request->getIsPost()) {
                $item_id = (int)\Yii::$app->request->post('item_id');
                $website = \Yii::$app->request->post('website');
                $link = CommonLib::curlCoupon($item_id,$website);
                Yii::$app->response->statusCode = 200;
                
                return [
                    'link' => $link
                ];
            }
        } catch (\Exception $e) {
        }
        
        Yii::$app->response->statusCode = 442;
        
        return [
            'error' => 1,
        ];
    }



    public function actionAddCart()

    {

        try {


            $message = 'error';
              

            if (\Yii::$app->request->getIsPost()) {

                $totalSuccess = 0;

                $data = \Yii::$app->request->post();

                $arrData = [];

                $total_quantity = 0;



                if (isset($data['list_sku']) && !empty($data['list_sku'])) {

                    $data['shop_id'] = md5($data['shop_id']);

                    foreach ($data['list_sku'] as $v) {

                        $quantity =  !empty($v['quantity']) ? (int)$v['quantity'] : 0;



                        $price = !empty($v['price']) ? doubleval($v['price']) : 0;

                        $size =  !empty($v['size']) ? $v['size'] : '';

                        $color = !empty($v['color']) ? $v['color'] : '';

                        $image = !empty($v['image']) ? $v['image'] : '';

$name = !empty($v['name']) ? $v['name'] : '';
                        $data['title'] = !empty($data['title']) ? $data['title'] : $name;

                        if (!empty($v['name'])) {
                            $properties = explode(';', $v['name']);
                            $size = (empty($size) && !empty($properties[0])) ? $properties[0] : $size;
                            $color =  empty($color) && !empty($properties[1]) ? $properties[1] : $color;
                            //check co 3 thuoc tinh
                            if( count($properties) > 2){
                                $size =  $size .';'. $properties[2] ;
                            }                            
                        }


                        if (empty($image)) {
                            $image = !empty($data['image']) ? $data['image'] : '';
                        }

                        $note = !empty($data['note']) ? $data['note'] : '';



                        if ($price <= 0 && !empty(isset($v['price_ranges']))) {

                            $price_ranges = reset($v['price_ranges']);

                            $price = isset($price_ranges['price']) ? doubleval($price_ranges['price']) : 0;

                        }



                        $total_quantity += $quantity;

                        //set sku

                        $key = md5($data['shop_id'] . $data['id'] . $size . $color);



                        if (isset($arrData[$key])) {

                            $quantity = $arrData[$key]['quantity'] + $quantity;

                            $arrData[$key]['quantity'] = $quantity;

                        } else {

                            if (empty($size) && $v['name']) {

                                $properties = explode(';', $v['name']);

                                $size = !empty($properties[0]) ? $properties[0] : '';

                            }



                            $tmp = $data;

                            $tmp['size'] = $size;

                            $tmp['color'] = $color;

                            $tmp['image'] = $image;

                            $tmp['quantity'] = $quantity;

                            $tmp['price'] = $price;

                            $tmp['note'] = $note;



                            $arrData[$key] = $tmp;

                        }

                    }

                }



                if (!empty($arrData)) {

                    $setting = CommonLib::getSettingByName(['hotline', 'CNY']);

                    $customerID = \Yii::$app->user->id;

                    $customer = TbCustomers::findOne($customerID);

                    $CNY = CommonLib::getCNY($setting['CNY'], $customer->cny);



                    foreach ($arrData as $pkey => $value) {

                        //count quantity by shop

                        // $totalQty = TbOrdersSession::find()->where(['shop_id'=> $value['shop_id'], 'customerID'=>$customerID])->sum('quantity');

                        // $totalQty += $total_quantity;

                        // $totalShop = $arrTotalShop[$value['shop_id']] = $totalQty;



                        $model = new TbOrdersSession();

                        $model->customerID = $customerID;

                        $model->isCheck = 1;

                        $model->shop_id = $value['shop_id'];

                        $model->shop_name = !empty($value['shop_name']) ? $value['shop_name'] : $value['shop_id'];

                        $model->shop_address = !empty($value['shop_address']) ? $value['shop_address'] : '';

                        $model->source_site = $value['website'];

                        $model->shopProductID = $value['id'];

                        $model->title = $value['title'];

                        $model->link = \common\components\CommonLib::convertUrl($value['url']);

                        $image = !empty($data['image']) ? $data['image'] : '';

                        $model->image = !empty($value['image']) ? $value['image'] : $image ;

                        $model->quantity = $value['quantity'];

                        $model->noteProduct = $value['note'];

                        $model->size = (isset($value['size']) && !empty($value['size'])) ? trim($value['size']) : '';

                        $model->color = (isset($value['color']) && !empty($value['color'])) ? trim($value['color']) : '';

                        $priceInit = doubleval($value['price']);

                        $model->unitPrice = $priceInit; //tien tq

                        $model->md5 = $pkey;

                        $model->totalPrice = $model->unitPrice * $model->quantity; //tong tien TQ

                        $model->unitPriceVn = round($CNY * $model->unitPrice);

                        $model->totalPriceVn = round($model->unitPriceVn * $model->quantity); //tong tien TQ



                        if ($modelExits = TbOrdersSession::findOne(['md5' => $model->md5, 'customerID' => $model->customerID])) {

                            $modelExits->quantity += $model->quantity;

                            $modelExits->unitPrice = $model->unitPrice;

                            $modelExits->unitPriceVn = $model->unitPriceVn;

                            $modelExits->totalPrice = $modelExits->unitPrice * $modelExits->quantity; //tong tien TQ

                            $modelExits->totalPriceVn = $modelExits->unitPriceVn * $modelExits->quantity; //tong tien TQ

                            if (!empty($value['note'])) {

                                $modelExits->noteProduct = $modelExits->noteProduct . ', ' . $value['note'];

                            }

                            $modelExits->save(false);

                        } else {

                            $model->save(false);

                        }



                        $totalSuccess++;

                    }





                    Yii::$app->response->statusCode = 200;



                    return [
                        // 'totalSuccess' => $totalSuccess,
                        // 'totalProduct' => count($data),
                        'success' => true,
                        'error' => 0,

                    ];

                }

            }

        } catch (\Exception $e) {

            $message = '';//$e->getMessage();

        }



        Yii::$app->response->statusCode = 442;

        return [

            'error' => 1,

            'message' => $message

        ];

    }







    const LINK_TAOBAO = 'https://item.taobao.com/item.htm?';


 

}