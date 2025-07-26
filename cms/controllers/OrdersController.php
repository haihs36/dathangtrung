<?php
namespace cms\controllers;

use common\components\CommonLib;
use common\models\Photo;
use common\models\TbComplain;
use common\models\TbComplainReply;
use common\models\TbHistory;
use common\models\TbOrdersDetail;
use common\models\TbOrderSupplier;
use common\models\TbProductComplain;
use common\models\TbTransfercode;
//    use kartik\mpdf\Pdf;
use common\models\UploadForm;
use Yii;
use common\models\TbOrders;
use common\models\TbOrderSearch;
use common\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\filters\AccessControl;
use common\models\AccessRule;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * OrdersController implements the CRUD actions for TbOrders model.
 */
class OrdersController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'delete', 'approve', 'approval', 'approved', 'process', 'costs', 'update', 'removeitem'],
                'rules' => [
                    [
                        'actions' => [], //
                        'allow' => false,
                        'roles' => [WAREHOUSETQ],
                    ],
                    [
                        'actions' => ['approved', 'index', 'view'], //
                        'allow' => true,
                        'roles' => [WAREHOUSE, CLERK],
                    ],
                    [
                        'actions' => ['index', 'view', 'update', 'approve'],
                        'allow' => true,
                        'roles' => [BUSINESS]
                    ],
                    [
                        'actions' => ['index', 'view', 'approve', 'approved', 'update'],
                        'allow' => true,
                        'roles' => [STAFFS]
                    ],
                    [
                        'actions' => ['index', 'view', 'approval', 'delete', 'approve', 'approved', 'process', 'costs', 'update', 'removeitem'],
                        'allow' => true,
                        'roles' => [ADMIN]
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $status = (int)Yii::$app->request->get('status');
        $customerID = (int)Yii::$app->request->get('customerID');
        $searchModel = new TbOrderSearch();
        $params = Yii::$app->request->queryParams;
        if ($status) {
            $searchModel->status = $status;
        }

        if (isset($params['orderNumber']) && !empty($params['orderNumber'])) {
            $this->flash('danger', 'Kết quả tìm kiếm mã: ' . $params['orderNumber']);
        }
        if ($customerID) {
            $params['TbOrderSearch']['customerID'] = $customerID;
        }

        $params['TbOrderSearch']['orderID'] = isset($params['orderNumber']) ? $params['orderNumber'] : null;
        $searchModel->load($params);
        $status = $searchModel->status;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'params' => $params,
            'status' => $status,
        ]);
    }

    //don hang qua han
    public function actionApprove()
    {
        $status = (int)Yii::$app->request->get('status', 0);

        $searchModel = new TbOrderSearch();
        $params = Yii::$app->request->queryParams;
        if ($status) {
            $searchModel->status = $status;
        }
        $params['status'] = $status;
        $searchModel->load(Yii::$app->request->get());
        $status = $searchModel->status;
        $isBook = 0;

        $dataProvider = $searchModel->search($params, $isBook);

        return $this->render('approve', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'params' => $params,
            'status' => $status,
            'isBook' => $isBook,
        ]);
    }

    /*don hang da dat*/
    public function actionApproved()
    {
        $status = (int)Yii::$app->request->get('status', 2);
        /*if($status != 3)
            return $this->redirect(['orders/approved','status'=>3]);*/
        $isBook = 1;
        $searchModel = new TbOrderSearch();
        $params = Yii::$app->request->queryParams;
        $params['status'] = $status;
        if ($status) {
            $searchModel->status = $status;
        }
        $searchModel->load(Yii::$app->request->get());
        $status = $searchModel->status;
        $dataProvider = $searchModel->search($params, $isBook);

        return $this->render('approved', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'params' => $params,
            'status' => $status,
            'isBook' => $isBook,
        ]);
    }

    /*cap nhat lai trang thai don hang*/
    public function actionProcess($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $role = Yii::$app->user->identity->role;
            //chi nhung don chua tra hang va ko het hang
            if ($role == WAREHOUSE && !in_array($model->status, [5, 6])) {
                $this->flash('success', 'Bạn chỉ có quyền cập nhật trạng thái trả hàng. Xin vui lòng thử lại.');
                return $this->redirect(['orders/process', 'id' => $model->orderID]);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            } else {

                $tbHistory = new TbHistory();
                $tbHistory->userID = Yii::$app->user->id;
                $tbHistory->orderID = $model->orderID;
                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';

                $statusText = \common\components\CommonLib::statusText();

                if ($model->status != $model->oldAttributes['status']) {
                    $tbHistory->content .= 'Thay đổi trạng thái đơn hàng từ <b>' . $statusText[$model->oldAttributes['status']] . '</b> thành <b>' . $statusText[$model->status] . '</b>';
                }

                $tbHistory->save(false);
                if ($model->save(false)) {
                    // CommonLib::updateOrder($model);
                    $this->flash('success', 'Cập nhật thành công');
                }


            }
        }


        return $this->render('_form', [
            'model' => $model,
        ]);

    }

    //cai dat phi
    public function actionCosts($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            //% phi dich vu ap dung cho don hang
            $model->discountDeals = CommonLib::getPercentDVofOrder($model->totalOrder, $model->customer->discountRate, $model->discountDeals, $model->provinID);
            //get tien giam gia kg

            $model->weightDiscount = CommonLib::getKgofOrder($model->totalWeight, $model->customer->discountKg, $model->weightDiscount, $model->provinID);
            $model->weightCharge = $model->weightDiscount; //tien can nang cai dat cho don hang
            //end
            $customer = $model->customer;
            $cnys = isset($customer->cny) ? $customer->cny : '';
            $cny = CommonLib::getCNY($this->setting['CNY'],$cnys,$model->cny);
            $cny = CommonLib::toInt($cny);  //T gi p dng cho n hng

            $model->cny = $cny;
            $model->totalShipVn = $model->cny * $model->totalShip;


            $str = '';
            if ($model->provinID != $model->oldAttributes['provinID']) {
                $Province = \yii\helpers\ArrayHelper::map(\common\models\Province::find()->select('id,name')->asArray()->all(), 'id', 'name');
                $str .= 'Thay đổi kho chứa ' . $Province[$model->oldAttributes['provinID']] . ' thành ' . $Province[$model->provinID] . ' <br>';

            }
            if ($model->cny != $model->oldAttributes['cny']) {
                $str .= 'Thay đổi tỉ giá  <b>' . number_format($model->oldAttributes['cny']) . '</b> thành <b>' . number_format($model->cny) . '</b> <br>';
            }
            if ($model->discountDeals != $model->oldAttributes['discountDeals']) {
                $str .= 'Thay đổi % dịch vụ  <b>' . $model->oldAttributes['discountDeals'] . '</b>% thành <b>' . $model->discountDeals . '</b>% <br>';
            }

            if ($model->weightDiscount != $model->oldAttributes['weightDiscount']) {
                $str .= 'Thay đổi phí cân nặng <b>' . number_format($model->oldAttributes['weightDiscount']) . '</b> thành <b>' . number_format($model->weightDiscount) . '</b> <br>';
            }
            if ($model->deposit != $model->oldAttributes['deposit']) {
                $str .= 'Thay đổi đặt cọc <b>' . $model->oldAttributes['deposit'] . '</b>% thành <b>' . $model->deposit . '</b>%';
            }
            //update history
            $tbHistory = new TbHistory();
            $tbHistory->orderID = $model->orderID;
            $tbHistory->userID = Yii::$app->user->id;
            $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
            $tbHistory->content .= 'Cài đặt phí đơn hàng: <b>' . $model->identify . '</b><br/>';
            $tbHistory->content .= $str;
            $tbHistory->save(false);

            if ($model->save(false)) {
                CommonLib::updateOrder($model);
                $this->flash('success', 'Cập nhật thành công');
            }


        }

        return $this->render('_form', [
            'model' => $model
        ]);
    }

    /*duyet don*/
    public function actionApproval($id)
    {

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                $model->discountKg = CommonLib::toInt($model->discountKg);
                //phi chiet khau cho kinh doanh
                $model = CommonLib::getBusinessFee($model);
                //tinh tien chiet khau cho nv
                // $model->discountBusiness =  ($model->totalDiscountVn + $model->orderFee)*$model->discountRate/100  + ($model->discountKg * $model->totalWeight);

                $model->discountBusiness = ($model->orderFee) * $model->discountRate / 100 + ($model->discountKg * $model->totalWeight);
                $model->staffdiscountTotal = $model->totalDiscountVn * $model->staffDiscount / 100;

                $tbHistory = new TbHistory();
                $tbHistory->userID = Yii::$app->user->id;
                $tbHistory->orderID = $model->orderID;
                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                $tbHistory->content .= ' duyệt đơn hàng: <b>' . $model->identify . '</b>';

                $listUser = \common\components\CommonLib::listUser();
                if ($model->businessID != $model->oldAttributes['businessID'] && isset($listUser[$model->businessID])) {
                    $tbHistory->content .= '<br/>Duyệt đơn hàng cho kinh doanh: <b>' . $listUser[$model->businessID] . '</b>';
                }

                if ($model->discountRate != $model->oldAttributes['discountRate']) {
                    $tbHistory->content .= '<br/>Cài đặt chiết khấu dịch vụ cho kinh doanh: ' . $model->discountRate . '%';
                }
                if ($model->discountKg != $model->oldAttributes['discountKg']) {
                    $tbHistory->content .= '<br/>Cài đặt chiết khấu cân nặng cho kinh doanh: ' . number_format($model->discountKg) . 'vn';
                }

                if ($model->orderStaff != $model->oldAttributes['orderStaff'] && isset($listUser[$model->orderStaff])) {
                    $tbHistory->content .= '<br/>Duyệt đơn hàng cho đặt hàng: <b>' . $listUser[$model->orderStaff] . '</b>';
                }
                if ($model->staffDiscount != $model->oldAttributes['staffDiscount']) {
                    $tbHistory->content .= '<br/>Cài đặt chiết khấu cho đặt hàng: ' . ($model->staffDiscount) . '%';
                }


                $tbHistory->save(false);
                if ($model->save(false)) {
                    // CommonLib::updateOrder($model); //khong cap nhat lai tranh tinh trang don da tra hang ko may lai thay doi
                    $this->flash('success', 'Cập nhật thành công');
                }
            }
        }

        return $this->render('_form', [
            'model' => $model
        ]);
    }

    public function actionView($id)
    {
        $currentOrder = $this->findModel($id);
        if ($currentOrder->load(Yii::$app->request->post())) {
            if ($currentOrder->save()) {
                $this->flash('success', 'Cập nhật thành công.');
            }
        }
        $data = TbOrders::getOrderDetailView($id);




        $order = [];
        if ($data) {
            foreach ($data as $item) {
                $order[$item['supplierID']][$item['id']] = $item;
            }
        }

        $uploadForm = new UploadForm();

        return $this->render('order-detail', [
            'order' => $order,
            'uploadForm' => $uploadForm,
            'currentOrder' => $currentOrder,
        ]);
    }

    public function actionDelete($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $role = Yii::$app->user->identity->username;
        $success = '';
        $message = '';
        $error = '';

        if ($role == ADMINISTRATOR) {
            $mdlOrder = $this->findModel($id);
            TbComplainReply::deleteAll(['orderID' => $id]);
            $TbComplain = TbComplain::findAll(['orderID' => $id]);
            if ($TbComplain) {
                foreach ($TbComplain as $item) {
                    $dbProduct = TbProductComplain::find()->select(['productID'])->where(['complainID' => $item['id']])->all();
                    if ($dbProduct) { //del from photo
                        foreach ($dbProduct as $product) {
                            if ($photo = Photo::findOne(['productID' => $product->productID])) {
                                $photo->delete();
                            }
                        }
                    }
                    TbProductComplain::deleteAll(['complainID' => $item['id']]);
                }
            }
            TbComplain::deleteAll(['orderID' => $id]);
            TbOrdersDetail::deleteAll(['orderID' => $id]);
            TbOrderSupplier::deleteAll(['orderID' => $id]);
            TbTransfercode::deleteAll(['orderID' => $id]);

            $tbHistory = new TbHistory();
            $tbHistory->userID = Yii::$app->user->id;
            $tbHistory->orderID = $mdlOrder->orderID;
            $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b>';
            $tbHistory->content .= '<br/> xóa đơn hàng có mã: <b>' . $mdlOrder->identify . '</b>';
            $tbHistory->content .= 'Tất cả các khiếu nại, mã kiện hàng của đơn hàng cũng bị xóa theo';
            $tbHistory->save(false);

            if ($mdlOrder->delete()) {
                $success = 'success';
                $message = 'Xóa đơn hàng thành công.';
            } else {
                $error = 'Xóa đơn hàng thất bại.';
            }
        }
        return ['result' => $success, 'message' => $message, 'error' => $error];
        //return $this->redirect(['index']);
    }

    public function actionDelall($id)
    {
        $mdlOrder = $this->findModel($id);

        $orders = TbOrders::find()->where(['status' => 5])->asArray()->all();
        echo count($orders);
        die;

        TbComplainReply::deleteAll(['orderID' => $id]);
        $TbComplain = TbComplain::findAll(['orderID' => $id]);
        if ($TbComplain) {
            foreach ($TbComplain as $item) {
                $dbProduct = TbProductComplain::find()->select(['productID'])->where(['complainID' => $item['id']])->all();
                if ($dbProduct) { //del from photo
                    foreach ($dbProduct as $product) {
                        if ($photo = Photo::findOne(['productID' => $product->productID])) {
                            $photo->delete();
                        }
                    }
                }
                TbProductComplain::deleteAll(['complainID' => $item['id']]);
            }
        }
        TbComplain::deleteAll(['orderID' => $id]);
        TbOrdersDetail::deleteAll(['orderID' => $id]);
        TbOrderSupplier::deleteAll(['orderID' => $id]);
        TbTransfercode::deleteAll(['orderID' => $id]);

        $tbHistory = new TbHistory();
        $tbHistory->userID = Yii::$app->user->id;
        $tbHistory->orderID = $mdlOrder->orderID;
        $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b>';
        $tbHistory->content .= '<br/> xóa đơn hàng có mã: <b>' . $mdlOrder->identify . '</b>';
        $tbHistory->content .= 'Tất cả các khiếu nại, mã kiện hàng của đơn hàng cũng bị xóa theo';
        $tbHistory->save(false);

        if ($mdlOrder->delete()) {
            $success = 'success';
            $message = 'Xóa đơn hàng thành công.';
        } else {
            $error = 'Xóa đơn hàng thất bại.';
        }
    }

    /*remove item*/
    public function actionRemoveitem()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $status = false;
        if (Yii::$app->request->post()) {
            $productId = (int)Yii::$app->request->post('productId');
            if ($productId) {
                TbOrdersDetail::deleteAll(['id' => $productId]);
                $status = true;
                $tbHistory = new TbHistory();
                $tbHistory->orderID = 0;
                $tbHistory->userID = Yii::$app->user->id;
                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/> xóa sản phẩm có mã: <b>' . $productId . '</b><br/>';
                $tbHistory->save(false);
            }
        }

        return ['status' => $status];
    }

    /**
     * Content: update loi don hang
     * author: Administrator
     * createdDate: 2018-05-14 12:28
     */
    public function actionFix()
    {
        $orderID = Yii::$app->request->get('id');
        if ($orderID) {
            //lay  don hang da tra
            $currentOrder = $this->findModel(['orderID' => $orderID, 'status' => 6]);
            if ($currentOrder) {
                //% phi dich vu ap dung cho don hang
                $currentOrder->discountDeals = CommonLib::getPercentDVofOrder($currentOrder->totalOrder, $currentOrder->customer->discountRate, $currentOrder->discountDeals, $currentOrder->provinID);
                //get tien giam gia kg
                $weightDiscount = CommonLib::getKgofOrder($currentOrder->totalWeight, $currentOrder->customer->discountKg, $currentOrder->weightDiscount, $currentOrder->provinID);
                $currentOrder->weightCharge = $weightDiscount;
                CommonLib::updateDataOrders($currentOrder);
            }

            echo 'cập nhật thành công';
            die;
        }

        echo 'không có mã đơn hàng';
        die;
    }

    //cap nhat don hang

    public function actionUpdate()
    {
        $post = Yii::$app->request->post();
        if ($post) {
            $orderID = $post['orderID'];
            if (!isset($post['shop'])) {
                // $this->flash('danger', 'Không có shop nào được chọn cập nhật');
                return $this->redirect(['orders/view', 'id' => $orderID]);
            }

            $shopAll = $post['shop'];

            $supID = 0;
            $currentOrder = $this->findModel($orderID);

            //% phi dich vu ap dung cho don hang
            $customer = $currentOrder->customer;
            $currentOrder->discountDeals = CommonLib::getPercentDVofOrder($currentOrder->totalOrder, $customer->discountRate, $currentOrder->discountDeals, $currentOrder->provinID);
            //get tien giam gia kg
            $weightDiscount = CommonLib::getKgofOrder($currentOrder->totalWeight, $customer->discountKg, $currentOrder->weightDiscount, $currentOrder->provinID);
            $currentOrder->weightCharge = $weightDiscount;

            //cap nhat shop tren form
            if ($shopAll && $orderID) {
                $isBaogia = false;


                $cnys = isset($customer->cny) ? $customer->cny : '';
                $cny = CommonLib::getCNY($this->setting['CNY'],$cnys,$currentOrder->cny);
                $cny = CommonLib::toInt($cny);  //T gi p dng cho n hng

                //lay uu dai giam gia cai dat cho don hang, ko co lay mac dinh
                $currentOrder->cny = $cny;
                //cap nhat thay doi cac shop
                foreach ($shopAll as $supID => $item) {
                    if ($item) {
                        $shop = TbOrderSupplier::findOne(['supplierID' => $supID, 'orderID' => $orderID]);

                        // pr($currentOrder);
                        // pr($post);  die;


                        if ($shop) {

                            $shop->cny = $cny;
                            if (isset($item['shipmentFee']) && $item['shipmentFee'] != $shop->shipmentFee) {
                                $shop->shipmentFee = $item['shipmentFee'];
                                $shop->shipmentVn = $item['shipmentFee'] * $cny; //quy doi sang vn

                                //update history
                                $tbHistory = new TbHistory();
                                $tbHistory->orderID = $currentOrder->orderID;
                                $tbHistory->userID = Yii::$app->user->id;
                                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                                $tbHistory->content .= 'Cập nhật phí nội địa : <b>' . $item['shipmentFee'] . '</b> () cho đơn hàng ' . $currentOrder->identify;
                                $tbHistory->save(false);

                            }

                            if (isset($item['shopProductID']) && $item['shopProductID'] != $shop->shopProductID) {
                                /*if (!empty($item['shopProductID'])) {
                                    if (TbOrderSupplier::CheckOrderNumber($shop->orderID, $item['shopProductID'], $shop->supplierID)) {
                                        $this->flash('success', 'M Order Number: <b>' . $item['shopProductID'] . '</b>  tn ti');
                                        return $this->redirect(['orders/view', 'id' => $shop->orderID, '#' => 'orderNumber-' . $shop->supplierID]);
                                    }
                                }*/
                                $shop->shopProductID = $item['shopProductID'];

                                //update history
                                $tbHistory = new TbHistory();
                                $tbHistory->orderID = $currentOrder->orderID;
                                $tbHistory->userID = Yii::$app->user->id;
                                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                                $tbHistory->content .= 'Cập nhật ph m nh cung cp : <b>' . $item['shopProductID'] . '</b> đơn hàng ' . $currentOrder->identify;
                                $tbHistory->save(false);
                            }
                            /*if (isset($item['weight'])) {

                                $shop->weight = $item['weight'];
                                $phi_kg = CommonLib::getKgofOrder($shop->weight, $currentOrder->customer->discountKg, $currentOrder->weightDiscount, $currentOrder->provinID);//CommonLib::getFeeKg($shop->weight);
                                $shop->shopPriceKg = ($shop->weight > 0) ? ($shop->weight * $phi_kg) : 0;
                            }*/

                            if (isset($item['actualPayment']) && $item['actualPayment'] != $shop->actualPayment) {
                                $shop->actualPayment = $item['actualPayment'];


                                //update history
                                $tbHistory = new TbHistory();
                                $tbHistory->orderID = $currentOrder->orderID;
                                $tbHistory->userID = Yii::$app->user->id;
                                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                                $tbHistory->content .= 'Cập nhật TT thực: <b>' . $item['actualPayment'] . '</b> () cho đơn hàng ' . $currentOrder->identify;
                                $tbHistory->save(false);
                            }
                            /*if (isset($item['noteInsite'])) {
                                $shop->noteInsite = trim($item['noteInsite']);
                            }*/
                            $shop->discountDeals = $currentOrder->discountDeals; //gan lai uu dai giam gia cho shop


                            if (isset($item['incurredFee'])) {
                                $incurredFee = CommonLib::toInt($item['incurredFee']);
                                if ($incurredFee != $shop->incurredFee) {
                                    $shop->incurredFee = $incurredFee;//tien phat sinh
                                    //update history
                                    $tbHistory = new TbHistory();
                                    $tbHistory->orderID = $currentOrder->orderID;
                                    $tbHistory->userID = Yii::$app->user->id;
                                    $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                                    $tbHistory->content .= 'Cập nhật phí phát sinh: <b>' . number_format($incurredFee) . '</b> () cho đơn hàng ' . $currentOrder->identify;
                                    $tbHistory->save(false);
                                }

                            }

                            if (isset($item['mvd'])) {
                                //insert transfer code
                                /*if (!empty($item['billLadinID'])) {
                                    if (TbOrderSupplier::CheckBillLadin($shop->orderID, $item['billLadinID'], $shop->supplierID)) {
                                        $this->flash('success', 'M vn n: <b>' . $item['billLadinID'] . '</b>  tn ti');
                                        return $this->redirect(['orders/view', 'id' => $shop->orderID, '#' => 'mvd-' . $shop->supplierID]);
                                    }
                                }*/

                                if (!empty($item['mvd'])) {

                                    $shop = CommonLib::shippingProcess($currentOrder, $shop, $item['mvd']);
                                    $billLadinID = implode(';', $item['mvd']);

                                    if (!empty($billLadinID) && md5($billLadinID) != md5($shop->billLadinID)) {
                                        //update history
                                        $tbHistory = new TbHistory();
                                        $tbHistory->orderID = $currentOrder->orderID;
                                        $tbHistory->userID = Yii::$app->user->id;
                                        $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                                        $tbHistory->content .= 'Cập nhật mã vận đơn: <b>' . $billLadinID . ' đơn hàng ' . $currentOrder->identify . '</b>';
                                        $tbHistory->save(false);

                                        $shop->billLadinID = $billLadinID;
                                    }

                                }
                            }

                            /*if (isset($item['kgFee'])) {//mvd <= 1.5kg
                                $shop->kgFee = $this->setting['kgFee'];
                            } else {
                                $shop->kgFee = 0;
                            }*/

                            if (isset($item['status'])) {
                                $shop->status = $item['status'];
                            }

                            //da bao gia va don hang dang cho xu ly
                            if ($shop->status == 2) {
                                $isBaogia = true;//da bao gia
                            }

                            if ($currentOrder->status == 7 && $isBaogia == false || $currentOrder->totalPaid <= 0) {
                                $shop->status = null;
                            }

                            //da bao gia chua co ma order_number
                            if ($isBaogia || ($currentOrder->totalPaid <= 0 && empty($shop->shopProductID) && !is_null($shop->status))) {
                                $shop->status = 2; //da bao gia
                            }

                            //khi chua co ma order_number va don hang da dat coc
                            if ($currentOrder->totalPaid && empty($shop->shopProductID)) {
                                $shop->status = 0; //dang giao dich
                            }

                            //da co ma order va da dat coc
                            if (!empty($shop->shopProductID) && $currentOrder->totalPaid) {
                                $shop->status = 1;  //co order_number => da thanh toan
                            }
                            //het hang
                            //isStock = 1 => trang thai het hang

                            if (isset($item['isStock'])) {
                                $shop->isStock = (isset($item['isStock']) && $item['isStock']) ? 1 : 0;

                                //update history
                                $tbHistory = new TbHistory();
                                $tbHistory->orderID = $currentOrder->orderID;
                                $tbHistory->userID = Yii::$app->user->id;
                                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                                $tbHistory->content .= 'Cập nhật hủy đơn hàng: <b>' . $currentOrder->identify . '</b>';
                                $tbHistory->save(false);
                            }

                            $shop->status = ($shop->isStock == 1) ? 3 : $shop->status;

                            //kiem tra xem shop co ma van don chua
                            /*if($shop->shippingStatus === 0 && $modelTransfer = TbTransfercode::findOne(['orderID'=>$currentOrder->orderID,'shopID'=>$shop->id])){
                                $shop->shippingStatus = 4;//Trang thai dang tren duong
                            }*/
                            $shop->save(false);
                        }
                    }
                }
            }

            $isBox = isset($post['isBox']) ? 1 : $currentOrder->isBox;


            if ($isBox != $currentOrder->isBox) {
                $str = $isBox == 1 ? 'Đóng gói' : 'Hủy đóng gói';
                //update history
                $tbHistory = new TbHistory();
                $tbHistory->orderID = $currentOrder->orderID;
                $tbHistory->userID = Yii::$app->user->id;
                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                $tbHistory->content .= 'Cập nhật <b>' . $str . ' cho đơn hàng ' . $currentOrder->identify . '</b>';
                $tbHistory->save(false);

                $currentOrder->isBox = $isBox;
            }

            $isCheck = isset($post['isCheck']) ? 1 : $currentOrder->isCheck;

            if ($isCheck != $currentOrder->isCheck) {
                $str = ($isCheck == 1) ? 'Kiểm mã' : 'Hủy kiểm mã';
                //update history
                $tbHistory = new TbHistory();
                $tbHistory->orderID = $currentOrder->orderID;
                $tbHistory->userID = Yii::$app->user->id;
                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
                $tbHistory->content .= 'Cập nhật <b>' . $str . ' cho đơn hàng ' . $currentOrder->identify . '</b>';
                $tbHistory->save(false);

                $currentOrder->isCheck = $isCheck;
            }

            if (isset($post['noteOrder'])) {
                $currentOrder->noteOrder = $post['noteOrder'];
            }


            //cap nhat lai du lieu cho don hang
            CommonLib::updateDataOrders($currentOrder);
            //update history
            /*  $tbHistory = new TbHistory();
              $tbHistory->orderID = $currentOrder->orderID;
              $tbHistory->userID = Yii::$app->user->id;
              $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
              $tbHistory->content .= 'Cập nhật đơn hàng: <b>' . $currentOrder->identify . '</b>';
              $tbHistory->save(false);*/

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                return ['status' => 1, 'url' => Url::toRoute(['orders/view', 'id' => $post['TbOrderSupplier']['orderID'], '#' => $supID])];
            } else {
                $this->flash('success', 'Cập nhật thành công');

                return $this->redirect(['orders/view', 'id' => $orderID]);
            }
        }

        return false;
    }

    protected function findModel($id)
    {
        if (($model = TbOrders::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionBooking($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if (\Yii::$app->request->isAjax) {
            if ($order = TbOrders::findOne($id)) {
                $order->status = 3;
                $order->shipDate = date('Y-m-d H:i:s');//ngay dat hang
                $order->save(false);
            }


            return ['alert' => 'Cập nhật thành công.'];
        }
        return ['alert' => 'Cập nhật thất bại'];
    }

    public function actionExportExcel()
    {
//        error_reporting(E_ALL);
//        ini_set('display_errors', TRUE);
//        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Asia/Ho_Chi_Minh');


        $query = new \yii\db\Query;
        $query->select('d.id,o.orderID,d.`quantity`, d.unitPrice,d.totalPrice,d.unitPriceVn,d.totalPriceVn,d.image,d.color,d.size,d.noteProduct,d.quantity,p.name AS title,p.link,d.status,t.`transferID`,t.`createDate`,t.`note`')
            ->from('tb_orders o')
            ->leftJoin('tb_orders_detail d', 'o.orderID = d.orderID')
            ->leftJoin('tb_product p', 'd.productID = p.productID')
            ->leftJoin('tb_transfercode t', 'o.orderID = t.orderID')
            ->where(['o.status' => 4]);

        $data =  $query->createCommand()->queryAll();
        $dataItem = [];
        if(!empty($data)){
            foreach ($data as $item){
                $dataItem[$item['transferID']][] = $item;
            }
        }



        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        $sheet->setTitle('DS');
        $sheet->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getDefaultStyle()->getFont()->setSize(13);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);


        $styleArray = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0C0C0')
            ),
        );
        $sheet->getStyle('A1:H1')->applyFromArray($styleArray);


        $sheet->setCellValue('A1', 'STT');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('B1', 'Ngày tháng năm');
        $sheet->getStyle('B1')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('C1', 'Mã vận đơn');
        $sheet->getStyle('C1')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('D1', 'Tên sản phẩm');
        $sheet->getStyle('D1')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('E1', 'Số lượng');
        $sheet->getStyle('E1')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('F1', 'Đơn giá (CNY)');
        $sheet->getStyle('F1')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('G1', 'Thành tiền');
        $sheet->getStyle('G1')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('H1', 'Ghi chú');
        $sheet->getStyle('H1')->applyFromArray(['font' => ['bold' => true]]);


        $row = 2; $i= 0;
        foreach ($dataItem as $items) {
            foreach ($items as $item) {
                $i++;

//                     $sheet->setCellValue('A'.$row, $i);
//                    $objPHPExcel->getActiveSheet()->getStyle('A'.$row);
//                    $sheet->setCellValue('B'.$row, date('d/m/Y',strtotime($item['createDate'])));
//                    $objPHPExcel->getActiveSheet()->getStyle('B'.$row);
//                    $sheet->setCellValue('C'.$row, $item['transferID']);
//                    $objPHPExcel->getActiveSheet()->getStyle('C'.$row);
//                    $sheet->setCellValue('D'.$row, $item['title']);
//                    $objPHPExcel->getActiveSheet()->getStyle('D'.$row);
//                    $sheet->setCellValue('E'.$row, $item['quantity']);
//                    $objPHPExcel->getActiveSheet()->getStyle('E'.$row);
//                    $sheet->setCellValue('F'.$row, $item['unitPrice']);
//                    $objPHPExcel->getActiveSheet()->getStyle('F'.$row);
//                    $sheet->setCellValue('G'.$row, $item['totalPrice']);
//                    $objPHPExcel->getActiveSheet()->getStyle('G'.$row);
//                    $sheet->setCellValue('H'.$row, $item['note']);
//                    $objPHPExcel->getActiveSheet()->getStyle('H'.$row);



                $col = 'A';
                $cell = $col . $row;

                $sheet->getStyle($cell)->applyFromArray(['font' => ['bold' => true]]);
                $sheet->setCellValue($cell,  $i);
                $col ++;
                $cell = $col . $row;
                $sheet->setCellValue($cell, date('d/m/Y H:i',strtotime($item['createDate'])));
                $col ++;
                $cell = $col . $row;

                $sheet->setCellValue($cell, $item['transferID']);
                $objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $col ++;
                $cell = $col . $row;

                $sheet->setCellValue($cell, $item['title']);
                $objPHPExcel->getActiveSheet()->getStyle($cell);
                $col ++;
                $cell = $col . $row;

                $sheet->setCellValue($cell, $item['quantity']);
                $col ++;
                $cell = $col . $row;

                $sheet->setCellValue($cell, $item['unitPrice']);
                $objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $col ++;
                $cell = $col . $row;

                $sheet->getStyle($cell)->applyFromArray(['font' => ['bold' => true]]);
                $sheet->setCellValue($cell,$item['totalPrice']);
                $objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $col ++;
                $cell = $col . $row;


                $sheet->setCellValue($cell, $item['note']);
                $objPHPExcel->getActiveSheet()->getStyle($cell);


                $row ++;

                $row ++;

            }

        }

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '262626')
                )
            )
        );
        $sheet->getStyle('A1:H'.$row)->applyFromArray($styleArray);
        $sheet->getStyle('A1:H'.$row)->getAlignment()->setWrapText(true);
        $objPHPExcel->setActiveSheetIndex(0);



        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="khaiquan.xlsx"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;


    }
}