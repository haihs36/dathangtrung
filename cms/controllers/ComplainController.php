<?php

    namespace cms\controllers;

    use common\components\CommonLib;
    use common\helpers\Image;
    use common\models\AccessRule;
    use common\models\Photo;
    use common\models\TbComplainReply;
    use common\models\TbOrders;
    use common\models\TbProductComplain;
    use common\models\User;
    use Yii;
    use common\models\TbComplain;
    use common\models\TbComplainSearch;
    use yii\bootstrap\ActiveForm;
    use yii\filters\AccessControl;
    use yii\helpers\Url;
    use yii\web\NotFoundHttpException;
    use yii\web\UploadedFile;

    /**
     * ComplainController implements the CRUD actions for TbComplain model.
     */
    class ComplainController extends \common\components\Controller
    {
       /* public function behaviors()
        {

            return [
                'access' => [
                    'class'      => AccessControl::className(),
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'only'       => ['index', 'view', 'create', 'update', 'delete'],
                    'rules'      => [
                        [
                            'actions' => ['index', 'view', 'create', 'update'],
                            'allow'   => true,
                            'roles'   => [BUSINESS,COMPLAIN]
                        ],
                        [
                            'actions' => ['index', 'view', 'create', 'update', 'delete'],
                            'allow'   => true,
                            'roles'   => [User::ROLE_ADMIN,STAFFS],


                        ],
                    ],
                ],
            ];
        }*/

        /**
         * Lists all TbComplain models.
         * @return mixed
         */
        public function actionIndex($id = null)
        {
            if($id){
                $model = $this->findModel($id);
                if ($model->load(Yii::$app->request->post())) {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', 'Cập nhật thành công');
                    }
                    return $this->refresh();
                }

                $listCmt    = TbComplainReply::find()->where(['complainID' => $id, 'orderID' => $model->orderID])->all();
                $modelReply = new TbComplainReply();
                if ($modelReply->load(Yii::$app->request->post())) {
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return ActiveForm::validate($modelReply);
                    } else {
                        $modelReply->userID    = Yii::$app->user->id;
                        $modelReply->customerID   = $model->customerID;
                        $modelReply->complainID = $id;
                        $modelReply->orderID    = $model->orderID;
                        $modelReply->create_date = date('Y-m-d H:i:s');
                        $modelReply->create_time = time();
                        if ($modelReply->save()) {
                            Yii::$app->session->setFlash('success', 'Phản hồi thành công');
                            return $this->redirect(['complain/index','id'=>$id,'#'=>'tbcomplainreply-message']);
                        }
                        return $this->refresh();
                    }
                }

                //get product
                $sql = "SELECT a.productID,a.image,a.sapo,a.complainID,d.unitPrice,d.quantity,p.link,p.`name`,d.image as pimage,d.color,d.size
                FROM tb_product_complain a 
                INNER JOIN tb_orders_detail d on a.productID =d.id
                LEFT JOIN tb_product p on d.productID = p.productID
                WHERE a.complainID= $id";

                $pComplain = TbProductComplain::findBySql($sql)->asArray()->all();

                return $this->render('view', [
                    'listCmt'    => $listCmt,
                    'pComplain' => $pComplain,
                    'model'      => $model,
                    'modelReply' => $modelReply
                ]);
            }else{
                $status = (int)Yii::$app->request->get('status', '');
                $searchModel = new TbComplainSearch();
                if ($status) {
                    $searchModel->status = $status;
                }

              /*  if ($searchModel->load(Yii::$app->request->post())) {
                    $businessID = Yii::$app->request->post('TbComplainSearch')['businessID'];
                    $params['customerID'] = $searchModel->customerID;
                    $params['orderID'] = $searchModel->orderID;
                    $params['status'] = $searchModel->status;
                    $params['businessID'] = $businessID;//pr($params);die;
                    $searchModel->businessID = $businessID;
                    $dataProvider = $searchModel->search($params);
                }else{*/
                    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
              //  }


                return $this->render('index', [
                    'searchModel'  => $searchModel,
                    'dataProvider' => $dataProvider,
                    'status' => $searchModel->status,
                ]);
            }
        }

          public function actionCreate()
        {
            $id = (int)Yii::$app->request->get('id');
            $tborder = TbOrders::findOne($id);
            if(!$tborder)
                return CommonLib::redirectError();
            $model = new TbComplain();

//            CommonLib::pr(Yii::$app->request->post());
            if ($model->load(Yii::$app->request->post())) {
                if($tborder) {
                    $uid             = Yii::$app->user->id;
                    $model->customerID = $tborder->customerID;
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return ActiveForm::validate($model);
                    } else {
                        switch ($model->type){
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                                if (isset($_FILES)) {
                                    $model->image = UploadedFile::getInstance($model, 'image');
                                    $fileName     = $model->orderID . '-' . CommonLib::getRandomInt(10);
                                    $model->compensation = CommonLib::toInt($model->compensation);//!empty($model->compensation) ? str_replace(',','',$model->compensation) : 0;
                                    if ($model->image && $model->validate(['image'])) {
                                        $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                                    }

                                    if ($model->save()) {
                                        return $this->redirect(['complain/index']);
                                    } else {
                                        @unlink(\Yii::getAlias('@upload_dir') . str_replace(Url::base(true), '', $model->image));
                                    }
                                }
                                break;

                            case 6:
                            case 7:
                            case 8:
                            case 9:
                            case 10:
                                $listProduct = [];
                                $check = 0;
                                if ($model->khieunai) {
                                    foreach ($model->khieunai as $pid => $product) {
                                        $photoID = (int)$product['file_img_fid'];
                                        $photo   = Photo::findOne($photoID);
                                        if (isset($product['check']) && $photoID) {
                                            $check++;
                                            $product['file_img_fid'] = $photoID;
                                            $product['img']          = $photo->image;
                                            $listProduct[$pid]       = $product;

                                        } elseif ($photoID && $photo) {
                                            $photo->delete();//th khong check sp xoa img
                                        }
                                    }
                                }

                                if ($listProduct) {
                                    if (isset($_FILES)) {
                                        $model->image = UploadedFile::getInstance($model, 'image');
                                        $fileName     = $model->orderID . '-' . CommonLib::getRandomInt(10);
                                        if ($model->image && $model->validate(['image'])) {
                                            $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                                        }

                                        $model->compensation = CommonLib::toInt($model->compensation);;//!empty($model->compensation) ? str_replace(',','',$model->compensation) : 0;
                                        if ($model->save()) {
                                            foreach ($listProduct as $productID => $item) {
                                                $mdlProductComplain             = new TbProductComplain();
                                                $mdlProductComplain->productID  = $productID;//id orderdetail
                                                $mdlProductComplain->complainID = $model->id;
                                                $mdlProductComplain->sapo       = $item['ghichu'];
                                                $mdlProductComplain->image      = $item['img'];
                                                if (!$mdlProductComplain->save()) {
                                                    //xoa file
                                                    @unlink(\Yii::getAlias('@upload_dir') . str_replace(Url::base(true), '', $item['img']));
                                                    Photo::findOne($item['file_img_fid'])->delete();
                                                }
                                            }
                                            return $this->redirect(['complain/index']);
                                        } else {
                                            @unlink(\Yii::getAlias('@upload_dir') . str_replace(Url::base(true), '', $model->image));
                                            foreach ($listProduct as $item) {
                                                @unlink(\Yii::getAlias('@upload_dir') . str_replace(Url::base(true), '', $item['img']));
                                                Photo::findOne($item['file_img_fid'])->delete();
                                            }
                                        }

                                    }

                                }
                                if(!$check){
                                    \Yii::$app->session->setFlash('error', 'Bạn chưa chọn sản phẩm hoặc chưa upload ảnh sản phẩm khiếu nại.');
                                }

                                break;
                        }
                    }
                }else{
                    \Yii::$app->session->setFlash('error', 'Đơn hàng không hợp lệ.');
                }
            }


            $data_seo['title'] = 'Đơn khiếu nại';
            \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);



            $model->orderID = $tborder->orderID;

            return $this->render('_form', [
                'model' => $model,
                'tborder' => $tborder,
            ]);

        }

        /**
         * Updates an existing TbComplain model.
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
         * Deletes an existing TbComplain model.
         * If deletion is successful, the browser will be redirected to the 'index' page.
         * @param string $id
         * @return mixed
         */
        public function actionDelete($id)
        {

            TbComplainReply::deleteAll(['complainID' => $id]);
            $dbProduct = TbProductComplain::find()->select(['productID'])->where(['complainID' => $id])->all();
            if ($dbProduct) {
                //del from photo
                foreach ($dbProduct as $product) {
                    if ($photo = Photo::findOne(['productID' => $product->productID])) {
                        $photo->delete();
                    }
                }
            }
            TbProductComplain::deleteAll(['complainID' => $id]);
            $this->findModel($id)->delete();
            return $this->redirect(['index']);
        }

        /**
         * Finds the TbComplain model based on its primary key value.
         * If the model is not found, a 404 HTTP exception will be thrown.
         * @param string $id
         * @return TbComplain the loaded model
         * @throws NotFoundHttpException if the model cannot be found
         */
        protected function findModel($id)
        {
            if (($model = TbComplain::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
    }
