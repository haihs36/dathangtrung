<?php

    namespace frontend\controllers;

    use app\models\Test;
    use common\components\CommonLib;
    use common\helpers\Image;
    use common\models\Custommer;
    use common\models\Photo;
    use common\models\TbComplain;
    use common\models\TbComplainReply;
    use common\models\TbCustomers;
    use common\models\TbOrders;
    use common\models\TbOrdersDetail;
    use common\models\TbOrderSearch;
    use common\models\TbOrdersSession;
    use common\models\TbOrderSupplier;
    use common\models\TbProduct;
    use common\models\TbProductComplain;
    use common\models\TbProductItem;
    use common\models\TbSupplier;
    use common\models\UploadForm;
    use PHPExcel;
    use PHPExcel_Style_Fill;
    use Yii;
    use yii\base\ErrorException;
    use yii\bootstrap\ActiveForm;
    use yii\db\Exception;
    use yii\helpers\StringHelper;
    use yii\web\NotFoundHttpException;
    use PHPExcel_Style_Border;
    use yii\web\UploadedFile;
    use yii\web\User;

    class OrdersController extends \common\components\APPController
    {

        public $layout = 'customer';

        public function behaviors()
        {
            return array_merge(parent::behaviors(), [

                // For cross-domain AJAX request
                'corsFilter' => [
                    'class' => \yii\filters\Cors::className(),
                    'cors'  => [
                        // restrict access to domains:
                        'Origin' => CommonLib::allowedDomains(),
                        'Access-Control-Request-Method'    => ['GET','POST'],
                        'Access-Control-Allow-Credentials' => true,
                        'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
                    ],
                ],

            ]);
        }


        public function actionIndex()
        {
            $status      = (int)Yii::$app->request->get('status');
            $searchModel = new TbOrderSearch();
            if ($status) {
                $searchModel->status = $status;
            }
            $uid                     = Yii::$app->user->id;
            $searchModel->customerID = $uid;
            if ($searchModel->load(Yii::$app->request->post())) {
                $params['customerID'] = $searchModel->customerID;
                $params['identify']   = $searchModel->identify;
                $params['status']     = $searchModel->status;
                $params['orderDate']  = $searchModel->startDate;
                $dataProvider         = $searchModel->searchHome($params);
            } else {
                $dataProvider = $searchModel->searchHome(Yii::$app->request->queryParams);
            }
            $data_seo['title'] = 'Quản lý đơn hàng ';
            \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);

            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'status'       => $status,
            ]);
        }

        public function actionView($id)
        {

            $currentOrder = TbOrders::findOne(['orderID' => $id]);

            if(!$currentOrder)
                return CommonLib::redirectError();

            $userID       = \Yii::$app->user->id;
            $customer     = TbCustomers::findOne($userID);
            $data         = TbOrders::getOrderDetail($id, $userID);
            $order        = [];
            if ($data) {
                foreach ($data as $item) {
                    $order[$item['supplierID']][$item['id']] = $item;
                }
            }

            $data_seo['title'] = 'Chi tiết đơn hàng - '.$currentOrder->identify;
            \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);

            $uploadForm = new UploadForm();

            return $this->render('order-detail', [
                'order'        => $order,
                'uploadForm' => $uploadForm,
                'customer'     => $customer,
                'currentOrder' => $currentOrder,
            ]);

        }

