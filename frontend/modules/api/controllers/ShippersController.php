<?php

    namespace frontend\modules\api\controllers;

    use common\components\CommonLib;
    use common\models\TbShippers;
    use common\models\TbShippersSearch;
    use frontend\modules\api\resources\ShippersResource;
    use Yii;
    use yii\filters\auth\CompositeAuth;
    use yii\filters\auth\HttpBearerAuth;

    use yii\rest\Controller;


    class ShippersController extends Controller
    {

        public $modelClass = ShippersResource::class;

        const LIMIT = 10;

        public function behaviors()
        {
            $behaviors = parent::behaviors();
            $behaviors['authenticator'] = [
                'class'       => CompositeAuth::className(),
                'authMethods' => [
                    HttpBearerAuth::className(),
                ],
            ];

            $behaviors['verbs'] = [
                'class'   => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'list'   => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'create-all' => ['post'],
                    'delete' => ['post', 'delete'],
                ],
            ];

            return $behaviors;
        }

        //ds don ky gui
        public function actionList()
        {
            $searchModel = new TbShippersSearch();
            $params = Yii::$app->request->queryParams;
            $searchModel->shippingCode = isset($params['shippingCode']) ? $params['shippingCode'] : '';
            $searchModel->shippingStatus = isset($params['shippingStatus']) ? $params['shippingStatus'] : '';
            $searchModel->startDate = isset($params['startDate']) ? $params['startDate'] : '';
            $searchModel->endDate = isset($params['endDate']) ? $params['endDate'] : '';

            $page = isset($params['page']) ? (int)$params['page'] : 1;
            $offset = ($page - 1) * self::LIMIT;

            $orders = $searchModel->searchHomeApi($params, $offset, self::LIMIT + 1);
            $next_page = false;

            $data = [];
            if (!empty($orders)) {
                foreach ($orders as $val) {

                    $tmp = [];
                    $tmp['id'] = (int)$val->id;
                    $tmp['shippingCode'] = trim(strip_tags($val->shippingCode));
                    $tmp['weight'] = (int)$val->weight;
                    $tmp['quantity'] = (int)($val->quantity);
                    $tmp['price'] = doubleval($val->price);
                    $tmp['totalMoney'] = doubleval($val->totalMoney);
                    $tmp['status'] = (int)$val->status;
                    $tmp['shippingStatus'] = (int)$val->shippingStatus;
                //    $tmp['method'] = (int)$val->method;
                    $tmp['note'] = trim(strip_tags($val->note));
                    $tmp['image'] = !is_null($val->image) ? $val->image : '';
                    $tmp['createDate'] = $val->createDate;
                    $tmp['editDate'] = !is_null($val->editDate) ? $val->editDate : '';

                    $data[] = $tmp;
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

        //tao don ky gui
        public function actionCreate()
        {
            $success = false;
            $message = 'Gửi đơn thất bại';

            if (Yii::$app->request->isPost) {
                $shippingCode = trim(strip_tags(Yii::$app->request->post('shippingCode')));
                $shippingCode = strip_tags($shippingCode);
                $shippingCode = trim(str_replace(' ', '', $shippingCode));

                $post = Yii::$app->request->post();
                if (!empty($shippingCode)) {

                    $productName = isset($post['productName']) ? $post['productName'] : '';
                    $quantity = isset($post['quantity']) ? $post['quantity'] : '';
                    $link = isset($post['link']) ? $post['link'] : '';
                    $price = isset($post['price']) ? $post['price'] : '';
                    $image = isset($post['image']) ? strip_tags($post['image']) : '';
                    $note = isset($post['note']) ? strip_tags($post['note']) : '';

                    $model = new TbShippers();
                    $model->scenario = 'create';
                    $model->shippingCode = CommonLib::xss_cleaner($shippingCode) ;
                    $model->quantity = (int)$quantity;
                    $model->price = doubleval($price);
                    $model->link = CommonLib::xss_cleaner($link);
                    $model->productName = CommonLib::xss_cleaner($productName);
                    $model->image = $image;
                    $model->note = CommonLib::xss_cleaner($note);
                    $model->totalMoney = $model->quantity * $model->price;
                    $model->userID = Yii::$app->user->id;

                    if ($model->save()) {
                        $success = true;
                        Yii::$app->response->statusCode = 200;
                        return ['success' => $success, 'message' => 'Gửi đơn thành công.'];
                    } else {
                        Yii::$app->response->statusCode = 422;
                        return ['success' => $success, 'message' => $model->errors];
                    }
                }
            }

            Yii::$app->response->statusCode = 422;
            return ['success' => $success, 'message' => $message];
        }

        /*api old update multiple filed*/
        public function actionUpdate()
        {
            $message = 'Cập nhật thất bại';
            $user_id = Yii::$app->user->id;

            if (Yii::$app->request->isPost) {
                $data = \Yii::$app->request->post();
                $id = isset($data['id']) ? (int)$data['id'] : 0;
                $model = TbShippers::findOne(['id' => (int)$id, 'userID' => $user_id, 'status' => 0]);

                if (!empty($data) && $model) {
                    if ($model->userID) {
                        $model->shippingCode = CommonLib::xss_cleaner($data['code']);
                        $model->image = $data['image'];
                        $model->quantity = (int)$data['quantity'];
                        $model->price = (double)$data['price'];
                        $model->link = trim(strip_tags($data['link']));
                        $model->productName = trim(strip_tags($data['productName']));
                        $model->note = trim(strip_tags($data['note']));
                        $model->totalMoney = $model->quantity * $model->price;
                        $model->scenario = 'update';

                        if ($model->save()) {
                            Yii::$app->response->statusCode = 200;
                            return ['success' => true, 'message' => 'cập nhật thành công.'];
                        } else {
                            Yii::$app->response->statusCode = 422;
                            return ['success' => false, 'message' => $model->errors];
                        }
                    }
                }
            }

            Yii::$app->response->statusCode = 422;
            return ['success' => false, 'message' => $message];

        }

        //tao nhieu don ky gui 1 luc
        public function actionCreateAll()
        {
            $arrErr = [];
            $arrSuccess = [];

            if (Yii::$app->request->isPost) {
                $dataPost = Yii::$app->request->post('data');

                if(!empty($dataPost) && is_array($dataPost)){
                    foreach ($dataPost as $item) {
                        $model = new TbShippers();
                        $model->scenario = 'create';
                        $model->shippingCode = CommonLib::xss_cleaner($item['code']);
                        $model->method = (int)$item['method'];
                        $model->note = CommonLib::xss_cleaner($item['note']);
                        $model->userID = Yii::$app->user->id;

                        if ($model->save()) {
                            $arrSuccess[] = $model->shippingCode;
                        } else {
                            if(!empty($model->errors)){
                                foreach ($model->errors as $arrError){
                                    foreach ($arrError as $err) {
                                        $arrErr[] = 'Kiện hàng {'.$model->shippingCode .'}, '.$err;
                                    }
                                }
                            }
                        }
                    }
                }
            }


            $result = [];
            if(!empty($arrSuccess)){
                $result['success'] = [
                    'message' => 'Thêm thành công '.count($arrSuccess).' kiện hàng',
                    'data' => implode(',',$arrSuccess),
                ];
            }

            if(!empty($arrErr)){
                $result['error'] = $arrErr;
            }

            Yii::$app->response->statusCode = 200;
            if(empty($result)){
                $result['error'] = 'Thêm đơn hàng thất bại.';
            }

            return $result;
        }


        /*api update new*/
        public function actionUpdateOne()
        {
            $message = 'Cập nhật thất bại';
            $success = false;

            if (Yii::$app->request->isPost) {
                $dataPost = Yii::$app->request->post();
                $id = isset($dataPost['id']) ? (int)$dataPost['id'] : 0;
                $model = TbShippers::findOne(['id' => $id, 'userID' => Yii::$app->user->id, 'status' => 0]);
                if (!empty($model)) {
                    if ($model->userID && !empty($model->shippingCode)) {
                        $model->scenario = 'update';
                        $model->shippingCode = CommonLib::xss_cleaner($dataPost['code']);
                        $model->method = (int)$dataPost['method'];
                        $model->note = CommonLib::xss_cleaner($dataPost['note']);

                        if ($model->save(false)) {
                            Yii::$app->response->statusCode = 200;
                            return ['success' => true, 'message' => 'cập nhật thành công.'];
                        } else {
                            Yii::$app->response->statusCode = 422;
                            return ['success' => true, 'message' => $model->errors];
                        }
                    }
                }
            }

            Yii::$app->response->statusCode = 422;
            return ['success' => $success, 'message' => $message];

        }

        public function actionDelete($id)
        {
            $user_id = Yii::$app->user->id;
            $shipper = TbShippers::findOne(['id' => (int)$id, 'userID' => $user_id, 'status' => 0]);

            if ($shipper) {
                $shipper->delete();
                Yii::$app->response->statusCode = 200;
                return ['success' => true, 'message' => 'Xóa thành công.'];
            } else {
                Yii::$app->response->statusCode = 422;
                return ['success' => false, 'message' => 'Xóa thất bại'];
            }
        }

    }
