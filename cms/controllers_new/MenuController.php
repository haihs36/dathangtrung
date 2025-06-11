<?php

    namespace cms\controllers;

    use cms\models\TbNews;
    use common\behaviors\SortableModel;
    use common\components\CommonLib;
    use common\helpers\Image;
    use common\models\Photo;
    use Yii;
    use cms\models\TbMenu;
    use cms\models\TbMenuSearch;
    use yii\web\NotFoundHttpException;
    use yii\web\UploadedFile;
    use yii\widgets\ActiveForm;

    /**
     * MenuController implements the CRUD actions for TbMenu model.
     */
    class MenuController extends \common\components\Controller
    {
        /**
         * Lists all TbMenu models.
         * @return mixed
         */
        public function actionIndex()
        {
            $searchModel  = new TbMenuSearch();
            $params       = Yii::$app->request->queryParams;
            $dataProvider = $searchModel->search($params);

            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'params'       => $params,
            ]);
        }

        /**
         * Creates a new TbCategory model.
         * If creation is successful, the browser will be redirected to the 'view' page.
         * @return mixed
         */
        public function actionCreate($parent = null)
        {
            $model = new TbMenu();

                if ($model->load(Yii::$app->request->post())) {
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return ActiveForm::validate($model);
                    } else {
                        //update image
                        if (isset($_FILES)) {
                            $model->image = UploadedFile::getInstance($model, 'image');
                            if ($model->image && $model->validate(['image'])) {
                                $model->image = Image::upload($model->image, $this->getUniqueId());
                                $model->thumb = Image::thumb($model->image, Photo::PHOTO_THUMB_WIDTH);
                            } else {
                                $model->thumb = '';
                                $model->image = '';
                            }
                        }
                        $model->status  = $model::STATUS_ON;

                        if ($model->parent_id > 0 && ($parentCategory = TbMenu::findOne($model->parent_id))) {
                            $model->order_num = $parentCategory->order_num;
                            $model->appendTo($parentCategory);
                        } elseif (!empty($model->parent_id)) {
                            $model->attachBehavior('sortable', SortableModel::className());
                            $model->makeRoot();
                        } else {
                            $model->save();
                        }

                        Yii::$app->cache->flush();
                        if (!$model->hasErrors()) {
                            $this->flash('success', 'menu created');
                            return $this->redirect(['/menu/create']);
                        } else {
                            $this->flash('error', 'Create error. {0}', $model->formatErrors());
                            return $this->refresh();
                        }
                    }
                }

                return $this->render('_form', [
                    'model' => $model,
                ]);

        }

        /**
         * Updates an existing TbMenus model.
         * If update is successful, the browser will be redirected to the 'view' page.
         * @param integer $id
         * @return mixed
         */
        public function actionUpdate($id)
        {
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post())) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                } else {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if ($model->image && $model->validate(['image'])) {
                        $slug     = CommonLib::slug($model->title);
                        $fileName = $slug . '-' . StringHelper::truncate(md5($slug),10,'');
                        $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                        $model->thumb = Image::thumb($model->image,Photo::PHOTO_MEME_THUMB_WIDTH,Photo::PHOTO_MEME_THUMB_HEIGHT,true,$this->upload_thumb,$fileName);
                    } else {
                        $model->image = $model->oldAttributes['image'];
                        $model->thumb = $model->oldAttributes['thumb'];
                    }

                    if ($model->parent_id > 0 && ($parentCategory = TbMenu::findOne($model->parent_id))) {
                        $model->order_num = $parentCategory->order_num;
                        $model->appendTo($parentCategory);
                    } elseif (!empty($model->parent_id)) {
                        $model->attachBehavior('sortable', SortableModel::className());
                        $model->makeRoot();
                    } else {
                        $model->save();
                    }
                    Yii::$app->cache->flush();
                    if (!$model->hasErrors()) {
                        $this->flash('success', 'Cập nhật menu thành công');
                        return $this->redirect(['/menu/update/'.$id]);
                    } else {
                        $this->flash('error', 'Có lỗi trong quá trình xử lý');
                        return $this->refresh();
                    }
                }
            }

            return $this->render('_form', [
                'model' => $model,
            ]);
        }

        /**
         * Remove category image
         * @param $id
         * @return \yii\web\Response
         */
        public function actionClearImage($id)
        {
            //$class = $this->categoryClass;
            $model = TbMenu::findOne($id);
            if ($model === null) {
                $this->flash('error', 'Not found');
            } elseif ($model->image) {
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
         * Delete the category by ID
         * @param $id
         * @return mixed
         */
        public function actionDelete($id)
        {
            if (($model = TbMenu::findOne($id))) {
                $children = $model->children()->all();
                $model->deleteWithChildren();
                foreach ($children as $child) {
                    $child->afterDelete();
                }
            } else {
                $this->error = 'Not found';
            }
            Yii::$app->cache->flush();
            return $this->formatResponse('Menu deleted');
        }

        /**
         * Move category one level up up
         * @param $id
         * @return \yii\web\Response
         */
        public function actionUp($id)
        {
            return $this->MoveCate(TbMenu::className(), $id, 'up');
        }

        /**
         * Move category one level down
         * @param $id
         * @return \yii\web\Response
         */
        public function actionDown($id)
        {
            return $this->MoveCate(TbMenu::className(), $id, 'down');
        }

        /**
         * Activate category action
         * @param $id
         * @return mixed
         */
        public function actionOn($id)
        {
            return $this->changeStatusCate(TbMenu::className(), $id, TbMenu::STATUS_ON);
        }

        /**
         * Activate category action
         * @param $id
         * @return mixed
         */
        public function actionOff($id)
        {
            return $this->changeStatusCate(TbMenu::className(), $id, TbMenu::STATUS_OFF);
        }

        public function actionStatus()
        {
            $params = Yii::$app->request->post();
            $id = $params['id'];
            return $this->changeStatus(TbMenu::className(),$id,'status');
        }


        /**
         * Finds the TbMenus model based on its primary key value.
         * If the model is not found, a 404 HTTP exception will be thrown.
         * @param integer $id
         * @return TbMenus the loaded model
         * @throws NotFoundHttpException if the model cannot be found
         */
        protected function findModel($id)
        {
            if (($model = TbMenu::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

    }
