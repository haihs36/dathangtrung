<?php

    namespace cms\controllers;

    use cms\models\Payment;
    use cms\models\Warehouse;
    use common\components\CommonLib;
    use common\components\Controller;
    use common\models\AccessRule;
    use common\models\TbAccountBanking;
    use common\models\TbCustomers;
    use common\models\TbHistory;
    use common\models\TbOrders;
    use common\models\TbOrderSupplier;
    use common\models\TbShippers;
    use common\models\TbShipping;
    use common\models\TbTransfercode;
    use Yii;
    use cms\models\Lo;
    use cms\models\LoSearch;
    use yii\filters\AccessControl;
    use yii\helpers\Url;
    use yii\web\NotFoundHttpException;
    use yii\filters\VerbFilter;

    class LoController extends Controller
    {


        public function behaviors()
        {
            return [
                'access' => [
                    'class'      => AccessControl::className(),
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'only'       => ['index', 'delete', 'detailrel', 'update', 'create'],
                    'rules'      => [
                        [
                            'actions' => ['index', 'delete', 'detailrel', 'update', 'create'],
                            'allow'   => true,
                            'roles'   => [WAREHOUSE, ADMIN],
                        ],
//                    [
//                        'actions' => ['index', 'delete'],
//                        'allow' => true,
//                        'roles' => [ADMIN],
//                    ],
                        [
                            'actions' => ['index'],
                            'allow'   => false,
                            'roles'   => [BUSINESS]
                        ],
                        [
                            'actions' => ['index', 'delete', 'detailrel', 'update'],
                            'allow'   => false,
                            'roles'   => [WAREHOUSETQ]
                        ],
                    ],
                ],
            ];
        }


        /**
         * Lists all Lo models.
         * @return mixed
         */
        public function actionIndex()
        {
            $searchModel = new LoSearch();
            $params = Yii::$app->request->queryParams;
            $dataProvider = $searchModel->search($params);

            return $this->render(
                'index', [
                    'searchModel'  => $searchModel,
                    'dataProvider' => $dataProvider,
                    'params'       => $params,
//                'pxks' => $pxks,
                ]
            );
        }

        /*
         Hien thi expand-detail-row
         * */
        public function actionDetail()
        {
            if (isset($_POST['expandRowKey'])) {
                $id = (int)$_POST['expandRowKey'];

                $tbLo = Lo::findOne(['id' => $id]);
                if (!$tbLo) {
                    return '<div class="alert alert-danger">No data found</div>';
                }

                $keys = !empty($tbLo->delivery) ? explode(',', $tbLo->delivery) : [];
                $orderIds = $barcode = [];
                //da tra hang
                if ($tbLo->status == 1) {
                    $tbWareHoure = Warehouse::find()->where(['loID' => $id])->asArray()->all();
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
                }

                if ($tbLo->payType == 2) { //hang order
                    $query = TbTransfercode::find()->select('b.totalPriceKg,b.transferID,b.note,b.orderID,b.identify,b.long,b.wide,b.high,b.kg, b.kgChange,b.kgPay,b.payDate,o.weightCharge, o.phikiemhang,o.phidonggo')
                        ->from(TbTransfercode::tableName() . ' b')
                        ->leftJoin(TbOrders::tableName() . ' o', 'b.orderID = o.orderID')
                        ->where(['o.customerID' => (int)$tbLo->customerID]);



                    if (isset($orderIds) && !empty($orderIds)) {
                        $query->andWhere(['b.orderID' => $orderIds]);
                    }

                } else { //hang ky gui
                    $query = TbTransfercode::find()->select('b.totalPriceKg,b.transferID,b.note,b.orderID,b.identify,b.long,b.wide,b.high,b.kg,b.kgChange,b.kgPay,b.payDate,b.kgfee')
                        ->from(TbTransfercode::tableName() . ' b')
                        ->leftJoin(TbShipping::tableName() . ' si', 'b.id = si.tranID')
                        ->leftJoin(TbShippers::tableName() . ' sp', 'si.shipperID = sp.id')
                        ->where(['sp.userID' => (int)$tbLo->customerID]);

                }

                if(in_array($tbLo->status,[1,2,3])){
                    $query->andWhere(['b.shipStatus' => 5]);//da hoan thanh thi lay ma da tra
                }else{
                    $query->andWhere(['b.shipStatus' => 3]); //neu chua hoan thanh phieu thi show cac ma da ve kho vn
                }

                if (!empty($barcode)) {
                    $query->andWhere(['b.transferID' => $barcode]);
                }

                if (isset($keys) && !empty($keys)) {
                    $query->andWhere(['b.id' => $keys]);
                }

                $dataDetail = $query->asArray()->all();

                return $this->renderPartial('_expand-row-details', ['data' => $dataDetail, 'loInfo' => $tbLo]);
            } else {
                return '<div class="alert alert-danger">No data found</div>';
            }
        }

        /**
         *display lo detail
         * shipStatus = 5 => da tra hang
         */
        public function actionView($id)
        {

            $sql = "SELECT  DISTINCT a.*,c.`username`,c.`fullname`,c.`billingAddress`,c.`phone`,b.`totalResidual`
                    FROM `tb_lo` a
                    INNER JOIN tb_customers c ON a.`customerID` = c.`id`
                    INNER JOIN `tb_account_banking` b ON c.`id` = b.`customerID`
                    WHERE a.`id`=$id";

            $loInfo = Lo::findBySql($sql)->asArray()->one();
            if (!$loInfo) {
                return $this->redirect(['lo/index']);
            }

            //get lo detail
            $tbWareHoure = Warehouse::find()->where(['loID' => $id])->asArray()->all();
            $barcode = $orderIds = $tran_ids = [];
            if ($tbWareHoure) {
                foreach ($tbWareHoure as $item) {
                    if (!empty($item['orderID'])) {
                        $orderIds[] = $item['orderID'];
                    }
                    if (!empty($item['shippingCode'])) {
                        $barcode[] = $item['shippingCode'];
                    }
//                    if (isset($item['tran_id']) && !empty($item['tran_id'])) {
//                        $tran_ids[] = $item['tran_id'];
//                    }
                }

                $customerID = $loInfo['customerID'];


                if (!empty($orderIds)) {
                    $query = TbTransfercode::find()->select('b.totalPriceKg,b.transferID,b.note,b.orderID,b.identify,b.long,b.wide,b.high,b.kg,
                    b.kgChange,b.kgPay,b.payDate,o.weightCharge, o.phikiemhang,o.phidonggo')
                        ->from(TbTransfercode::tableName() . ' b')
                        ->innerJoin(TbOrders::tableName() . ' o', 'b.orderID = o.orderID')
                        ->where(['o.customerID' => (int)$customerID, 'b.shipStatus' => 5, 'b.orderID' => $orderIds]);

                } else {
                    $query = TbTransfercode::find()->select('b.totalPriceKg,b.transferID,b.note,b.orderID,b.identify,b.long,b.wide,b.high,b.kg,b.kgChange,b.kgPay,b.payDate,b.kgfee')
                        ->from(TbTransfercode::tableName() . ' b')
                        ->leftJoin(TbShipping::tableName() . ' si', 'b.id = si.tranID')
                        ->leftJoin(TbShippers::tableName() . ' sp', 'si.shipperID = sp.id')
                        ->where(['sp.userID' => (int)$customerID, 'b.shipStatus' => 5]);

                }

                if (!empty($barcode)) {
                    $query->andWhere(['b.transferID' => $barcode]);
                }

                $totalKgPay = 0;
                $totalKgPrice = 0;
                $phidonggo = 0;
                $phikiemhang = 0;
                $dataDetail = $query->asArray()->all();

                if ($dataDetail) {
                    foreach ($dataDetail as $item) {
                        if (isset($item['phidonggo']) && $item['phidonggo'] > 0) {
                            $phidonggo += $item['phidonggo'];
                        }
                        if (isset($item['phikiemhang']) && $item['phikiemhang'] > 0) {
                            $phikiemhang += $item['phikiemhang'];
                        }

                        $totalKgPay += $item['kgPay'];
                        $totalKgPrice += $item['totalPriceKg'];
                    }
                }
            }


            $data = $this->renderPartial('_expand-row-details', ['data' => isset($dataDetail) ? $dataDetail : [], 'loInfo' => $loInfo]);

            return $this->render(
                'ex-warehouse', [
                    'totalBarcode' => count($barcode),
                    'loInfo'       => $loInfo,
                    'data'         => $data,
                    'totalKgPrice' => isset($totalKgPrice) ? $totalKgPrice : 0,
                    'phikiemhang'  => isset($phikiemhang) ? $phikiemhang : 0,
                    'phidonggo'    => isset($phidonggo) ? $phidonggo : 0,
                ]
            );

        }


        public function actionDelivery($id)
        {
            $sql = "SELECT  DISTINCT a.*,c.`username`,c.`fullname`,c.`billingAddress`,c.`phone`,b.`totalResidual`
                    FROM `tb_lo` a
                    INNER JOIN tb_customers c ON a.`customerID` = c.`id`
                    INNER JOIN `tb_account_banking` b ON c.`id` = b.`customerID`
                    WHERE a.`id`=$id";

            $loInfo = Lo::findBySql($sql)->asArray()->one();
            if (!$loInfo) {
                return $this->redirect(['lo/index']);
            }

            $customerID = (int)$loInfo['customerID'];
            $keys = !empty($loInfo['delivery']) ? explode(',', $loInfo['delivery']) : [];

            if ($loInfo['payType'] == 2) {//hang order
                $query = TbTransfercode::find()->select('b.totalPriceKg,b.transferID,b.note,b.orderID,b.identify,b.long,b.wide,b.high,b.kg, b.kgChange,b.kgPay,b.payDate,o.weightCharge, o.phikiemhang,o.phidonggo')
                    ->from(TbTransfercode::tableName() . ' b')
                    ->leftJoin(TbOrders::tableName() . ' o', 'b.orderID = o.orderID')
                    ->where(['o.customerID' => (int)$customerID]);

            } else { //ky gui
                $query = TbTransfercode::find()->select('b.totalPriceKg,b.transferID,b.note,b.orderID,b.identify,b.long,b.wide,b.high,b.kg,b.kgChange,b.kgPay,b.payDate,b.kgfee')
                    ->from(TbTransfercode::tableName() . ' b')
                    ->leftJoin(TbShipping::tableName() . ' si', 'b.id = si.tranID')
                    ->leftJoin(TbShippers::tableName() . ' sp', 'si.shipperID = sp.id')
                    ->where(['sp.userID' => (int)$customerID]);

            }

            if (isset($keys) && !empty($keys)) {
                $query->andWhere(['b.id' => $keys]);
            }

            $totalKgPay = 0;
            $totalKgPrice = 0;
            $phidonggo = 0;
            $phikiemhang = 0;
            $dataDetail = $query->asArray()->all();
            if ($dataDetail) {
                foreach ($dataDetail as $item) {
                    if (isset($item['phidonggo']) && $item['phidonggo'] > 0) {
                        $phidonggo += $item['phidonggo'];
                    }
                    if (isset($item['phikiemhang']) && $item['phikiemhang'] > 0) {
                        $phikiemhang += $item['phikiemhang'];
                    }

                    $totalKgPay += $item['kgPay'];
                    $totalKgPrice += $item['totalPriceKg'];
                }
            }

            $data = $this->renderPartial('_expand-row-details', ['data' => isset($dataDetail) ? $dataDetail : [], 'loInfo' => $loInfo]);

            return $this->render(
                'ex-warehouse', [
                    'totalBarcode' => count($keys),
                    'loInfo'       => $loInfo,
                    'data'         => $data,
                    'totalKgPrice' => isset($totalKgPrice) ? $totalKgPrice : 0,
                    'phikiemhang'  => isset($phikiemhang) ? $phikiemhang : 0,
                    'phidonggo'    => isset($phidonggo) ? $phidonggo : 0,
                ]
            );

        }



        /**
         * Creates a new Lo model.
         * If creation is successful, the browser will be redirected to the 'view' page.
         * @return mixed
         */
        public function actionCreate()
        {
            $model = new Lo();
            $error = 0;
            if ($model->load(Yii::$app->request->post())) {
                $model->userID = Yii::$app->user->id; //nguoi tao lo

                // var_dump($model->customerID);die;

                if ($model->customerID) {
                    if (Lo::findOne(['customerID' => $model->customerID, 'payType' => $model->payType, 'status' => 0])) {
                        $this->flash('danger', 'Đã tồn tại phiếu chờ xuất vui lòng kiểm tra lại');
                        return $this->render(
                            'create', [
                                'model' => $model,
                                'error' => 0
                            ]
                        );
                    }

                    $data = [];
                    switch ($model->payType) {
                        case ORDER_TYPE: //hang order = 2
                            $data = TbTransfercode::getAllBarcodeVnByCustomerId($model->customerID);
                            break;
                        case SHIPPER_TYPE: //hang ky gui = 1

                            $data = TbTransfercode::getAllOrderShipVnByCustomerId($model->customerID);
                            break;
                    }

                    if ($data) {
                        //check account
                        /*$bank = TbAccountBanking::findOne(['customerID' => $model->customerID]);
                        if (!$bank || ($bank && $bank->totalResidual <= 0)) {
                            $error = 2;
                        } else {*/
                        $model->save(false);
                        return $this->redirect(['update', 'id' => $model->id]);
//                    }

                    } else {
                        $error = 1;
                    }

                }
            }

            return $this->render(
                'create', [
                    'model' => $model,
                    'error' => $error
                ]
            );

        }

        /**
         * Ban ma van don cho don hang
         */
        public function actionUpdate($id)
        {
            //chi lay nhung phieu chua tra hang
            $tbLo = Lo::findOne(['id' => $id, 'status' => 0]);
            if (!$tbLo) {
                return $this->redirect(['lo/index']);
            }
            $customerID = $tbLo->customerID;
            //load shop
            if ($tbLo && $customerID) {
                $customer = TbCustomers::find()->select('')->where(['id' => $customerID])->one();

                switch ($tbLo->payType) {
                    case ORDER_TYPE: //hang order = 2
                        $data = TbTransfercode::getAllBarcodeVnByCustomerId($customerID);
                        $order = [];
                        $tran_ids = [];
                        if ($data) {
                            foreach ($data as $item) {
//                            $item['status'] = 1;
                                $order[$item['orderID']][] = $item;
                                $tran_ids[] = $item['id'];
                            }
                        }
                        //update all is checked
                        /*if(!empty($tran_ids)){
                            TbTransfercode::updateAll(['status' => 1], ['id' => $tran_ids]);
                        }*/
                        //  pr($order);die;

                        $dataRender = $this->renderPartial(
                            '@app/views/payment/_ajax_load_shop', [
                                'loID'     => $tbLo->id,
                                'order'    => $order,
                                'customer' => $customer,
                                'token'        =>  CommonLib::generateAccessToken(),
                            ]
                        );
                        break;
                    case SHIPPER_TYPE: //hang ship = 1
                        $data = TbTransfercode::getAllOrderShipVnByCustomerId($customerID);
                        $dataRender = $this->renderPartial(
                            '@app/views/payment/_order_shipper', [
                                'loID'     => $tbLo->id,
                                'data'     => $data,
                                'customer' => $customer,
                                'token'        =>  CommonLib::generateAccessToken(),
                            ]
                        );

                        break;

                }
            }


            return $this->render(
                'update', [
                    'model'      => $tbLo,
                    'dataRender' => isset($dataRender) ? $dataRender : '',
                    'error'      => 3
                ]
            );

        }

        /**
         * Deletes an existing Lo model.
         * If deletion is successful, the browser will be redirected to the 'index' page.
         * @param integer $id
         * @return mixed
         */
        public function actionDelete($id)
        {
            $lo = $this->findModel($id);
            if ($lo) {
                $tbHistory = new TbHistory();
                $tbHistory->orderID = 0;
                $tbHistory->userID = Yii::$app->user->id;
                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>Đã xóa phiếu xuất có mã: <b>PXK-' . $lo->id . '</b><br/>';
                $tbHistory->save(false);

                Warehouse::deleteAll(['loID' => $lo->id]);//xoa cac mvd thuoc lo
                $lo->delete();
            } else {
                $this->error = 'Not found';
            }

            return $this->formatResponse('delete success');
        }

        /**
         * Finds the Lo model based on its primary key value.
         * If the model is not found, a 404 HTTP exception will be thrown.
         * @param integer $id
         * @return Lo the loaded model
         * @throws NotFoundHttpException if the model cannot be found
         */
        protected function findModel($id)
        {
            if (($model = Lo::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
    }
