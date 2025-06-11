<?php

namespace cms\controllers;

use common\components\CommonLib;
use common\helpers\Image;
use common\helpers\Upload;
use common\models\Custommer;
use common\models\ImportExcel;
use common\models\TbOrders;
use common\models\TbOrderSupplier;
use common\models\TbShippers;
use common\models\TbShippersSearch;
use common\models\TbTransfercode;
use Yii;
use common\models\TbShipping;
use common\models\TbShippingSearch;
use common\components\Controller;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use common\models\AccessRule;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;


/**
 * ShippingController implements the CRUD actions for TbShipping model.
 */
class ShippingController extends Controller
{
    public function behaviors ()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'delete', 'barcode', 'import'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'delete', 'barcode', 'import'],
                        'allow' => true,
                        'roles' => [WAREHOUSE, WAREHOUSETQ, ADMIN],
                    ],
                    [
                        'actions' => ['index', 'view', 'delete', 'barcode', 'import'],
                        'allow' => true,
                        'roles' => [ADMIN]
                    ],
                ],
            ],
        ];
    }


    /**
     * Lists all TbShipping models.
     * @return mixed
     */

    public function actionUpdate ()
    {
        //update shipping
        CommonLib::updateOrderIDToShipping();
        echo 'shipping ok <b/>';
        CommonLib::updateOrderIDToTranfercode();
        echo 'Tranfercode ok <b/>';
        die;

    }

    public function actionIndex ()
    {
        $searchModel = new TbShippingSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params, 1); //search
        $count = $dataProvider->getCount();
        if ($count <= 0) {
            $this->flash('success', 'Không tìm thấy mã vận đơn: <b>' . $searchModel->shippingCode . '</b>');
        }

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'params' => $params,]);
    }

    public function actionBarcode ()
    {
        $searchModel = new TbShippingSearch();


        $is_same = false;
        if ($searchModel->load(Yii::$app->request->post())) {
            $success = false;
            $barcode = trim($searchModel->barcode);
            $user = \Yii::$app->user->identity;

            if (!empty($barcode)) {
                $listTransfer = [];

                $role = $user->role;
                if (in_array($role,[WAREHOUSETQ,WAREHOUSE])) {
                    /*kiem tra xem ma nay da duoc kho vn hay kho TQ ban chua*/
                    $nv_kho = ($role === WAREHOUSETQ) ? 2 : 1;
                    //kiem tra kho TQ hoac kho vn da ban hay chua
                    $model = TbShipping::find()->where(['shippingCode' => ($barcode), 'city' => $nv_kho])->one();
                    if (!$model) {
                        //chua co tao kien vo chu
                        $model = new TbShipping();
                        $model->shippingCode = $barcode;
                        $model->userID = $user->id;
                        $model->status = 0; //kien vo chu
                        $model->createDate = date('Y-m-d H:i:s');
                        $model->city = $nv_kho;//kho TQ, kho VN
                        $model->save(false);
                    }

                    $shipStatus = ($role === WAREHOUSE ? 3 : 2);
                    $khoname = ($shipStatus == 3) ? 'Kho VN' : 'Kho TQ';
                    /*
                      * check shippingcode in system
                      * */
                    $transfer = TbTransfercode::find()->where(['transferID' => ($barcode)])->asArray()->all();
                    if ($transfer) { //ton tai ma van don
                        if ($model->status == 0) { //kien vo chu thi cap nhat
                            $model->status = 1;
                            //$model->shipperID = ($shipperExits) ? $shipperExits->id : 0;
                            $model->editDate = date('Y-m-d H:i:s');
                            $model->tranID = $transfer[0]['id'];
                            $model->save(false);
                        }

                        $orderIds = [];
                        $orderIdsInVN = [];
                        $transferIds = [];

                        foreach ($transfer as $item) {
                            if($item['id'] != $model->tranID ){
                                $listTransfer[] = $item;
                            }

                            if(!in_array($item['shipStatus'], [3, 5])){ // chua tra hang va chua ve kho vn
                                if(!empty($item['orderID'])){ //thuoc don hang
                                    $orderIds[] = $item['orderID'];
                                }else{
                                    $transferIds[] = $item['id'];//khong thuoc don hang => hang ky gui
                                }
                            }
                            if (!empty($item['orderID']) && $item['shipStatus'] == 3) {//nhung don ve kho vn thuoc don hang
                                $orderIdsInVN[] = $item['orderID'];
                            }
                        }

                        //kiem tra hang ky gui
                        $shipperExits = TbShippers::findOne(['shippingCode' => $barcode]);

                        if($transferIds && $shipperExits){ //cap nhat loai ma van don la ma ky gui
                            $shipperExits->shippingStatus = $shipStatus;
                            $shipperExits->save(false);
                            //cap nhat shipping chua ma van don la hang ky gui
                            $model->shipperID = $shipperExits->id;
                            $model->save(false);
                            TbTransfercode::updateAll(['type' => 1,'shipStatus' => $shipStatus, 'shipDate' => date('Y-m-d H:i:s')], ['id' => $transferIds,'transferID' => $barcode]);
                        }
                        //neu co ma chua duoc cap nhat ve kho vn or tq thi cap nhat
                        if ($orderIds) {
                            TbTransfercode::updateAll(['shipStatus' => $shipStatus, 'shipDate' => date('Y-m-d H:i:s')], ['transferID' => ($barcode), 'orderID' => $orderIds]);
                        }

                        $orderIds = array_unique(array_merge($orderIds,$orderIdsInVN));
                        if($orderIds){
                            foreach ($orderIds as $orderId) {
                                $tborder = TbOrders::findOne($orderId);
                                CommonLib::updateOrder($tborder);
                            }
                        }
                        if($user->id !== $model->userID){
                            $message = 'Đã có nhân viên khác cập nhật mã <b>' . $barcode . ' </b>  về ' . $khoname;
                        }else{
                            $message = 'Mã <b>' . $barcode . ' </b> đã được cập nhật về ' . $khoname;
                        }

                        $this->flash('success', $message);
                        $success = true;

                        if(count($transfer) > 1){
                            $is_same = true;
                    }

                    } else {
                        //kien chua duoc cap nhat cho don hang
                        //kiem tra co ton tai o ky gui
                        $shipperExits = TbShippers::findOne(['shippingCode' => $barcode]);
                        if ($shipperExits) {
                            $businessID = (isset($shipperExits->customer) && $shipperExits->customer) ? $shipperExits->customer->userID : 0;
                            $modelTransfer = new TbTransfercode();
                            $modelTransfer->businessID = $businessID;
                            $modelTransfer->transferID = $barcode;
                            $modelTransfer->shipStatus = $shipStatus; //0: chua ship,2: kho TQ;3: kho VN
                            $modelTransfer->createDate = date('Y-m-d H:i:s');
                            $modelTransfer->shipDate = date('Y-m-d H:i:s');
                            $modelTransfer->type = 1 ;//hang ky gui
                            $modelTransfer->save(false);
                            if ($model->status == 0) { //kien vo chu thi cap nhat
                                $model->status = 1;
                                $model->editDate = date('Y-m-d H:i:s');
                                $model->tranID = $modelTransfer->id;
                                $model->shipperID = $shipperExits->id;
                                $model->save(false);
                            }
                            $shipperExits->shippingStatus = $shipStatus;
                            $shipperExits->save(false);
                            $this->flash('danger', 'Mã <b>' . $barcode . '</b> đã được cập nhật về ' . $khoname);
                            $success = true;
                        } else {
                            $model->status = 0;
                            $model->editDate = date('Y-m-d H:i:s');
                            $model->save(false);
                            $this->flash('danger', 'Mã <b>' . $barcode . '</b> vô chủ, chưa được cập nhật cho đơn hàng.');
                        }

                    }
                } else {
                    $this->flash('danger', 'Bạn không có quyền cập nhật mã.');
                }
            } else {
                $this->flash('danger', 'Vui lòng nhâp mã vận đơn.');
            }



            if($is_same){
                $alert_title = '<h1 class="text-center pt0 mt0"><i class="fa fa-check-circle font50 text-red"></i><br/>Mã kiện đã bị trùng</h1>';
            }else{
                if($success){
                    $alert_title = '<h1 class="text-center pt0 mt0"><i class="fa fa-check-circle font50 text-red"></i><br/>Cập nhật thành công</h1>';
                }else{
                    $alert_title = '<h1 class="text-center pt0 mt0"><i class="fa fa-check-circle font50 text-red"></i><br/> Cập nhật thất bại</h1>';
                }
            }

        } 

        $alert_body  = '';
        $params = Yii::$app->request->queryParams;
        if(isset($barcode) && !empty($barcode)){
            $params['TbShippingSearch']['shippingCode'] = $barcode;
        }

        $dataProvider = $searchModel->search($params, 0);//type = 0//xu ly cap nhat trang thai kho ban ma
        $shipper_id = [];
      //  $listTransfer = current($listTransfer);

        if($dataProvider->getTotalCount() && !$is_same){
            foreach ($dataProvider->getModels() as $val){
                if(isset($barcode) && !empty($barcode) && trim($val->shippingCode) == trim($barcode)){
                    $alert_box = $alert_province = '';
                    if(!empty($val->name)){
                        $alert_province = '<h3><i class="fa fa-check font24 text-red"></i> '.$val->name.'</h3>';
                    }
                    if($val->isBox){
                        $alert_box      = '<h3><i class="fa fa-check font24 text-red"></i> ĐÓNG GỖ</h3>';
                    }
                    if($val->isCheck){
                        $alert_box      .= '<h3><i class="fa fa-check font24 text-red"></i> KIỂM ĐẾM</h3>';
                    }

                    $alert_body = '<div class="col-lg-12">' . $alert_province .  $alert_box . '</div>';
                }

                if($val->shipperID){
                    $shipper_id[] = $val->shipperID;
                }
            }
        }

        if($shipper_id){
            //get list customer_id
            $shiperInfo = TbShippers::find()->distinct()->select('id,userID')->where(['id'=>$shipper_id])->asArray()->all();
            if($shiperInfo){
                foreach ($shiperInfo as $val){
                    $shippers[$val['id']] = $val['userID'];
                }
            }
        }
      //
        //Shipper
        //$searchModelShipper = new TbShippersSearch();
       // $dataProviderShipper = $searchModelShipper->search($params);
        $searchModel->barcode = '';//gan = null de ban tiep ma khac
        $customers = \common\components\CommonLib::listCustomer();
        return $this->render('barcode', [
            'alert' => isset($alert_title) ? $alert_title.$alert_body :'',
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
           // 'searchModelShipper' => $searchModelShipper,
           // 'dataProviderShipper' => $dataProviderShipper,
            'shippers' => isset($shippers) ? $shippers : [],
            'params' => $params,
            'customers' => $customers,
        ]
        );
    }

    public function actionImpossrt ()
    {
        $model = new ImportExcel();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->validate()) {

                $user = Yii::$app->user->identity;
                if ($user->role == WAREHOUSE || $user->role == WAREHOUSETQ) {

                    $filePath = Upload::getUploadPath('doc') . DIRECTORY_SEPARATOR;
                    $model->file->saveAs($filePath . $model->file->name);
                    $inputFile = $filePath . $model->file->name;

                    try {
                        $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFile);
                    }
                    catch (Exception $e) {
                        die('Error');
                    }

                    $sheet = $objPHPExcel->getSheet(0);
                    $highestRow = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();

                    $alert_success = '';
                    $alert_error = '';
                    $alert_exits = '';

                    for ($row = 1; $row <= $highestRow; $row++) {
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
                        if ($row == 1) {
                            continue;
                        }

                        $barcode = $rowData[0][0];
                        //khong cho phep admin tra hang
                        /*kiem tra xem ma nay da duoc kho vn hay kho TQ ban chua*/
                        $kho = ($user->role === WAREHOUSETQ) ? 2 : 1;
                        $model = TbShipping::find()->where(['shippingCode' => trim($barcode), 'city' => $kho])->one();
                        if (!$model) {
                            $model = new TbShipping();
                        }
                        /*
                          * check shippingcode in system
                          * */
                        $transfer = TbTransfercode::find()->where(['transferID' => trim($barcode)])->all();
                        if ($transfer) {
                            /* neu thay ma tren he thong thi update trang thai
                             * update tb_shipping
                             * */
                            foreach ($transfer as $trans) {
                                $model->shippingCode = $barcode;
                                $model->userID = $user->id;
                                $model->tranID = $trans->id;
                                $model->shopID = $trans->shopID;
                                $model->status = ($trans->orderID > 0) ? 1 : 0; //1: thanh cong; 0: not found
                                $model->city = $kho;//kho vn or TQ
                                $model->createDate = date('Y-m-d H:i:s');
                                //update TbShipping
                                try {
                                    if ($model->save(false)) {
                                        $shop = TbOrderSupplier::findOne(['id' => $trans->shopID, 'orderID' => $trans->orderID]);
                                        if ($shop) {
                                            if ($shop->shippingStatus === 3) {
                                                $alert_exits .= $barcode . '<br>';
                                                //$this->flash('danger', 'Mã <b>' . $barcode . '</b> đã được cập nhật cho shop: <a target="_blank" href="'.Url::toRoute(['orders/view','id'=>$trans->orderID, '#' => 'shop-'.$trans->shopID]).'"><b>'.$trans->shopID.'</b></a>');
                                                continue;
                                            }
                                            //th kho tq da xac nhan
                                            if ($user->role === WAREHOUSETQ) {
                                                $shop->shippingStatus = 2;
                                            } else if ($user->role === WAREHOUSE) { //kho vn
                                                $shop->shippingStatus = 3;
                                            }
                                            if ($shop->save(false)) {
                                                $alert_success .= $barcode . '<br>';
                                                //$this->flash('success', 'Mã <b>' . $barcode . ' đã được cập nhật về kho </b>' . ($shop->shippingStatus == 3 ? 'Việt Nam' : 'Trung Quốc'));
                                                $currentOrder = TbOrders::findOne($shop->orderID);
                                                CommonLib::updateDataOrders($currentOrder);
                                            }
                                        }
                                    }
                                }
                                catch (\yii\db\Exception $ex) {
                                    // $this->flash('danger', 'Error: ' . $ex->getMessage());
                                }
                            }
                        } else {
                            //th khong tim thay ma tren he thong van luu vao bang shipping
                            if ($model->isNewRecord) {
                                $model->shippingCode = $barcode;
                                $model->userID = $user->id;
                                $model->shopID = null;
                                $model->tranID = null;
                                $model->status = 0;
                                $model->createDate = date('Y-m-d H:i:s');
                                $model->city = $kho;//kho TQ, kho VN
                                if ($model->save(false)) {
                                    $alert_success .= $barcode . '<br>';
                                    // $this->flash('danger', 'Mã ' . $barcode . ' đã được thêm vào hệ thống.');
                                }
                            } else {
                                $alert_exits .= $barcode . '<br>';
//                                $this->flash('danger', 'Mã <b>' . $barcode . '</b> đã tồn tại.');
                            }
                        }
                        /*if (!empty($shippingCode)) {
                            //update shipping
                            $query = TbShipping::find()->where(['city' => ($user->role == WAREHOUSETQ ? 2 : 1),'userID' => $user->id,'shippingCode'=>$shippingCode]);
                            $dataAll = $query->all();
                            //xu ly truong hop bi duplicate shippingcode voi cung 1 country, 1 user
                            //xem lai
                            $itemDelete = [];
                            if (count($dataAll) > 1) {
                                foreach ($dataAll as $ship) {
                                    $itemDelete[] = $ship->id;
                                }
                                //xoa tat ca item duplicate de insert moi
                                TbShipping::deleteAll(['id' => $itemDelete]);
                            }

                            $dataExist = $query->one();
                            //insert tb shopping
                            if (!$dataExist) {
                                $model = new TbShipping();
                            } else {
                                $model = $dataExist;
                            }

                            //kiem tra xem da co mavd he thong chua
                            $transfer = TbTransfercode::find()->where(['transferID' => trim($shippingCode)])->all();
                            if ($transfer) {//update
                                foreach ($transfer as $trans) {
                                    if (empty($trans->orderID)) {
                                        TbTransfercode::deleteAll(['id' => $trans->id]);
                                    } else {
                                        $model->shippingCode = $shippingCode;
                                        $model->userID = $user->id;
                                        $model->tranID = $trans->id;
                                        $model->shopID = $trans->shopID;
                                        $model->status = ($trans->orderID > 0) ? 1 : 0;
                                        $model->city = ($user->role == WAREHOUSETQ ? 2 : 1);

                                        if ($model->save()) {
                                            $shop = TbOrderSupplier::findOne(['id' => $trans->shopID, 'orderID' => $trans->orderID]);
                                            if ($shop) {
                                                //th kho tq da xac nhan
                                                if ($user->role == WAREHOUSETQ) {
                                                    $shop->shippingStatus = 2;
                                                } else if ($user->role == WAREHOUSE) {
                                                    $shop->shippingStatus = 3;
                                                }
                                                $shop->save();
                                            }
                                        }
                                    }
                                }
                            } else {
                                $model->shippingCode = $shippingCode;
                                $model->userID = $user->id;
                                $model->shopID = null;
                                $model->tranID = null;
                                $model->status = 0;
                                $model->city = ($user->role == WAREHOUSETQ ? 2 : 1);
                                $model->save();
                            }
                            //update shipper kho vn ban,
                            $shipperExits = \common\models\TbShippers::findOne(['shippingCode' => $shippingCode]);
                            if ($shipperExits) { //th neu ban ma trung voi ma khach => da ship
                                $shipperExits->shippingStatus = ($user->role == WAREHOUSETQ ? 2 : 3);
                                $shipperExits->save(false);
                            }

                        }*/
                    }

                    $alert_success = '<br>1. Các mã đã được cập nhât vào hệ thống<br>' . $alert_success;
                    $alert_exits = '<br>2. Các mã đã tồn tại: <br>' . $alert_exits;
                    $alert_error = '<br>3. Các mã không được cập nhật <br>' . $alert_error;

                    $html = '<h4>THÔNG BÁO:</h4>' . $alert_success . $alert_exits . $alert_error;

                    return ['rs' => 'success', 'status' => 0, 'mess' => $html];
                }

                return ['rs' => 'error', 'mess' => 'Bạn không có quyền cập nhật mã.'];

            } else {
                return ['rs' => 'error', 'status' => 1, 'mess' => ActiveForm::validate($model)];
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax(
                '_import', [
                'model' => $model
            ]
            );
        }

        return $this->render('_import', ['model' => $model]);

    }

    //import don ky gui
    public function actionImpsortactive ()
    {
        $model = new ImportExcel();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->validate()) {
                $user = Yii::$app->user->identity;
                if ($user->role == WAREHOUSE || $user->role == WAREHOUSETQ) {

                    $filePath = Upload::getUploadPath('doc') . DIRECTORY_SEPARATOR;
                    $model->file->saveAs($filePath . $model->file->name);
                    $inputFile = $filePath . $model->file->name;

                    try {
                        $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFile);
                    }
                    catch (Exception $e) {
                        die('Error');
                    }

                    $sheet = $objPHPExcel->getSheet(0);
                    $highestRow = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();

                    for ($row = 1; $row <= $highestRow; $row++) {
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
                        if ($row == 1) {
                            continue;
                        }

                        $shippingCode = isset($rowData[0][0]) ? trim($rowData[0][0]) : '';
                        $username = isset($rowData[0][1]) ? trim($rowData[0][1]) : '';
                        $kg = isset($rowData[0][2]) ? trim($rowData[0][2]) : 0;
                        if (!empty($shippingCode) && !empty($username)) {
                            $shippingCode = trim($shippingCode);
                            if ($cUser = Custommer::findOne(['username' => $username])) {
                                //kiem tra neu chua co ma van don thi insert
                                if ($shipperExits = TbShippers::findOne(['shippingCode' => $shippingCode])) {
                                    $shipperExits->shippingStatus = 3; //kho vn
                                    $shipperExits->save(false);
                                } else {
                                    $shippers = new TbShippers();
                                    $shippers->shippingCode = $shippingCode;
                                    $shippers->userID = $cUser->id;//luu ma nguoi import
                                    $shippers->shippingStatus = 3; //kho vn
                                    $shippers->weight = $kg; //can nang
                                    $shippers->save(false);
                                }
                            }
                        }
                    }

                    return ['rs' => 'success', 'status' => 0, 'mess' => 'Mã vận đơn đã được cập nhật vào hệ thống!'];
                }
            } else {
                return ['rs' => 'error', 'status' => 1, 'mess' => ActiveForm::validate($model)];
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax(
                '_import', [
                'model' => $model
            ]
            );
        }

        return $this->render('_import', ['model' => $model]);
    }


    /**
     * Deletes an existing TbShipping model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete ($id)
    {
        $this->findModel($id)->delete();
        $this->flash('success', 'Xóa dữ liệu thành công');

        return $this->redirect(['index']);
    }

    public function actionDeleteCode ($id)
    {
        $this->findModel($id)->delete();
        $this->flash('success', 'Xóa dữ liệu thành công');

        return $this->redirect(['shipping/barcode']);
    }

    /**
     * Finds the TbShipping model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TbShipping the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel ($id)
    {
        if (($model = TbShipping::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
