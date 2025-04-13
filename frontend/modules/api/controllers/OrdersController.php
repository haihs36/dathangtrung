<?php

    namespace frontend\modules\api\controllers;

    use common\components\CommonLib;
    use common\models\Custommer;
    use common\models\Province;
    use common\models\TbAccountBanking;
    use common\models\TbAccountTransaction;
    use common\models\TbComplain;
    use common\models\TbCustomers;
    use common\models\TbOrders;
    use common\models\TbOrdersDetail;
    use common\models\TbOrderSearch;
    use common\models\TbOrdersSession;
    use common\models\TbOrderSupplier;
    use common\models\TbProduct;
    use common\models\TbShippers;
    use common\models\TbShippersSearch;
    use common\models\TbSupplier;
    use common\models\TbTransfercode;
    use frontend\modules\api\resources\OrdersResource;
    use Yii;
    use yii\base\ErrorException;
    use yii\filters\auth\HttpBearerAuth;
    use yii\filters\Cors;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Url;
    use yii\rest\ActiveController;
    use yii\rest\Controller;
    use yii\web\UploadedFile;


    class OrdersController extends ActiveController
    {
        public $modelClass = OrdersResource::class;

        public function actions()
        {
            $action = parent::actions();
            unset($action['index']);
            unset($action['create']);
            //unset($action['update']);
            unset($action['delete']);
        }

        public function behaviors()
        {
            return ArrayHelper::merge(
                parent::behaviors(), [
                    'authenticator' => [
                        'class' => HttpBearerAuth::className(),
                        // 'except' => ['login', 'resetpassword'],
                    ],
                    'cors'          => [
                        'class' => Cors::class
                    ]
                ]
            );
        }

        public function actionDashboard()
        {
            if (Yii::$app->request->isGet) {
                $users = \Yii::$app->user->identity;
                $user_id = $users->getId();

                $cache = \Yii::$app->cache;
                $key = 'dashboard-' . $user_id;
                $data = false;//$cache->get($key);
                if ($data === false) {
                    $orderStatus = \common\models\TbOrders::getOrderCount(null, true);
                    $query = (new \yii\db\Query())->from(\common\models\TbOrders::tableName());
                    $query->where(['customerID' => $users->id, 'status' => 1]);
                    $totalPayment = $query->sum('totalPayment');
                    $totalPayment = round($totalPayment);
                    $total_coc = \common\components\CommonLib::getTotalPriceCoc($totalPayment,$users->deposit);
                    $total_kho_vn_thieu = \common\models\TbOrders::find()->where(['customerID' => $user_id, 'status' => 9])->sum('debtAmount');
                    $total_shipper = TbShippers::find()->select(['id', 'status'])->where(['userID' => $user_id, 'status' => 3])->count();
                    $total_kg_ky_gui = TbShippers::find()->where(['userID' => $user_id, 'status' => 3])->sum('weight');
                    // $weightFee_price = $users->weightFee;//phi can nang ky gui

                    // if (empty($weightFee_price)) {
                    $provin_id = $users->provinID ? $users->provinID : 1;//mac dinh tinh thanh ha noi neu khong cai dat
                    $weightFee_price = CommonLib::getFeeKg($total_kg_ky_gui, $provin_id);
                    //  }

                    $total_price_ky_gui = $total_kg_ky_gui * $weightFee_price;//tong tien ky gui = tien can nang ky gui cai cho user hoac bang gia * tong can nang
                    $setting = CommonLib::getSettingByName(['hotline', 'CNY']);

                    $CNY = CommonLib::getCNY($setting['CNY'], Yii::$app->user->identity->cny);
                    $total_dang_dat_thieu = \common\models\TbOrders::find()->where(['customerID' => $user_id, 'status' => [2, 3, 4, 8, 11]])->sum('debtAmount');
                    $data['cny'] = doubleval($CNY);
                    $data['total_balance'] = !empty($users->accounting) ? round($users->accounting->totalResidual, 2) : 0;

                    $data['orders'] = [
                        'pending'      => [
                            'status'      => 1,
                            'number'      => $orderStatus[1],
                            'total_price' => round($total_coc, 2),
                        ],
                        'processing'   => [
                            'status'      => 11,
                            'number'      => $orderStatus[11] + $orderStatus[2] + $orderStatus[3] + $orderStatus[4] + $orderStatus[8],
                            'total_price' => round($total_dang_dat_thieu, 2),
                        ],
                        'warehouse_vn' => [
                            'status'      => 9,
                            'number'      => $orderStatus[9],
                            'total_price' => round($total_kho_vn_thieu, 2),
                        ],
                        'consignment'  => [
                            'number'      => round($total_shipper, 2),//so luong don ky gui kho vn
                            'total_price' => round($total_price_ky_gui, 2),
                        ],
                    ];

                    //$cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
                }


                Yii::$app->response->statusCode = 200;
                return [
                    'success' => true,
                    "data"    => isset($data) ? $data : [],
                    'warehouse' => Province::getAll()
                ];
            }

            Yii::$app->response->statusCode = 422;
            return [
                'success' => false,
                "message" => 'bad request'
            ];
        }

        const LIMIT = 10;

        public function actionList()
        {
            $cache = \Yii::$app->cache;
            $user_id = Yii::$app->user->id;
            $params = Yii::$app->request->queryParams;

            $page = isset($params['page']) ? (int)$params['page'] : 1;
            $offset = ($page - 1) * self::LIMIT;

            $searchModel = new TbOrderSearch();
            $searchModel->identify = isset($params['identify']) ? $params['identify'] : '';
            $searchModel->status = isset($params['status']) ? $params['status'] : '';
            $searchModel->startDate = isset($params['startDate']) ? $params['startDate'] : '';
            $searchModel->endDate = isset($params['endDate']) ? $params['endDate'] : '';

            if (is_array($params))
                $key = 'List-orders-' . $user_id . implode('-', $params);
            else
                $key = 'List-orders-' . $user_id . $page;

            $data = false;//$cache->get($key);
            $next_page = false;
            if ($data === false) {
                $orders = $searchModel->searchHomeApi($params, $offset, self::LIMIT + 1);

                if (!empty($orders)) {
                    $customer = Yii::$app->user->identity;
                    foreach ($orders as $val) {
                        $perCent = \common\components\CommonLib::getPercentDeposit($val->totalPayment, $customer->deposit, $val->deposit);

                        $tmp = $val->toArray();
                        $tmp['image'] = $val->image;
                        $tmp['sourceName'] = $val->sourceName;
                        $tmp['percent'] = $perCent;

                        $data[] = $tmp;
                    }
                    // $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
                }
            }

            if (count($data) > self::LIMIT) {
                unset($data[count($data) - 1]);
                $next_page = true;
            }

            Yii::$app->response->statusCode = 200;
            return [
                'success'   => true,
                'next_page' => $next_page,
                "data"      => $data
            ];
        }

        public function actionDetail($id)
        {
            if (Yii::$app->request->isGet) {
                $userID = \Yii::$app->user->id;
                $cache = \Yii::$app->cache;
                $key = 'Detail-orders-' . $userID . $id;
                $data = $cache->get($key);
                $data = false;
                if ($data === false) {
                    $currentOrder = TbOrders::findOne(['orderID' => (int)$id, 'customerID' => $userID]);
                    if (!$currentOrder) {
                        Yii::$app->response->statusCode = 422;
                        return [
                            'success' => false,
                            "message" => 'Dữ liệu không tìm thấy',
                        ];
                    }

                    $business = isset($currentOrder->business) ? $currentOrder->business : [];
                    $salesManager = '';
                    if (!empty($business)) {
                        $salesManager = $business->first_name . ' ' . $business->last_name;
                    }

                    $currentOrder = $currentOrder->toArray();
                    $orders = TbOrders::getOrderDetailApi($id, $userID);
                    if (!empty($orders)) {
                        $currentOrder['salesManager'] = $salesManager;
                        $data['orders'] = $currentOrder;
                        $data['orders']['shopID'] = $orders[0]['shopID'];
                        $data['orders']['shopName'] = $orders[0]['shopName'];

                        foreach ($orders as $item) {
                            unset($item['shopID']);
                            unset($item['shopName']);

                            $data['products'][] = $item;
                        }
                    }

                    $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
                }

                Yii::$app->response->statusCode = 200;
                return [
                    'success' => true,
                    "data"    => $data
                ];
            }

            Yii::$app->response->statusCode = 422;
            return [
                'success' => false,
                "message" => 'bad request'
            ];
        }

        //dat coc api
        public function actionDeposit()
        {
            $success = false;
            $message = 'Đặt cọc thất bại. Dữ liệu không tồn tại';

            if (Yii::$app->request->isPost) {
                $orderID = (int)Yii::$app->request->post('order_id');
                $type = (int)Yii::$app->request->post('type');
                if ($orderID) {
                    $customer = Yii::$app->user->identity;
                    $customerID = $customer->getId();
                    $orders = TbOrders::findOne(['customerID' => $customerID, 'orderID' => $orderID]);
                    if ($orders) {
                        if ($orders->totalPaid > 0) {
                            Yii::$app->response->statusCode = 422;
                            $message = 'Bạn đã đặt cọc đơn hàng này.';
                            return ['success' => $success, 'message' => $message];
                        }
                        //% tien hang
                        $perCent = \common\components\CommonLib::getPercentDeposit($orders->totalPayment, $customer->deposit, $orders->deposit);
                        switch ($type) {
                            case 1: //thanh toan duoi 100% tien hang, ko phai tong don
                                $tienCoc = round(($orders->totalPayment * $perCent) / 100);
                                break;
                            case 2://thanh toan 100%
                            default:
                                $tienCoc = $orders->totalPayment;
                                $perCent = 100;
                                break;
                        }

                        if ($tienCoc > 0) {
                            $bank = TbAccountBanking::findOne(['customerID' => $customerID]);

                            //kt so du tai khoan < so tien thanh toan hoac con no
                            if (!$bank || ($bank && (round($bank->totalResidual, 2) < $tienCoc))) {
                                $setting = CommonLib::getSettingByName(['bank_account']);
                                $message = $setting['bank_account'];
                                Yii::$app->response->statusCode = 422;
                                return ['success' => $success, 'message' => $message];
                            }

                            //cap nhat lai so tien tai khoan vi dien tu
                            $bank->totalResidual -= $tienCoc;
                            $bank->totalPayment += $tienCoc;
                            //update trang thai don hang
                            //thong bao
                            $orders->totalPaid = $tienCoc; //cap nhat so tien dat coc
                            //$orders->toalResidual = $tienCoc; //cap nhat so tien dat coc
                            $orders->status = 11; //chuyen trang thai da coc
                            $note = 'Đã cọc ' . $perCent . '% = <b class="vnd-unit">' . number_format($tienCoc) . '<em>đ</em></b><br/>(<i>Chưa bao gồm phí phát sinh</i>)'; //ghi chu dat coc
                            $orders->noteCoc = $note;
                            //tong tien no =   tong tien hang - tong so tien dat coc
                            $orders->debtAmount = ($orders->totalPaid < $orders->totalPayment) ? $orders->totalPayment - $orders->totalPaid : 0;
                            $orders->perCent = $perCent;
                            $orders->setDate = date('Y-m-d H:i:s');//ngay coc

                            if ($orders->save(false)) {
                                //da dat coc cap nhat trang thai tat ca cac shop thanh dang giao dich
                                $orderSupplier = TbOrderSupplier::find()->where(['orderID' => $orderID])->all();
                                if ($orderSupplier) {
                                    foreach ($orderSupplier as $shop) {
                                        $tbOrderSupplier = TbOrderSupplier::findOne($shop->id);
                                        if ($tbOrderSupplier && $tbOrderSupplier->status != 3) { //cap nhat cac shop chua het hang
                                            $tbOrderSupplier->totalPaid = ($tbOrderSupplier->shopPrice * $orders->perCent / 100);//so tien coc chia deu cho cac shop
                                            $tbOrderSupplier->setDate = date('Y-m-d H:i:s');//ngay coc
                                            $tbOrderSupplier->status = 11; //trang thai da coc
                                            $tbOrderSupplier->save(false);
                                        }
                                    }
                                }

                                $bank->save(false);
                                $maDH = $orders->identify;
                                /*insert lich su giao dich*/
                                $mdlTransaction = new TbAccountTransaction();
                                $mdlTransaction->orderNumber = $orders->identify; //trang thai dat coc don hang
                                $mdlTransaction->type = 4; //trang thai dat coc don hang
                                $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                                $mdlTransaction->customerID = $customerID;
                                $mdlTransaction->sapo = 'Đặt cọc: ' . $perCent . '% tiền hàng</b><br/>(<i>Chưa bao gồm phí phát sinh</i>)<br>MĐH: ' . $maDH;
                                $mdlTransaction->value = $tienCoc;//so tien thanh toan
                                $mdlTransaction->accountID = $bank->id;//ma tai khoan
                                $mdlTransaction->balance = $bank->totalResidual;//so du sau khi giao dich
                                $mdlTransaction->create_date = date('Y-m-d H:i:s');
                                $mdlTransaction->save(false);

                                $message = 'Bạn đã đặt cọc: ' . $perCent . '% tiền hàng = <b class="vnd-unit">' . number_format($tienCoc) . '<em>đ</em></b> (<i>Chưa bao gồm phí phát sinh</i>) MĐH: ' . $maDH . '.
                                         <br>Chúng tôi sẽ đặt hàng và liên hệ với bạn trong thời gian sớm nhất.';
                                $success = true;
                                Yii::$app->response->statusCode = 200;
                                return ['success' => $success, 'content' => $message, 'message' => 'Đặt cọc thành công'];
                            }

                        }
                    }
                }
            }
            Yii::$app->response->statusCode = 422;
            return ['success' => $success, 'message' => $message];
        }

        //huy don hang
        public function actionCancel($id)
        {
            $success = false;
            $message = 'Dữ liệu không tồn tại';
            $mdlOrder = TbOrders::findOne(['orderID' => (int)$id, 'status' => 1, 'customerID' => Yii::$app->user->id]);
            if ($mdlOrder) {
                $mdlOrder->status = 5;
                if (TbOrders::updateAll(['status' => 5, 'totalOrder' => 0], ['orderID' => (int)$id, 'customerID' => Yii::$app->user->id])) {
                    Yii::$app->response->statusCode = 200;
                    $success = true;
                    $message = 'Hủy đơn hàng thành công!';
                } else {
                    Yii::$app->response->statusCode = 422;
                    $message = 'Hủy đơn hàng thất bại.';
                }
            }

            return [
                'success' => $success,
                "message" => $message
            ];

        }

        //get history order status
        public function actionHistory($id)
        {
            $userID = \Yii::$app->user->id;
            $cache = \Yii::$app->cache;
            $key = 'History-orders-' . $userID . $id;
            $data = $cache->get($key);
            $data = false;
            if ($data === false) {
                $mdlOrder = TbOrders::findOne(['orderID' => (int)$id, 'customerID' => Yii::$app->user->id]);
                if ($mdlOrder) {
                    $order_code = TbTransfercode::find()->select('transferID')->where(['orderID' => $mdlOrder->orderID])->asArray()->all();
                    if (!empty($order_code))
                        $order_code = array_column($order_code, 'transferID');


                    $data['order'] = [
                        'orderID'    => $mdlOrder->orderID,
                        'identify'   => $mdlOrder->identify,
                        'order_code' => $order_code
                    ];

                    $data['status'] = [
                        0 => [
                            'name' => 'Ngày lên đơn',
                            'date' => (strtotime($mdlOrder->orderDate) > 0 && !empty($mdlOrder->orderDate)) ? date('d/m/Y', strtotime($mdlOrder->orderDate)) : ''
                        ],
                        1 => [
                            'name' => 'Đã đặt cọc',
                            'date' => (strtotime($mdlOrder->setDate) > 0 && !empty($mdlOrder->setDate)) ? date('d/m/Y', strtotime($mdlOrder->setDate)) : ''
                        ],
                        2 => [
                            'name' => 'Đã đặt hàng',
                            'date' => (strtotime($mdlOrder->shipDate) > 0 && !empty($mdlOrder->shipDate)) ? date('d/m/Y', strtotime($mdlOrder->shipDate)) : ''
                        ],
                        3 => [
                            'name' => 'Shop xưởng giao',
                            'date' => (strtotime($mdlOrder->deliveryDate) > 0 && !empty($mdlOrder->deliveryDate)) ? date('d/m/Y', strtotime($mdlOrder->deliveryDate)) : ''
                        ],
                        4 => [
                            'name' => 'Đang vận chuyển',
                            'date' => (strtotime($mdlOrder->shippingDate) > 0 && !empty($mdlOrder->shippingDate)) ? date('d/m/Y', strtotime($mdlOrder->shippingDate)) : ''
                        ],
                        5 => [
                            'name' => 'Kho VN nhận',
                            'date' => (strtotime($mdlOrder->vnDate) > 0 && !empty($mdlOrder->vnDate)) ? date('d/m/Y', strtotime($mdlOrder->vnDate)) : ''
                        ],
                        6 => [
                            'name' => 'Đã trả hàng',
                            'date' => (strtotime($mdlOrder->paymentDate) > 0 && !empty($mdlOrder->paymentDate)) ? date('d/m/Y', strtotime($mdlOrder->paymentDate)) : ''
                        ],
                        7 => [
                            'name' => 'Hủy đơn',
                            'date' => (strtotime($mdlOrder->finshDate) > 0 && !empty($mdlOrder->finshDate)) ? date('d/m/Y', strtotime($mdlOrder->finshDate)) : ''
                        ],
                    ];

                }

                $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
            }

            Yii::$app->response->statusCode = 200;
            return [
                "data"    => isset($data) ? $data : [],
                'success' => true
            ];
        }

        //cart list
        public function actionListCart()
        {
            $params = Yii::$app->request->queryParams;
            $userID = Yii::$app->user->id;
            $page = isset($params['page']) ? (int)$params['page'] : 1;
            $limit = 1000;
            $offset = ($page - 1) * $limit;

            $data = [];
            $ordersession = TbOrdersSession::find()->where(['customerID' => $userID])->orderBy('id DESC')->offset($offset)->limit($limit + 1)->asArray()->all();
            if ($ordersession) {
                foreach ($ordersession as $item) {
                    unset($item['md5']);
                    unset($item['customerID']);
                    unset($item['identify']);

                    $data[] = $item;
                }
            }

            $next_page = false;

            if (count($data) > $limit) {
                unset($data[count($data) - 1]);
                $next_page = true;
            }


            Yii::$app->response->statusCode = 200;
            return [
                'success'   => true,
                'next_page' => $next_page,
                "data"      => $data
            ];
        }

        //them gio hang
        public function actionCreate()
        {
            $success = false;
            $message = 'Thêm giỏ hàng thất bại';

            if (Yii::$app->request->isPost) {
                $data = \Yii::$app->request->post();
                // pr($data);die;

                if (!empty($data)) {
                    $shop_name = CommonLib::getRandomInt(8) . time();
                    $setting = CommonLib::getAllSettings();
                    $CNY = CommonLib::getCNY($setting['CNY'], Yii::$app->user->identity->cny);


                    foreach ($data as $k => $item) {
                        if (empty($item['link']) || empty($item['pname']) || empty($item['color']) || empty($item['size']) || empty($item['qty']) || empty($item['price'])) {
                            continue;
                        }
                        $model = new TbOrdersSession();

                        $model->image = $item['img'];
                        $model->customerID = \Yii::$app->user->id;
                        $model->shop_id = $shop_name;
                        $model->shop_name = $shop_name;
                        $model->shop_address = '';
                        $model->source_site = 'auto';
                        $model->shopProductID = '';
                        $model->title = $item['pname'];
                        $model->link = $item['link'];
                        $model->md5 = md5($item['color'] . $item['size'] . $item['pname'] . $shop_name);
                        $model->quantity = $item['qty'];
                        $model->size = $item['size'];
                        $model->color = $item['color'];
                        $model->noteProduct = isset($item['desc']) ? $item['desc'] : '';
                        $model->unitPrice = $item['price'];//tien tq
                        $model->unitPriceVn = round($CNY * $item['price'], 2);
                        $model->totalPrice = $model->unitPrice * $model->quantity; //tong tien TQ
                        $model->totalPriceVn = ($model->unitPriceVn * $model->quantity); //tong tien vn

                        if ($modelExits = TbOrdersSession::findOne(['md5' => $model->md5, 'customerID' => $model->customerID])) {
                            $modelExits->quantity += $model->quantity;
                            $modelExits->totalPrice = $modelExits->unitPrice * $modelExits->quantity; //tong tien TQ
                            $modelExits->totalPriceVn = $modelExits->unitPriceVn * $modelExits->quantity; //tong tien TQ
                            $modelExits->update();
                        } else {
                            $model->save(false);
                        }

                        $success = true;
                    }

                    if ($success) {
                        Yii::$app->response->statusCode = 200;
                        return ['success' => $success, 'message' => 'Thêm giỏ hàng thành công.'];
                    }
                }
            }

            Yii::$app->response->statusCode = 422;
            return ['success' => $success, 'message' => $message];
        }

        //Tạo don hang
        public function actionBuynow()
        {
            $customer = Yii::$app->user->identity;

            if (Yii::$app->request->isPost && $customer) {
                $userID = $customer->getId();
                $data = \Yii::$app->request->post();
                $order = [];
                $ordersession = TbOrdersSession::find()->where(['customerID' => $userID])->orderBy('id DESC')->limit(1000)->asArray()->all();
                if ($ordersession) {
                    foreach ($ordersession as $item) {
                        $order[$item['shop_id']][$item['id']] = $item;
                    }
                }
                if (isset($data['shop_cart_item']) && count($data['shop_cart_item']) > 0) {
                    $setting = CommonLib::getAllSettings();
                    $CNY = CommonLib::getCNY($setting['CNY'], $customer->cny);
                    //insert tb orders
                    $number = TbOrders::find()->where(['customerID' => $userID])->count();
                    $number++;
                    $identify = CommonLib::checkOrder($userID, $number);

                    $arrDeleteChecked = [];

                    try {
                        $modelOrder = new TbOrders();
                        //update ma order identify
                        $modelOrder->identify = $identify;
                        $modelOrder->customerID = $userID;
                        $modelOrder->isMobile = 2;
                        $modelOrder->isBox = isset($data['isBox']) ? $data['isBox'] : 0;
                        $modelOrder->isCheck = isset($data['isCheck']) ? $data['isCheck'] : 0;
                        $modelOrder->businessID = !empty($customer->userID) ? $customer->userID : null; //nhan vien kd ?
                        $modelOrder->orderStaff = isset($customer->staffID) ? $customer->staffID : null; //nhan vien dh ?
                        $modelOrder->orderDate = date('Y-m-d H:i:s');
                        $modelOrder->provinID = isset($data['provinceID']) ? $data['provinceID'] : 0;
                        $modelOrder->noteOrder = isset($data['note_order']) ? strip_tags($data['note_order']) : '';
                        //luu kho dich va dia chi giao hang
                        $modelOrder->shipAddress = isset($data['address']) ? strip_tags($data['address']) : '';

                        if ($modelOrder->save(false)) {
                            $shopID = 0;
                            /*14/04/2018 fix phi cho KH*/
                            $totalOrder = 0;//tong tien hang
                            $totalOrderTQ = 0;//tong tien hang tq
                            $totalQuantity = 0;
                            $orderID = $modelOrder->orderID;
                            $img = '';

                            //% phi giao dich ap dung cho don hang
                            foreach ($data['shop_cart_item'] as $shop_id => $value) {
                                if (isset($order[$shop_id]) && !empty($order[$shop_id])) {
                                    $shopID = $shop_id;
                                    /*save supplier*/
                                    $quantity = 0;
                                    $totalShopPrice = 0;//tong tien shop
                                    $totalPriceTq = 0;//tong tien tq
                                    $shop = current($order[$shop_id]);
                                    $suplieID = $this->saveSuplier($shop);//save  shop
                                    if ($suplieID && isset($value['item']) && count($value['item']) > 0) {
                                        //save tb_order_supplier
                                        $tbOrderSupplier = new TbOrderSupplier();
                                        $tbOrderSupplier->orderID = $orderID;
                                        $tbOrderSupplier->supplierID = $suplieID;
                                        $tbOrderSupplier->noteInsite = '';
                                        $tbOrderSupplier->status = 2;//chuong trang thai sang dat coc luon

                                        if ($tbOrderSupplier->save(false)) {
                                            $orderSID = $tbOrderSupplier->id;
                                            $productID = 0;
                                            foreach ($value['item'] as $k => $detail) {

                                                $arrDeleteChecked[] = $k;
                                                $productInfo = $order[$shop_id][$k];
                                                $img = $productInfo['image'];
                                                if (isset($detail['qty']) && !empty($detail['qty'])) {
                                                    $productInfo['quantity'] = $detail['qty'];
                                                }

                                                if (!isset($productInfo['quantity']) || (int)$productInfo['quantity'] <= 0)
                                                    continue;

                                                //insert tb product
                                                $productID = $this->saveProduct($productInfo, $suplieID);
                                                if ($productID && $orderSID) {
                                                    //insert order detail
                                                    $orderDetail = new TbOrdersDetail();
                                                    $orderDetail->orderID = $orderID;
                                                    $orderDetail->productID = $productID;
                                                    $orderDetail->orderSupplierID = $orderSID;
                                                    $orderDetail->quantity = $productInfo['quantity'];
                                                    $orderDetail->noteProduct = isset($detail['note']) ? $detail['note'] : '';
                                                    $orderDetail->unitPrice = $productInfo['unitPrice'];
                                                    $orderDetail->unitPriceVn = (int)$productInfo['unitPriceVn'];
                                                    $orderDetail->totalPrice = $productInfo['unitPrice'] * $orderDetail->quantity;
                                                    $orderDetail->totalPriceVn = (int)($productInfo['unitPriceVn'] * $orderDetail->quantity);
                                                    $orderDetail->size = $productInfo['size'];
                                                    $orderDetail->color = $productInfo['color'];
                                                    $orderDetail->image = $productInfo['image'];

                                                    if ($orderDetail->save(false)) {
                                                        $totalPriceTq += $orderDetail->totalPrice;
                                                        $totalShopPrice += $orderDetail->totalPriceVn;
                                                        $quantity += $orderDetail->quantity;
                                                    }
                                                }

                                            }

                                            if (!$productID) {
                                                //ko luu duoc san pham thi xoa shop
                                                $tbOrderSupplier->delete();
                                            } else { //update order supplier
                                                $tbOrderSupplier->shopPriceTQ = $totalPriceTq;
                                                $tbOrderSupplier->shopPriceKg = ($modelOrder->weightCharge > 0 && $tbOrderSupplier->weight) ? round($tbOrderSupplier->weight * $modelOrder->weightCharge) : 0;
                                                $tbOrderSupplier->quantity = $quantity;
                                                $tbOrderSupplier->shopPrice = $totalShopPrice;//tong tien hang of shop
                                                //tien dich vu theo shop
                                                $tbOrderSupplier->discountDeals = CommonLib::getPercentDVofOrder($totalShopPrice, $customer->discountRate, $modelOrder->discountDeals, $modelOrder->provinID);
                                                $tbOrderSupplier->orderFee = round(($tbOrderSupplier->shopPrice * $tbOrderSupplier->discountDeals) / 100);
                                                $tbOrderSupplier->shopPriceTotal = $totalShopPrice + $tbOrderSupplier->orderFee;//tong tien shop
                                                $tbOrderSupplier->update();
                                                $totalOrder += $totalShopPrice;//tinh tong tien cac shop
                                                $totalOrderTQ += $totalPriceTq;//tinh tong tien cac shop
                                                $totalQuantity += $quantity;
                                            }
                                        }
                                    } else {
                                        TbSupplier::findOne($suplieID)->delete();// xoa shop
                                    }
                                }
                            }
                            /*update order total price*/
                            if ($totalOrder && $orderID) {

                                $modelOrder->orderID = $orderID;
                                $modelOrder->image = $img;
                                $modelOrder->totalQuantity = $totalQuantity;
                                $modelOrder->cny = $CNY;//ti gia mac dinh he thong
                                /*% phi dv cho don hang*/
                                $modelOrder->discountDeals = CommonLib::getPercentDVofOrder($totalOrder, $customer->discountRate, $modelOrder->discountDeals, $modelOrder->provinID);
                                $modelOrder->orderFee = round(($totalOrder * $modelOrder->discountDeals) / 100);//tong tien phi dich vu
                                //phi giam gia kg
                                $modelOrder->weightDiscount = CommonLib::getKgofOrder(null, $modelOrder->totalWeight, $customer->discountKg, $modelOrder->weightDiscount, $modelOrder->provinID);
                                $modelOrder->weightCharge = $modelOrder->weightDiscount;//tong tien giam gia
                                $modelOrder->totalOrder = $totalOrder; //tong tien hang
                                $modelOrder->totalOrderTQ = $totalOrderTQ; //tong tien hang tq
                                $modelOrder->totalPayment = $totalOrder + $modelOrder->orderFee;//tong tien don hang
                                //tien no =   tong tien don hang - so tien dat coc
                                $modelOrder->debtAmount = ($modelOrder->totalPaid < $modelOrder->totalPayment) ? $modelOrder->totalPayment - $modelOrder->totalPaid : 0;
                                $modelOrder->status = 1;//cho coc
                                $modelOrder->save(false);
                                CommonLib::updateOrder($modelOrder);
                                //Cap nhat thong tin khach hang
                                $customer->provinID = $modelOrder->provinID;
                                $customer->billingAddress = !empty($modelOrder->shipAddress) ? $modelOrder->shipAddress : $customer->billingAddress;
                                $customer->save(false);

                                if ($arrDeleteChecked) {
                                    TbOrdersSession::deleteAll(['customerID' => $userID, 'id' => $arrDeleteChecked]);
                                }

                                Yii::$app->response->statusCode = 200;
                                return ['success' => true, 'message' => 'Gửi đơn hàng thành công.', 'orderCode' => $modelOrder->identify];
                            } else {
                                $modelOrder->delete();
                                Yii::$app->response->statusCode = 422;
                                return ['success' => false, 'message' => 'Gửi đơn hàng thất bại.'];
                            }
                        }
                    } catch (ErrorException $exception) {
                        //co loi
                        TbOrderSupplier::deleteAll(['orderID' => $modelOrder->orderID]);
                        if (isset($suplieID) && $suplieID) {
                            TbSupplier::findOne($suplieID)->delete();
                            TbProduct::deleteAll(['supplierID' => $suplieID]);
                        }
                        TbOrdersDetail::deleteAll(['orderID' => $modelOrder->orderID]);
                        $modelOrder->delete();

                        Yii::$app->response->statusCode = 422;
                        return ['success' => false, 'message' => $exception->getMessage()];

                    }
                }
            }
            Yii::$app->response->statusCode = 422;
            return ['success' => false, 'message' => 'bad request'];
        }

        protected function saveSuplier($data)
        {
            if (!isset($data['shop_id']) || !isset($data['shop_name']) || empty($data['shop_id']) || empty($data['shop_name'])) {
                return false;
            }

            $model = new TbSupplier();
            $model->shopID = isset($data['shop_id']) ? $data['shop_id'] : '';
            $model->shopName = isset($data['shop_name']) ? $data['shop_name'] : '';
            $model->sourceName = isset($data['source_site']) ? $data['source_site'] : '';
            $model->shopProductID = isset($data['shopProductID']) ? $data['shopProductID'] : '';
            $model->address = isset($data['shop_address']) ? $data['shop_address'] : '';
            $model->shopUrl = isset($data['link']) ? $data['link'] : '';

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
                $model = new TbProduct();
                $model->supplierID = $supplierID;
                $model->shopProductID = isset($data['shopProductID']) ? $data['shopProductID'] : '';
                $model->shopID = isset($data['shop_id']) ? $data['shop_id'] : '';
                $model->sourceName = isset($data['source_site']) ? $data['source_site'] : '';
                $model->md5 = $data['md5'];
                $model->name = trim($data['title']);
                $model->unitPrice = isset($data['unitPrice']) ? $data['unitPrice'] : 0;
                $model->quantity = isset($data['quantity']) ? $data['quantity'] : 0;
                $model->image = isset($data['image']) ? $data['image'] : '';
                $model->link = isset($data['link']) ? $data['link'] : '';
                $model->size = isset($data['size']) ? $data['size'] : '';
                $model->color = isset($data['color']) ? $data['color'] : '';
                $model->time = time();

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
        /*
         * search home
         */
        public function actionSearch()
        {
            $params = Yii::$app->request->queryParams;
            $limitHome = 10;
            $data = [];//$cache->get($key);
            $next_page = false;

            if (isset($params['identify']) && !empty($params['identify'])) {
                $page = isset($params['page']) ? (int)$params['page'] : 1;
                $offset = ($page - 1) * $limitHome;
                $searchShipper = new TbShippersSearch();
                $searchModel = new TbOrderSearch();
                $searchModel->identify = isset($params['identify']) ? $params['identify'] : '';
                $searchShipper->shippingCode = isset($params['identify']) ? $params['identify'] : '';

//        if(is_array($params))
//            $key = 'List-orders-' .$user_id. implode('-',$params);
//        else
//            $key = 'List-orders-' .$user_id. $page;


                $limit1 = $limitHome / 2;
                $orders = $searchModel->searchHomeApi($params, $offset, $limit1 + 1);
                if (!empty($orders)) {
                    $customer = Yii::$app->user->identity;
                    foreach ($orders as $val) {
                        $perCent = \common\components\CommonLib::getPercentDeposit($val->totalPayment, $customer->deposit, $val->deposit);
                        $tmp = $val->toArray();
                        $tmp['image'] = $val->image;
                        $tmp['sourceName'] = $val->sourceName;
                        $tmp['shippingCode'] = $val->transferID;
                        $tmp['percent'] = $perCent;
                        $tmp['type'] = 'order';

                        $data[] = $tmp;
                    }
                    // $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
                }


                $limit2 = $limitHome / 2;
                $orderShipper = $searchShipper->searchHomeApi($params, $offset, $limit2);
                $dataShipper = [];
                if (!empty($orderShipper)) {
                    foreach ($orderShipper as $val) {
                        $tmp = $val->toArray();
                        $tmp['type'] = 'sign';
                        $dataShipper[] = $tmp;
                    }

                    $data = array_merge($data, $dataShipper);
                }

                if ($data && count($data) > $limitHome) {
                    unset($data[count($data) - 1]);
                    $next_page = true;
                }

            }

            Yii::$app->response->statusCode = 200;
            return [
                'success'   => true,
                'next_page' => $next_page,
                "data"      => $data
            ];

        }

        //delete cart item
        public function actionDeleteCartItem()
        {
            if (Yii::$app->request->isPost) {
                $customerID = \Yii::$app->user->id;
                $item_id = (int)Yii::$app->request->post('item_id');

                if ($item_id) {
                    if ($model = TbOrdersSession::findOne(['customerID' => $customerID, 'id' => $item_id])) {
                        $model->delete();

                        Yii::$app->response->statusCode = 200;
                        return ['success' => true, 'message' => 'delete success'];
                    }
                }
            }

            Yii::$app->response->statusCode = 422;
            return ['success' => false, 'message' => 'item not found'];
        }

        public function actionDeleteShop()
        {
            if (Yii::$app->request->isPost) {
                $customerID = \Yii::$app->user->id;
                $shop_id = (int)Yii::$app->request->post('shop_id');
                if ($shop_id) {
                    if (TbOrdersSession::deleteAll(['customerID' => $customerID, 'shop_id' => $shop_id])) {

                        Yii::$app->response->statusCode = 200;
                        return ['success' => true, 'message' => 'delete success'];
                    }
                }
            }

            Yii::$app->response->statusCode = 422;
            return ['success' => false, 'message' => 'shop not found'];
        }

        public function actionDepositAll()
        {
            $success = false;
            $message = 'Đặt cọc thất bại';
            $content = [];

            if (Yii::$app->request->isPost) {
                $orderIDs = Yii::$app->request->post('order_ids');
                $userLogin = Yii::$app->user->identity;

                if (is_array($orderIDs) && !empty($orderIDs) && $userLogin) {
                    $totalPriceCoc = (int)Yii::$app->request->post('total_deposit');

                    $bank = TbAccountBanking::findOne(['customerID' => $userLogin->id]);
                    //kt so du tai khoan < so tien thanh toan hoac con no
                    if (empty($bank) || ($bank && ($bank->totalResidual < $totalPriceCoc)) || $totalPriceCoc <= 0) {
                        $setting = CommonLib::getSettingByName(['bank_account']);
                        $message = $setting['bank_account'];
                        Yii::$app->response->statusCode = 422;

                        return ['success' => $success, 'message' => $message];

                    } else {
                        $dataOrder = TbOrders::find()->where(['totalPaid' => 0, 'orderID' => $orderIDs])->all();
                        if (!empty($dataOrder)) {
                            //$perCent = 100;
                            $customer = Yii::$app->user->identity;
                            $totalCoc = 0;
                            foreach ($dataOrder as $orders) {
                                $perCent = (double)\common\components\CommonLib::getPercentDeposit($orders->totalPayment, $customer->deposit, $orders->deposit);
                                $tienCoc = round(($orders->totalPayment * $perCent) / 100);
                                $totalCoc += $tienCoc;

                                //kt so du tai khoan < so tien thanh toan hoac con no
                                if ($bank->totalResidual < $tienCoc || $tienCoc <= 0) {
                                    $setting = CommonLib::getSettingByName(['bank_account']);
                                    $message = $setting['bank_account'];
                                    Yii::$app->response->statusCode = 422;

                                    return ['success' => $success, 'message' => $message];
                                } else {
                                    //cap nhat lai so tien tai khoan vi dien tu
                                    //if ($type == 1 || $type == 2) { //dat coc
                                    $bank->totalResidual -= $tienCoc;
                                    $bank->totalPayment += $tienCoc;
                                    //update trang thai don hang
                                    //thong bao
                                    $orders->totalPaid = $tienCoc; //cap nhat so tien dat coc
                                    //$orders->toalResidual = $tienCoc; //cap nhat so tien dat coc
                                    $orders->status = 11; //chuyen trang thai da coc
                                    $note = 'Đã cọc ' . $perCent . '% = <b class="vnd-unit">' . number_format($tienCoc) . '<em>đ</em></b><br/>(<i>Chưa bao gồm phí phát sinh</i>)'; //ghi chu dat coc
                                    $orders->noteCoc = $note;
                                    $content[(int)$orders->orderID] = $note;
                                    //tong tien no =   tong tien hang - tong so tien dat coc
                                    $orders->debtAmount = ($orders->totalPaid < $orders->totalPayment) ? $orders->totalPayment - $orders->totalPaid : 0;
                                    $orders->perCent = $perCent;
                                    $orders->setDate = date('Y-m-d H:i:s');//ngay coc

                                    if ($orders->save(false)) {
                                        //da dat coc cap nhat trang thai tat ca cac shop thanh dang giao dich
                                        $orderSupplier = TbOrderSupplier::find()->where(['orderID' => $orders->orderID])->all();
                                        if ($orderSupplier) {
                                            foreach ($orderSupplier as $shop) {
                                                $tbOrderSupplier = TbOrderSupplier::findOne($shop->id);
                                                if ($tbOrderSupplier && $tbOrderSupplier->status != 3) { //cap nhat cac shop chua het hang
                                                    $tbOrderSupplier->totalPaid = ($tbOrderSupplier->shopPrice * $orders->perCent / 100);//so tien coc chia deu cho cac shop
                                                    $tbOrderSupplier->setDate = date('Y-m-d H:i:s');//ngay coc
                                                    $tbOrderSupplier->status = 11; //trang thai da coc
                                                    $tbOrderSupplier->save(false);
                                                }
                                            }
                                        }

                                        $bank->save(false);
                                        $maDH = '<a class="link_order" target="_blank" href="' . Url::toRoute(['orders/view', 'id' => $orders->orderID]) . '"><b>' . $orders->identify . ' </b></a>';
                                        /*insert lich su giao dich*/
                                        $mdlTransaction = new TbAccountTransaction();
                                        $mdlTransaction->type = 4; //trang thai dat coc don hang
                                        $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                                        $mdlTransaction->orderID = $orders->orderID;
                                        $mdlTransaction->customerID = Yii::$app->user->id;
                                        $mdlTransaction->sapo = 'Đặt cọc: ' . $perCent . '% tiền hàng</b><br/>(<i>Chưa bao gồm phí phát sinh</i>)<br>MĐH: ' . $maDH;
                                        $mdlTransaction->value = $tienCoc;//so tien thanh toan
                                        $mdlTransaction->accountID = $bank->id;//ma tai khoan
                                        $mdlTransaction->balance = $bank->totalResidual;//so du sau khi giao dich
                                        $mdlTransaction->create_date = date('Y-m-d H:i:s');
                                        $mdlTransaction->save(false);

                                        $success = true;
                                        $message = 'Đặt cọc thành công';
                                    }
                                }
                            }
                        }
                    }
                }
            }

            Yii::$app->response->statusCode = 422;
            return ['success' => $success, 'message' => $message,'content' => $content];
        }
    }