//disable
        public function actionUpdate_disable($id)
        {
            $model = TbOrdersSession::findOne($id);
            if (!$model) CommonLib::redirectError();

            if ($model->load(Yii::$app->request->post())) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                } else {
                    $model->image        = (!empty($model->image)) ? $model->image : $model->oldAttributes['image'];
                    $model->totalPrice   = $model->quantity * $model->unitPrice; //tien tq
                    $model->unitPriceVn  = round($model->unitPrice * $this->CNY); //vien viet
                    $model->totalPriceVn = $model->quantity * $model->unitPriceVn;
                    if ($model->save(false))
                        return $this->redirect(['orders/cart']);
                }
            }

            return $this->render('editCart', [
                'model' => $model,

            ]);
        }

        /* clearn image
         * */
        public function actionClearImage($id)
        {
            $model = TbOrdersSession::findOne($id);

            if ($model === null) {
                $this->flash('error', 'Not found');
            } else {
                $model->image = '';
                if ($model->update()) {
                    @unlink(Yii::getAlias('@upload_dir') . $model->image);
                    $this->flash('success', 'Ảnh đã được xóa');
                } else {
                    $this->flash('error', 'Update error');
                }
            }
            return $this->back();
        }

        protected function saveSuplier($data)
        {
            if (!isset($data['shop_id']) || !isset($data['shop_name']) || empty($data['shop_id']) || empty($data['shop_name'])) {
                return false;
            }

            $model                = new TbSupplier();
            $model->shopID        = isset($data['shop_id']) ? $data['shop_id'] : '';
            $model->shopName      = isset($data['shop_name']) ? $data['shop_name'] : '';
            $model->sourceName    = isset($data['source_site']) ? $data['source_site'] : '';
            $model->shopProductID = isset($data['shopProductID']) ? $data['shopProductID'] : '';
            $model->address       = isset($data['shop_address']) ? $data['shop_address'] : '';
            $model->shopUrl       = isset($data['link']) ? $data['link'] : '';

            if ($modelExist = TbSupplier::findOne(['shopID' => $model->shopID])) {
                return $modelExist->supplierID;
            } else {
                if ($model->save(false)) {
                    return $model->supplierID;
                }
                var_dump($model->errors);
                die;
            }
        }

        protected function saveProduct($data, $supplierID)
        {
            if (isset($data['title']) && !empty($data['title'])) {
                $model                = new TbProduct();
                $model->supplierID    = $supplierID;
                $model->shopProductID = isset($data['shopProductID']) ? $data['shopProductID'] : '';
                $model->shopID        = isset($data['shop_id']) ? $data['shop_id'] : '';
                $model->sourceName    = isset($data['source_site']) ? $data['source_site'] : '';
                $model->md5           = $data['md5'];
                $model->name          = trim($data['title']);
                $model->unitPrice     = isset($data['unitPrice']) ? $data['unitPrice'] : 0;
                $model->quantity      = isset($data['quantity']) ? $data['quantity'] : 0;
                $model->image         = isset($data['image']) ? $data['image'] : '';
                $model->link          = isset($data['link']) ? $data['link'] : '';
                $model->size          = isset($data['size']) ? $data['size'] : '';
                $model->color         = isset($data['color']) ? $data['color'] : '';
                $model->time          = time();

                if ($modelExits = TbProduct::findOne(['md5' => $model->md5, 'supplierID' => $supplierID])) {
                    return $modelExits->productID;
                } else {
                    if ($model->save(false)) {
                        return $model->productID;
                    } else {
                        return 0;
                        //  var_dump($model->errors);
                        //die;
                    }
                }
            }
        }

        public function actionCart()
        {
           // $uinfo = CommonLib::getUserIdentify();
            $userID = Yii::$app->user->id;
            $customer = Custommer::findOne($userID);
            //$session  = \Yii::$app->session;
            $order        = [];
            //$ordersession = TbOrdersSession::find()->andWhere(['or', ['customerID'=>$userID],['identify'=>$uinfo['identify']]])->orderBy('id DESC')->asArray()->all();
            $ordersession = TbOrdersSession::find()->where(['customerID' => $userID])->orderBy('id DESC')->limit(1000)->asArray()->all();
            $totalQuantityAll = 0;
            $totalFinalTQAmountAll = 0;
            $totalFinalAmountAll = 0;
            $arrShop = [];
            $arrShopChecked = [];
            if ($ordersession) {
                // $trans = new GoogleTranslate();
                foreach ($ordersession as $item) {
                    if(!in_array($item['shop_id'],$arrShop)){
                        $arrShop[] = $item['shop_id'];
                    }
                    if(isset($item['isCheck']) && $item['isCheck']){
                        $totalQuantityAll += $item['quantity'];
                        $totalFinalTQAmountAll += $item['totalPrice'];
                        $totalFinalAmountAll += $item['totalPriceVn'];
                    }

                    $order[$item['shop_id']][$item['id']] = $item;//CommonLib::Translate($trans,$item);
                    if(isset($item['isCheck']) && $item['isCheck'] && !in_array($item['shop_id'],$arrShopChecked)){
                        $arrShopChecked[$item['shop_id']][] =  $item['id'];
                    }
                }
            }

            $totalShopAll = count($arrShop);

            if (Yii::$app->request->isPost) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                }
                $data = \Yii::$app->request->post();
