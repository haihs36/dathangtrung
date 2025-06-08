<?php

namespace cms\controllers;

use common\models\AccessRule;
use common\models\TbShippers;
use common\models\TbShippersSearch;
use Yii;

use common\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\CommonLib;
use yii\web\UploadedFile;
use common\helpers\Image;

/**
 * ShipperController implements the CRUD actions for Tbshippers model.
 */
class ShipperController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'delete', 'view', 'update', 'create'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'update'],
                        'allow' => true,
                        'roles' => [WAREHOUSE,WAREHOUSETQ],
                    ],
                    [
                        'actions' => ['index', 'delete', 'view', 'update', 'create'],
                        'allow' => true,
                        'roles' => [ADMIN],
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => [BUSINESS]
                    ],
                    // [
                    //     'actions' => ['index', 'view'],
                    //     'allow' => true,
                    //     'roles' => [WAREHOUSETQ]
                    // ],
                ],
            ],
        ];
    }



    /**
     * Lists all Tbshippers models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new TbShippersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //pr($dataProvider->getModels());die;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tbshippers model.
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
     * Creates a new Tbshippers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TbShippers();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                if ($model->save()) {
                    return $this->redirect(['shipper/index']);
                }
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Tbshippers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {
            if (isset($_FILES)) {
                $fileName = $model->shippingCode . CommonLib::getRandomInt(5);
                $model->image = UploadedFile::getInstance($model, 'image');
                if (!empty($model->image) && $model->validate(['image'])) {
                    $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                }
            }
            if(empty($model->image)) {
                $model->image = $model->oldAttributes['image'];
            }

            if(!empty($model->price)){
                $model->price = CommonLib::toInt($model->price);
            }

            if($model->save()){
               // return $this->redirect(['index']);
            }

        }

       
        return $this->render('update', [
            'model' => $model,
        ]);
    
    }

    /**
     * Deletes an existing Tbshippers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
     * Finds the Tbshippers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tbshippers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbShippers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionExportExcel(){
        ini_set("memory_limit","2048M");

        $searchModel = new TbShippersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,1000);



        return $this->renderPartial('exportexcel', [
            'data' => $dataProvider->getModels(),
        ]);


    }
}
