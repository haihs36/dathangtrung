<?php

namespace cms\controllers;

use cms\models\ConsignmentDetail;
use common\components\CommonLib;
use common\helpers\Image;
use common\models\TbCustomers;
use Yii;
use cms\models\Consignment;
use cms\models\ConsignmentSearch;
use common\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ConsignmentController implements the CRUD actions for Consignment model.
 */
class ConsignmentController extends Controller
{

    /**
     * Lists all Consignment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ConsignmentSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        //$users = CommonLib::getallAdmin();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
          //  'users' => $users,
            'params' => $params,
        ]);
    }

    /**
     * Displays a single Consignment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $data = Consignment::find()
            ->select('a.*,b.username as customerName,b.id as customerID,b.discountKg,b.phone,c.username')
            ->from(Consignment::tableName() . ' a')
            ->innerJoin(TbCustomers::tableName() . ' b', 'a.customerID = b.id')
            ->innerJoin(\common\models\User::tableName() . ' c', 'a.userID = c.id')
            ->where(['a.id'=>$id])->asArray()->one();

        $detail = ConsignmentDetail::find()->where(['consignID' =>$id])->asArray()->all();
        $detail = $this->renderPartial('_print', ['data' => $detail,'discountKg'=>$data['discountKg']]);

        return $this->render('view', [
            'data' => $data,
            'detail' => $detail,
            'setting' => $this->setting
        ]);
    }

    public function actionPrint(){
        if (\Yii::$app->request->isAjax) {
            $id = (int)\Yii::$app->request->get('id');

        }
        $data = Consignment::find()
            ->select('a.*,b.username as customerName,b.id as customerID,b.discountKg,b.phone,c.username')
            ->from(Consignment::tableName() . ' a')
            ->innerJoin(TbCustomers::tableName() . ' b', 'a.customerID = b.id')
            ->innerJoin(\common\models\User::tableName() . ' c', 'a.userID = c.id')
            ->where(['a.id'=>$id])->asArray()->one();

        $detail = ConsignmentDetail::find()->where(['consignID' =>$id])->asArray()->all();
        $detail = $this->renderPartial('_print', ['data' => $detail,'discountKg'=>$data['discountKg']]);

        $res =  $this->renderPartial('view', [
            'data' => $data,
            'detail' => $detail,
            'setting' => $this->setting,
            'print' => true
        ]);


        $title = 'Phiếu trả hàng ký gửi, Số phiếu: ' .$data['customerName'].'-'.$data['id'].'-'.date('dmY',strtotime($data['create']));
        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['data' => isset($res) ? $res : '', 'title' => $title];
    }
    /**
     * Creates a new Consignment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->db->createCommand('select 1 from '.Consignment::tableName().' order by id DESC')->execute();
        $id = Yii::$app->db->getLastInsertID();

        $model = new Consignment();
        $model->userID = \Yii::$app->user->id;
        if ($model->load(Yii::$app->request->post())) {
             $model->userID = Yii::$app->user->id;
             $model->save(false);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Consignment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post())) {
            $model->userID = \Yii::$app->user->id;
            if($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Consignment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        if ($kho = $this->findModel($id)) {
            ConsignmentDetail::deleteAll(['consignID' => $id]);//xoa cac mvd thuoc lo
            $kho->delete();
        } else {
            $this->error = 'Xóa phiếu thất bại';
        }

        return $this->formatResponse('Xóa phiếu thành công');
    }

    /**
     * Finds the Consignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Consignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Consignment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionUpload()
    {
        $success = null;

        $identify = (int)Yii::$app->request->post('identify');
        $model = Consignment::findOne($identify);
        if ($model) {
            $model->images = UploadedFile::getInstance($model, 'images');
            if ($model->images && $model->validate(['images'])) {
                $fileName = $model->images->size . '-' . CommonLib::getRandomInt(5);
                $model->images = Image::upload($model->images, $this->upload_image, null, null, false, $fileName);
                if ($model->images) {
                    if ($model->save(false)) {
                        $success = [
                            'message' => 'Photo uploaded',
                            'photo' => $model->primaryKey
                        ];
                    } else {
                        @unlink(\Yii::getAlias('@upload_dir') . str_replace(Url::base(true), '', $model->images));
                        $this->error = 'Upload error';
                    }
                } else {
                    $this->error = 'File upload error. Check uploads folder for write permissions';
                }

            }else{
                $this->error = 'File is incorrect';
            }
        }else{
            $this->error = 'Item not found';
        }

        return $this->formatResponse($success);
    }
}
