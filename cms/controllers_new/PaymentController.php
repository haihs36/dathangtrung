<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2/9/2018
     * Time: 11:58 AM
     */

    namespace cms\controllers;

    use cms\models\Lo;
    use cms\models\Warehouse;
    use common\components\CommonLib;
    use common\models\AccessRule;
    use common\models\TbAccountBanking;
    use common\models\TbAccountTransaction;
    use common\models\TbChatMessage;
    use common\models\TbCustomers;
    use common\models\TbOrders;
    use common\models\TbShippers;
    use common\components\Controller;
    use common\models\TbOrderSupplier;
    use common\models\TbTransfercode;
    use yii\db\Exception;
    use yii\filters\AccessControl;
    use yii\helpers\Url;
    use yii\web\NotFoundHttpException;
    use yii\web\Response;
    use Yii;


    class PaymentController extends Controller
    {

        public function behaviors()
        {
            return [
                'access' => [
                    'class'      => AccessControl::className(),
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'only'       => ['print', 'delete-shop', 'pay', 'payship'],
                    'rules'      => [
                        [
                            'actions' => ['print'],
                            'allow'   => true,
                            'roles'   => [ADMIN],
                        ],
                        [
                            'actions' => ['pay', 'print', 'delete-shop', 'payship'],
                            'allow'   => true,
                            'roles'   => [WAREHOUSE],
                        ],
                    ],
                ],
            ];
        }

        protected function findModel($id)
        {

            if (($model = TbOrders::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        //Tra hang order
        public function actionPay()
        {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $message = 'Trả hàng thất bại';
            if ($params = Yii::$app->request->post()) {
                $customerID = isset($params['customerID']) ? (int)$params['customerID'] : 0;
                $loID = isset($params['loID']) ? (int)$params['loID'] : 0;
                
                $token = isset($params['_csrf']) ? trim(strip_tags($params['_csrf'])) : '';

                $valideToken = CommonLib::isAccessTokenValid($token);
                if ($valideToken === false) {
                    return $this->formatResponse(['success' => false, 'message' => 'Đã hết thời gian xử lý. Vui lòng thử lại']);
                }else{

                    try{

                        $tbLo = Lo::findOne(['customerID' => $customerID, 'id' => $loID, 'status' => 0]);
                        if ($tbLo) {
                            $customerInfo = TbCustomers::findOne($customerID);

                            $object = $params['object'];
                            $listObj = [];
                            if (!empty($object)) {
                                foreach ($object as $value) {
                                    $listObj[$value['oid']][] = $value;
                                }
                            }

                            $order = $params['order'];
                            $debt = (double)$params['debt'];//tong tien phai thanh toan
                            $shipfee = !empty($params['shipfee']) ? (int)str_replace(',', '', $params['shipfee']) : 0;
                            $debt += $shipfee;

                            $bank = TbAccountBanking::findOne(['customerID' => $customerID]);
                            //truong hop chua co vi hoac co vi nhung so tien ko du thanh toan
                            if (!$bank || ($bank && ($debt > $bank->totalResidual)) || $debt <= 0) {
                                $message .= '<br/>Tài khoản: <b>' . $customerInfo->username . '</b> không đủ thực hiện giao dịch. Vui lòng nạp tiền.';
                                return ['message' => $message];
                            } else {

                                //du tien thanh toan
                                $success = '';
                                $message = '';
                                if (!empty($order)) {
                                    $listOrder = TbOrders::find()->where(['orderID' => $order])->all();
                                    if (!empty($listOrder)) {
                                        $totalKg = 0;
                                        $totalPayment = 0;

                                        foreach ($listOrder as $currentOrder) {
                                            $amountPay = $currentOrder->debtAmount;//+ $shipfee; //so tien phai thanh toan cho don hang nay + toem phi phat sinh neu co
                                            $totalPaid = $currentOrder->totalPaid + $amountPay; //cong tong so tien da dat coc voi so tien con thieu
                                            $totalPayment += $amountPay;
                                            //cap nhat so tien coc = tien thanh toan, tat toan don hang
                                            $currentOrder->totalPaid = $totalPaid;
                                            $currentOrder->debtAmount = 0;
                                            //luu ma vao tb_ware_houre
                                            $listBarcode = [];
                                            if (isset($listObj[$currentOrder->orderID]) && !empty($listObj[$currentOrder->orderID])) {
                                                foreach ($listObj[$currentOrder->orderID] as $item) {
                                                    $listBarcode[] = $item['barcode'];

                                                    $condition = ['loID' => $loID, 'orderID' => $item['oid'], 'tran_id' => $item['tran_id'], 'shippingCode' => trim($item['barcode'])];
                                                    $tbWareHouse = Warehouse::findOne($condition);
                                                    if (empty($tbWareHouse)) {
                                                        $warehouse = new Warehouse();
                                                        $warehouse->shippingCode = trim($item['barcode']);
                                                        $warehouse->loID = $loID;
                                                        $warehouse->tran_id = $item['tran_id'];
                                                        $warehouse->shopID = $item['sid'];
                                                        $warehouse->orderID = $item['oid'];
                                                        $warehouse->create = date('Y-m-d H:i:s');//ngay tao = ngay tra hang
                                                        $warehouse->save(false);
                                                    }
                                                }
                                            }

                                            if (!empty($listBarcode)) {
                                                //update trang thai tra hang cho cac ma
                                                TbTransfercode::updateAll(['shipStatus' => 5, 'payDate' => date('Y-m-d H:i:s')], ['transferID' => $listBarcode, 'orderID' => $currentOrder->orderID]);
                                                $message .= '<br/>Đơn hàng: <b>' . $currentOrder->identify . '</b>';
                                                $message .= '<br>Mã vận đơn: <b>' . implode(';', $listBarcode) . '</b>';
                                            }
                                            //kiem tra xem don hang co bao nhieu ma van don
                                            $allCode = TbTransfercode::find()->select('transferID,shipStatus')->where(['orderID' => $currentOrder->orderID])->asArray()->all();
                                            if (!empty($allCode)) {
                                                $total_code_pay = 0;
                                                $total_code = 0;
                                                foreach ($allCode as $tranfer) {
                                                    $total_code++;
                                                    if ($tranfer['shipStatus'] == 5) {
                                                        $total_code_pay++; //dem so ma da tra
                                                    }
                                                }

                                                if ($total_code_pay == $total_code) {
                                                    $currentOrder->status = 6; //trang thai da tra hang
                                                    $currentOrder->paymentDate = date('Y-m-d H:i:s');    //cap nhat ngay thanh toan
                                                }
                                            }

                                            //update order
                                            $currentOrder->save(false);
                                            $currentOrder = CommonLib::updateOrder($currentOrder);
                                            //lay can nang cua nhung ma da thanh toan  'status' => 1
                                            $kgPay = TbTransfercode::find()->where(['orderID' => $currentOrder->orderID, 'shipStatus' => 5, 'transferID' => $listBarcode])->sum('kgPay');
                                            $totalKg += $kgPay;

                                            //tru vi
                                            $bank->totalResidual -= $amountPay; //lay tien trong tai khoan tru di tien con thieu
                                            $bank->totalPayment += $amountPay; //cap nhat tong tien da thanh toan vai vi
                                            $bank->save(false);
                                            /*history*/
                                            $mdlTransaction = new TbAccountTransaction();
                                            $mdlTransaction->type = 5; //trang thai tra hang cho shop
                                            $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                                            $mdlTransaction->customerID = $currentOrder->customerID;
                                            $mdlTransaction->userID = \Yii::$app->user->id;//nhan vien giao dich
                                            $note = 'Thanh toán số tiền còn thiếu cho đơn hàng: <b>' . $currentOrder->identify . '</b>';
                                            $note .= '<br/>Các mã vđ đã trả: ' . implode('<br>', $listBarcode);
                                            $mdlTransaction->sapo = $note;
                                            $mdlTransaction->value = $amountPay;//so tien thanh toan
                                            $mdlTransaction->accountID = $bank->id;//ma tai khoan
                                            $mdlTransaction->balance = $bank->totalResidual;//so du sau khi giao dich
                                            $mdlTransaction->create_date = date('Y-m-d H:i:s');
                                            $mdlTransaction->save(false);

                                            //notify
                                            $msg = 'Tất toán đơn hàng: ' . $currentOrder->identify . ' Số tiền: ' . number_format($amountPay) . ' vnđ';
                                            $modelMessage = new TbChatMessage();
                                            $modelMessage->title = $msg;
                                            $modelMessage->message = $msg;
                                            $modelMessage->order_id = $currentOrder->orderID;
                                            $modelMessage->type = 2; //trang thai gui tin don hang
                                            $modelMessage->status = 0;
                                            $modelMessage->to_user_id = $currentOrder->customerID;
                                            $modelMessage->from_user_id = \Yii::$app->user->id;
                                            $modelMessage->timestamp = date('Y-m-d H:i:s');
                                            $modelMessage->save(false);
                                        }

                                        //cap nhat trang thai lo da tra hang
                                        $tbLo->name = $customerInfo->username . '-' . date('d-m-Y H:i:s');
                                        $tbLo->amount = $totalPayment; //tong tien thanh toan
                                        $tbLo->kg = $totalKg; //tong kg
                                        $tbLo->status = 1; //trang thai dong lo
                                        $tbLo->shipFee = $shipfee;
                                        $tbLo->lastDate = date('Y-m-d H:i:s'); //ngay tra hang
                                        $tbLo->save(false);

                                        //tru vi
                                        $bank->totalResidual -= $shipfee; //lay tien trong tai khoan tru di tien phat sinh
                                        $bank->totalPayment += $shipfee; //cap nhat tong tien da thanh toan vai vi
                                        $bank->save(false);

                                        /*history*/
                                        $mdlTransaction = new TbAccountTransaction();
                                        $mdlTransaction->type = 5; //trang thai tra hang cho shop
                                        $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                                        $mdlTransaction->customerID = $customerID;
                                        $mdlTransaction->userID = \Yii::$app->user->id;//nhan vien giao dich
                                        $mdlTransaction->sapo = 'Phí phát sinh khi trả hàng cho phiếu xuất ID = PXK-' . $tbLo->id;
                                        $mdlTransaction->value = $shipfee;//so tien thanh toan
                                        $mdlTransaction->accountID = $bank->id;//ma tai khoan
                                        $mdlTransaction->balance = $bank->totalResidual;//so du sau khi giao dich
                                        $mdlTransaction->create_date = date('Y-m-d H:i:s');
                                        $mdlTransaction->save(false);
                                    }
                                }

                                return ['success' => true, 'message' => $message, 'loid' => $loID];
                            }
                        }

                    }catch (Exception $e){
                        return ['success' => false, 'message' => $e->getMessage()];
                    }

                }
            }

            return ['message' => $message, 'success' => false];
        }

        //tra hang ky gui
        public function actionPayShip()
        {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $message = 'Trả hàng thất bại';
            if ($params = Yii::$app->request->post()) {

                $customerID = isset($params['customerID']) ? (int)$params['customerID'] : 0;
                $loID = isset($params['loID']) ? (int)$params['loID'] : 0;
                $token = isset($params['_csrf']) ? trim(strip_tags($params['_csrf'])) : '';

                $valideToken = CommonLib::isAccessTokenValid($token);
                if ($valideToken === false) {
                    return $this->formatResponse(['success' => false, 'message' => 'Đã hết thời gian xử lý. Vui lòng thử lại']);
                }else{
                    try {
                        if ($customerID && $loID) {
                            $tbLo = Lo::findOne(['customerID' => $customerID, 'id' => $loID, 'status' => 0]);
                            if ($tbLo) {
                                $tran_ids = $params['tran_id'];
                                $customerInfo = TbCustomers::findOne($customerID);
                                if (!empty($tran_ids)) {
                                    //get all transfer by list barcode
                                    $data = TbTransfercode::find()->where(['type' => SHIPPER_TYPE, 'status' => 1, 'shipStatus' => 3, 'id' => $tran_ids])->asArray()->all();
                                    if (!empty($data)) {
                                        $totalKg = 0;
                                        $totalPriceKg = 0;
                                        $listKg = [];
                                        $barcodes = [];
                                        foreach ($data as $val) {
                                            $totalKg += $val['kgPay'];
                                            $totalPriceKg += $val['totalPriceKg'];
                                            $barcodes[] = $val['transferID'];

                                            $tmp = [];
                                            $tmp['transferID'] = $val['transferID'];
                                            $tmp['kgPay'] = $val['kgPay'];

                                            $listKg[$val['id']] = $tmp;
                                        }

                                        $shipfee = !empty($params['shipfee']) ? str_replace(',', '', $params['shipfee']) : 0;
                                        $totalPay = round($totalPriceKg) + $shipfee;

                                        $bank = TbAccountBanking::findOne(['customerID' => $customerID]);
                                        //truong hop chua co vi hoac co vi nhung so tien ko du thanh toan
                                        if (!$bank || ($bank && ($totalPay > $bank->totalResidual))) {
                                            $message .= '<br/>Tài khoản: <b>' . $customerInfo->username . '</b> không đủ thực hiện giao dịch. Vui lòng nạp tiền.';
                                            return ['message' => $message];
                                        }


                                        //luu ma vao tb_ware_houre
                                        if (!empty($listKg)) {
                                            foreach ($listKg as $tran_id => $items) {
                                                $tbWareHouse = Warehouse::findOne(['loID' => $loID, 'tran_id' => $tran_id, 'type' => SHIPPER_TYPE, 'shippingCode' => trim($items['transferID'])]);
                                                if (empty($tbWareHouse)) {
                                                    $warehouse = new Warehouse();
                                                    $warehouse->shippingCode = trim($items['transferID']);
                                                    $warehouse->tran_id = $tran_id;
                                                    $warehouse->loID = $loID;
                                                    $warehouse->type = SHIPPER_TYPE; //loai hang ky gui
                                                    $warehouse->create = date('Y-m-d H:i:s');//ngay tao = ngay tra hang
                                                    $warehouse->save(false);
                                                }

                                                //update tb shipper
                                                TbShippers::updateAll(['weight' => $items['kgPay'], 'shippingStatus' => 5], ['userID' => $customerID, 'shippingCode' => trim($items['transferID'])]);

                                                $shipperExits = TbShippers::findOne(['shippingCode' => $items['transferID']]);
                                                if (!empty($shipperExits)) {
                                                    $msg = 'ĐH ký gửi có mã KG-' . \Yii::$app->user->id . $shipperExits->id . ' đã được trả hàng';
                                                    $modelMessage = new TbChatMessage();
                                                    $modelMessage->title = $msg;
                                                    $modelMessage->message = $msg;
                                                    $modelMessage->order_id = $shipperExits->id; //ma ky gui
                                                    $modelMessage->type = 2; //trang thai gui tin don hang
                                                    $modelMessage->isType = 'sign'; //trang thai dh kg
                                                    $modelMessage->status = 0;
                                                    $modelMessage->to_user_id = $shipperExits->userID;
                                                    $modelMessage->from_user_id = \Yii::$app->user->id;
                                                    $modelMessage->timestamp = date('Y-m-d H:i:s');
                                                    $modelMessage->save(false);
                                                }
                                            }

                                            //update trang thai tra hang cho cac ma
                                            TbTransfercode::updateAll(['shipStatus' => 5, 'payDate' => date('Y-m-d H:i:s')], ['id' => $tran_ids, 'type' => SHIPPER_TYPE]);

                                            //tru vi
                                            $bank->totalResidual -= $totalPay; //lay tien trong tai khoan tru di tong tien
                                            $bank->totalPayment += $totalPay; //cap nhat tong tien da thanh toan vai vi
                                            $bank->save(false);

                                            $message .= '<br>Mã vận đơn: <b>' . implode(';', $barcodes) . '</b>';
                                            /*history*/
                                            $mdlTransaction = new TbAccountTransaction();
                                            $mdlTransaction->type = 5; //trang thai tra hang cho shop
                                            $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                                            $mdlTransaction->customerID = $customerInfo->id;
                                            $mdlTransaction->userID = \Yii::$app->user->id;//nhan vien giao dich
                                            $note = 'Thanh toán phiếu xuất: PXK-' . $tbLo->id;
                                            $note .= '<br/>Các mã vđ đã trả: ' . implode('<br>', $barcodes);
                                            $mdlTransaction->sapo = $note;
                                            $mdlTransaction->value = $totalPay;//so tien thanh toan
                                            $mdlTransaction->accountID = $bank->id;//ma tai khoan
                                            $mdlTransaction->balance = $bank->totalResidual;//so du sau khi giao dich
                                            $mdlTransaction->create_date = date('Y-m-d H:i:s');
                                            $mdlTransaction->save(false);

                                            $tbLo->name = $customerInfo->username . '-' . date('d-m-Y H:i:s');
                                            $tbLo->amount = $totalPay; //tong tien thanh toan
                                            $tbLo->kg = $totalKg; //tong kg
                                            $tbLo->status = 1; //trang thai dong lo
                                            $tbLo->shipFee = $shipfee;
                                            $tbLo->create = date('Y-m-d H:i:s'); //ngay tra hang
                                            $tbLo->save(false);
                                            //$message .= '<br/><a target="_blank" href="' . Url::toRoute(['payment/complete', 'uid' => $customerID, 'loid' => $loID]) . '">Xem kết quả trả hàng</a>';
                                            return ['success' => true, 'message' => $message, 'loid' => $loID];

                                        }
                                    }
                                }
                            }
                        }
                    }catch (Exception $e){
                        return ['success' => false, 'message' => $e->getMessage()];
                    }
                }
            }

            return ['message' => $message, 'success' => false];
        }

        /*printer*/
        public function actionPrint()
        {
            $id = (int)Yii::$app->request->get('loid');
            $sql = "SELECT  DISTINCT a.*,c.`username`,c.`fullname`,c.`billingAddress`,c.`phone`,b.`totalResidual`
                    FROM `tb_lo` a
                    INNER JOIN tb_warehouse h ON a.`id` = h.loID
                    INNER JOIN tb_customers c ON a.`customerID` = c.`id`
                    INNER JOIN `tb_account_banking` b ON c.`id` = b.`customerID`
                    WHERE a.`id`=$id";


            $loInfo = Lo::findBySql($sql)->asArray()->one();
            if (!$loInfo) {
                return $this->redirect(['lo/index']);
            }

            //get lo detail
            $tbWareHoure = Warehouse::find()->where(['loID' => (int)$id])->asArray()->all();
            $orderIds = [];
            $barcode = [];
            if ($tbWareHoure) {
                foreach ($tbWareHoure as $item) {
                    if (!empty($item['orderID'])) {
                        $orderIds[] = $item['orderID'];
                    }
                    if (!empty($item['shippingCode'])) {
                        $barcode[] = $item['shippingCode'];
                    }
                }
            }
            $query = TbTransfercode::find()->select('b.totalPriceKg,b.transferID,b.note,b.orderID,b.identify,b.long,b.wide,b.high,b.kg,b.kgChange,b.kgPay,b.payDate,o.weightCharge, o.phikiemhang,o.phidonggo')
                ->from(TbTransfercode::tableName() . ' b')
                ->leftJoin(TbOrders::tableName() . ' o', 'b.orderID = o.orderID')
                ->where(['b.shipStatus' => 5]);

            if ($barcode) {
                $query->andWhere(['b.transferID' => $barcode]);
            }
            if ($orderIds) {
                $query->andWhere(['b.orderID' => $orderIds]);
            }

            $dataDetail = $query->asArray()->all();
            $totalKgPay = 0;
            $totalKgPrice = 0;
            $phidonggo = 0;
            $phikiemhang = 0;
            if ($dataDetail) {
                foreach ($dataDetail as $item) {
                    $phidonggo += $item['phidonggo'];
                    $phikiemhang += $item['phikiemhang'];
                    $totalKgPay += $item['kgPay'];
                    $totalKgPrice += $item['totalPriceKg'];
                }
            }


            $data = $this->renderPartial('@app/views/lo/_expand-row-details', ['data' => $dataDetail]);
            $res = $this->renderPartial('_print', [
                'totalBarcode' => count($barcode),
                'loInfo'       => $loInfo,
                'data'         => $data,
                'totalKgPrice' => $totalKgPrice,
                'phikiemhang'  => $phikiemhang,
                'phidonggo'    => $phidonggo,
            ]);

            $title = 'Thống kê trả hàng lô: ' . $id . ', Khách hàng: ' . $loInfo['fullname'];
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ['data' => isset($res) ? $res : '', 'title' => $title];
        }

        public function getAllShop($customerID, $loID, $post, $bank)
        {
            $order = [];
            if ($customerID && $loID) {
                $result = TbOrders::getShopPayment($customerID, $loID);
                if ($result) {
                    foreach ($result as $item) {
                        $order[$item['id']][$item['did']] = $item;
                    }
                }
            }

            $res = $this->renderAjax('_ajax_load_shop', [
                'customerID' => $customerID,
                'loID'       => $loID,
                'order'      => $order,
                'post'       => $post,
                'bank'       => $bank,
            ]);
            return ['data' => $res, 'empty' => count($order) > 0 ? 0 : 1];
        }

        //delete shop
        public function actionDeleteShop()
        {

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $sms = 'Xóa thất bại';
            $status = false;
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                if ($post) {
                    $orderID = (int)$post['orderID'];
                    $shopID = (int)$post['shopID'];
                    if ($orderID && $shopID) {
                        $transfer = TbTransfercode::findOne(['shopID' => $shopID, 'orderID' => $orderID]);
                        if ($orderSupplier = TbOrderSupplier::findOne(['orderID' => $orderID, 'id' => $shopID])) {
                            $orderSupplier->isSelected = 0;
                            $orderSupplier->save(false);
                            $sms = 'Shop có mã vận đơn "' . $transfer->transferID . '" đã được xóa khỏi danh sách trả hàng';
                            $status = true;
                        }
                    }

                }
            }
            return ['status' => $status, 'sms' => $sms];
        }
    }