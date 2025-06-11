<?php

namespace cms\controllers;

use common\components\CommonLib;

use common\models\TbAccountTransaction;
use common\models\TbCustomers;
use common\models\TbHistory;
use common\models\TbOrders;
use Yii;
use common\models\TbAccountBanking;
use common\models\TbAccountBankingSearch;
use common\components\Controller;
use yii\filters\AccessControl;
use common\models\AccessRule;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * BankController implements the CRUD actions for TbAccountBanking model.
 */
class BankController extends Controller
{
    public function behaviors()
    {

        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'create', 'update', 'delete','return'],
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => [STAFFS],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete','return'],
                        'allow' => true,
                        'roles' => [ADMIN,CLERK],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TbAccountBanking models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new TbAccountBanking();

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                if (!empty($model->totalMoney) && !empty($model->customerID)) {
                    $type = !empty($_POST['type']) ? $_POST['type'] : 1;
                    if(!empty($model->totalMoney)) {
                        $model->totalMoney = CommonLib::toInt($model->totalMoney);
                        $totalMoney_post = $model->totalMoney;
                    }else{
                        $totalMoney_post = 0;
                    }


                    if ($totalMoney_post) {
                        $account = TbAccountBanking::find()->where(['customerID' => $model->customerID])->one();
                       // $model->create_date = date('Y-m-d H:i:s');
                        $model->edit_date = date('Y-m-d H:i:s');

                        if ($account) { //da co tai khoan
                            if ($type == TbAccountTransaction::TYPE_WITHDRAW) {
                                $account->totalResidual = round($account->totalResidual);

                                //33042685
                                if ($account->totalResidual < $totalMoney_post) {
                                    $this->flash('danger', 'Số tiền rút phải nhỏ hơn số dư tài khoản.');
                                    return $this->refresh();
                                } else {
                                    //cap nhat nap tien vao tai khoan
                                    $account->totalMoney -= $totalMoney_post;
                                    $account->totalResidual -= $totalMoney_post;
                                    $account->edit_date = date('Y-m-d H:i:s');
                                    $account->note = trim($model->note);
                                    $status = $account::updateAll($account->attributes, 'customerID=' . $model->customerID);
                                    if ($status) {
                                        $this->flash('success', 'Giao dịch thành công.');
                                    }
                                    $model = $account;

                                     /*insert lich su giao dich*/
				                        $mdlTransaction = new TbAccountTransaction();
				                        $mdlTransaction->type = $type;
				                        $mdlTransaction->status = 2; //trang thai giao dich thanh cong
				                        $mdlTransaction->customerID = $model->customerID;
				                        $mdlTransaction->userID = Yii::$app->user->id;//nhan vien giao dich
				                        $mdlTransaction->sapo = !empty($model->note) ? $model->note : 'Nạp tiền vào tài khoản';
				                        $mdlTransaction->value = $totalMoney_post;//so tien nap
				                        $mdlTransaction->accountID = $model->id;//ma tai khoan
				                        $mdlTransaction->balance = $model->totalResidual;//số dư
				                        $mdlTransaction->create_date = date('Y-m-d H:i:s');
				                        $mdlTransaction->save();
                                }
                            } else {
                                //cap nhat nap tien vao tai khoan
                                $account->totalMoney += $totalMoney_post;
                                $account->totalResidual += $totalMoney_post;
                                $account->edit_date = date('Y-m-d H:i:s');
                                $account->note = trim($model->note);
                                $status = $account::updateAll($account->attributes, 'customerID=' . $model->customerID);
                                if ($status) {
                                    $this->flash('success', 'Giao dịch thành công.');
                                }
                                $model = $account;

                                 /*insert lich su giao dich*/
			                        $mdlTransaction = new TbAccountTransaction();
			                        $mdlTransaction->type = $type;
			                        $mdlTransaction->status = 2; //trang thai giao dich thanh cong
			                        $mdlTransaction->customerID = $model->customerID;
			                        $mdlTransaction->userID = Yii::$app->user->id;//nhan vien giao dich
			                        $mdlTransaction->sapo = !empty($model->note) ? $model->note : 'Nạp tiền vào tài khoản';
			                        $mdlTransaction->value = $totalMoney_post;//so tien nap
			                        $mdlTransaction->accountID = $model->id;//ma tai khoan
			                        $mdlTransaction->balance = $model->totalResidual;//số dư
			                        $mdlTransaction->create_date = date('Y-m-d H:i:s');
			                        $mdlTransaction->save();
                            }
                        } else {
                            $model->totalResidual = $totalMoney_post; //so du tai khoan khoi tao
                            $model->totalMoney = $totalMoney_post;
                            //tao moi
                            if ($type == TbAccountTransaction::TYPE_WITHDRAW) {
                                $this->flash('danger', 'Tài khoản chưa nạp tiền nên không thể rút tiền.');
                                return $this->refresh();
                            }
                            if ($model->save()) {
                                $this->flash('success', 'Giao dịch thành công.');

                                 /*insert lich su giao dich*/
		                        $mdlTransaction = new TbAccountTransaction();
		                        $mdlTransaction->type = $type;
		                        $mdlTransaction->status = 2; //trang thai giao dich thanh cong
		                        $mdlTransaction->customerID = $model->customerID;
		                        $mdlTransaction->userID = Yii::$app->user->id;//nhan vien giao dich
		                        $mdlTransaction->sapo = !empty($model->note) ? $model->note : 'Nạp tiền vào tài khoản';
		                        $mdlTransaction->value = $totalMoney_post;//so tien nap
		                        $mdlTransaction->accountID = $model->id;//ma tai khoan
		                        $mdlTransaction->balance = $model->totalResidual;//số dư
		                        $mdlTransaction->create_date = date('Y-m-d H:i:s');
		                        $mdlTransaction->save();
                            }
                        }

                       
                    } else {
                        $this->flash('danger', 'Số tiền nạp vào không được nhỏ hơn 0.');
                    }

                } else {
                    $this->flash('danger', 'Nạp tiền vào tài khoản thất bại.');
                }

