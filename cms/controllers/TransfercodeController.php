<?php

namespace cms\controllers;

use cms\models\Lo;
use common\components\CommonLib;
use common\models\AccessRule;
use common\models\TbCustomers;
use common\models\TbOrders;
use common\models\TbOrderSupplier;
use common\models\TbShippers;
use common\models\TbShipping;
use Yii;
use common\models\TbTransfercode;
use common\models\TbTransfercodeSearch;
use common\components\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\TbHistory;

/**
 * TransfercodeController implements the CRUD actions for TbTransfercode model.
 */
class TransfercodeController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'create', 'update', 'view', 'delete', 'delCodeShip', 'save', 'saveShipCode', 'deleteCode'],
                'rules' => [
                    [

                        'allow' => true,
                        'roles' => [WAREHOUSETQ, WAREHOUSE],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => false,
                        'roles' => [BUSINESS]
                    ],
                    [
                        'actions' => [],
                        'allow' => false,
                        'roles' => [STAFFS]
                    ],
                    [
                        'allow' => true,
                        'roles' => [ADMIN]
                    ],
                ],
            ],
        ];
    }

    /*luu kg tinh tien theo ma*/
    public function actionUpdatekg()
    {
        $message = 'Cập nhật thất bại';
        $success = false;
        $error = 0;
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
          //  pr($params);die;
            if (isset($params['cid']) && $params['cid'] > 0) {
                //cap nhat kg va cac thong so cho ma co trang thai ok kho vn
                $tbTranfer = TbTransfercode::findOne($params['id']);
              //  pr($tbTranfer);die;
                if ($tbTranfer && $tbTranfer->shipStatus != 5) {
                    //kiem tra ma chua chon thi chon
                    $tbTranfer->kg = (double)$params['kg'];
                    $tbTranfer->long = (double)$params['long'];
                    $tbTranfer->wide = (double)$params['wide'];
                    $tbTranfer->high = (double)$params['high'];
                    $tbTranfer->kgChange = (double)$params['kgChange'];
                    $tbTranfer->kgPay = (double)$params['kgPay'];
                    $tbTranfer->status = (isset($params['type']) && $params['type'] == 1) ? 1 : (int)$params['checked'];
                    $tbTranfer->note = $params['note'];
                    $tbTranfer->quantity = (int)$params['qty'];

                    $tbTranfer->save(false);

                    $tbHistory = new TbHistory();
                    $tbHistory->orderID = isset($params['oid'])? $params['oid']: 0;
                    $tbHistory->userID = Yii::$app->user->id;
                    $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                    $tbHistory->content .= 'Cập nhật thông tin kiện: <b>' . $tbTranfer->transferID . '</b><br/>Mã đơn hàng: <b>'.$tbTranfer->identify.'</b><br>';

                    if ($tbTranfer->kg != $tbTranfer->oldAttributes['kg']) {
                        $tbHistory->content .= 'Thay đổi cân nặng thực tế: <b>' . $tbTranfer->oldAttributes['kg'] . '</b>kg thành <b>' . $tbTranfer->kg . '</b>kg <br>';
                    }
                    if ($tbTranfer->long != $tbTranfer->oldAttributes['long']) {
                        $tbHistory->content .= 'Thay đổi chiều dài: <b>' . $tbTranfer->oldAttributes['long'] . '</b> thành <b>' . $tbTranfer->long . '</b> <br>';
                    }

                    if ($tbTranfer->wide != $tbTranfer->oldAttributes['wide']) {
                        $tbHistory->content .= 'Thay đổi chiều rộng: <b>' . $tbTranfer->oldAttributes['wide'] . '</b> thành <b>' . $tbTranfer->wide . '</b> <br>';
                    }
                    if ($tbTranfer->high != $tbTranfer->oldAttributes['high']) {
                        $tbHistory->content .= 'Thay đổi chiều cao: <b>' . $tbTranfer->oldAttributes['high'] . '</b> thành <b>' . $tbTranfer->high . '</b> <br>';
                    }
                    if ($tbTranfer->quantity != $tbTranfer->oldAttributes['quantity']) {
                        $tbHistory->content .= 'Thay đổi số lượng: <b>' . $tbTranfer->oldAttributes['quantity'] . '</b> thành <b>' . $tbTranfer->quantity . '</b> <br>';
                    }

                    $tbHistory->save(false);

                    $customer = TbCustomers::findOne($params['cid']);
                    //th la order
                    $totalWeight = 0;
                    $totalPayment = 0;
                    $totalPaid = 0;
                    $debtAmount = 0;
                    $kgfee = 0;

                    $tbOrder = TbOrders::findOne(['orderID' => $params['oid']]);
                    if (!empty($tbOrder)) {
                        if($tbOrder->status == 5){
                            $error = 3;
                            return $this->formatResponse(['success' => false, 'error' => $error, 'message' => 'Đơn hàng này đã bị hủy không thể cập nhật.']);
                        }
                        //tong kg theo don hang co ma duoc tich chon tra hang
                        $totalKg = TbTransfercode::find()->where(['orderID' => (int)$params['oid'], 'status' => 1])->sum('kgPay');
                        $tbOrder->totalWeight = $totalKg;
                        $totalQuantity = TbTransfercode::find()->where(['orderID' => (int)$params['oid']])->sum('quantity');
                        $tbOrder->quantity = $totalQuantity;

                        if ($totalQuantity > $tbOrder->totalQuantity) {
                            $error = 3;
                            return $this->formatResponse(['success' => false, 'error' => $error, 'message' => 'Số lượng kiểm đếm không được lớn hơn số lượng đơn hàng']);
                        }

                        if ($tbOrder->quantity == 0) {
                            $error = 2;
                        }

                        //phi kg
                        //check de lay kg
                        $orderIDCheck = ($tbOrder->status != 1) ? $tbOrder->orderID : null;
                        $tbOrder->weightCharge = CommonLib::getKgofOrder($orderIDCheck,$tbOrder->totalWeight, $customer->discountKg, $tbOrder->weightDiscount, $tbOrder->provinID);
                        $tbTranfer->kgFee = $tbOrder->weightCharge;
                        $tbTranfer->totalPriceKg = $tbOrder->weightCharge * $tbTranfer->kgPay;
                        $tbTranfer->save(false);//update lai tien can nang va phi ap dung tuong ung

                        $tbOrder->save(false);

                        if ($tborderSup = TbOrderSupplier::findOne(['id' => $params['sid']])) {
                            $tborderSup->weight = $tbOrder->totalWeight;
                            $tborderSup->save(false);
                        }

                        //update all
                        $tbOrder = CommonLib::updateOrder($tbOrder);
                        $gtdh = $tbOrder['totalOrder'] + $tbOrder['orderFee'] + $tbOrder['incurredFee'] + $tbOrder['totalShipVn'];

                        $totalWeight = $tbOrder->totalWeight;
                        $totalWeightPrice = $tbOrder->totalWeightPrice;
                        $totalPayment = $tbOrder->totalPayment;
                        $totalPaid = $tbOrder->totalPaid;
                        $debtAmount = $tbOrder->debtAmount;
                        $phidonggo = $tbOrder->phidonggo;
                    }else{
                        //can nang ky gui weightFee
                        $weightCharge = is_numeric($customer->weightFee) ? $customer->weightFee : CommonLib::getKgofOrder(null,$tbTranfer->kgPay, $customer->discountKg, null);
                        $tbTranfer->kgFee = $weightCharge;
                        $tbTranfer->totalPriceKg = $weightCharge * $tbTranfer->kgPay;
                        $tbTranfer->save(false);//update lai tien can nang va phi ap dung tuong ung

                        $shippers = TbShippers::find()
                                 ->select('a.kgPay,a.totalPriceKg')
                                  ->from(TbShippers::tableName().' si')
                                  ->innerJoin(TbShipping::tableName().' s','si.id = s.`shipperID`')
                                  ->innerJoin(TbTransfercode::tableName().' a','s.tranID = a.`id`')
                                  ->where(['si.userID'=>$params['cid'],'a.shipStatus'=>3,'a.status'=>1,'a.type'=>SHIPPER_TYPE,'s.city'=>1])
                                  ->asArray()->all();
                        if(!empty($shippers)){
                            foreach ($shippers as $item){
                                $totalWeight += $item['kgPay'];
                                $totalPayment += $item['totalPriceKg'];
                            }
                        }

                       // $shipperExits = TbShippers::findOne(['shippingCode' => $tbTranfer->transferID]);
                        $shipperExits = TbShippers::find()->where(['like', 'shippingCode', $tbTranfer->transferID])->one();
                        if($shipperExits){
                            $shipperExits->weight = $tbTranfer->kgPay;
                             $shipperExits->save(false);
                        }    

                        $totalWeightPrice = $tbTranfer->totalPriceKg;
                        $kgfee = $tbTranfer->kgFee;
                    }

                    $data = [
                        'quantity' => $tbTranfer->quantity,
                        'gtdh' => isset($gtdh) ? round($gtdh) : 0,
                        'kgfee' => (double)$kgfee,
                        'totalWeight' => $totalWeight,
                        'totalWeightPrice' => round($totalWeightPrice),
                        'totalPayment' => round($totalPayment),
                        'totalPaid' => $totalPaid,
                        'debtAmount' => round($debtAmount),
                        'phidonggo' => isset($phidonggo) ? $phidonggo : 0,
                    ];

                    // }
                     $message = 'Cập nhật thành công.';
                     $success = true;
                }else{
                     $message = 'Cập nhật thất bại.';
                     $success = false;
                }

               
            }
        }

        return $this->formatResponse(['success' => $success, 'error' => $error, 'message' => $message, 'data' => isset($data) ? $data : false]);
    }

    //update all
    public function actionSave()
    {
        $message = 'Cập nhật thất bại';
        $success = false;
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            if (!empty($params)) {
                $listCode = isset($params['myArray']) ? $params['myArray'] : [];
                $loID = isset($params['loID']) ? (int)$params['loID'] : 0;
                $shipfee = isset($params['shipfee']) ? (int)(str_replace(',','',$params['shipfee'])) : 0;
                $tbLo = Lo::findOne($loID);
                if($tbLo){
                    $tbLo->shipFee = $shipfee;
                    $tbLo->save(false);
                }
                if(!empty($listCode)) {
                    foreach ($listCode as $item) {
                        //cap nhat kg va cac thong so cho ma co trang thai ok kho vn
                        $tbTranfer = TbTransfercode::findOne($item['id']);
                        if ($tbTranfer) {
                            $tbOrder = TbOrders::findOne(['orderID' => $item['oid']]);
                            if ($tbOrder) {
                                $customer = TbCustomers::findOne($tbOrder->customerID);
                                $discountKg = 0;
                                if ($customer) {
                                    $discountKg = isset($customer->discountKg) ? $customer->discountKg : 0;
                                }
                                //kiem tra ma chua chon thi chon
                                $tbTranfer->kg = $item['kg'];
                                $tbTranfer->long = $item['long'];
                                $tbTranfer->wide = $item['wide'];
                                $tbTranfer->high = $item['high'];
                                $tbTranfer->kgChange = $item['kgChange'];
                                $tbTranfer->kgPay = $item['kgPay'];
                                $tbTranfer->note = $item['note'];
                                $tbTranfer->status = $item['checkbox'];//trang thai tich chon ma
                                $tbTranfer->save(false);

                                //lay phi can nang duoc ap dung
                                $totalKg = (double)TbTransfercode::find()->where(['orderID' => (int)$item['oid'], 'status' => 1])->sum('kgPay');
                                $tbOrder->totalWeight = $totalKg;
                                //get tien giam gia kg
                                //check de lay kg
                                $orderIDCheck = ($tbOrder->status != 1) ? $tbOrder->orderID : null;
                                $tbOrder->weightCharge = CommonLib::getKgofOrder($orderIDCheck, $tbOrder->totalWeight, $discountKg, $tbOrder->weightDiscount, $tbOrder->provinID);

                                //
                                $totalQuantity = TbTransfercode::find()->where(['orderID' => (int)$item['oid']])->sum('quantity');
                                $tbOrder->quantity = $totalQuantity;
                                if ($totalQuantity > $tbOrder->totalQuantity) {
                                    //  $error = 3;
                                    // return $this->formatResponse(['success' => false,'error'=>$error, 'message' => 'Số lượng kiểm đếm không được lớn hơn số lượng đơn hàng']);
                                }
                                if ($tbOrder->quantity == 0) {
                                    $error = 2;
                                }
                                //
                                $tbOrder->save(false);
                                $tbTranfer->kgFee = $tbOrder->weightCharge;
                                $tbTranfer->totalPriceKg = $tbOrder->weightCharge * $tbTranfer->kgPay;
                                $tbTranfer->save(false);//update lai tien can nang va phi ap dung tuong ung

                                if ($tborderSup = TbOrderSupplier::findOne(['id' => $item['sid']])) {
                                    $tborderSup->weight = $tbOrder->totalWeight;
                                    $tborderSup->save(false);
                                }
                                //update all
                                CommonLib::updateOrder($tbOrder);
                            }
                        }
                    }
                }



                $success = true;
                $message = '';
            }
        }

        return $this->formatResponse(['success' => $success, 'message' => $message]);
    }

    public function actionDeleteCode()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            if ($params) {
                $tbTranfer = TbTransfercode::findOne((int)$params['id']);
                if ($tbTranfer) {
                    $tbOrder = TbOrders::findOne(['orderID' => (int)$params['oid']]);
                    if ($tbOrder) {
                        $totalWeight = $tbOrder->totalWeight;
                        if ($tbTranfer->status == 1) { //truong hop tich chon tra hang
                            $totalWeight = ($totalWeight >= $tbTranfer->kgPay ? $totalWeight - $tbTranfer->kgPay : 0);//tong kg - so kg da xoa
                            if ($tborderSup = TbOrderSupplier::findOne($params['sid'])) {
                                $tborderSup->weight = $totalWeight;
                                $tborderSup->save(false);
                            }

                            $tbOrder->totalWeight = $totalWeight;
                            $tbOrder->save(false);//cap nhat lai can nang don hang
                            $tbTranfer->status = 0;//trang thai bo chon tra hang
                            $tbTranfer->save(false);
                            $tbOrder = CommonLib::updateOrder($tbOrder);
                        }


                      
                        //totalkgPay, total debt is checked
                        $res = TbTransfercode::getTotalKgofPrice($tbOrder->customerID);

                        return $this->formatResponse(
                            [
                                'data' => [
                                    'numCode' => $res['numCode'],
                                    'totalKgPay' => (double)$res['kgPay'],
                                    'debt' => (double)$res['debt'],
                                    'totalKg' => $totalWeight,
                                    'debtAmount' => number_format($tbOrder->debtAmount),
                                    'totalPaid' => number_format($tbOrder->totalPaid),
                                    'weightPrice' => number_format($tbOrder->totalWeightPrice),
                                    'totalPayment' => number_format($tbOrder->totalPayment)
                                ],
                                'message' => 'Đã xóa thành công.']
                        );
                    }

                }
            }
        }

        return $this->formatResponse(['message' => 'Đã xóa thành công.']);
    }

    public function actionSaveShipCode()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            if ($params) {
                //cap nhat kg va cac thong so cho ma co trang thai ok kho vn 'shopID' => $params['sid'],
                $tbTranfer = TbTransfercode::findOne(['type' => 1, 'shipStatus' => 3, 'transferID' => trim($params['mvd'])]);
                if ($tbTranfer && $customer = TbCustomers::findOne((int)$params['cusId'])) {
                    $tbTranfer->kg = $params['kg'];
                    $tbTranfer->long = $params['long'];
                    $tbTranfer->wide = $params['wide'];
                    $tbTranfer->high = $params['high'];
                    $tbTranfer->kgChange = $params['kgChange'];
                    $tbTranfer->kgPay = $params['kgPay'];
                    $tbTranfer->note = $params['note'];
                    $tbTranfer->status = 1;//trang thai tich chon ma
                    $kgFee = CommonLib::getKgofOrder(null,$tbTranfer->kgPay, $customer->discountKg, null, $customer->provinID);
                    $kgPay = $tbTranfer->kgPay > $tbTranfer->kg ? $tbTranfer->kgPay : $tbTranfer->kg;
                    $tbTranfer->totalPriceKg = $kgFee * $kgPay;
                    $tbTranfer->save(false);
                    $totalKg = 0;
                    $totalPrice = 0;

                    $dbInfo = TbTransfercode::getAllOrderShipVnByCustomerId($customer->id, 3, 1);
                    $num_code = 0;
                    if ($dbInfo) {
                        foreach ($dbInfo as $item) {
                            $num_code++;
                            $totalKg += $item['kgPay'];
                            $totalPrice += $item['totalPriceKg'];
                        }
                    }

                    return $this->formatResponse(
                        ['data' => [
                            'totalKg' => $totalKg,
                            'numCode' => $num_code,
                            'totalPrice' => number_format($totalPrice),
                            'feekg' => number_format($tbTranfer->totalPriceKg),
                        ], 'message' => 'Cập nhật thành công.']
                    );

                }
            }
        }

        return $this->formatResponse(['message' => 'Cập nhật thất bại.']);
    }


    //xoa ma ky gui
    public function actionDelCodeShip()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            if ($params) {
                $tbTranfer = TbTransfercode::findOne(['type' => 1, 'shipStatus' => 3, 'transferID' => trim($params['mvd'])]);
                if ($tbTranfer && $customer = TbCustomers::findOne((int)$params['cusId'])) {
                    if ($tbTranfer->status == 1) {
                        $tbTranfer->status = 0;//trang thai bo chon tra hang
                        $tbTranfer->save(false);
                    }

                    $totalKg = 0;
                    $totalPrice = 0;

                    $dbInfo = TbTransfercode::getAllOrderShipVnByCustomerId($customer->id, 3, 1);
                    $num_code = 0;
                    if ($dbInfo) {
                        foreach ($dbInfo as $item) {
                            $num_code++;
                            $totalKg += $item['kgPay'];
                            $totalPrice += $item['totalPriceKg'];
                        }
                    }

                    return $this->formatResponse(
                        ['data' => [
                            'totalKg' => $totalKg,
                            'numCode' => $num_code,
                            'totalPrice' => number_format($totalPrice),
                        ], 'message' => 'Xóa thành công.']
                    );
                }
            }
        }

        return $this->formatResponse(['message' => 'Xóa không thành công.']);
    }

    /**
     * Lists all TbTransfercode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TbTransfercodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single TbTransfercode model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render(
            'view', [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new TbTransfercode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TbTransfercode();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render(
                'create', [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Updates an existing TbTransfercode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render(
                'update', [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing TbTransfercode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (($model = TbTransfercode::findOne($id))) {
            $tbOrder = TbOrders::findOne(['orderID' => $model->orderID]);

            //update history
            $tbHistory = new TbHistory();
            $tbHistory->orderID = isset($tbOrder->orderID) ? $tbOrder->orderID : '';
            $tbHistory->userID = Yii::$app->user->id;
            $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
            $tbHistory->content .= 'xóa mã vận đơn : <b>' .$model->transferID . '</b>';
            $tbHistory->save(false);

            $model->delete();
        } else {
            $this->error = 'Xóa mã thất bại';
        }

        return $this->formatResponse('Xóa mã thành công');
    }

    /**
     * Finds the TbTransfercode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TbTransfercode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbTransfercode::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
