<?php

namespace frontend\modules\api\controllers;

use common\components\CommonLib;
use common\models\Custommer;
use common\models\TbAccountTransactionSearch;
use common\models\TbChatMessage;
use common\models\UploadForm;
use frontend\modules\api\models\LoginForm;
use frontend\modules\api\resources\UserResource;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\Controller;
use yii\web\UploadedFile;
use common\models\Province;

class UserController extends Controller
{
    public $modelClass = UserResource::class;

    public function actions()
    {
        $action = parent::actions();
        unset($action['index']);
        unset($action['create']);
        //unset($action['update']);
        unset($action['delete']);
    }

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => HttpBearerAuth::className(),
                    'except' => ['login'],
                ],
                'cors' => [
                    'class' => Cors::class
                ]
            ]
        );
    }

    public function actionLogin()
    {
        if (Yii::$app->request->isPost) {
            $model = new LoginForm();

            if ($model->load(Yii::$app->request->post(), '') && $model->loginApi()) {
                Yii::$app->response->statusCode = 200;

                return [
                    'success' => true,
                    'data' => $model->getUser()
                ];
            }

            $message = $model->errors;
        }

        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            "message" => isset($message) ? $message : 'Xác thực dữ liệu không thành công',
        ];
    }

    public function actionUpload()
    {

        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstanceByName('file');
            if (empty($model->file)) {
                Yii::$app->response->statusCode = 422;
                return [
                    'error' => 1,
                    'message' => 'Must upload at least 1 file in upfile form-data POST'
                ];
            }

            if ($model->file && $model->validate()) {
                $fileName = strtotime(date('Y-m-d H:i:s')) . '-' . $model->file->baseName;
                $path = '/uploads/' . $fileName . '.' . $model->file->extension;
                $target_path = \Yii::getAlias('@upload_dir') . $path;
                $model->file->saveAs($target_path);

                Yii::$app->response->statusCode = 200;
                return [
                    'error' => 0,
                    'path' => '/file' . $path
                ];

            } else {
                $msg = $model->getFirstError('file');
                Yii::$app->response->statusCode = 422;
                return [
                    'error' => 1,
                    'message' => $msg
                ];
            }

        }

        Yii::$app->response->statusCode = 422;
        return [
            'error' => 1,
            'message' => 'bad request'
        ];

    }

    public function actionUpdate()
    {
        $uid = \Yii::$app->user->id;
        if (Yii::$app->request->isPost && $uid) {
            $model = UserResource::findOne($uid);
            $model->scenario = 'editUser';

            if ($model->load(Yii::$app->request->post(), '')) {
                if ($model->save()) {
                    Yii::$app->response->statusCode = 200;
                    return [
                        'success' => true,
                        'data' => $model->findById($uid)
                    ];
                }
            }

            $message = $model->errors;
        }

        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            "message" => isset($message) ? $message : 'Xác thực dữ liệu không thành công',
        ];

    }

    public function actionChangePassword()
    {
        $model = \Yii::$app->user->getIdentity();
        $model->scenario = 'changePassword';
        $uid = \Yii::$app->user->id;
        if ($uid && $model->load(Yii::$app->request->post(), '')) {

            if ($model->validate()) {
                $model->auth_key = Custommer::generateNewAuthKey();
                $model->password_hash = Custommer::setNewPassword($model->password);
                $model->password_reset_token = $model->password;

                if ($model->update()) {
                    Yii::$app->response->statusCode = 200;
                    return [
                        'success' => true,
                        'message' => 'Thay đổi mật khẩu thành công'
                    ];
                }

            } else {
                $message = $model->errors;
            }
        }


        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            "message" => isset($message) ? $message : 'Thay đổi mật khẩu không thành công',
        ];

    }

    const LIMIT = 15;

    public function actionHistoryTransaction()
    {
        $uid = \Yii::$app->user->identity;
        if ($uid) {
            $params = Yii::$app->request->queryParams;
            $searchModel = new TbAccountTransactionSearch();
            $searchModel->customerID = (int)$uid->getId();
            $searchModel->status = 2;
            $searchModel->orderNumber = isset($params['orderNumber']) ? $params['orderNumber'] : '';
            $searchModel->type = isset($params['type']) ? $params['type'] : '';
            $searchModel->startDate = isset($params['startDate']) ? $params['startDate'] : '';
            $searchModel->endDate = isset($params['endDate']) ? $params['endDate'] : '';

            $page = isset($params['page']) ? (int)$params['page'] : 1;
            $offset = ($page - 1) * self::LIMIT;

            $orders = $searchModel->searchHomeApi($params, $offset, self::LIMIT + 1);
            $next_page = false;

            $data = [];
            if (!empty($orders)) {
                foreach ($orders as $val) {
                    $tmp = $val->toArray();
                    $data[] = $tmp;
                }
            }

            if (count($data) > self::LIMIT) {
                unset($data[count($data) - 1]);
                $next_page = true;
            }


            Yii::$app->response->statusCode = 200;
            $accounting = isset($uid->accounting) ? $uid->accounting : [];
            $dataBank = [];
            if ($accounting) {
                $dataBank['recharge_money'] = ($accounting->totalMoney);//tong tien nap
                $dataBank['residual'] = ($accounting->totalResidual);//so du
                $dataBank['received'] = ($accounting->totalReceived);//tong tien rut
                $dataBank['refund'] = ($accounting->totalRefund);//tong hoan
            }

            return [
                'success' => true,
                'total_trans' => $dataBank,
                'next_page' => $next_page,
                "data" => $data
            ];

        }

        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            "message" => 'bad request',
        ];

    }

    public function actionSystemMessages()
    {
        Yii::$app->response->statusCode = 200;
        $setting = CommonLib::getSettingByName(['alert_system']);
        return [
            'sucess' => true,
            'data' => $setting['alert_system']
        ];
    }

    public function actionListMessages()
    {

        if (\Yii::$app->user->isGuest) {
            Yii::$app->response->statusCode = 422;
            return [
                'error' => 1,
                'message' => 'session timeout'
            ];
        } else {
            $customerID = \Yii::$app->user->id;
            $params = Yii::$app->request->queryParams;
            $page = isset($params['page']) ? (int)$params['page'] : 1;
            $status = isset($params['status']) ? (int)$params['status'] : null;

            $limit = 10;
            $offset = ($page - 1) * $limit;
            $next_page = false;
            $data = TbChatMessage::getAllMessage($customerID,$status, $offset, $limit + 1);

          //  $dataSet = TbChatMessage::getNotifyToCustomer($userID, 100);

            if (!empty($dataSet)) {
                foreach ($dataSet as $key => $value) {
                    $title = isset($value['title']) ? $value['title'] : $value['message'];
                    $value['title'] = CommonLib::cut_string($title, 50);
                    $value['message'] = CommonLib::cut_string($value['message'], 50); //  $value['timestamp'] = CommonLib::secondsToTime(strtotime($value['timestamp']));


                    if ($value['isType'] == 'order' || $value['isType'] == 'sign') {
                        $data[$value['type']][] = $value;
                    }

                    if ($value['isType'] == 'complain') {
                        $data[$value['isType']][] = $value;
                    }
                }

            }




            if (count($data) > $limit) {
                unset($data[count($data) - 1]);
                $next_page = true;
            }


            Yii::$app->response->statusCode = 200;
            return [
                'success' => true,
                'next_page' => $next_page,
                'total' => count($data),
                "data" => $data
            ];
        }

    }
}
