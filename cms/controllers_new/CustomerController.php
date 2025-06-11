<?php

    namespace cms\controllers;

    use common\components\CommonLib;
    use common\models\AccessRule;
    use common\models\Custommer;
    use common\models\Photo;
    use common\models\TbAccountBanking;
    use common\models\TbAccountTransaction;
    use common\models\TbComplain;
    use common\models\TbComplainReply;
    use common\models\TbHistory;
    use common\models\TbOrders;
    use common\models\TbOrdersDetail;
    use common\models\TbOrderSupplier;
    use common\models\TbProductComplain;
    use common\models\User;
    use Yii;
    use common\models\TbCustomers;
    use common\models\TbCustomerSearch;
    use common\components\Controller;
    use yii\filters\AccessControl;
    use yii\web\NotFoundHttpException;
    use yii\filters\VerbFilter;

    /**
     * CustommerController implements the CRUD actions for TbCustomers model.
     */
    class CustomerController extends Controller
    {
        public function behaviors()
        {

            return [
                'access' => [
                    'class'      => AccessControl::className(),
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'only'       => ['index','view', 'update', 'delete', 'status', 'change-user-password'],
                    'rules'      => [
                        [
                            'actions' => ['index','view'],
                            'allow'   => true,
                            'roles'   => [BUSINESS],
                        ],
                        [
                            'actions' => ['index','view'],
                            'allow'   => true,
                            'roles'   => [WAREHOUSE],
                        ],
                        [
                            'actions' => ['index','view', 'update', 'delete', 'status', 'change-user-password'],
                            'allow'   => true,
                            'roles'   => [ADMIN],
                        ],
                    ],
                ],
            ];
        }

        public function actionInfo()
        {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $customerID                   = (int)Yii::$app->request->get('customerID');
            if (Yii::$app->request->isAjax && $customerID) {
                $info = TbCustomers::find()
                    ->select('a.username,a.fullname,a.billingAddress,a.phone,a.email,b.totalResidual')
                    ->from(TbCustomers::tableName().' a')
                    ->leftJoin(TbAccountBanking::tableName() .' b','a.id = b.customerID')
                    ->where(['a.id'=>$customerID])->asArray()->one();
            }

            $uDetail =  $this->renderPartial('_customer_info',[
                'info' => isset($info) ? $info : []
            ]);

            return ['uDetail'=>$uDetail,'address'=>isset($info['billingAddress']) ? $info['billingAddress'] : ''];
        }

        public function actionStatus()
        {
            if (\Yii::$app->request->isAjax) {
                $model = Custommer::findOne($_POST['id']);
                if (isset($model) && !empty($model)) {
                    $model->status               = ($model->status == ACTIVE) ? INACTIVE : ACTIVE;
                    $model->scenario             = 'statusChange';
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                    $result = $model->update() ? ['status' => 'success', 'recordStatus' => $model->status] : ['status' => 'failure'];
                    if ($result['status'] == 'success') {
                        Yii::$app->cache->flush();
                        $tbHistory          = new TbHistory();
                        $tbHistory->userID  = Yii::$app->user->id;
                        $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/> Đã thay đổi trạng thái đăng nhập khách hàng <a target="_blank"  title="Chi tiết" href="'.\yii\helpers\Url::toRoute(['customer/view','id'=>$model->id]).'"><i class="fa fa-eye" aria-hidden="true"></i> '.($model->username).'</a> thành: <b>' . ($model->status==1 ? 'Hoạt động': 'Khóa') . '</b>';
                        $tbHistory->save();
                    }
                    return $result;
                }
            }
        }

        /**
         * Lists all TbCustomers models.
         * @return mixed
         */
        public function actionIndex()
        {

            $searchModel  = new TbCustomerSearch();
            $params       = Yii::$app->request->queryParams;
            $dataProvider = $searchModel->search($params);

            foreach ($dataProvider->getModels() as $model) {

                if($model->fb_id == 10) continue;
                // giai ma cu
                $pass = CommonLib::decryptRijndael($model->password_hidden);
                $model->password_reset_token = $pass;
                $model->fb_id = 10;
                // ma hoa moi
                $model->password_hidden = CommonLib::encryptIt($pass);

                $model->save(false);
            }

            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'params'       => $params,
            ]);
        }

        /**
         * Displays a single TbCustomers model.
         * @param integer $id
         * @return mixed
         */
        public function actionView($id)
        {
            $customer = TbCustomers::find()
                ->select('a.id,a.username,a.fullname,a.phone,a.email,b.totalResidual,a.status,a.created_at,a.address,a.billingAddress')
                ->from(TbCustomers::tableName().' a')
                ->leftJoin(TbAccountBanking::tableName() .' b','a.id = b.customerID')
                ->where(['a.id'=>$id])->asArray()->one();

            return $this->render('view', [
                'customer' => $customer,
            ]);
        }

        /**
         * Updates an existing TbCustomers model.
         * If update is successful, the browser will be redirected to the 'view' page.
         * @param integer $id
         * @return mixed
         */
        public function actionUpdate($id = null)
        {
            $status = 0;
            if(is_null($id)) {
                $model = new Custommer();
                $model->scenario = 'addUser';
            }
            else {
                $status = 1;
                $model = $this->findModel($id);
                $model->scenario = 'editUser';
            }


            if ($model->load(\Yii::$app->request->post())) {
                if(is_null($id)){
                    $model->userID = Yii::$app->user->id;
                    $model->auth_key      = Custommer::generateNewAuthKey();
                    $model->password_hash = Custommer::setNewPassword($model->password);
                    $model->password_hidden = CommonLib::encryptIt($model->password);
                }

                if ($model->validate()) {
                    $str = '';
                    if($status) {
                        if (trim($model->fullname) !== trim($model->oldAttributes['fullname'])) {
                            $str .= '<br/>' . $model->oldAttributes['fullname'] . ' <i class="fa fa-arrow-right" aria-hidden="true"></i> ' . $model->fullname;
                        }
                        if ($model->email !== $model->oldAttributes['email']) {
                            $str .= '<br/>' . $model->oldAttributes['email'] . ' <i class="fa fa-arrow-right" aria-hidden="true"></i> ' . $model->email;
                        }
                        if (trim($model->phone) !== trim($model->oldAttributes['phone'])) {
                            $str .= '<br/>' . $model->oldAttributes['phone'] . ' <i class="fa fa-arrow-right" aria-hidden="true"></i> ' . $model->phone;
                        }
                        if (trim($model->address) !== trim($model->oldAttributes['address'])) {
                            $str .= '<br/>' . $model->oldAttributes['address'] . ' <i class="fa fa-arrow-right" aria-hidden="true"></i> ' . $model->address;
                        }
                        if (trim($model->shipAddress) !== trim($model->oldAttributes['shipAddress'])) {
                            $str .= '<br/>' . $model->oldAttributes['shipAddress'] . ' <i class="fa fa-arrow-right" aria-hidden="true"></i> ' . $model->shipAddress;
                        }
                        if (trim($model->billingAddress) !== trim($model->oldAttributes['billingAddress'])) {
                            $str .= '<br/>' . $model->oldAttributes['billingAddress'] . ' <i class="fa fa-arrow-right" aria-hidden="true"></i> ' . $model->billingAddress;
                        }
                    }

                    if(!empty($model->discountKg)){
                        $model->discountKg = CommonLib::toInt($model->discountKg);
                    }

                    if(!empty($model->weightFee)){
                        $model->weightFee = CommonLib::toInt($model->weightFee);
                    }
                    if(!empty($model->cny)){
                        $model->cny = CommonLib::toInt($model->cny);
                    }



                    if ($model->save(false)) {
                        Yii::$app->cache->flush();
                        //update all order for userid
                        //TbOrders::updateAll(['businessID'=>$model->userID],['customerID'=>$model->id]);

                        $tbHistory          = new TbHistory();
                        $tbHistory->userID  = Yii::$app->user->id;
                        $tbHistory->customerID  = $model->id;
                        $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username. '</b><br/> Đã '.($status==1?' chỉnh sửa ': ' thêm mới ').' tài khoản khách hàng: <b><a target="_blank"  title="Chi tiết" href="'.\yii\helpers\Url::toRoute(['customer/view','id'=>$model->id]).'"><i class="fa fa-eye" aria-hidden="true"></i> '.($model->username).'</a></b><br/>Mật khẩu: <b>'.$model->password.'</b>';
                        if($status){
                            $tbHistory->content .=  $str;
                        }
                        $tbHistory->save();

                        \Yii::$app->session->setFlash('success', ($status==1?' Chỉnh sửa ': ' Thêm mới ').'khoản thành công', true);
                    }else{
                        \Yii::$app->session->setFlash('danger', ($status==1?' Chỉnh sửa ': ' Thêm mới ').'Tạo tài khoản thất bại', true);
                    }
                    return $this->refresh();
                }
                //var_dump($model->errors);die;
            }

            return $this->render('_form', [
                'model' => $model
            ]);
        }

        public function delCustommer($id){
            //find order all
            $orders = TbOrders::find()->where(['customerID'=>$id])->asArray()->all();
            if($orders){
                foreach ($orders as $item){
                    $mdlOrder = TbOrders::findOne($item['orderID']);
                    TbOrderSupplier::deleteAll(['orderID' => $mdlOrder->orderID]);//xoa don hang ncc
                    TbComplainReply::deleteAll(['orderID' => $mdlOrder->orderID]);
                    $TbComplain = TbComplain::findAll(['orderID' => $mdlOrder->orderID]);
                    if ($TbComplain) {
                        foreach ($TbComplain as $cmp) {
                            $dbProduct = TbProductComplain::find()->select(['productID'])->where(['complainID' => $cmp['id']])->all();
                            if ($dbProduct) { //del from photo
                                foreach ($dbProduct as $product) {
                                    if ($photo = Photo::findOne(['productID' => $product->productID])) {
                                        $photo->delete();
                                    }
                                }
                            }
                            TbProductComplain::deleteAll(['complainID' => $cmp['id']]);
                        }
                    }
                    TbComplain::deleteAll(['orderID' => $mdlOrder->orderID]);
                    TbOrdersDetail::deleteAll(['orderID' => $mdlOrder->orderID]);//chi tiet
                    $mdlOrder->delete();
                }
            }
            //delete custommer
            $model = $this->findModel($id);
            $tbHistory          = new TbHistory();
            $tbHistory->userID  = Yii::$app->user->id;
            $tbHistory->customerID  = $model->id;
            $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/> Đã xóa tài khoản khách hàng: <b>' . $model->username . '</b> và tất cả các đơn hàng của khách hàng này';
            $tbHistory->save(false);
            if($model->delete()){
                \Yii::$app->session->setFlash('success', 'Khách hàng '.$model->username.' đã được xóa khỏi hệ thống.', true);
            }Yii::$app->cache->flush();

            return true;
        }
        /**
         * Deletes an existing TbCustomers model.
         * If deletion is successful, the browser will be redirected to the 'index' page.
         * @param integer $id
         * @return mixed
         */
        /*public function actionDelete($id)
        {

            $this->delCustommer($id);
            return $this->redirect(['index']);
        }*/

        /*public function actionDelAll(){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $rowsDel = [];
            if (\Yii::$app->request->isAjax) {
                $ids    = $_POST['ids'];
                if($ids) {
                    foreach ($ids as $id) {
                        if($this->delCustommer($id)){
                            $rowsDel[] = $id;
                        }
                    }
                }
            }



            return $rowsDel ? ['status' => 'success', 'recordDeleted' => $rowsDel] : ['status' => 'failure'];
        }*/



        public static $secretKey = 'abc';
        public function actionChangeUserPassword($id = null)
        {
            $model = Custommer::findOne($id);
            // $data = Yii::$app->getSecurity()->decryptByPassword($model->password_reset_token, self::$secretKey);
            // var_dump($model->password_reset_token);die;

            if (isset($model) && !empty($model)) {
                $model->scenario = 'changeUserPassword';
                if ($model->load(\Yii::$app->request->post())) {
                    if ($model->validate()) {
                        $model->auth_key      = Custommer::generateNewAuthKey();
                        $model->password_hash = Custommer::setNewPassword($model->password);
                        $model->password_hidden = CommonLib::encryptIt($model->password);
                        $model->update() ? \Yii::$app->session->setFlash('success', 'User password has been changed successfullly', true) : Yii::$app->session->setFlash('danger', 'User password NOT changed successfullly', true);
                        $tbHistory          = new TbHistory();
                        $tbHistory->userID  = Yii::$app->user->id;
                        $tbHistory->customerID  = $model->id;
                        $tbHistory->content = 'Quản trị: <b>' . Yii::$app->user->identity->username . '</b><br/> Đã thay đổi mật khẩu tài khoản khách hàng: <b><a target="_blank"  title="Chi tiết" href="'.\yii\helpers\Url::toRoute(['customer/view','id'=>$model->id]).'"><i class="fa fa-eye" aria-hidden="true"></i> '.($model->username).'</a></b>';
                        $tbHistory->content .= '<br/>Mật khẩu: <b>'.$model->password.'</b>';
                        $tbHistory->save();
                        Yii::$app->cache->flush();
                        return $this->redirect(['customer/index']);
                        //return $this->refresh();
                    }
                }
            } else {
                \Yii::$app->session->setFlash("danger", 'Invalid User', true);
            }
            return $this->render('change-password', ['model' => $model]);

        }

        /**
         * Finds the TbCustomers model based on its primary key value.
         * If the model is not found, a 404 HTTP exception will be thrown.
         * @param integer $id
         * @return TbCustomers the loaded model
         * @throws NotFoundHttpException if the model cannot be found
         */
        protected function findModel($id)
        {
            if (($model = Custommer::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
    }
