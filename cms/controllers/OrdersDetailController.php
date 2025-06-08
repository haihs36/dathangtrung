<?php

namespace cms\controllers;

use common\components\CommonLib;
use common\helpers\Image;
use common\models\AccessRule;
use common\models\Photo;
use common\models\TbComplain;
use common\models\TbComplainReply;
use common\models\TbHistory;
use common\models\TbOrders;
use common\models\TbOrderSupplier;
use common\models\TbProduct;
use common\models\TbProductComplain;
use Yii;
use common\models\TbOrdersDetail;
use common\models\TbOrdersDetailSearch;
use common\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * OrdersDetailController implements the CRUD actions for TbOrdersDetail model.
 */
class OrdersDetailController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class'      => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only'       => ['index', 'view', 'delete', 'approve', 'approval', 'approved', 'process', 'costs', 'update', 'removeitem'],
                'rules'      => [
                    [
                        'allow'   => false,
                        'roles'   => [WAREHOUSE,WAREHOUSETQ,BUSINESS],
                    ],
                    [
                        'actions' => ['index', 'view', 'update'],
                        'allow'   => true,
                        'roles'   => [STAFFS]
                    ],
                    [
                        'actions' => ['index', 'view', 'approval', 'delete', 'approve', 'approved', 'process', 'costs', 'update', 'removeitem'],
                        'allow'   => true,
                        'roles'   => [ADMIN]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TbOrdersDetail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TbOrdersDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TbOrdersDetail model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TbOrdersDetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TbOrdersDetail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TbOrdersDetail model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);       
        $mdlProduct = TbProduct::findOne($model->productID);

        if ($model->load(Yii::$app->request->post()) && $mdlProduct->load(Yii::$app->request->post())) {

          //  pr(Yii::$app->request->post());
          //  pr($model->attributes);
            //pr($mdlProduct->attributes);
          //  die;

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                /*if (isset($_FILES)) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if ($model->image && $model->validate(['image'])) {
                        $fileName = $id . '-' . CommonLib::getRandomInt(10);
                        $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                    } else {
                        $model->image = $model->oldAttributes['image'];
                    }
                }*/
                $pname = $mdlProduct->oldAttributes['name'];

                $str = '';

                if (md5($mdlProduct->name) != md5($mdlProduct->oldAttributes['name'])) {
                     //update history
                    $str .= ' Thay đổi tên sản phẩm thành <b>'.$mdlProduct->name.'</b> <br>';

                }

               
                if ($model->image != $model->oldAttributes['image']) {
                    $model->image = (!empty($model->image)) ? $model->image : $model->oldAttributes['image'];
                     //update history
                    $str .=' Thay đổi hình ảnh sản phẩm  <br>';

                }

                 if ($model->size != $model->oldAttributes['size']) {
                     //update history
                    $str .=' Thay đổi kích thước sản phẩm thành <b>'.$model->size.'</b> <br>';

                }  

                 if ($model->color != $model->oldAttributes['color']) {
                     //update history
                    $str .=' Thay đổi màu sắc thành <b>'.$model->color.'</b> <br>';

                }   

                 if ($model->quantity != $model->oldAttributes['quantity']) {
                     //update history
                    $str .=' Thay đổi số lượng thành <b>'.$model->quantity.'</b> <br>';

                }    

                 if ($model->unitPrice != $model->oldAttributes['unitPrice']) {
                     //update history
                    $str .=' Thay đổi tiền TQ thành <b>'.$model->unitPrice.'</b> <br>';

                }      


                $model->totalPrice = $model->quantity*$model->unitPrice; //tien tq
                $model->unitPriceVn = round($model->unitPrice*$model->order->cny); //vien viet
                $model->totalPriceVn = $model->quantity*$model->unitPriceVn;

                if($model->save(false) && $mdlProduct->save(false)){
                    $tbOrder = $model->order;
                    $tbHistory          = new TbHistory();
                    $tbHistory->orderID = $model->orderID;
                    $tbHistory->userID  = Yii::$app->user->id;
                    $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>Đã chỉnh sửa sản phẩm : <b>' . $pname . '</b><br/>';
                    $tbHistory->content .= $str;
                    $tbHistory->content .= '<br/>Đơn hàng: <b><a target="_blank" title="Chi tiết đơn hàng" href="'.Url::toRoute(['orders/view','id'=>$model->orderID]).'"><i class="fa fa-eye" aria-hidden="true"></i>'.$tbOrder->identify.'</a></b>';
                    $tbHistory->save(false);

                    $customer = $tbOrder->customer;
                    $cnys = isset($customer->cny) ? $customer->cny : '';
                    $cny = CommonLib::getCNY($this->setting['CNY'],$cnys,$tbOrder->cny);
                    $cny = CommonLib::toInt($cny);  //Tỉ giá áp dụng cho đơn hàng

                    $tbOrder->cny = $cny;
                    //% phi giao dich ap dung cho don hang => bo ko tinh cho shop ma tinh cho don hang
                    if (CommonLib::updateOrder($tbOrder)) {
//                        $this->flash('success', 'Cập nhật thành công');
                        return $this->redirect(['orders/view','id'=>$model->orderID, '#' => 'w1']);
                    }

                  
                }

            }
        }

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     hien tai dang ko cho xoa
     */
    public function actionDelete($id)
    {
        $orderSupplierID = (int)Yii::$app->request->get('orderSupplierID');
        $currentDetail = $this->findModel($id);
        $tbOrder    = $currentDetail->order;
        if($currentDetail) {
            $totalRows = TbOrdersDetail::find()->where(['orderID' => $tbOrder->orderID])->count();
            if ($totalRows > 1) {
                //kiem tra xem co san pham nao cung shop khong
                $totalProductInShop = TbOrdersDetail::find()->where(['orderSupplierID' => $currentDetail->orderSupplierID])->count();
                if($totalProductInShop == 1){
                    //xoa luon shop trong bang tb_order_supplier
                     TbOrderSupplier::findOne(['id' => $currentDetail->orderSupplierID])->delete();
                }

                $currentDetail->delete();


                if (CommonLib::updateOrder($tbOrder)) { //cap nhat lai don hang
                    $this->flash('success', 'Cập nhật thành công');
                }

                $mdlProduct = TbProduct::findOne($currentDetail->productID);
                $tbHistory          = new TbHistory();
                $tbHistory->orderID = $tbOrder->orderID;
                $tbHistory->userID  = Yii::$app->user->id;
                $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>Đã xóa sản phẩm : <b>' . $mdlProduct->name . '</b><br/>';
                $tbHistory->content .= 'Khỏi shop: <b>' . $mdlProduct->shop->shopName . '</b>';
                $tbHistory->content .= '<br/>Đơn hàng: <b><a target="_blank" title="Chi tiết đơn hàng" href="' . Url::toRoute(['orders/view', 'id' => $tbOrder->orderID]) . '"><i class="fa fa-eye" aria-hidden="true"></i>' . $tbOrder->identify . '</a></b>';
                $tbHistory->save();

            }else{
                TbOrderSupplier::findOne(['id' => $orderSupplierID])->delete();
                TbComplainReply::deleteAll(['orderID' => $tbOrder->orderID]);
                $TbComplain = TbComplain::findAll(['orderID' => $tbOrder->orderID]);
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
                TbComplain::deleteAll(['orderID' => $tbOrder->orderID]);
                TbOrdersDetail::deleteAll(['orderID' => $tbOrder->orderID]);
                TbOrderSupplier::deleteAll(['orderID' => $tbOrder->orderID]);
                $tbOrder->delete();
                return $this->redirect(['orders/index']);
            }
        }

        return $this->redirect(['orders/view','id'=>$tbOrder->orderID]);

    }

    /* clearn image
 * */
    public function actionClearImage($id)
    {
        $model = $this->findModel($id);

        if ($model === null) {
            $this->flash('error', 'Not found');
        } else {
            $model->image = '';
            if ($model->update()) {
                @unlink(Yii::getAlias('@upload_dir') . $model->image);
                $this->flash('success', 'Image cleared');
            } else {
                $this->flash('error', 'Update error.');
            }
        }
        return $this->back();
    }
    /**
     * Finds the TbOrdersDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TbOrdersDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbOrdersDetail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
