<?php

namespace cms\controllers;

use common\components\CommonLib;
use common\models\AccessRule;
use common\models\TbAccountBanking;
use common\models\User;
use common\models\TbCustomers;
use Yii;
use common\models\TbAccountTransaction;
use common\models\TbAccountTransactionSearch;
use common\components\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * AccountTransactionController implements the CRUD actions for TbAccountTransaction model.
 */
class AccountTransactionController extends Controller
{
    public function behaviors()
    {

        return [
            'access' => [
                'class' => AccessControl::className(),
                // We will override the default rule config with the new AccessRule class
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules'      => [
                        [
                            'actions' => ['index'],
                            'allow'   => true,
                            'roles'   => [
                                BUSINESS,
                                STAFFS,
                                WAREHOUSE,
                                WAREHOUSETQ
                            ],
                        ],
                        [
                            'actions' => ['index', 'view'],
                            'allow'   => true,
                            'roles'   => [CLERK]

                        ],
                        [
                            'actions' => ['index', 'view', 'create', 'update', 'delete'],
                            'allow'   => true,
                            'roles'   =>[ADMIN]
                        ],
                    ],
            ],
        ];
    }

    public function actionExport(){
        ini_set("memory_limit","2048M");

        $searchModel = new TbAccountTransactionSearch();
        $params = Yii::$app->request->queryParams;
        $user = \Yii::$app->user->identity;
        if ($user->role !== ADMIN) {
            $searchModel->userID = $user->id;
        }

        if(!isset($params['TbAccountTransactionSearch']['startDate']) || !isset($params['TbAccountTransactionSearch']['endDate'])){
            $startDate = date('d-m-Y', strtotime("-1 months", strtotime("NOW")));
            $endDate = date('d-m-Y', strtotime("NOW"));
            $params['TbAccountTransactionSearch']['startDate'] = $startDate;
            $params['TbAccountTransactionSearch']['endDate'] = $endDate;
        }

        $dataProvider = $searchModel->search($params,1);

        return $this->renderPartial('export', [
            'data' => $dataProvider->getModels(),
        ]);

    }

    /**
     * Lists all TbAccountTransaction models.
     * @return mixed
     */
    public function actionIndex()
    { //moi
        $searchModel = new TbAccountTransactionSearch();
        $params = Yii::$app->request->queryParams;
        $user = \Yii::$app->user->identity;
        if ($user->role !== ADMIN) {
            $searchModel->userID = $user->id;
        }

        $dataProvider = $searchModel->search($params);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'params' => $params,
        ]);
    }

    /**
     * Displays a single TbAccountTransaction model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                /*
                 * cap nhat giao dich rut tien
                 * trang thai = 2 va loai gd = rut tien
                 * */
                //cap nhat lai so tai khoan
                $bank = TbAccountBanking::findOne($model->accountID);
                if ($bank) {
                    if ($model->type == 2 && $model->status == 2) {
                        if ($bank->totalResidual < $model->value) {
                            $this->flash('error', 'Số tiền giao dịch không hợp lệ');
                            return $this->redirect(['index', 'customerID' => $model->customerID]);
                        }

                        $bank->totalResidual -= $model->value; //so du
                        $bank->totalReceived += $model->value; //tong rut
                        $bank->totalRefund += $model->value; // tong hoan tra
                        //cap nhat lai tk bank
                        if ($bank->save()) {
                            //cap nhat lai bang giao dich
                            $model->balance = $bank->totalResidual;
                            if ($model->save()) {
                                $this->flash('success', 'Cập nhật thành công');
                                return $this->redirect(['index', 'customerID' => $model->customerID]);
                            }
                        }
                    } else {
                        if ($model->save()) {
                            $this->flash('success', 'Cập nhật thành công');
                            return $this->redirect(['index', 'customerID' => $model->customerID]);
                        }
                    }

                } else {
                    $this->flash('success', 'Cập nhật thất bại. Tài khoản trên hệ thống của bạn không tồn tại.');
                }


                return $this->refresh();
            }
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new TbAccountTransaction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TbAccountTransaction();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TbAccountTransaction model.
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
     * Deletes an existing TbAccountTransaction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    // public function actionDelete($id)
    // {
    //     if (($model = $this->findModel($id))) {
    //         $model->delete();
    //     } else {
    //         $this->error = 'Not found';
    //     }
    //     return $this->formatResponse('Lịch xử đã được xóa');
    // }

    /**
     * Finds the TbAccountTransaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TbAccountTransaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbAccountTransaction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