//            pr($order);
//            pr($ordersession);
          // pr($data['shop_cart_item']);die;

                if (isset($data['shop_cart_item']) && count($data['shop_cart_item']) > 0) {
                    //insert tb orders
                    $number = TbOrders::find()->where(['customerID' => $userID])->count();
                    $number ++;
                    $identify = CommonLib::checkOrder($userID,$number);
                   // $identify =   (int)TbOrders::find()->select(['orderID'=>'( MAX(`orderID`)+ 1) '])->one()->orderID;
                   // if(!$identify)
                     //   $identify = 1;

                //    $identify = 'A'.$userID.'-'.$identify;

                    $arrDeleteChecked = [];

                    try{
                        $modelOrder             = new TbOrders();
                        //update ma order identify
                        $modelOrder->identify   = $identify;
                        $modelOrder->customerID = $userID;
                        $modelOrder->isBox = isset($data['isBox']) ? $data['isBox'] : 0;
                        $modelOrder->isCheck = isset($data['isCheck']) ? $data['isCheck'] : 0;
                        $modelOrder->businessID = !empty($customer->userID) ? $customer->userID : null; //nhan vien kd ?
                        $modelOrder->orderStaff = isset($customer->staffID) ? $customer->staffID : null; //nhan vien dh ?
                        $modelOrder->orderDate  = date('Y-m-d H:i:s');
                        $modelOrder->provinID = isset($data['provinceID']) ? $data['provinceID'] : 0;
                        $modelOrder->noteOrder = isset($data['ghi_chu']) ? strip_tags($data['ghi_chu']) : '';
                        //luu kho dich va dia chi giao hang
                        $modelOrder->shipAddress = isset($data['shipAddress']) ? strip_tags($data['shipAddress']) : '';

                        if ($modelOrder->save(false)) {
                            $shopID = 0;
                            /*14/04/2018 fix phi cho KH*/
                            $totalOrder    = 0;//tong tien hang
                            $totalOrderTQ  = 0;//tong tien hang tq
                            $totalQuantity = 0;
                            $orderID       = $modelOrder->orderID;
                            $img = '';

                            //% phi giao dich ap dung cho don hang
                            foreach ($data['shop_cart_item'] as $shop_id => $value) {
                                if (isset($order[$shop_id]) && !empty($order[$shop_id])) {
                                    $shopID = $shop_id;
                                    /*save supplier*/
                                    $quantity       = 0;
                                    $totalShopPrice = 0;//tong tien shop
                                    $totalPriceTq   = 0;//tong tien tq
                                    $shop           = current($order[$shop_id]);
                                    $suplieID       = $this->saveSuplier($shop);//save  shop
                                    if ($suplieID && isset($value['item']) && count($value['item']) > 0) {
                                        //save tb_order_supplier
                                        $tbOrderSupplier                = new TbOrderSupplier();
                                        $tbOrderSupplier->orderID       = $orderID;
                                        $tbOrderSupplier->supplierID    = $suplieID;
                                        $tbOrderSupplier->noteInsite    = '';
                                        $tbOrderSupplier->status = 2;//chuong trang thai sang dat coc luon

                                        if ($tbOrderSupplier->save(false)) {
                                            $orderSID  = $tbOrderSupplier->id;
                                            $productID = 0;
                                            foreach ($value['item'] as $k => $detail) {
                                                if (isset($order[$shop_id][$k]) && isset($detail['product_check']) && $detail['product_check']) {
                                                   
                                                    $arrDeleteChecked[] = $k;  
                                                    $productInfo = $order[$shop_id][$k];
                                                    $img = $productInfo['image'];
                                                    if(isset($detail['qty']) && !empty($detail['qty'])){
                                                        $productInfo['quantity'] = $detail['qty'];
                                                    }

                                                    if(!isset($productInfo['quantity']) || (int)$productInfo['quantity'] <=0)
                                                        continue;


                                                    //insert tb product
                                                    $productID = $this->saveProduct($productInfo, $suplieID);
                                                    if ($productID && $orderSID) {
                                                        //insert order detail
                                                        $orderDetail                  = new TbOrdersDetail();
                                                        $orderDetail->orderID         = $orderID;
                                                        $orderDetail->productID       = $productID;
                                                        $orderDetail->orderSupplierID = $orderSID;
                                                        $orderDetail->quantity        = $productInfo['quantity'];
                                                        $orderDetail->noteProduct     = isset($detail['ghi_chu']) ? $detail['ghi_chu'] : '';
                                                        $orderDetail->unitPrice       = $productInfo['unitPrice'];
                                                        $orderDetail->unitPriceVn     = (int)$productInfo['unitPriceVn'];
                                                        $orderDetail->totalPrice      = $productInfo['unitPrice'] * $orderDetail->quantity;
                                                        $orderDetail->totalPriceVn    = (int)($productInfo['unitPriceVn'] * $orderDetail->quantity);
                                                        $orderDetail->size            = $productInfo['size'];
                                                        $orderDetail->color           = $productInfo['color'];
                                                        $orderDetail->image           = $productInfo['image'];

                                                        if ($orderDetail->save(false)) {
                                                            $totalPriceTq   += $orderDetail->totalPrice;
                                                            $totalShopPrice += $orderDetail->totalPriceVn;
                                                            $quantity       += $orderDetail->quantity;
                                                        }
                                                    }
                                                }
                                            }

                                            if (!$productID) {
                                                //ko luu duoc san pham thi xoa shop
                                                $tbOrderSupplier->delete();
                                            } else { //update order supplier
                                                $tbOrderSupplier->shopPriceTQ = $totalPriceTq;
                                                $tbOrderSupplier->shopPriceKg = ($modelOrder->weightCharge > 0 && $tbOrderSupplier->weight) ? round($tbOrderSupplier->weight * $modelOrder->weightCharge) : 0;
                                                $tbOrderSupplier->quantity    = $quantity;
                                                $tbOrderSupplier->shopPrice   = $totalShopPrice;//tong tien hang of shop
                                                //tien dich vu theo shop
                                                $tbOrderSupplier->discountDeals = CommonLib::getPercentDVofOrder($totalShopPrice,$customer->discountRate,$modelOrder->discountDeals,$modelOrder->provinID);
                                                $tbOrderSupplier->orderFee       = round(($tbOrderSupplier->shopPrice * $tbOrderSupplier->discountDeals) / 100);
                                                $tbOrderSupplier->shopPriceTotal = $totalShopPrice + $tbOrderSupplier->orderFee;//tong tien shop
                                                $tbOrderSupplier->update();
                                                $totalOrder    += $totalShopPrice;//tinh tong tien cac shop
                                                $totalOrderTQ  += $totalPriceTq;//tinh tong tien cac shop
                                                $totalQuantity += $quantity;
                                            }
                                        }
                                    } else {
                                        TbSupplier::findOne($suplieID)->delete();//ko co xoa shop
                                    }
                                }
                            }
                            /*update order total price*/
                            if ($totalOrder && $orderID) {

                                $modelOrder->orderID       = $orderID;
                                $modelOrder->image = $img;
                                $modelOrder->totalQuantity = $totalQuantity;
                                $modelOrder->cny           = $this->CNY;//ti gia mac dinh he thong
                                /*% phi dv cho don hang*/
                                $modelOrder->discountDeals = CommonLib::getPercentDVofOrder($totalOrder,$customer->discountRate,$modelOrder->discountDeals,$modelOrder->provinID);
                                $modelOrder->orderFee      = round(($totalOrder * $modelOrder->discountDeals) / 100);//tong tien phi dich vu
                                //phi giam gia kg
                                $modelOrder->weightDiscount = CommonLib::getKgofOrder($modelOrder->totalWeight,$customer->discountKg,$modelOrder->weightDiscount,$modelOrder->provinID);

                                $modelOrder->weightCharge   = $modelOrder->weightDiscount;//tong tien giam gia
                                $modelOrder->totalOrder    = $totalOrder; //tong tien hang
                                $modelOrder->totalOrderTQ  = $totalOrderTQ; //tong tien hang tq
                                $modelOrder->totalPayment  = $totalOrder + $modelOrder->orderFee;//tong tien don hang
                                //tien no =   tong tien don hang - so tien dat coc
                                $modelOrder->debtAmount = ($modelOrder->totalPaid < $modelOrder->totalPayment) ? $modelOrder->totalPayment - $modelOrder->totalPaid : 0;
                                $modelOrder->status     = 1;//cho coc
                                $modelOrder->save(false);
                                CommonLib::updateOrder($modelOrder);
                                //Cap nhat thong tin khach hang
                                $customer->provinID       = $modelOrder->provinID;
                                $customer->billingAddress = !empty($modelOrder->shipAddress) ? $modelOrder->shipAddress : $customer->billingAddress;
                                $customer->save(false);

                                if($arrDeleteChecked){
                                   TbOrdersSession::deleteAll(['customerID'=>$userID,'id'=>$arrDeleteChecked]);
                                }

                                \Yii::$app->session->get('num_cart');

                                if (Yii::$app->request->isAjax) {
                                    return ['success' => true,'identify'=>$identify];
                                    // return ['success' => true,'message'=>'Gửi đơn hàng thành công.'];
                                }else{
                                    $this->flash('success', 'Gửi đơn hàng thành công.');
                                }
                                // return $this->redirect(['orders/index']);
                            } else {
                                $modelOrder->delete();

                                if (Yii::$app->request->isAjax) {
                                    return ['success' => false,'message'=>'Gửi đơn hàng thất bại.'];
                                }else{
                                    $this->flash('success', 'Gửi đơn hàng thất bại.');
                                }
                            }
                            return $this->refresh();
                        }

                    }catch (ErrorException $exception){

                          //co loi
                        TbOrderSupplier::deleteAll(['orderID'=>$modelOrder->orderID]);
                        if(isset($suplieID) && $suplieID) {
                            TbSupplier::findOne($suplieID)->delete();
                            TbProduct::deleteAll(['supplierID'=>$suplieID]);
                        }
                        TbOrdersDetail::deleteAll(['orderID'=>$modelOrder->orderID]);
                        $modelOrder->delete();


                        if (Yii::$app->request->isAjax) {
                            return ['success' => false,'message'=>'Gửi đơn hàng thất bại.'];
                        }else{
                            $this->flash('success', 'Gửi đơn hàng thất bại.');
                        }
                        return $this->refresh();
                       // var_dump($exception->getMessage());die;

                    }
                }else{
                    if (Yii::$app->request->isAjax) {
                        return ['success' => false,'message'=>'Gửi đơn hàng thất bại.'];
                    }
                }
            }


            $data_seo['title'] = 'Quản lý Giỏ hàng';

            \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);
            return $this->render('cart', [
                'order'    => $order,
                'customer' => $customer,
                'arrShopChecked' => $arrShopChecked,
                'totalShopAll' => $totalShopAll,
                'totalQuantityAll' => $totalQuantityAll,
                'totalFinalTQAmountAll' => $totalFinalTQAmountAll,
                'totalFinalAmountAll' => $totalFinalAmountAll,
            ]);
        }


        /*
     * tao don hang tay
     * */
        public function actionCreate()
        {
            


            $user_id  = \Yii::$app->user->id;
            $customer = TbCustomers::findOne($user_id);
            $check    = true;
            if (\Yii::$app->request->getIsPost()) {
                $data = \Yii::$app->request->post();

                if (isset($data['sanpham_item']) && $data['sanpham_item']) {
                    $arrImage = [];
                    if(isset($_FILES['sanpham_item']) && !empty($_FILES['sanpham_item'])){
                        foreach ($_FILES['sanpham_item'] as $key => $item){
                            if($key == 'tmp_name')
                                $key = 'tempName';

                            if(!empty($item)){
                                foreach ($item as $k=> $val){
                                    $arrImage[$k][$key] = $val['img'];
                                }
                            }
                        }
                    }

                    $shop_name =  CommonLib::getRandomInt(8).time();
                    foreach ($data['sanpham_item'] as $k=> $item) {
                        if (empty($item['link']) || empty($item['tensanpham']) || empty($item['color']) || empty($item['size']) || empty($item['qty']) || empty($item['price'])) {
                            continue;
                        }
                        $model                = new TbOrdersSession();
                        if (!empty($arrImage) && !empty($arrImage[$k]['name'])) {
                            $imgObj = new UploadedFile($arrImage[$k]);
                            $photo = new Photo();
                            $photo->image = $imgObj;
                            if ($photo->image && $photo->validate(['image'])) {
                                $fileName = $photo->image->size . '-' . CommonLib::getRandomInt(5);
                                $photo->image = Image::upload($photo->image, $this->upload_image, null, null, false, $fileName);
                                $model->image = Yii::$app->params['FileDomain'] . $photo->image;
                            }
                        }


                        $model->customerID    = \Yii::$app->user->id;
                        $model->shop_id       = $shop_name;
                        $model->shop_name     = $shop_name;
                        $model->shop_address  = isset($item['shop_address']) ? $item['shop_address'] : '';
                        $model->source_site   = isset($item['website']) ? $item['website'] : '';
                        $model->shopProductID = isset($item['id']) ? $item['id'] : '';
                        $model->title         = $item['tensanpham'];
                        $model->link          = \common\components\CommonLib::convertUrl($item['link']);
                        $model->md5           = md5($item['color'] . $item['size'] . $item['tensanpham'] . $shop_name);
                        $model->quantity      = $item['qty'];
                        $model->size          = $item['size'];
                        $model->color         = $item['color'];
                        $model->noteProduct   = $item['mota'];
                        $model->unitPrice     = $item['price'];//tien tq
                        $model->unitPriceVn   =  round($this->CNY * $item['price']);
                        $model->totalPrice    = $model->unitPrice * $model->quantity; //tong tien TQ
                        $model->totalPriceVn  =  ($model->unitPriceVn * $model->quantity); //tong tien vn

                        if ($modelExits = TbOrdersSession::findOne(['md5' => $model->md5, 'customerID' => $model->customerID])) {
                            $modelExits->quantity     += $model->quantity;
                            $modelExits->totalPrice   = $modelExits->unitPrice * $modelExits->quantity; //tong tien TQ
                            $modelExits->totalPriceVn = $modelExits->unitPriceVn * $modelExits->quantity; //tong tien TQ
                            $modelExits->update();
                        } else {
                            $model->save(false);
                        }
                    }

                    $this->flash('success', 'Thêm giỏ hàng thành công');

                    return $this->redirect(['orders/cart']);
                } else {
                    $this->flash('success', 'Sản phẩm thêm vào không hợp lệ.');
                }

            }


            $data_seo['title'] = 'Tạo đơn hàng | LOGISTICS';
            \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);

            return $this->render('create', [
                'customer' => $customer,
                'check'    => $check,
            ]);
        }


        public function actionExport()
        {

            $id       = Yii::$app->request->get('id');
            $order    = TbOrders::findOne($id);
            $filename = 'donhang-' . date('d-m-Y');

            $excel = new PHPExcel();
            // Add some data
            $styleArray  = array(
                'font' => array(
                    'size' => 13,
                    'name' => 'Times new roman'
                ));
            $styleArray2 = [
                'font' => [
                    'size' => 14,
                    'name' => 'Times new roman'
                ]
            ];

            $excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
            $excel->getActiveSheet()->getStyle('A2:C2')->applyFromArray($styleArray);
            $excel->getActiveSheet()->getStyle('A3:C3')->applyFromArray($styleArray);
            $excel->getActiveSheet()->getStyle('A4:A4')->applyFromArray($styleArray);
            $excel->getActiveSheet()->getStyle('A5:A5')->applyFromArray($styleArray);
            $excel->getActiveSheet()->getStyle('A6:A6')->applyFromArray($styleArray2);

            $excel->setActiveSheetIndex(0);
            //lay phi dich vu cai dat cho don hang, nguoi dung va mac dinh
            //kiem tra co chiet khau cho nguoi dung
            $tigia        = $order->cny > 0 ? $order->cny : $this->CNY;
            $header_order = ['Mã đơn hàng', 'Ngày đặt', 'Trạng Thái', 'Tỉ giá', 'Phí dịch vụ'];
            $value_order  = [$order->identify, date('d-m-Y', strtotime($order->orderDate)), CommonLib::statusText($order->status), $order->discountDeals . ' %', number_format(round($tigia)) . ' VNĐ'];
            $index        = 1;
            $sheet        = $excel->getActiveSheet();

            foreach ($header_order as $title) {
                $excel->getActiveSheet()->setCellValue('A' . $index, $title);
                $excel->getActiveSheet()->setCellValue('B' . $index, $value_order[$index - 1]);
                //$sheet->mergeCells('B' . $index . ':' . 'C' . $index);
                $index++;
            }

            $sheet->getStyle('A1:B5')->applyFromArray(
                array(
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'CCFFCC')
                    )
                )
            );


            $sheet->setCellValueByColumnAndRow(0, 6, "CHI TIẾT ĐƠN HÀNG");
            $sheet->mergeCells('A6:G6');
            $excel->getActiveSheet()->setTitle($filename); //title sheet
            /*show tb2*/
            $data        = [];
            $userID      = \Yii::$app->user->id;
            $orderDetail = TbOrders::getOrderDetail($id, $userID);
            if ($orderDetail) {
                foreach ($orderDetail as $key => $item) {
                    $data[$item['supplierID']][] = $item;
                }
            }

            $supplier = [];

            $totalPrice = 0;
            if ($data) {
                foreach ($data as $shopId => $product) {
                    //if($index != 9) continue;
                    $supplierInfo = current($product);
                    $listsp       = [];
                    $listsp[0]    = ['Sản phẩm', 'Đơn giá (đ)', 'Đơn giá (tệ)', 'Số lượng', 'Ghi chú', 'Thành tiền (đ)', 'Thành tiền (tệ)'];

                    foreach ($product as $k => $item) {
                        $totalPrice     += $item['totalPriceVn'];
                        $listsp[$k + 1] = [$item['link'], number_format(round($item['unitPriceVn'])), $item['unitPrice'], $item['quantity'], strip_tags($item['noteProduct']), number_format(round($item['totalPriceVn'])), $item['totalPrice']];;
                    }

                    $shipmentFee             = 0;//$product[0]->orderSupplier->shipmentFee; //phi ship noi dia
                    $supplier[$shopId]       = ['NHÀ CUNG CẤP:' . $supplierInfo['title'], 'Phí ship TQ', $shipmentFee];
                    $supplier[$shopId]['sp'] = $listsp;


                }
            }

            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

            $row = 7;
            if ($supplier) {
                foreach ($supplier as $row_data) {
                    $col = 0;
                    foreach ($row_data as $key => $value) {
                        if ($key === 'sp') {
                            $rows = $row + 1;
                            foreach ($value as $sp) {
                                $col = 0;
                                foreach ($sp as $child) {
                                    $excel->getActiveSheet()->setCellValueByColumnAndRow($col, $rows, $child);
                                    $excel->getActiveSheet()->getStyle('A' . ($rows) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $excel->getActiveSheet()->getStyle('B' . ($rows) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $excel->getActiveSheet()->getStyle('C' . ($rows) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $excel->getActiveSheet()->getStyle('D' . ($rows) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $excel->getActiveSheet()->getStyle('E' . ($rows) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $excel->getActiveSheet()->getStyle('F' . ($rows) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $excel->getActiveSheet()->getStyle('G' . ($rows) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                                    $col++;
                                }
                                $rows++;
                            }
                            $row = $rows;
                        } else {
                            $excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
                            $col++;
                        }

                        $excel->getActiveSheet()->getStyle('A' . ($row) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $excel->getActiveSheet()->getStyle('B' . ($row) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $excel->getActiveSheet()->getStyle('C' . ($row) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $excel->getActiveSheet()->getStyle('D' . ($row) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $excel->getActiveSheet()->getStyle('E' . ($row) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $excel->getActiveSheet()->getStyle('F' . ($row) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $excel->getActiveSheet()->getStyle('G' . ($row) . '')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    }
                    $row++;
                }
            }

            $priceService = ($totalPrice * $phidv) / 100;
            $excel->getActiveSheet()->setCellValue('A' . ($row + 1), 'Tổng tiền hàng: ' . number_format(round($totalPrice)) . ' VNĐ');
            $excel->getActiveSheet()->setCellValue('A' . ($row + 2), 'Phí dịch vụ: ' . number_format(round($priceService)) . ' VNĐ');
            $excel->getActiveSheet()->setCellValue('A' . ($row + 3), 'TỔNG TIỀN ĐƠN: ' . number_format(round($totalPrice + $priceService)) . ' VNĐ');
            //$sheet->mergeCells('A' . ($row + 1) . ':B' . ($row + 1));

            $sheet->getStyle('A' . ($row + 1) . ':B' . ($row + 3))->applyFromArray(
                array(
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFCC99')
                    )
                )
            );

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save('php://output');
            exit();
        }

        /*
        ** Author:HAIHS
        ** Content: Xoa don hang
        ** CreateDate: 129-05-2018 22:02
        */
        public function actionDelete($id)
        {
            $mdlOrder = TbOrders::findOne($id);
            if($mdlOrder->status == 1){
                $mdlOrder->status = 5;
                if(TbOrders::updateAll(['status' => 5], ['orderID'=> $id,'customerID'=>Yii::$app->user->id]))
                    $this->flash('success','Hủy đơn hàng thành công!');
                else
                    $this->flash('success','Hủy đơn hàng thất bại.');

            }

            return $this->redirect(['index']);
        }

        /*
         * tracking kien hang
         * */
        public function actionTracking(){

            $barcode = Yii::$app->request->get('barcode');

            return $this->render('tracking',[
                    'barcode' => $barcode
            ]);
        }
    }
