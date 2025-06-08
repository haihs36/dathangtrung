<?php

    namespace cms\controllers;

    use common\components\CommonLib;
    use common\helpers\Image;
    use common\models\Photo;
    use Yii;
    use cms\models\TbNews;
    use cms\models\TbNewsSearch;
    use yii\helpers\StringHelper;
    use yii\web\UploadedFile;
    use yii\widgets\ActiveForm;

    /**
     * NewsController implements the CRUD actions for TbNews model.
     */
    class NewsController extends \common\components\Controller
    {


        public function actionIndex()
        {
            $searchModel = new TbNewsSearch();
            $params      = Yii::$app->request->queryParams;

            $dataProvider = $searchModel->search($params);

            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'params'       => $params,
            ]);
        }


        /**
         * Creates a new TbNews model.
         * If creation is successful, the browser will be redirected to the 'view' page.
         * @return mixed
         */
        public function actionCreate()
        {
            $model       = new TbNews();
            if ($model->load(Yii::$app->request->post())) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                } else {
                    $rdoImg   = Yii::$app->request->post('rdoimg');
                    $slug     = CommonLib::slug($model->title);
                    $fileName = $slug . '-' . StringHelper::truncate(md5($slug),10,'');
                    $model->image = '';
                    $model->thumb = '';

                    if ($rdoImg == 'yes' && isset($_FILES) && !empty($_FILES)) {
                        $model->image = UploadedFile::getInstance($model, 'image');
                        if ($model->image && $model->validate(['image'])) {
                            $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                            $model->thumb = Image::thumb($model->image,Photo::PHOTO_MEME_THUMB_WIDTH,Photo::PHOTO_MEME_THUMB_HEIGHT,true,$this->upload_thumb,$fileName);
                        }
                    } else {
                        $imageUrl = Yii::$app->request->post('LinkIMG');
                        if (!empty($imageUrl)) {
                            $image = CommonLib::downloadImageUrl($imageUrl, $slug);
                            if ($image) {
                                $model->image = Image::thumb($image, null, null, false, $this->upload_image, $fileName);
                                $model->thumb = Image::thumb($image,Photo::PHOTO_MEME_THUMB_WIDTH,Photo::PHOTO_MEME_THUMB_HEIGHT,true,$this->upload_thumb,$fileName);
                                //xoa image download
                                @unlink(Yii::getAlias('@upload_dir') . $image);
                            }
                        }
                    }
                    $model->publishtime = date('Y-m-d H:i:s');
                    $model->time = time();

                    if ($model->save()) {
                        $this->flash('success', 'Tạo bài viết thành công.');
                        return $this->refresh();
                    } else {
                        $this->flash('error', 'có lỗi xảy ra');
                        return $this->refresh();
                    }
                }
            }
            // else {
            return $this->render('_form', [
                'model' => $model
            ]);
            //  }
        }

        /**
         * Updates an existing TbNews model.
         * If update is successful, the browser will be redirected to the 'view' page.
         * @param integer $id
         * @return mixed
         */
        public function actionUpdate($id)
        {
            $model = TbNews::findOne($id);
            if ($model === null) {
                return $this->redirect(['/articles/index']);
            }

            if ($model->load(Yii::$app->request->post())) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                } else {
                    $rdoImg       = Yii::$app->request->post('rdoimg');
                    $slug     = CommonLib::slug($model->title);
                    $fileName = $slug . '-' . StringHelper::truncate(md5($slug),10,'');
                    $model->image = $model->oldAttributes['image'];
                    $model->thumb = $model->oldAttributes['thumb'];

                    if ($rdoImg == 'yes' && isset($_FILES) && !empty($_FILES)) {
                        $model->image = UploadedFile::getInstance($model, 'image');
                        if ($model->image && $model->validate(['image'])) {
                            $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                            $model->thumb = Image::thumb($model->image, Photo::PHOTO_MEME_THUMB_WIDTH, Photo::PHOTO_MEME_THUMB_HEIGHT, true, $this->upload_thumb, $fileName);
                        }
                    } else {
                        $imageUrl = Yii::$app->request->post('LinkIMG');
                        if (!empty($imageUrl)) {
                            $image = CommonLib::downloadImageUrl($imageUrl, $slug);
                            if ($image) {
                                $model->image = Image::thumb($image, null, null, false, $this->upload_image, $fileName);
                                $model->thumb = Image::thumb($image, Photo::PHOTO_MEME_THUMB_WIDTH, Photo::PHOTO_MEME_THUMB_HEIGHT, true, $this->upload_thumb, $fileName);
                                //xoa image tam
                                @unlink(Yii::getAlias('@upload_dir') . $image);
                            }
                        }
                    }

                    $model->lastmodify = date('Y-m-d H:i:s');
                    $model->time = time();
                    if ($model->save()) {
                        $this->flash('success', 'Cập nhật thành công.');
                        return $this->refresh();
                        // return $this->redirect(['/news/index']);
                    } else {
                        $this->flash('error', 'Có lỗi xảy ra');
                        return $this->refresh();
                    }

                }
            }
            //  else {
            return $this->render('_form', [
                'model' => $model
            ]);
            // }
        }


        /* clearn image
         * */
        public function actionClearImage($id)
        {
            $model = TbNews::findOne($id);

            if ($model === null) {
                $this->flash('error', 'Not found');
            } else {
                $model->image = '';
                $model->thumb = '';
                if ($model->update()) {
                    @unlink(Yii::getAlias('@upload_dir') . $model->image);
                    @unlink(Yii::getAlias('@upload_dir') . $model->thumb);
                    $this->flash('success', 'Image cleared');
                } else {
                    $this->flash('error', 'Update error. {0}', $model->formatErrors());
                }
            }
            return $this->back();
        }

        /**
         * Deletes an existing TbNews model.
         * If deletion is successful, the browser will be redirected to the 'index' page.
         * @param integer $id
         * @return mixed
         */

        public function actionDelete($id)
        {
            if (($model = TbNews::findOne($id))) {
                $model->delete();
            } else {
                $this->error = 'Not found';
            }
            return $this->formatResponse('News deleted');
        }

        public function actionHot()
        {
            $params = Yii::$app->request->post();
            return $this->changeStatus(TbNews::className(),$params['id'],'is_hot');
        }

        public function actionStatus()
        {
            $params = Yii::$app->request->post();
            return $this->changeStatus(TbNews::className(),$params['id'],'status');
        }

        public function actionUp($id)
        {
            return $this->move(TbNews::className(),$id, 'up');
        }

        public function actionDown($id)
        {
            return $this->move(TbNews::className(),$id, 'down');
        }

        public function actionPhotos($id)
        {

            if (!($model = TbNews::findOne($id))) {
                return $this->redirect(['/news/' . $id]);
            }

            return $this->render('photos', [
                'model' => $model,
            ]);
        }

    }
