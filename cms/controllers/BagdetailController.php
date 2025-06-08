<?php

namespace cms\controllers;

use common\models\Bag;
use Yii;
use common\models\BagDetail;
use common\models\BagDetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BagdetailController implements the CRUD actions for BagDetail model.
 */
class BagdetailController extends Controller
{


    /**
     * Lists all BagDetail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BagDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BagDetail model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BagDetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BagDetail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing BagDetail model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing BagDetail model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
       $bagDetail = $this->findModel($id);
       if($bagDetail->bagID){
           $bag = Bag::findOne($bagDetail->bagID);
           if($bag && ($bag->userID == Yii::$app->user->id)){ //neu dung nguoi tao thi duoc xoa
               //update history
               $tbHistory = new TbHistory();
               $tbHistory->orderID = '';
               $tbHistory->userID = Yii::$app->user->id;
               $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>';
               $tbHistory->content .= 'Đã xóa : <b>' . $bagDetail->barcode . '</b> khỏi bao <b>B-'.$bag->id.'</b>';
               $tbHistory->save(false);

               $bagDetail->delete();//xoa khoi bao
           }
       }

        return $this->redirect(['index']);
    }

    /**
     * Finds the BagDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return BagDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BagDetail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
