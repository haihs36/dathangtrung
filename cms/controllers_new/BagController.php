<?php

namespace cms\controllers;

use common\models\BagDetail;
use common\models\TbOrders;
use common\models\TbTransfercode;
use common\models\User;
use Yii;
use common\models\Bag;
use common\models\BagSearch;
use common\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BagController implements the CRUD actions for Bag model.
 */
class BagController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Bag models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $bag_ids = [];
        if($dataProvider->count){
            foreach ($dataProvider->getModels() as $items){
                $bag_ids[] = $items['id'];
            }
        }

        $bags = [];
        if(!empty($bag_ids)){
            $bagDetail = BagDetail::find()->where(['bagID' =>$bag_ids])->asArray()->all();
            if($bagDetail){
                foreach ($bagDetail as $item){
                    $bags[$item['bagID']][] = $item;
                }
            }
        }

        $users = User::find()->asArray()->all();
        $listUser = [];
        if(!empty($users)){
            foreach ($users as $item){
                $listUser[$item['id']] = $item;
            }
        }

        return $this->render('index', [
            'bags' => $bags,
            'listUser' => $listUser,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
    Hien thi expand-detail-row
    * */
    public function actionDetail()
    {
        if (isset($_POST['expandRowKey'])) {
            $id = (int)$_POST['expandRowKey'];
            $bag = Bag::findOne(['id' => $id]);
            if (!$bag) {
                return false;
            }

            $dataDetail = BagDetail::getBagDetailByBagId($id);
            return $this->renderPartial('@app/views/ajax/bag-info', ['data' => $dataDetail]);

        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }


    /**
     * Displays a single Bag model.
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
     * Creates a new Bag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Bag();

        if ($model->load(Yii::$app->request->post())) {
            $model->userID = Yii::$app->user->id;
            if($model->save()) {
                return $this->redirect(['bag/update','id'=>$model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Bag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->userID = Yii::$app->user->id;
            if($model->save(false)) {
                if($model->status == 2){
                    //update all barcode in bage to success
                    BagDetail::updateAll(['status'=>2],['bagID'=>$model->id]);
                }
                return $this->redirect(['index']);
            }
        }

        $dataDetail = BagDetail::getBagDetailByBagId($id);
        $total_kg = 0;
        if($dataDetail){
            foreach ($dataDetail as $item){
                $total_kg += $item['kgPay'];
            }
        }
        $data = $this->renderPartial('@app/views/ajax/bag-info', ['data' => $dataDetail]);

        return $this->render('update', [
            'model' => $model,
            'list_barcode' => $data,
            'total_kg' => $total_kg,
        ]);

    }

    /**
     * Deletes an existing Bag model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Bag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Bag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bag::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
