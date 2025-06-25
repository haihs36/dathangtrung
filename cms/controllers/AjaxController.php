<?php

/**
 * Created by PhpStorm.
 * User: SUTI
 * Date: 11/11/2018
 * Time: 10:40 AM
 */

namespace cms\controllers;


use cms\models\Consignment;
use cms\models\ConsignmentDetail;
use common\components\CommonLib;
use common\components\Controller;
use common\helpers\Image;
use common\helpers\Upload;
use common\models\Bag;
use common\models\BagDetail;
use common\models\PaymentSupport;
use common\models\Photo;
use common\models\TbChatMessage;
use common\models\TbCustomers;
use common\models\TbHistory;
use common\models\TbOrders;
use common\models\TbOrdersDetail;
use common\models\TbProduct;
use common\models\TbShippers;
use common\models\TbShipping;
use common\models\TbTransfercode;
use common\models\UploadForm;
use common\models\User;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use Yii;

class AjaxController extends Controller
{

    public static function checkOrigin()
    {
        $allowOrigin = [Yii::$app->params['adminUrl']];
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        if ($origin == '') {
            return true;
        } elseif (in_array($origin, $allowOrigin)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Methods: GET, POST');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 3600');
            return true;
        }
        return false;
    }

    public function actionShootBarcode(){
        $success = false;

        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $validate = new TbShippers();
            $validate->shippingCode = trim(\Yii::$app->request->post('barcode'));

            if($validate->validate(['shippingCode'])){
                if (!empty($barcode)) {
                    $barcode = $validate->shippingCode;
                    $user    = \Yii::$app->user->identity;
                    $role = $user->role;
                    /*kiem tra xem ma nay da duoc kho vn hay kho TQ ban chua*/
                    $nv_kho = ($role === WAREHOUSETQ) ? 2 : 1;
                    //kiem tra kho TQ hoac kho vn da ban hay chua
                    $model = TbShipping::find()->where(['shippingCode' => $barcode, 'city' => $nv_kho])->one();


                    return $this->formatResponse([
                        'success' => true,
                    ]);

                }
            }else{
                if(!empty($validate->errors)){
                    $errors = '';
                    foreach ($validate->errors as $error){
                        $errors .= $error[0];
                    }

                    return $this->formatResponse([
                        'success' => false,
                        'message' => $errors
                    ]);
                }
            }
        }

        return $this->formatResponse(['success' => $success]);
    }
    public function actionGetios()
    {
        $url = 'https://thietkewebos.com/thong-bao.html';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($result, true);


        return $this->formatResponse($json);
    }

    public function actionQuantityReceived()
    {
        $success = false;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $detail_id = (int)\Yii::$app->request->post('pid');
            $qty = \Yii::$app->request->post('qty');
            $note = \Yii::$app->request->post('note');

            if ($orderDetail = TbOrdersDetail::findOne($detail_id)) {
                if (!empty($note)) {
                    $orderDetail->note_receive = $note;
                }
                if (!empty($qty)) {
                    $orderDetail->qty_receive = $qty;
                }

                $orderDetail->save(false);

                return $this->formatResponse([
                    'success' => true,
                    'qty' => $orderDetail->qty_receive
                ]);

            }
        }

        return $this->formatResponse(['success' => $success]);
    }


    public function actionUploadImage(){
        if (!CommonLib::checkOriginAd()) {
            header("HTTP/1.1 403 Origin Denied");
            $success = [
                'message' => 'File upload error',
            ];
            return $this->formatResponse($success);
        }

        reset($_FILES);
        $temp = current($_FILES);
        if(!empty($temp)) {
            $temp['tempName'] = $temp['tmp_name'];
            unset($temp['tmp_name']);

            $success = null;
            $imgObj = new UploadedFile($temp);
            $photo = new Photo();
            $photo->image = $imgObj;
            if ($photo->image && $photo->validate(['image'])) {
                $filename = pathinfo($temp['name'], PATHINFO_FILENAME);
                $filename =  str_replace(' ','-',$filename) . '-'.date('ym').'-'.date('H-i-s');
                $photo = Image::upload($photo->image, $this->upload_image, null, null, false, $filename);

                echo json_encode(array('location' => Yii::$app->params['FileDomain'].$photo));
            }
        }

        // Notify editor that the upload failed
        header("HTTP/1.1 500 Server Error");

        return ;
    }

    public function actionUpload()
    {
        if (!CommonLib::checkOriginAd()) {
            header("HTTP/1.1 403 Origin Denied");
            $success = [
                'message' => 'File upload error',
            ];
            return $this->formatResponse($success);
        }

        $success = null;
        $photo = new Photo();
        $photo->image = UploadedFile::getInstance($photo, 'image');

        if ($photo->image && $photo->validate(['image'])) {
            $fileName = $photo->image->size . '-' . CommonLib::getRandomInt(5);
            $photo->image = Image::upload($photo->image, $this->upload_image, null, null, false, $fileName);
            if ($photo->image) {
                // $photo->thumb = Image::thumb($photo->image, Photo::PHOTO_MEME_THUMB_WIDTH, Photo::PHOTO_MEME_THUMB_HEIGHT, true, $this->upload_thumb, $fileName);
                $photo->productID = (int)\Yii::$app->request->post('pid');
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

    public function actionCheckform()
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (\Yii::$app->request->isAjax && \Yii::$app->request->post()) {
            $orderID = \Yii::$app->request->post('id');
            $type = \Yii::$app->request->post('type');
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

    /*update consignment detail*/
    public function actionInsertBarcode()
    {
        $success = false;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {

            $consignID = (int)\Yii::$app->request->post('pid');
            $barcode = trim(\Yii::$app->request->post('barcode'));
            $cusID = (int)\Yii::$app->request->post('cusID');
            $cutommer = TbCustomers::findOne($cusID);

            if ($consignID && !empty($barcode) && $cutommer) {
                if (ConsignmentDetail::findOne(['consignID' => $consignID, 'barcode' => $barcode])) {
                    return $this->formatResponse(['message' => 'M "' . $barcode . '"  tn ti. Vui lng kim tra li', 'success' => $success]);
                }

                //insert
                $ConsignDetail = new ConsignmentDetail();
                $ConsignDetail->barcode = $barcode;
                $ConsignDetail->consignID = $consignID;
                if ($ConsignDetail->save(false)) {
                    $success = true;
                }

                //get all item consignment detail
                $dataGet = ConsignmentDetail::find()->where(['consignID' => $consignID])->orderBy('id desc')->asArray()->all();
                $consign = Consignment::findOne($consignID);
                $data = $this->renderPartial('@app/views/consignmentdetail/_list', ['data' => $dataGet, 'discountKgPrice' => $cutommer->discountKg, 'consign' => $consign]);

                return $this->formatResponse([
                    'html' => $data,
                    'message' => 'Cp nht tht bi.',
                    'success' => $success
                ]);

            }
        }

        return $this->formatResponse(['message' => 'Cp nht tht bi.', 'success' => $success]);
    }

    /*get consignment detail*/
    public function actionGetConsignDetail()
    {
        $success = false;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $consignID = (int)\Yii::$app->request->post('pid');
            $cusID = (int)\Yii::$app->request->post('cusID');
            $cutommer = TbCustomers::findOne($cusID);

            if ($consignID && $cutommer) {
                //get all item consignment
                $dataGet = ConsignmentDetail::find()->where(['consignID' => $consignID])->orderBy('id desc')->asArray()->all();
                $consign = Consignment::findOne($consignID);
                $data = $this->renderPartial('@app/views/consignmentdetail/_list', ['data' => $dataGet, 'discountKgPrice' => $cutommer->discountKg, 'consign' => $consign]);

                return $this->formatResponse([
                    'html' => $data,
                    'success' => $success
                ]);

            }
        }

        return $this->formatResponse(['success' => $success]);
    }

    /*delete consign detail*/
    public function actionDeleteConsignDetail()
    {
        $success = false;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $id = (int)\Yii::$app->request->post('id');
            if ($model = ConsignmentDetail::findOne($id)) {
                if ($model->delete())
                    $success = true;
            }
        }

        return $this->formatResponse(['success' => $success]);
    }

    //save
    public function actionSaveConsignDetail()
    {
        $success = false;
        $message = 'Cp nht tht bi.';
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $params = \Yii::$app->request->post();
            if (!empty($params)) {
                if ($consignModel = Consignment::findOne((int)$params['consignID'])) {
                    $totalKg = 0;
                    $totalIncurredFee = 0;
                    $totalKgFee = 0;
                    if (isset($params['myArray']) && !empty($params['myArray'])) {
                        foreach ($params['myArray'] as $item) {
                            if ($model = ConsignmentDetail::findOne($item['id'])) {
                                $model->kg = $item['kg'];
                                $model->long = $item['long'];
                                $model->wide = $item['wide'];
                                $model->high = $item['high'];
                                $model->kgChange = $item['kgChange'];
                                $model->kgPay = $item['kgPay'];
                                $model->kgFee = $item['kgFee'];
                                $model->incurredFee = $item['incurredFee'];
                                $model->note = $item['note'];
                                $model->save(false);

                                $totalKg += $model->kgPay; //tong can nang thuc te
                                $totalIncurredFee += $model->incurredFee; //tong phu thu
                                $totalKgFee += $model->kgFee; //tong phi < 1.5 kg
                            }
                        }
                    }

                    $totalMoney = (double)$params['vitual_pay'];
                    if ($totalMoney <= 0) {
                        $cusID = (int)$params['cusID'];
                        $cutommer = TbCustomers::findOne($cusID);
                        if ($cutommer && $totalKg) {
                            $totalMoney = $cutommer->discountKg * $totalKg + $totalIncurredFee + $totalKgFee;
                        }
                    }

                    $consignModel->kg = $totalKg;
                    $consignModel->actualPayment = ($params['vitual_pay'] > 0) ? $params['vitual_pay'] : $totalMoney;
                    $consignModel->amount = $totalMoney;
                    $consignModel->status = $params['status'];
                    $consignModel->lastDate = date('Y-m-d H:i:s');
                    $consignModel->save(false);
                    $success = true;
                    $message = '';
                }

            }
        }

        return $this->formatResponse(['success' => $success, 'message' => $message]);
    }

    //chat message
    public function actionChatMessage()
    {
        $success = false;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $action = $post['action'];
            $order_id = (int)$post['order_id'];

            switch ($action) {
                case 'insert_data':
                    $message = trim($post['message']);
                    if (!empty($message)) {
                        $tbchat = new TbChatMessage();
                        $tbchat->from_user_id = \Yii::$app->user->id;
                        $tbchat->message = $message;
                        $tbchat->order_id = $order_id;
                        $tbchat->to_user_id = 0;
                        $tbchat->status = 0;
                        $tbchat->type = 1; //admin
                        $tbchat->save();
                    }
                    break;
            }

            $data = TbChatMessage::fetch_group_chat_history($order_id);
            // pr($data);die;
            if (!empty($data)) {
                $success = true;
            }
        }

        return ['success' => $success, 'data' => isset($data) ? $data : []];
    }

    public function actionUpfile()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $err = 0;
        $file = '';
        $msg = '';
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $model = new UploadForm();

            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()) {
                $target_path = \Yii::getAlias('@upload_dir') . '/uploads/' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs($target_path);
                $src = \Yii::$app->params['baseUrl'] . '/file/uploads/' . $model->file->baseName . '.' . $model->file->extension;
                $file = '<p><img src="' . $src . '" class="img-thumbnail"  /></p><br />';
            } else {
                $msg = $model->getFirstError('file');
                $err = 1;
            }
        }

        return ['file' => $file, 'err' => $err, 'msg' => $msg];

    }

