<?php

namespace frontend\controllers;

use cms\models\TbDistricts;
use common\components\APPController;
use common\components\CommonLib;
use common\helpers\Image;
use common\helpers\Upload;
use common\models\ImportExcel;
use common\models\PaymentSupport;
use common\models\Photo;
use common\models\TbAccountBanking;
use common\models\TbAccountTransaction;
use common\models\TbChatMessage;
use common\models\TbOrders;
use common\models\TbOrdersDetail;
use common\models\TbOrdersSession;
use common\models\TbOrderSupplier;
use common\models\TbProduct;
use common\models\TbShippers;
use common\models\UploadForm;
use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\UploadedFile;

class AjaxController extends APPController
{


    public function actionGetCurrency()
    {
        header("Cache-Control: private, max-age=0");
        header("Content-Type: application/json");
        header("Accept: application/json");
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Content-Type');

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $totalPrice = 0;
        if (Yii::$app->request->isAjax) {

            $totalPrice = $_POST['totalprice'];
            $totalPrice = CommonLib::getCNY_TABLE($totalPrice);
        }

        return ['cny'=>$totalPrice];
    }

    public function actionUpdatesms()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $status = 0;
        $msg = 'Cập nhật thất bại';
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $userID = \Yii::$app->user->id;
            if (TbChatMessage::findOne(['type'=>2,'status'=>0,'to_user_id' => $userID])){
                TbChatMessage::updateAll(['status'=>1,'last_activity'=>date('Y-m-d H:i:s')],['type'=>2,'status'=>0,'to_user_id' => $userID]);
                $msg = 'Cập nhật thành công';
            }

        }

        return ['msg'=>$msg,'status'=>$status];
    }

    public function actionImportexcel(){
        ini_set("memory_limit","2048M");
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $count = 0; $arrError = [];
        $number = 0;


        if (\Yii::$app->request->getIsPost()) {
            $allowed =  array('xls','xlsx');
            $filePath = Upload::getUploadPath('doc') . DIRECTORY_SEPARATOR;
            $fname = str_replace(" ","-",$_FILES['file']['name']);
            $ext = pathinfo($fname, PATHINFO_EXTENSION);
            if(!in_array($ext,$allowed) ) {
                $arrError[] .= 'import file thất bại';
            }

            if(move_uploaded_file($_FILES['file']['tmp_name'], $filePath.'/'.$fname )){

                $inputFile = $filePath . $fname;

                try {
                    $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                    $objReader     = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel   = $objReader->load($inputFile);

                    $sheet         = $objPHPExcel->getSheet(0);
                    $highestRow    = $sheet->getHighestRow();
                    $number = ($highestRow > 1) ? $highestRow-1 : 1;
                    $highestColumn = $sheet->getHighestColumn();

                    if($highestRow <= 200) {
                        for ($row = 2; $row <= $highestRow; $row++) {
                            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
                            $barcode = trim(strip_tags($rowData[0][1]));
                            $productName = trim(strip_tags($rowData[0][2]));
                            $quantity = (int)$rowData[0][3];
                            $price = (double)$rowData[0][4];

                            if(CommonLib::has_specchar($barcode) && !empty($barcode) && !empty($productName) && $quantity>0 && $price>0) {
                                $userID     = Yii::$app->user->id;

                                if (!TbShippers::findOne(['shippingCode' => trim($barcode), 'userID' => $userID])) {

                                    $totalPrice = $quantity*$price;
                                    $note = trim(strip_tags($rowData[0][6]));;

                                    $model               = new TbShippers();
                                    $model->userID       = $userID;
                                    $model->productName = $productName;
                                    $model->shippingCode = $barcode;
                                    $model->quantity = $quantity;
                                    $model->price = $price;
                                    $model->totalMoney = $totalPrice;
                                    $model->note = $note;
                                    if($model->save()){
                                        $count ++;
                                    }else{
                                        if(!empty($model->errors)){
                                            $str_err = '';
                                            foreach ($model->errors as $error){
                                                $str_err .= "\n".$error['0'];
                                            }

                                            $arrError[] .= 'row '.$row.' lỗi: '.$str_err;
                                        }
                                    }
                                }else{
                                    $arrError[] .= 'Mã kiện: '.$barcode.', đã được đăng ký';
                                }
                            }else{
                                $arrError[] .= 'row '.$row.' lỗi mã kiện không hợp lệ';
                            }
                        }

                    }

                    //xoa image download
                    @unlink($filePath . $fname);


                } catch (Exception $e) {
                    //xoa image download
                    $arrError[] .= 'lỗi ngoại lệ: '.$e->getMessage();
                    @unlink($filePath . $fname);
                }

            }

        }

        return ['count' => $count,'arrError'=>$arrError,'error'=>count($arrError),'number'=>$number];

    }
    /*delete shop cart session */
    public function actionShopCartDelete()
    {
        $userID = \Yii::$app->user->id;
        $shop_id = Yii::$app->request->post('shop_id');
        $success = false;
//        $uinfo = CommonLib::getUserIdentify();
//        if (!TbOrdersSession::deleteAll(['or',['customerID'=>$userID,'shop_id'=>$shop_id],['shop_id'=>$shop_id,'identify' => $uinfo['identify']]])) {
//            $this->error = 'Xóa shop thất bại.';
//        }
        if (TbOrdersSession::deleteAll(['customerID' => $userID, 'shop_id' => $shop_id])) {
            $success = true;
        }

        return $this->formatResponse(['success' => $success]);
    }

    /*delete order session item*/
    public function actionDeleteOrderItem()
    {
        $success = false;

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $userID = \Yii::$app->user->id;
            $item_id = (int)Yii::$app->request->post('item_id');

            if ($model = TbOrdersSession::findOne(['customerID' => $userID, 'id' => $item_id])) {
                $model->delete();
                $success = true;
            }
        }

        return $this->formatResponse(['success' => $success]);
    }

    public function actionCanceltsport()
    {

        $success = false;
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $id = (int)Yii::$app->request->post('id');
            if (PaymentSupport::updateAll(['status' => 4, 'update_time' => date('Y-m-d H:i:s')], ['id' => $id,'customerID'=>Yii::$app->user->id])) {
                $success = true;
            }
        }

       return $this->formatResponse(['success' => $success]);

    }

    public function actionPaymenttransport()
    {
        $success = false;
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $id = (int)Yii::$app->request->post('id');
            if (PaymentSupport::updateAll(['status' => 2, 'update_time' => date('Y-m-d H:i:s')], ['id' => $id,'customerID'=>Yii::$app->user->id])) {
                $success = true;
            }
        }

        return $this->formatResponse(['success' => $success]);

    }

    /** Author:HAIHS
     ** Content: update tinh nang dat coc
     ** CreateDate: 126-05-2018 13:36
     */
    public function actionDeposit()
    {
        if (!CommonLib::checkOrigin()) {
            header("HTTP/1.1 403 Origin Denied");
            return false;
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $success = false;
        $alert = 'Xác nhận thất bại. Vui lòng liên hệ với chúng tôi để được xử lý.';
        $note = '';
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $orderID = (int)Yii::$app->request->post('id');
            $type = (int)Yii::$app->request->post('type');

            $orders = TbOrders::findOne($orderID);

            if ($orders) {
                if($orders->totalPaid > 0){
                    $alert = 'Bạn đã đặt cọc đơn hàng này.';
                    return ['success' => $success, 'alert' => $alert, 'content' => $note];
                }
                //% tien hang
                $perCent = \common\components\CommonLib::getPercentDeposit($orders->totalOrder, $orders->customerID,$orders->deposit);
                switch ($type) {
                    case 1: //thanh toan duoi 100% tien hang, ko phai tong don
                        $tienCoc = round(($orders->totalOrder * $perCent) / 100);
                        break;
                    case 2://thanh toan 100%
                    default:
                        $tienCoc = $orders->totalOrder;
                        $perCent = 100;
                        break;

                }


                $bank = TbAccountBanking::findOne(['customerID' => Yii::$app->user->id]);
                //kt so du tai khoan < so tien thanh toan hoac con no
                if (!$bank || ($bank && ($bank->totalResidual < $tienCoc))) {
                    $alert = $this->setting['bank_account'];
                    return ['success' => $success, 'alert' => $alert, 'content' => $note];
                }

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

                    $maDH = '<a class="link_order" target="_blank" href="' . Url::toRoute(['orders/view', 'id' => $orderID]) . '"><b>' . $orders->identify . ' </b></a>';
                    /*insert lich su giao dich*/
                    $mdlTransaction = new TbAccountTransaction();
                    $mdlTransaction->type = 4; //trang thai dat coc don hang
                    $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                    $mdlTransaction->customerID = Yii::$app->user->id;
                    $mdlTransaction->sapo = 'Đặt cọc: ' . $perCent . '% tiền hàng</b><br/>(<i>Chưa bao gồm phí phát sinh</i>)<br>MĐH: ' . $maDH;
                    $mdlTransaction->value = $tienCoc;//so tien thanh toan
                    $mdlTransaction->accountID = $bank->id;//ma tai khoan
                    $mdlTransaction->balance = $bank->totalResidual;//so du sau khi giao dich
                    $mdlTransaction->create_date = date('Y-m-d H:i:s');
                    $mdlTransaction->save(false);

                    $alert = 'Bạn đã đặt cọc: ' . $perCent . '% tiền hàng = <b class="vnd-unit">' . number_format($tienCoc) . '<em>đ</em></b> (<i>Chưa bao gồm phí phát sinh</i>) MĐH: ' . $maDH . '.
                                         <br>Chúng tôi sẽ đặt hàng và liên hệ với bạn trong thời gian sớm nhất.';
                    $success = true;

                }
            }
        }

        return ['success' => $success, 'alert' => $alert, 'content' => $note];
    }

    /*coc all*/
    public function actionDepositAll(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $success = false;
        $alert = 'Thao tác thất bại. Vui lòng liên hệ với chúng tôi để được xử lý.';
        $note = '';
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $orderIDs = Yii::$app->request->post('keys');
           if(!empty($orderIDs)){

               $query = (new \yii\db\Query())->from(TbOrders::tableName());
               $query->where(['totalPaid'=>0,'orderID'=>$orderIDs]);
               $totalPriceCoc = $query->sum('totalOrder');
               $bank = TbAccountBanking::findOne(['customerID' => Yii::$app->user->id]);
               //kt so du tai khoan < so tien thanh toan hoac con no
               if (empty($bank) || ($bank && ($bank->totalResidual < $totalPriceCoc))) {
                   $alert = $this->setting['bank_account'];
                   return ['success' => $success, 'alert' => $alert, 'content' => $note];
               }else{
                   $dataOrder = TbOrders::find()->where(['totalPaid'=>0,'orderID'=>$orderIDs])->all();
                   if(!empty($dataOrder)){
                       $perCent = 100;
                       foreach ($dataOrder as $orders){
                           $tienCoc = $orders->totalOrder;
                           //kt so du tai khoan < so tien thanh toan hoac con no
                           if (!$bank || ($bank && ($bank->totalResidual < $tienCoc))) {
                               $alert = $this->setting['bank_account'];
                               continue;
                               //return ['success' => $success, 'alert' => $alert, 'content' => $note];
                           }

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
                               $mdlTransaction->customerID = Yii::$app->user->id;
                               $mdlTransaction->sapo = 'Đặt cọc: ' . $perCent . '% tiền hàng</b><br/>(<i>Chưa bao gồm phí phát sinh</i>)<br>MĐH: ' . $maDH;
                               $mdlTransaction->value = $tienCoc;//so tien thanh toan
                               $mdlTransaction->accountID = $bank->id;//ma tai khoan
                               $mdlTransaction->balance = $bank->totalResidual;//so du sau khi giao dich
                               $mdlTransaction->create_date = date('Y-m-d H:i:s');
                               $mdlTransaction->save(false);

                              // $alert = 'Bạn đã đặt cọc: ' . $perCent . '% tiền hàng = <b class="vnd-unit">' . number_format($tienCoc) . '<em>đ</em></b> (<i>Chưa bao gồm phí phát sinh</i>) MĐH: ' . $maDH . '.
                                       //  <br>Chúng tôi sẽ đặt hàng và liên hệ với bạn trong thời gian sớm nhất.';

                               $success = true;
                           }
                       }

                   }
               }
           }
        }

        return ['success' => $success, 'alert' => $alert, 'content' => $note];


    }


    public function actionUpload()
    {
        if (!CommonLib::checkOrigin()) {
            header("HTTP/1.1 403 Origin Denied");
            return false;
        }
        $success = null;
        $photo = new Photo();
        $photo->image = UploadedFile::getInstance($photo, 'image');
        if ($photo->image && $photo->validate(['image'])) {
            $fileName = $photo->image->size . '-' . CommonLib::getRandomInt(5);
            $photo->image = Image::upload($photo->image, $this->upload_image, null, null, false, $fileName);
            if ($photo->image) {
                // $photo->thumb = Image::thumb($photo->image, Photo::PHOTO_MEME_THUMB_WIDTH, Photo::PHOTO_MEME_THUMB_HEIGHT, true, $this->upload_thumb, $fileName);
                $photo->productID = (int)Yii::$app->request->post('pid');
                if ($photo->save()) {
                    $success = [
                        'message' => 'Photo uploaded',
                        'photo' => [
                            'id' => $photo->primaryKey,
                            //                                'image' => $photo->image,
                        ]
                    ];
                } else {
                    @unlink(\Yii::getAlias('@upload_dir') . str_replace(Url::base(true), '', $photo->image));
                    // @unlink(\Yii::getAlias('@upload_dir') . str_replace(Url::base(true), '', $photo->thumb));
                    $this->error = 'Upload error';
                }
            } else {
                $this->error = 'File upload error. Check uploads folder for write permissions';
            }

        } else {

            $this->error = 'File is incorrect';
        }

        return $this->formatResponse($success);
    }

    public function actionDistrict()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $citycode = Yii::$app->request->post('id');
            $districtDb = TbDistricts::getDistrictByCity($citycode);
        }

        return ['data' => isset($districtDb) ? $districtDb : []];
    }

    public function actionCheckform()
    {
        if (!CommonLib::checkOrigin()) {
            header("HTTP/1.1 403 Origin Denied");
            return false;
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->post()) {
            $orderID = Yii::$app->request->post('id');
            $type = Yii::$app->request->post('type');
            if ($orderID && $type) {
                switch ($type) {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                        return $this->renderAjax('@app/views/complain/_complain');
                        break;
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                    case 10:
                        //chow order
                        $data = TbOrdersDetail::find()->select('a.id,a.orderID,a.quantity,a.unitPrice,a.image,b.name,b.link')
                            ->from(TbOrdersDetail::tableName() . ' a')
                            ->leftJoin(TbProduct::tableName() . ' b', 'a.productID = b.productID')
                            ->where(['a.orderID' => (int)$orderID])->asArray()->all();

                        return $this->renderAjax('@app/views/complain/_order_detail', [
                            'data' => $data,
                        ]);
                        break;

                }
            }
        }

        return [];
    }


    public function actionUpdateCart()
    {
        if (!CommonLib::checkOrigin()) {
            header("HTTP/1.1 403 Origin Denied");
            return false;
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $success =false;
        $message = 'error';
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            if($cart = TbOrdersSession::findOne($id)){
                $qty = (int)Yii::$app->request->post('qty');
                $isCheck = (int)Yii::$app->request->post('is_check');
                $desc = Yii::$app->request->post('desc');
                $cart->quantity = $qty;
                $cart->isCheck = $isCheck;
                $cart->noteProduct = trim(strip_tags($desc));
                $cart->totalPrice = $cart->unitPrice*$qty;
                $cart->totalPriceVn = $cart->unitPriceVn*$qty;
                $cart->save(false);
                $success = true;
                $message = 'success';
            }


        }

        return ['success' => $success,'message'=>$message];
    }

    public function actionUpdateCartShop()
    {
        if (!CommonLib::checkOrigin()) {
            header("HTTP/1.1 403 Origin Denied");
            return false;
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $success =false;
        $message = 'error';
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $items = Yii::$app->request->post('items');
            $is_check = (int)Yii::$app->request->post('is_check');
            TbOrdersSession::updateAll(['isCheck' => $is_check], ['id' => $items]);
            $success = true;
            $message = 'success';
        }

        return ['success' => $success,'message'=>$message];
    }

    //chat message
    public function actionChatMessage(){

        if (!CommonLib::checkOrigin()) {
            header("HTTP/1.1 403 Origin Denied");
            return false;
        }

        $success = false;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $action = $post['action'];
            $order_id = (int)$post['order_id'];

            switch ($action){
                case 'insert_data':
                    $message = trim($post['message']);
                    if(!empty($message)) {
                        $tbchat = new TbChatMessage();
                        $tbchat->from_user_id = \Yii::$app->user->id;
                        $tbchat->message = $message;
                        $tbchat->order_id = $order_id;
                        $tbchat->to_user_id = 0;
                        $tbchat->status = 0;
                        $tbchat->type = 0; //customer
                        $tbchat->save();
                    }
                    break;
            }

            $data = TbChatMessage::fetch_group_chat_history($order_id);
            // pr($data);die;
            if(!empty($data)){
                $success = true;
            }
        }

        return ['success' => $success,'data'=> isset($data) ? $data : [] ];
    }

    public function actionUpfile(){

        if (!CommonLib::checkOrigin()) {
            header("HTTP/1.1 403 Origin Denied");
            return false;
        }


        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $err = 0;
        $file = '';
        $msg = '';
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $model = new UploadForm();

            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()) {
                $target_path =  \Yii::getAlias('@upload_dir') .'/uploads/' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs($target_path);
                $src = \Yii::$app->params['baseUrl'] .'/file/uploads/' . $model->file->baseName . '.' . $model->file->extension;
                $file = '<p><img src="'.$src.'" class="img-thumbnail"  /></p><br />';
            }else{
                $msg = $model->getFirstError('file');
                $err = 1;
            }
        }

        return ['file'=>$file,'err'=>$err,'msg'=>$msg];

    }

        // update status chat of ad
    public function actionChatStatus(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $err = 1;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            //find sms customer not read
            if (TbChatMessage::findOne(['type'=>1,'status'=>0])){
                TbChatMessage::updateAll(['status'=>1,'last_activity'=>date('Y-m-d H:i:s')],['type'=>1,'status'=>0]);
                $err = 0;
            }
        }

        return ['err'=>$err];
    }
        //count chat sms of ad
    public function actionChatCount(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $total = 0;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isGet) {
            $total = TbChatMessage::find()->select('1')->where(['type'=>1,'status'=>0])->count();
        }

        return ['total'=>$total];
    }

    public function actionMessages(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = [];
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isGet) {
            $userID = \Yii::$app->user->id;
            $dataSet = TbChatMessage::find()->select('*')->where(['type'=>2,'status'=>0,'to_user_id'=>$userID])->asArray()->limit(10)->all();
            
            if(!empty($dataSet)){
                foreach ($dataSet as $key => $value) {
                     $value['title'] ='';// CommonLib::cut_string($value['title'],20);
                     $value['message'] = CommonLib::cut_string($value['message'],50);
                      $value['timestamp'] = '';// CommonLib::secondsToTime(strtotime($value['timestamp']));

                    $data[] = $value;
                }               
            }
        }

        return ['data'=>$data];
    }

}