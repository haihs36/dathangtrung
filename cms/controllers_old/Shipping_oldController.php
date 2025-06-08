<?php

namespace cms\controllers;

use common\components\CommonLib;
use common\helpers\Image;
use common\helpers\Upload;
use common\models\Custommer;
use common\models\ImportExcel;
use common\models\TbOrderSupplier;
use common\models\TbShippers;
use common\models\TbShippersSearch;
use common\models\TbTransfercode;
use Yii;
use common\models\TbShipping;
use common\models\TbShippingSearch;
use common\components\Controller;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use common\models\AccessRule;
use yii\web\Response;
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
                'class'      => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only'       => ['index', 'view', 'delete','barcode','import'],
                'rules'      => [
                    [
                        'actions' => ['index', 'view', 'delete','barcode','import'],
                        'allow'   => true,
                        'roles'   => [WAREHOUSE,WAREHOUSETQ],
                    ],
                    [
                        'actions' => ['index', 'view', 'delete','barcode','import'],
                        'allow'   => true,
                        'roles'   => [ADMIN]
                    ],
                ],
            ],
        ];
    }


    /**
     * Lists all TbShipping models.
     * @return mixed
     */

    public function actionUpdate(){
        //update shipping
        CommonLib::updateOrderIDToShipping();
        echo 'shipping ok <b/>';
        CommonLib::updateOrderIDToTranfercode();
        echo 'Tranfercode ok <b/>';die;

    }


    public function actionBarcode()
    {
        $searchModel = new TbShippingSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params, 0);//type = 0
        //Shipper
        $searchModelShipper = new TbShippersSearch();
        $dataProviderShipper = $searchModelShipper->search($params);

        return $this->render('barcode', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'searchModelShipper' => $searchModelShipper, 'dataProviderShipper' => $dataProviderShipper, 'params' => $params,]);
    }


    public function actionImport()
    {
        $model = new ImportExcel();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->validate()) {

                $user = Yii::$app->user->identity;
                if ($user->role == ADMIN || $user->role == WAREHOUSE || $user->role == WAREHOUSETQ) {

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
//                            CommonLib::pr($rowData);
                        $shippingCode = $rowData[0][0];
                        if (!empty($shippingCode)) {
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

                        }
                    }

                    return ['rs' => 'success', 'status' => 0, 'mess' => 'Mã vận đơn đã được cập nhật vào hệ thống!'];
                }
            }else{
                return ['rs' => 'error', 'status' => 1, 'mess' => ActiveForm::validate($model)];
            }
        } if(Yii::$app->request->isAjax) {
            return $this->renderAjax('_import', [
                'model' => $model
            ]);
        }

        return $this->render('_import',['model'=>$model]);

    }

    //import don ky gui
    public function actionImportactive(){
        $model = new ImportExcel();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->validate()) {
                $user = Yii::$app->user->identity;
                if ($user->role == ADMIN || $user->role == WAREHOUSE || $user->role == WAREHOUSETQ) {

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
                            if($cUser = Custommer::findOne(['username'=>$username])){
                                //kiem tra neu chua co ma van don thi insert
                                if($shipperExits = TbShippers::findOne(['shippingCode' => $shippingCode])){
                                    $shipperExits->shippingStatus = 3; //kho vn                                   
                                    $shipperExits->save(false);
                                }else{
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
            }else{
                return ['rs' => 'error', 'status' => 1, 'mess' => ActiveForm::validate($model)];
            }
        } if(Yii::$app->request->isAjax) {
            return $this->renderAjax('_import', [
                'model' => $model
            ]);
        }

        return $this->render('_import',['model'=>$model]);
    }


    public function actionIndex()
    {
        $searchModel = new TbShippingSearch();
        $params = Yii::$app->request->queryParams;
        if ($searchModel->load(Yii::$app->request->get())) {
            $params['keywords'] = $searchModel->keywords;
        }
        $dataProvider = $searchModel->search($params, 1); //search
        $count = $dataProvider->getCount();
        if($count <= 0){
            $this->flash('success','Không tìm thấy mã vận đơn: <b>'.$searchModel->shippingCode.'</b>');
        }

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'params' => $params,]);
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
    protected function findModel($id)
    {
        if (($model = TbShipping::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