// update status chat in admin
    public function actionChatStatus()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $err = 1;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            //find sms customer not read
            if (TbChatMessage::findOne(['type' => 0, 'status' => 0])) {
                TbChatMessage::updateAll(['status' => 1, 'last_activity' => date('Y-m-d H:i:s')], ['type' => 0, 'status' => 0]);
                $err = 0;
            }
        }

        return ['err' => $err];
    }

//count chat sms of customer
    public function actionChatCount()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $total = 0;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isGet) {
            $total = TbChatMessage::find()->select('1')->where(['type' => 0, 'status' => 0])->count();
        }

        return ['total' => $total];
    }

    //sensms to user
    public function actionSendSms($id)
    {

        $model = new TbChatMessage();
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(\Yii::$app->request->post())) {
                if (ActiveForm::validate($model)) {
                    return ActiveForm::validate($model);//['rs' => 'error', 'status' => 1, 'mess' => ActiveForm::validate($model)];
                }
                if ($order = TbOrders::findOne($id)) {
                    $model->order_id = $order->orderID;
                    $model->type = 2; //trang thai gui tin don hang
                    $model->status = 0;
                    $model->to_user_id = $order->customerID;
                    $model->from_user_id = \Yii::$app->user->id;

                    if ($model->save(false)) {
                        return ['mess' => '<div style="text-align: center"><label class="font24 red-color"><i class="fa fa-check-circle"></i></label>  Gi thng bo thnh cng!</div>'];
                    }
                }
            } else {
                return $this->renderAjax('@app/views/chat/_form_sms', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('@app/views/chat/_form_sms', [
            'model' => $model,
        ]);
    }

    //update fullname
    public function actionUpdatefullname()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $total = 0;
        $data = User::find()->select('id,first_name,last_name,username')->asArray()->all();
        if ($data) {

            foreach ($data as $item) {

                $fullname = trim($item['first_name']) . ' ' . trim($item['last_name']);
                // if($item['id'] == 202){
                //     var_dump($fullname);die;
                // }
                User::updateAll(['fullname' => $fullname], ['id' => $item['id']]);
            }
            echo 'success';
            die;
        }

    }

    public function actionUpdatebag()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $err = 0;
        $file = '';
        $msg = '';
        $success = true;
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $barcode = \Yii::$app->request->post('barcode');
            $bagid = (int)\Yii::$app->request->post('bagid');
            $barcode = trim(strip_tags($barcode));

            $trans = TbTransfercode::findOne(['transferID' => $barcode]);
            if ($trans) {
                //barcode unique
                if (!BagDetail::findOne(['transferID' => $trans['id']])) {
                    //insert bag detail
                    $tbBagDetail = new BagDetail();
                    $tbBagDetail->bagID = $bagid;
                    $tbBagDetail->status = 1;
                    $tbBagDetail->transferID = $trans['id'];//ma id TbTransfercode
                    $tbBagDetail->barcode = $trans['transferID'];//ma barcode
                    $tbBagDetail->orderID = isset($trans['orderID']) ? $trans['orderID'] : '';
                    $tbBagDetail->createDate = date('Y-m-d H:i:s');
                    $tbBagDetail->save(false);


                    $msg = 'Cp nht thnh cng';
                } else {
                    $success = false;
                    $msg = 'Kin khng tn ti'; //$msg = 'Kin  c cho vo bao ng';
                }

                $dataDetail = BagDetail::getBagDetailByBagId($bagid);
                $html = $this->renderPartial('bag-info', ['data' => $dataDetail]);
            } else {
                $msg = 'Kin khng tn ti';
                $success = false;
            }
        }


        return ['success' => $success, 'html' => isset($html) ? $html : '', 'msg' => $msg];

    }


    public function actionBagdelitem()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $id = (int)\Yii::$app->request->post('id');
            $bagDetail = BagDetail::findOne($id);
            if ($bagDetail && $bagDetail->status != 2) {
                $bag = Bag::findOne($bagDetail->bagID);
                if ($bag && ($bag->userID == \Yii::$app->user->id)) { //neu dung nguoi tao thi duoc xoa
                    //update history
                    $tbHistory = new TbHistory();
                    $tbHistory->orderID = '';
                    $tbHistory->userID = \Yii::$app->user->id;
                    $tbHistory->content = 'Qun tr: <b>' . \Yii::$app->user->identity->username . '</b><br/>';
                    $tbHistory->content .= ' xa : <b>' . $bagDetail->barcode . '</b> khi bao <b>B-' . $bag->id . '</b>';
                    $tbHistory->save(false);

                    $bagDetail->delete();//xoa khoi bao

                    return ['success' => true, 'msg' => 'Xa thnh cng'];
                }
            }
        }

        return ['success' => false, 'msg' => 'Xa tht bi'];

    }

    public function actionCanceltsport()
    {

        $success = false;
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $id = (int)Yii::$app->request->post('id');
            if (PaymentSupport::updateAll(['status' => 4, 'update_time' => date('Y-m-d H:i:s')], ['id' => $id])) {
                $success = true;
            }
        }

        return $this->formatResponse(['success' => $success]);

    }

    //duyet don
    public function actionPaymenttransport()
    {
        $success = false;
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $id = (int)Yii::$app->request->post('id');
            if (PaymentSupport::updateAll(['status' => 3, 'update_time' => date('Y-m-d H:i:s')], ['id' => $id])) {
                $success = true;
            }
        }

        return $this->formatResponse(['success' => $success]);

    }
}