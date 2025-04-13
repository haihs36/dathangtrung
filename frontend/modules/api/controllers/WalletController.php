<?php

    namespace frontend\modules\api\controllers;

    use common\models\TbAccountBanking;
    use common\models\TbAccountTransaction;
    use common\models\TbCustomers;
    use frontend\modules\api\resources\WalletResource;
    use Yii;

    use yii\base\Exception;
    use yii\rest\Controller;


    class WalletController extends Controller
    {

        public $modelClass = WalletResource::class;

        const LIMIT = 10;

        public function behaviors()
        {
            $behaviors = parent::behaviors();

            $behaviors['verbs'] = [
                'class'   => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'update' => ['post'],
                ],
            ];

            return $behaviors;
        }


        //nap vi
        public function actionUpdate()
        {
            $success = false;
            $message = 'Giao dịch thất bại';

            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();

                if (isset($post['content']) && !empty($post['content'])) {
                    $data = $post['content'];
                    $monney = isset($data['money']) ? doubleval($data['money']) : 0;
                    $account = isset($data['account']) ? trim(strip_tags($data['account'])) : '';
                    $bankName = isset($data['bank']) ? trim(strip_tags($data['bank'])) : ''; //ten ngan hang
                    $content = 'Nạp ví tự động từ ngân hàng: '.$bankName;
                    $account = str_replace(' ','',$account);
                    $customer_id = ltrim($account, 'A');
                    $customer_id = str_replace(' ','',$customer_id);
                    $customer_id = trim($customer_id);
                    $customer_id = is_numeric($customer_id) ? (int)$customer_id : 0;

                    try {
                        if (!empty($bankName) && $monney > 0 && $customer_id > 0) {
                            $customer = TbCustomers::findOne($customer_id);
                            if($customer){
                                //kiem tra xem khach hang co vi hay khong
                                $bankAccount = TbAccountBanking::findOne(['customerID' => $customer_id]);
                                if ($bankAccount) {
                                    $bankAccount->totalMoney += $monney; //cong tong so tien da nap
                                    $bankAccount->totalResidual += $monney; //so tien nap
                                    $bankAccount->create_date = date('Y-m-d H:i:s');
                                    $bankAccount->note = $content; //noi dung

                                    if ($bankAccount->save(false)) {
                                        /*insert lich su giao dich*/
                                        $mdlTransaction = new TbAccountTransaction();
                                        $mdlTransaction->type = 1; //nap tien
                                        $mdlTransaction->action = 2; //nap tu dong ck
                                        $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                                        $mdlTransaction->customerID = $bankAccount->customerID;
                                        $mdlTransaction->sapo = !empty($bankAccount->note) ? $bankAccount->note : 'Nạp tiền vào tài khoản';
                                        $mdlTransaction->value = $monney;//so tien nap
                                        $mdlTransaction->accountID = $bankAccount->id;//ma tai khoan
                                        $mdlTransaction->balance = $bankAccount->totalResidual;//số dư
                                        $mdlTransaction->bank = $bankName;
                                        $mdlTransaction->create_date = date('Y-m-d H:i:s');
                                        $mdlTransaction->save(false);

                                        $success = true;
                                        Yii::$app->response->statusCode = 200;
                                        return ['success' => $success, 'message' => 'Giao dịch thành công. Đã nạp vào tài khoản '.$account.', số tiền: '.number_format($monney)];
                                    }
                                }else{
                                    //chua co vi thi tao moi
                                    $bankAccount = new TbAccountBanking();
                                    $bankAccount->totalResidual = $monney; //so du tai khoan khoi tao
                                    $bankAccount->totalMoney = $monney;
                                    $bankAccount->customerID = $customer_id;
                                    $bankAccount->create_date = date('Y-m-d H:i:s');
                                    $bankAccount->note = $content; //noi dung

                                    if ($bankAccount->save(false)) {
                                        /*insert lich su giao dich*/
                                        $mdlTransaction = new TbAccountTransaction();
                                        $mdlTransaction->type = 1; //nap tien
                                        $mdlTransaction->action = 2; //nap tu dong ck
                                        $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                                        $mdlTransaction->customerID = $bankAccount->customerID;
                                        $mdlTransaction->sapo = !empty($bankAccount->note) ? $bankAccount->note : 'Nạp tiền vào tài khoản';
                                        $mdlTransaction->value = $monney;//so tien nap
                                        $mdlTransaction->accountID = $bankAccount->id;//ma tai khoan
                                        $mdlTransaction->balance = $bankAccount->totalResidual;//số dư
                                        $mdlTransaction->bank = $bankName;
                                        $mdlTransaction->create_date = date('Y-m-d H:i:s');
                                        $mdlTransaction->save(false);

                                        $success = true;
                                        Yii::$app->response->statusCode = 200;
                                        return ['success' => $success, 'message' => 'Giao dịch thành công. Đã nạp vào tài khoản '.$account.', số tiền: '.number_format($monney)];
                                    }
                                }
                            }
                        }

                        $message = 'Giao dịch thất bại. Mã khách hàng ' . $account. ' không tồn tại';
                    }catch (Exception $e){
                        $message = $e->getMessage();
                    }
                }
            }

            Yii::$app->response->statusCode = 422;
            return ['success' => $success, 'message' => $message];

        }

    }
