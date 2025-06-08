<?php
    /**
     * Created by PhpStorm.
     * User: SUTI
     * Date: 11/11/2018
     * Time: 10:40 AM
     */

    namespace cms\controllers;


    use cms\models\Lo;
    use common\components\CommonLib;
    use common\components\Controller;
    use common\helpers\Image;
    use common\models\Bag;
    use common\models\BagDetail;
    use common\models\ChatSuggestion;
    use common\models\PaymentSupport;
    use common\models\Photo;
    use common\models\TbAccountBanking;
    use common\models\TbAccountTransaction;
    use common\models\TbChatMessage;
    use common\models\TbCustomers;
    use common\models\TbHistory;
    use common\models\TbOrders;
    use common\models\TbOrdersDetail;
    use common\models\TbOrderSupplier;
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


    /*
         * ke toan chuyen trang thai da dat hang
         * */
        public function actionBooking()
        {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            $alert = 'Cập nhật thất bại';
            $error = 1;
            if (\Yii::$app->request->isAjax) {
                $orderIds = Yii::$app->request->post('arrId');
                $orderID = (int)Yii::$app->request->post('orderID');

                if (!empty($orderIds) || $orderID) {
                    if (!empty($orderIds))
                        $orderIds = array_map('intval', explode(',', $orderIds));
                    else
                        $orderIds = $orderID;

                    //tim don hang trang thai = 2 va da gui yeu cau thanh toan
                    if ($orders = TbOrders::find()->where(['orderID' => $orderIds, 'status' => 2, 'requestPay' => 1])->all()) {
                        TbOrders::updateAll(['status' => 3, 'shipDate' => date('Y-m-d H:i:s')], ['orderID' => $orderIds, 'status' => 2]);

                        foreach ($orders as $order) {
                            $msg = 'ĐH ' . $order->identify . ' đã đặt hàng';
                            $modelMessage = new TbChatMessage();
                            $modelMessage->title = $msg;
                            $modelMessage->message = $msg;
                            $modelMessage->order_id = $order->orderID;
                            $modelMessage->type = 2; //trang thai gui tin don hang
                            $modelMessage->status = 0;
                            $modelMessage->to_user_id = $order->customerID;
                            $modelMessage->from_user_id = \Yii::$app->user->id;
                            $modelMessage->timestamp = date('Y-m-d H:i:s');
                            $modelMessage->save(false);

                            //update history
                            $tbHistory = new TbHistory();
                            $tbHistory->orderID = $order->orderID;
                            $tbHistory->userID = Yii::$app->user->id;
                            $tbHistory->createDate = date('Y-m-d H:i:s');
                            $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                            $tbHistory->content .= 'Chuyển trạng thái đơn hàng ' . $order->identify . ' sang đã đặt hàng</b><br/>';
                            $tbHistory->save(false);
                        }

                        $alert = 'Cập nhật thành công.';
                        $error = 0;
                    } else {
                        $alert = 'Cập nhật thất bại. Đơn hàng chưa được chuyển chờ thanh toán.';
                    }
                }
            }

            return ['alert' => $alert, 'error' => $error];
        }
        
        public static function checkOrigin()
        {
            $allowOrigin = [Yii::$app->params['adminUrl']];
            $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

            if ($origin == '') {
                return true;
            } else if (in_array($origin, $allowOrigin)) {
                header('Access-Control-Allow-Origin: ' . $origin);
                header('Access-Control-Allow-Methods: GET, POST');
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 3600');
                return true;
            }
            return false;
        }

        //update field in order
        public function actionEditorder()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $err = 1;
            $mes = 'Không thể chỉnh sửa';
            if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
                $data = \Yii::$app->request->post();
                if (!empty($data['name']) && !empty($data['pk'])) {
                    $orderId = (int)$data['pk'];
                    $TbOrders = TbOrders::findOne(['orderID' => $orderId]);

                    if ($TbOrders) {
                        switch ($data['name']) {
                            case "comments":
                                $TbOrders->note_company = trim(strip_tags($data['value']));
                                break;
                        }

                        if ($TbOrders->update()) {
                            $err = 0;
                            $mes = 'Cập nhật thành công';
                        }
                    }
                }

            }

            return ['err' => $err, 'mes' => $mes];
        }

        public function actionShootBarcode()
        {
            $success = false;

            if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
                $validate = new TbShippers();
                $validate->shippingCode = trim(\Yii::$app->request->post('barcode'));

                if ($validate->validate(['shippingCode'])) {
                    if (!empty($barcode)) {
                        $barcode = $validate->shippingCode;
                        $user = \Yii::$app->user->identity;
                        $role = $user->role;
                        /*kiem tra xem ma nay da duoc kho vn hay kho TQ ban chua*/
                        $nv_kho = ($role === WAREHOUSETQ) ? 2 : 1;
                        //kiem tra kho TQ hoac kho vn da ban hay chua
                        $model = TbShipping::find()->where(['shippingCode' => $barcode, 'city' => $nv_kho])->one();


                        return $this->formatResponse([
                            'success' => true,
                        ]);

                    }
                } else {
                    if (!empty($validate->errors)) {
                        $errors = '';
                        foreach ($validate->errors as $error) {
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
                        'qty'     => $orderDetail->qty_receive
                    ]);

                }
            }

            return $this->formatResponse(['success' => $success]);
        }


        public function actionUploadImage()
        {

            reset($_FILES);
            $temp = current($_FILES);
            if (!empty($temp)) {
                $temp['tempName'] = $temp['tmp_name'];
                unset($temp['tmp_name']);

                $success = null;
                $imgObj = new UploadedFile($temp);
                $photo = new Photo();
                $photo->image = $imgObj;
                if ($photo->image && $photo->validate(['image'])) {
                    $filename = pathinfo($temp['name'], PATHINFO_FILENAME);
                    $filename = str_replace(' ', '-', $filename) . '-' . date('ym') . '-' . date('H-i-s');
                    $photo = Image::upload($photo->image, $this->upload_image, null, null, false, $filename);

                    echo json_encode(array('location' => Yii::$app->params['FileDomain'] . $photo));
                }
            }

            // Notify editor that the upload failed
            header("HTTP/1.1 500 Server Error");

            return;
        }

        public function actionUpload()
        {
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
                            'photo'   => [
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
                        return $this->formatResponse(['message' => 'Mã "' . $barcode . '" đã tồn tại. Vui lòng kiểm tra lại', 'success' => $success]);
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
                        'html'    => $data,
                        'message' => 'Cập nhật thất bại.',
                        'success' => $success
                    ]);

                }
            }

            return $this->formatResponse(['message' => 'Cập nhật thất bại.', 'success' => $success]);
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
                        'html'    => $data,
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
            $message = 'Cập nhật thất bại.';
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

        public function actionMessages()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = [];
            if (\Yii::$app->request->isAjax && \Yii::$app->request->isGet) {

                $userLogin = \Yii::$app->user->identity;
                if(in_array($userLogin->role,[ADMIN])){
                    $dataSet = TbChatMessage::getNotifyToAdmin(100);

                }else{
                    $dataSet = TbChatMessage::getNotifyToManager($userLogin->getId(),100);
                }

                if (!empty($dataSet)) {
                    foreach ($dataSet as $key => $value) {
                        $title = isset($value['title']) ? $value['title'] : $value['message'];
                        $value['title'] = CommonLib::cut_string($title, 50);
                        $value['message'] = CommonLib::cut_string($value['message'], 50);
                        $data[$value['isType']][] = $value;
                    }
                }
            }


            $temp = $this->renderPartial('@app/views/ajax/notify', [
                'data' => $data
            ]);

            $total = isset($dataSet) ? count($dataSet) : 0;

            return [
                'temp' => $temp,
                'total' => $total,
            ];
        }

        //chat message
        public function actionChatMessage()
        {
            $success = false;
            $count = 0;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
                $post = \Yii::$app->request->post();
                $action = $post['action'];
                $order_id = (int)$post['order_id'];
                $plain_id = (int)$post['plain_id'];
                $isType =  isset($post['isType']) ? trim($post['isType']) : 'order';
                $isType = ($isType == 'complain' ? $isType :  'order');
                $tborder = TbOrders::findOne($order_id);

                if($tborder){
                    switch ($action) {
                        case 'insert_data':
                            $message = trim($post['message']);
                            $dom_html = str_get_html($message);
                            $listImg = [];
                            if (is_object($dom_html)) {
                                foreach($dom_html->find('img') as $element) {
                                    $listImg[] = $element;
                                }
                            }
                            $message = CommonLib::xss_cleaner($message);
                            if(!empty($listImg)){
                                $message .= implode('<br>',$listImg);
                            }

                            if (!empty($message)) {
                                $tbchat = new TbChatMessage();
                                $tbchat->from_user_id = \Yii::$app->user->id;
                                $tbchat->message = $message;
                                $tbchat->order_id = $order_id;
                                $tbchat->plain_id = $plain_id;
                                $tbchat->to_user_id = $tborder->customerID;
                                $tbchat->status = 0;
                                $tbchat->type = 1;//admin
                                $tbchat->isType = $isType; //trang thai gui tin
                                $tbchat->save();

                            }


                            break;
                    }

                    $data = TbChatMessage::fetch_group_chat_history($order_id,$isType);

                    if (!empty($data)) {
                        $dataView = $data;
                        $success = true;
                        rsort($dataView);
                        foreach ($dataView as $k => $item) {
                            if ($k > 2) break;

                            if ($item['type'] == 0)
                                $count++;

                        }
                    }
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
                            return ['mess' => '<div style="text-align: center"><label class="font24 red-color"><i class="fa fa-check-circle"></i></label>  Gửi thông báo thành công!</div>'];
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


                        $msg = 'Cập nhật thành công';
                    } else {
                        $success = false;
                        $msg = 'Kiện không tồn tại'; //$msg = 'Kiện đã được cho vào bao đóng';
                    }

                    $dataDetail = BagDetail::getBagDetailByBagId($bagid);
                    $html = $this->renderPartial('bag-info', ['data' => $dataDetail]);
                } else {
                    $msg = 'Kiện không tồn tại';
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
                        $tbHistory->content = 'Quản trị: <b>' . \Yii::$app->user->identity->username . '</b><br/>';
                        $tbHistory->content .= 'Đã xóa : <b>' . $bagDetail->barcode . '</b> khỏi bao <b>B-' . $bag->id . '</b>';
                        $tbHistory->save(false);

                        $bagDetail->delete();//xoa khoi bao

                        return ['success' => true, 'msg' => 'Xóa thành công'];
                    }
                }
            }

            return ['success' => false, 'msg' => 'Xóa thất bại'];

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


        public function actionCancelorder()
        {

            $success = false;
            if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
                $orderID = (int)Yii::$app->request->post('id');
                $currentOrder = TbOrders::findOne(['orderID' => $orderID]);
                if ($currentOrder) {
                    $shop = TbOrderSupplier::findOne(['orderID' => $orderID]);
                    //het hang
                    //isStock = 1 => trang thai het hang
                    $shop->isStock = 1;
                    $shop->status = 3;
                    $shop->save(false);
                    //cap nhat lai du lieu cho don hang
                    CommonLib::updateDataOrders($currentOrder);
                    //update history
                    $tbHistory = new TbHistory();
                    $tbHistory->orderID = $currentOrder->orderID;
                    $tbHistory->userID = Yii::$app->user->id;
                    $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                    $tbHistory->content .= 'Cập nhật hủy đơn hàng: <b>' . $currentOrder->identify . '</b>';
                    $tbHistory->save(false);

                    //hoan vi
                    $totalOrderPrice = round($currentOrder->totalPayment);//tong tien don hang
                    $totalPaid = round($currentOrder->totalPaid);//tong tien coc
                    $totalMoneyReturn = 0;
                    if ($totalPaid > $totalOrderPrice) {
                        $totalMoneyReturn = $totalPaid - $totalOrderPrice;
                    }

                    $bank = TbAccountBanking::findOne(['customerID' => $currentOrder->customerID]);
                    if ($totalMoneyReturn && $bank) {
                        $currentOrder->totalPaid -= $totalMoneyReturn;
                        $currentOrder->save(false);
                        $bank->totalResidual += $totalMoneyReturn; //so du tai khoan
                        $bank->totalRefund += $totalMoneyReturn; // tong hoan tra so tien
                        //cap nhat lai tk bank
                        if ($bank->save(false)) {
                            $customer = TbCustomers::findOne(['id'=>$currentOrder->customerID]);
                            $customerName = '';
                            if($customer){
                                $customerName = $customer->username;
                            }
                            //cap nhat lich su giao dich
                            $mdlTransaction = new TbAccountTransaction();
                            $mdlTransaction->type = 6;//trang thai hoan lai tien
                            $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                            $mdlTransaction->customerID = $currentOrder->customerID;
                            $mdlTransaction->userID = Yii::$app->user->id;//nhan vien giao dich
                            $mdlTransaction->sapo = "Hoàn lại tiền thanh toán đơn hàng <b>" . $currentOrder->identify . '</b> vào tài khoản <b>'.$customerName.'</b>';
                            $mdlTransaction->value = $totalMoneyReturn;//so tien nap
                            $mdlTransaction->accountID = $bank->id;//ma tai khoan
                            $mdlTransaction->balance = $bank->totalResidual;//số dư
                            $mdlTransaction->orderCode = $currentOrder->identify;
                            $mdlTransaction->orderID = $currentOrder->orderID;
                            $mdlTransaction->create_date = date('Y-m-d H:i:s');
                            $mdlTransaction->save(false);
                        }
                    }

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
        //update delivery
        public function actionUpdateDelivery()
        {
            $success = false;
            $alert = 'Xử lý thất bại';
            if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
                $id = (int)Yii::$app->request->post('id');
                $tbLo = Lo::findOne(['id'=>$id,'status'=>2]);
                if ($tbLo) {
                    Lo::updateAll(['status' => 1, 'finishDelivery' => date('Y-m-d H:i:s')], ['id' => $id]);
                    $success = true;
                    $alert = 'Xử lý thành công';
                }
            }

            return $this->formatResponse(['success' => $success,'alert' => $alert]);

        }

        /**
         * @author cuonghh
         * @function ajax assign order
         */
        public function actionAssignOrders()
        {
            $model = new TbOrders();
            $model->scenario = 'assign_order';
            \Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(\Yii::$app->request->post())) {
                if (ActiveForm::validate($model)) {
                    return ActiveForm::validate($model);
                } else {
                    $reqData = \Yii::$app->request->post();
                    //validate request
                    if (empty($reqData['TbOrders']['multipleID'])) {
                        return ['mess' => '<div align="center">Lỗi! Không có đơn hàng nào được chọn</div>'];
                    }
                    if (empty($reqData['TbOrders']['businessID'])) {
                        return ['mess' => '<div align="center">Lỗi! Bạn vui lòng chọn nhân viên kinh doanh</div>'];
                    }
                    if (empty($reqData['TbOrders']['orderStaff'])) {
                        return ['mess' => '<div align="center">Lỗi! Bạn vui lòng chọn nhân viên đặt hàng</div>'];
                    }
                    //create arr id
                    $multipleID = explode(',', $reqData['TbOrders']['multipleID']);
                    if (!is_array($multipleID)) {
                        return ['mess' => '<div align="center">Lỗi! Danh sách đơn hàng không đúng định dạng</div>'];
                    }
                    //get arr customer
                    $orders = TbOrders::findAll($multipleID);
                    if (empty($orders)) {
                        return ['mess' => '<div align="center">Lỗi! Không tìm thấy danh sách đơn hàng</div>'];
                    }
                    foreach ($orders as $key => $order) {
                        $order->businessID = $reqData['TbOrders']['businessID'];
                        $order->orderStaff = $reqData['TbOrders']['orderStaff'];
                        $order->update(false);

                        $tbHistory = new TbHistory();
                        $tbHistory->userID = Yii::$app->user->id;
                        $tbHistory->orderID = $order->orderID;
                        $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/> Đã gán đơn hàng ' . $order->orderID . ' thành công';
                        $tbHistory->save();
                    }
                    return ['mess' => '<div align="center"><span class="label label-success"><i class="fa fa-check-circle"></i></span> Gán đơn hàng thành công!</div>'];
                }
            }
            return $this->renderAjax('@app/views/ajax/_form_assign_order', [
                'model' => $model,
            ]);
        }

        /**
         * @author cuonghh
         * @function ajax assign customers
         */
        public function actionAssignCustomers()
        {
            $model = new TbCustomers();
            $model->scenario = 'assign_customer';
            \Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(\Yii::$app->request->post())) {
                if (ActiveForm::validate($model)) {
                    return ActiveForm::validate($model);
                } else {
                    $reqData = \Yii::$app->request->post();

                    //validate request
                    if (empty($reqData['TbCustomers']['multipleID'])) {
                        return ['mess' => '<div align="center">Lỗi! Không có khách hàng nào được chọn</div>'];
                    }
                    if (empty($reqData['TbCustomers']['userID'])) {
                        return ['mess' => '<div align="center">Lỗi! Bạn vui lòng chọn nhân viên kinh doanh</div>'];
                    }
                    if (empty($reqData['TbCustomers']['staffID'])) {
                        return ['mess' => '<div align="center">Lỗi! Bạn vui lòng chọn nhân viên đặt hàng</div>'];
                    }
                    //create arr id
                    $multipleID = explode(',', $reqData['TbCustomers']['multipleID']);
                    if (!is_array($multipleID)) {
                        return ['mess' => '<div align="center">Lỗi! Danh sách khách hàng không đúng định dạng</div>'];
                    }
                    //get arr customer
                    $customers = TbCustomers::findAll($multipleID);
                    if (empty($customers)){
                        return ['mess' => '<div align="center">Lỗi! Không tìm thấy danh sách khách hàng</div>'];
                    }
                    foreach ($customers as $key => $customer){
                        $customer->userID = $reqData['TbCustomers']['userID'];
                        $customer->staffID = $reqData['TbCustomers']['staffID'];
                        $customer->update(false);

                        $tbHistory = new TbHistory();
                        $tbHistory->userID = Yii::$app->user->id;
                        $tbHistory->customerID = $customer->id;
                        $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/> Đã gán khách hàng ' .$customer->id.' thành công';
                        $tbHistory->save();
                    }
                    return ['mess' => '<div align="center"><span class="label label-success"><i class="fa fa-check-circle"></i></span> Gán khách hàng thành công!</div>'];
                }
            }
            return $this->renderAjax('@app/views/ajax/_form_assign_customer', [
                'model' => $model,
            ]);
        }

        public function actionSearchChat()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
                $text = \Yii::$app->request->post('term');
                if(!empty($text)){
                    $text = trim(strip_tags($text));
                    $text = strtolower($text);
                    $data = ChatSuggestion::find()->andFilterWhere(['like', 'title', $text])->asArray()->limit(100)->all();
                }
            }


            return isset($data) ? $data : [];
        }

    }