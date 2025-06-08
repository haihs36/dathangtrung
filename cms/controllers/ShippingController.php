<?php

namespace cms\controllers;

use common\components\CommonLib;
use common\helpers\Image;
use common\helpers\Upload;
use common\models\ImportExcel;
use common\models\TbChatMessage;
use common\models\TbHistory;
use common\models\TbOrders;
use common\models\TbOrderSupplier;
use common\models\TbShippers;
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
    public function behaviors()
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
                        'roles' => [WAREHOUSE, WAREHOUSETQ],
                    ],
                    [
                        'actions' => ['index', 'view', 'barcode','delete', 'import'],
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

    public function actionUpdate()
    {
        //update shipping
        CommonLib::updateOrderIDToShipping();
        echo 'shipping ok <b/>';
        CommonLib::updateOrderIDToTranfercode();
        echo 'Tranfercode ok <b/>';
        die;

    }

    public function actionIndex()
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

    public function actionBarcode()
    {

        $searchModel = new TbShippingSearch();
        $alert_title = '';
        $customers = \common\components\CommonLib::listCustomer();
        $is_same = false;
        if ($searchModel->load(Yii::$app->request->post())) {
            $success = false;
            $barcode = trim(strip_tags($searchModel->barcode));
            $message = '';
            if (!empty($barcode)) {
                // $listTransfer = [];
                $user = \Yii::$app->user->identity;
                $role = $user->role;
                $city = $khoname = '';
                $shipStatus = 0;
                switch ($role) {
                    case WAREHOUSE: //kho vn
                        $city = 1;
                        $shipStatus = 3;
                        $khoname = 'Kho VN';
                        break;
                    case WAREHOUSETQ: //kho tq
                        $city = 2;
                        $shipStatus = 2;
                        $khoname = 'Kho TQ';
                        break;
                }

                //kiem tra kho TQ hoac kho vn da ban hay chua
                $model = TbShipping::find()->where(['shippingCode' => $barcode, 'city' => $city])->one();
                if (empty($model)) {
                    //chua co tao kien vo chu
                    $model = new TbShipping();
                    $model->shippingCode = $barcode;
                    $model->userID = $user->id;
                    $model->status = 0; //kien vo chu
                    $model->createDate = date('Y-m-d H:i:s');
                    $model->city = $city;//kho TQ, kho VN
                    $model->save(false);
                }

                if ($user->id !== $model->userID) {
                    $message = 'Đã có nhân viên khác cập nhật mã <b>' . $barcode . ' </b>  về ' . $khoname;
                    $alert_title = '<h1 class="text-center pt0 mt0"><i class="fa fa-check-circle font50 text-red"></i><br/> ' . $message . '</h1>';
                    return $this->formatResponse([
                        'success' => $success,
                        'message' => $alert_title
                    ]);
                }

                //check shippingcode in system
                $transfer = TbTransfercode::find()->where(['transferID' => $barcode])->asArray()->all();


                if (!empty($transfer)) { //ton tai ma van don
                    $orderIds = [];
                    $orderIdsInVN = [];
                    $transferIds = [];
                    foreach ($transfer as $item) {
                        if ($model->status == 0) { //kien vo chu thi cap nhat
                            $model->status = 1;
                            //$model->shipperID = ($shipperExits) ? $shipperExits->id : 0;
                            $model->editDate = date('Y-m-d H:i:s');
                            $model->tranID = $item['id'];
                            $model->save(false);
                        }

                        if ($item['shipStatus'] != 5) { // chua tra hang va chua ve kho vn
                            if (!empty($item['orderID'])) { //thuoc don hang
                                $orderIds[] = $item['orderID'];
                            } else if ($item['type'] == 1) {
                                $transferIds[] = $item['id'];//khong thuoc don hang => hang ky gui
                            }
                        }

                        if (!empty($item['orderID']) && $item['shipStatus'] == 3) {//nhung don ve kho vn thuoc don hang
                            $orderIdsInVN[] = $item['orderID'];
                        }
                    }
                    //kiem tra hang ky gui
                    $shipperExits = TbShippers::find()->where(['like', 'shippingCode', $barcode])->one();

                    if ($transferIds && $shipperExits) { //cap nhat loai ma van don la ma ky gui productName
                        if ($shipperExits->shippingStatus != 3) {
                            $shipperExits->shippingStatus = $shipStatus;
                            $shipperExits->editDate = date('Y-m-d H:i:s');
                            $shipperExits->save(false);
                            //update hang ky gui
                            TbTransfercode::updateAll(['type' => 1, 'shipStatus' => $shipStatus, 'shipDate' => date('Y-m-d H:i:s')], ['id' => $transferIds, 'transferID' => $barcode]);
                        }

                        //cap nhat shipping chua ma van don la hang ky gui
                        $model->shipperID = $shipperExits->id;
                        $model->save(false);
                    }

                    //neu co ma chua duoc cap nhat ve kho vn or tq thi cap nhat
                    if (!empty($orderIds) && in_array($role, [WAREHOUSETQ,WAREHOUSE])) {
                        TbTransfercode::updateAll(['shipStatus' => $shipStatus, 'shipDate' => date('Y-m-d H:i:s')], ['transferID' => ($barcode), 'orderID' => $orderIds]);
                    }

                    if (!empty($orderIds)) {
                        if (!empty($orderIdsInVN)) {
                            $orderIds = array_unique(array_merge($orderIds, $orderIdsInVN));
                        }

                        foreach ($orderIds as $orderId) {
                            $tborder = TbOrders::findOne($orderId);
                            CommonLib::updateOrder($tborder);
                        }
                    }

                    $message = 'Mã <b>' . $barcode . ' </b> đã được cập nhật về ' . $khoname;
                    $this->flash('success', $message);
                    $success = true;

                    if (count($transfer) > 1) {
                        $is_same = true;
                    }

                } else {
                    //kien chua duoc cap nhat cho don hang
                    //kiem tra co ton tai o ky gui
//                    $shipperExits = TbShippers::find()->where(['like', 'shippingCode', $barcode])->one();
                    $shipperExits = TbShippers::findOne(['shippingCode' => $barcode]);
                    if ($shipperExits) {
                        $businessID = (isset($shipperExits->customer) && $shipperExits->customer) ? $shipperExits->customer->userID : 0;
                        $modelTransfer = new TbTransfercode();
                        $modelTransfer->businessID = $businessID;
                        $modelTransfer->transferID = $barcode;
                        $modelTransfer->shipStatus = $shipStatus; //0: chua ship,2: kho TQ;3: kho VN
                        $modelTransfer->createDate = date('Y-m-d H:i:s');
                        $modelTransfer->shipDate = date('Y-m-d H:i:s');
                        $modelTransfer->type = 1;//hang ky gui
                        $modelTransfer->status = 1;//mac dinh tich chon
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
                        $message = 'Mã <b>' . $barcode . '</b> đã được cập nhật về ' . $khoname;

                        /* $msg = 'ĐH ký gửi có mã KG-' . $user->id . $shipperExits->id . ' đã về ' . $khoname;
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
                         $modelMessage->save(false);*/

                        $success = true;
                    } else {
                        $model->status = 0;
                        $model->editDate = date('Y-m-d H:i:s');
                        $model->save(false);
                        $this->flash('danger', 'Mã <b>' . $barcode . '</b> vô chủ, chưa được cập nhật cho đơn hàng.');

                        $message = 'Mã <b>' . $barcode . '</b> vô chủ, chưa được cập nhật cho đơn hàng.';
                    }

                }
            } else {
                $this->flash('danger', 'Vui lòng nhâp mã vận đơn.');
                $message = 'Vui lòng nhâp mã vận đơn.';
            }


            if ($is_same) {
                $alert_title = '<h1 class="text-center pt0 mt0"><i class="fa fa-check-circle font50 text-red"></i><br/>Mã kiện đã bị trùng</h1>';
            } else {
                if ($success) {
                    $alert_title = '<h1 class="text-center pt0 mt0"><i class="fa fa-check-circle font50 text-red"></i><br/>' . $message . '</h1>';
                } else {
                    $alert_title = '<h1 class="text-center pt0 mt0"><i class="fa fa-check-circle font50 text-red"></i><br/> ' . $message . '</h1>';
                }
            }
        }
        //end

        $alert_body = '';
        $params = Yii::$app->request->queryParams;
        if (isset($barcode) && !empty($barcode)) {
            $params['TbShippingSearch']['shippingCode'] = $barcode;
        }


        $dataProvider = $searchModel->search($params, 0);//type = 0//xu ly cap nhat trang thai kho ban ma
        $shipper_id = [];

        if ($dataProvider->getTotalCount() && !$is_same) {
            foreach ($dataProvider->getModels() as $val) {
                /*if(isset($barcode) && !empty($barcode) && trim($val->shippingCode) == trim($barcode)){
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
                }*/

                if ($val->shipperID) {
                    $shipper_id[] = $val->shipperID;
                }
            }
            $alert_title .= $alert_body;
        }


        if ($shipper_id) {
            //get list
            $shiperInfo = TbShippers::find()->distinct()->where(['id' => $shipper_id])->asArray()->all();
            if ($shiperInfo) {
                foreach ($shiperInfo as $val) {
                    $shippers[$val['id']] = $val['userID'];
                }
            }
        }

        //list-barcode-same hien thi nhap kg
        if (((isset($success) && $success) || $is_same) && isset($barcode)) {
            if (isset($shippers) && !empty($shippers)) {
                $customer_id = current($shippers);
            }
            $data = TbTransfercode::getAllBarcodeByBarcode($barcode);
            $order = [];
            if ($data) {
                foreach ($data as $item) {
                    $order[$item['orderID']][] = $item;
                }
            }

            $alert_title .= $this->renderPartial(
                '_ajax_update_kg', [
                    'order' => $order,
                    'customers' => $customers,
                    'shiperInfo' => isset($shiperInfo) ? reset($shiperInfo) : [],
                    'customer_id' => isset($customer_id) ? $customer_id : 0,
                ]
            );
        }

        $searchModel->barcode = '';//gan = null de ban tiep ma khac


        if (Yii::$app->request->isPost) {
            return $this->formatResponse([
                'success' => isset($success) ? $success : false,
                'message' => (isset($alert_title) && !empty($alert_title)) ? (trim($alert_title)) : ''
            ]);
        } else {

            return $this->render('barcode', [
                    'alert' => (isset($alert_title) && !empty($alert_title)) ? json_encode(trim($alert_title)) : '',
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'shippers' => isset($shippers) ? $shippers : [],
                    'transfer' => isset($transfer) ? $transfer : [],
                    'params' => $params,
                    'customers' => $customers,
                    'is_same' => $is_same,
                ]
            );
        }


    }

    //import don ky gui
    public function actionImpsortactive()
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
                    } catch (Exception $e) {
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
                                $shipperExits = TbShippers::find()->where(['like', 'shippingCode', $barcode])->one();

                                if ($shipperExits) {
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
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        $this->flash('success', 'Xóa dữ liệu thành công');

        return $this->redirect(['index']);
    }

    public function actionDeleteCode($id)
    {
        $shipping = $this->findModel($id);
        $tbHistory = new TbHistory();
        $tbHistory->orderID = 0;
        $tbHistory->userID = Yii::$app->user->id;
        $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>Đã xóa mã kiện: <b>' . $shipping->shippingCode . '</b> khỏi danh sách kho bắn mã';
        $tbHistory->save(false);

        $shipping->delete();

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
    protected function findModel($id)
    {
        if (($model = TbShipping::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    //import don ky gui
    public function actionImportExcel()
    {
        ini_set("memory_limit", "2048M");
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $count = 0;
        $arrError = [];
        $number = 0;


        if (\Yii::$app->request->getIsPost()) {
            $allowed = array('xls', 'xlsx');
            $filePath = Upload::getUploadPath('doc') . DIRECTORY_SEPARATOR;
            $fname = str_replace(" ", "-", $_FILES['file']['name']);
            $ext = pathinfo($fname, PATHINFO_EXTENSION);
            if (!in_array($ext, $allowed)) {
                $arrError[] .= 'import file thất bại';
            }

            if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath . '/' . $fname)) {

                $inputFile = $filePath . $fname;

                try {
                    $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFile);

                    $sheet = $objPHPExcel->getSheet(0);
                    $highestRow = $sheet->getHighestRow();
                    $number = ($highestRow > 1) ? $highestRow - 1 : 1;
                    $highestColumn = $sheet->getHighestColumn();

                    if ($highestRow <= 200) {
                        for ($row = 2; $row <= $highestRow; $row++) {
                            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
                            $barcode = trim(strip_tags($rowData[0][0]));

                            if (!empty($barcode)) {
                                $user = \Yii::$app->user->identity;
                                $role = $user->role;
                                $city = $khoname = '';
                                $shipStatus = 0;
                                switch ($role) {
                                    case WAREHOUSE: //kho vn
                                        $city = 1;
                                        $shipStatus = 3;
                                        $khoname = 'Kho VN';
                                        break;
                                    case WAREHOUSETQ: //kho tq
                                        $city = 2;
                                        $shipStatus = 2;
                                        $khoname = 'Kho TQ';
                                        break;
                                }

                                //kiem tra kho TQ hoac kho vn da ban hay chua
                                $model = TbShipping::find()->where(['shippingCode' => $barcode, 'city' => $city])->one();
                                if (empty($model)) {
                                    //chua co tao kien vo chu
                                    $model = new TbShipping();
                                    $model->shippingCode = $barcode;
                                    $model->userID = $user->id;
                                    $model->status = 0; //kien vo chu
                                    $model->createDate = date('Y-m-d H:i:s');
                                    $model->city = $city;//kho TQ, kho VN
                                    $model->save(false);
                                }

                                if ($user->id !== $model->userID) {
                                    $arrError[] .= 'row ' . $row . ' Đã có nhân viên khác cập nhật mã ' . $barcode . 'về '. $khoname;
                                }

                                //check shippingcode in system
                                $transfer = TbTransfercode::find()->where(['transferID' => $barcode])->asArray()->all();
                                if (!empty($transfer)) { //ton tai ma van don
                                    $orderIds = [];
                                    $orderIdsInVN = [];
                                    $transferIds = [];
                                    foreach ($transfer as $item) {
                                        if ($model->status == 0) { //kien vo chu thi cap nhat
                                            $model->status = 1;
                                            //$model->shipperID = ($shipperExits) ? $shipperExits->id : 0;
                                            $model->editDate = date('Y-m-d H:i:s');
                                            $model->tranID = $item['id'];
                                            $model->save(false);
                                        }

                                        if ($item['shipStatus'] != 5) { // chua tra hang va chua ve kho vn
                                            if (!empty($item['orderID'])) { //thuoc don hang
                                                $orderIds[] = $item['orderID'];
                                            } else if ($item['type'] == 1) {
                                                $transferIds[] = $item['id'];//khong thuoc don hang => hang ky gui
                                            }
                                        }

                                        if (!empty($item['orderID']) && $item['shipStatus'] == 3) {//nhung don ve kho vn thuoc don hang
                                            $orderIdsInVN[] = $item['orderID'];
                                        }
                                    }
                                    //kiem tra hang ky gui
                                    $shipperExits = TbShippers::find()->where(['like', 'shippingCode', $barcode])->one();

                                    if ($transferIds && $shipperExits) { //cap nhat loai ma van don la ma ky gui
                                        if ($shipperExits->shippingStatus != 3) {
                                            $shipperExits->shippingStatus = $shipStatus;
                                            $shipperExits->editDate = date('Y-m-d H:i:s');
                                            $shipperExits->save(false);
                                            //update hang ky gui
                                            TbTransfercode::updateAll(['type' => 1, 'shipStatus' => $shipStatus, 'shipDate' => date('Y-m-d H:i:s')], ['id' => $transferIds, 'transferID' => $barcode]);
                                        }

                                        //cap nhat shipping chua ma van don la hang ky gui
                                        $model->shipperID = $shipperExits->id;
                                        $model->save(false);
                                    }

                                    //neu co ma chua duoc cap nhat ve kho vn or tq thi cap nhat
                                    if (!empty($orderIds) && $role == WAREHOUSE) {
                                        TbTransfercode::updateAll(['shipStatus' => $shipStatus, 'shipDate' => date('Y-m-d H:i:s')], ['transferID' => ($barcode), 'orderID' => $orderIds]);
                                    }

                                    if (!empty($orderIds)) {
                                        if (!empty($orderIdsInVN)) {
                                            $orderIds = array_unique(array_merge($orderIds, $orderIdsInVN));
                                        }

                                        foreach ($orderIds as $orderId) {
                                            $tborder = TbOrders::findOne($orderId);
                                            CommonLib::updateOrder($tborder);
                                        }
                                    }

                                    $arrError[] .= 'Mã ' . $barcode . ' đã được cập nhật về '. $khoname;
                                    $count ++;
                                    $success = true;

                                    if (count($transfer) > 1) {
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
                                        $modelTransfer->type = 1;//hang ky gui
                                        $modelTransfer->status = 1;//mac dinh tich chon
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

                                        /*$msg = 'ĐH ký gửi có mã KG-' . $user->id . $shipperExits->id . ' đã về ' . $khoname;
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
                                        $modelMessage->save(false);*/

                                        $count ++;
                                        $arrError[] .= 'Mã ' . $barcode . ' đã được cập nhật về '. $khoname;
                                        $success = true;
                                    } else {
                                        $model->status = 0;
                                        $model->editDate = date('Y-m-d H:i:s');
                                        $model->save(false);

                                        $arrError[] .= 'Mã ' . $barcode . ' vô chủ, chưa được cập nhật cho đơn hàng. ';
                                    }

                                }
                            }else{
                                $arrError[] .= 'row ' . $row . ' lỗi mã '.$barcode.' không hợp lệ';
                            }
                        }

                    }

                    //xoa image download
                    @unlink($filePath . $fname);

                } catch (Exception $e) {
                    //xoa image download
                    $arrError[] .= 'lỗi ngoại lệ: ' . $e->getMessage();
                    @unlink($filePath . $fname);
                }

            }

        }

        return ['count' => $count, 'arrError' => $arrError, 'error' => count($arrError), 'number' => $number];

    }
}