                return $this->refresh();
                //return $this->redirect(['bank/update', 'id' => $model->id]);
            }
        }

        $searchModel = new TbAccountBankingSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
            'params' => $params
        ]);
    }

    /*
     * hoan lai tien vao vi tien tu
     * */
    public function actionReturn($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //find customer by id
        $customer = TbCustomers::findOne($id);
        if ($customer) {
            $bank = TbAccountBanking::findOne(['customerID' => $id]);
            $post = Yii::$app->request->post();
            if ($post && $bank) {
                $orderID = (double)$post['oid'];
                $price = (double)$post['price'];
               

                $tbOrder = TbOrders::findOne($orderID);
                if ($tbOrder->totalPaid && $tbOrder->totalPaid >= $price) {
                    $tbOrder->totalPaid -= $price;
                    $tbOrder->save(false);
                    
                    $bank->totalResidual += $price; //so du tai khoan
                    $bank->totalRefund += $price; // tong hoan tra so tien
                    //cap nhat lai tk bank
                    if ($bank->save(false)) {
                        //cap nhat lich su giao dich
                        $mdlTransaction = new TbAccountTransaction();
                        $mdlTransaction->type = 6;//trang thai hoan lai tien
                        $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                        $mdlTransaction->customerID = $id;
                        $mdlTransaction->userID = Yii::$app->user->id;//nhan vien giao dich
                        $mdlTransaction->sapo = 'Hoàn lại tiền thanh toán đơn hàng <a target="_blank" href="' . Url::toRoute(['orders/view', 'id' => $tbOrder->orderID]) . '"><b>' . $tbOrder->identify . '</b></a> vào tài khoản';
                        $mdlTransaction->value = $price;//so tien nap
                        $mdlTransaction->accountID = $bank->id;//ma tai khoan
                        $mdlTransaction->balance = $bank->totalResidual;//số dư
                        $mdlTransaction->create_date = date('Y-m-d H:i:s');
                        $mdlTransaction->save(false);

                        return ['sms' => 'Hoàn tiền thành công.', 'status' => 1];
                    }
                }

            }

        }

        return ['sms' => 'Hoàn tiền thất bai.', 'status' => 0];
    }

    /**
     * Displays a single TbAccountBanking model.
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
     * Updates an existing TbAccountBanking model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = TbAccountBanking::findOne($id);
        if ($model) {
            $totalMoney = $model->totalMoney;
            if ($model->load(Yii::$app->request->post())) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                } else {
                    $totalMoney_post = CommonLib::toInt($model->totalMoney);
                    $model->totalResidual += $totalMoney_post;//so du tk
                    $model->totalMoney = $totalMoney + $totalMoney_post;
                    $model->edit_date = date('Y-m-d H:i:s');
                    if ($model->save()) {
                        /*insert lich su giao dich*/
                        $mdlTransaction = new TbAccountTransaction();
                        $mdlTransaction->userID = Yii::$app->user->id;//nhan vien giao dich
                        $mdlTransaction->type = 1;//gd nap tien
                        $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                        $mdlTransaction->customerID = $model->customerID;
                        $mdlTransaction->sapo = !empty($model->note) ? $model->note : 'Nạp tiền vào tài khoản';
                        $mdlTransaction->value = $totalMoney_post;//so tien nap
                        $mdlTransaction->accountID = $model->id;//ma tai khoan
                        $mdlTransaction->balance = $model->totalResidual;//số dư
                        $mdlTransaction->create_date = date('Y-m-d H:i:s');
                        $mdlTransaction->save();

                        $this->flash('success', 'Giao dịch thành công.');
                        return $this->refresh();
                    }
                }
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        } else {
            $this->flash('success', 'Tài khoản chưa có ví điện tử');
            return $this->redirect(['bank/index']);
        }

    }

    /**
     * Deletes an existing TbAccountBanking model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $username = Yii::$app->user->identity->username;
        if(in_array($username,['admin'])) {
            $model = $this->findModel($id);
            TbAccountTransaction::deleteAll(['customerID' => $model->customerID, 'accountID' => $id]);

            $bank = isset($model->customer) ? $model->customer->username : null;
            $tbHistory = new TbHistory();
            $tbHistory->orderID = null;
            $tbHistory->userID = Yii::$app->user->id;
            $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/>Đã xóa ví điện tử: <b>' . $bank . '</b>';
            $tbHistory->save();
            $model->delete();
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the TbAccountBanking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TbAccountBanking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbAccountBanking::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
