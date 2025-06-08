<?php

    namespace cms\controllers;

    use common\behaviors\SortableController;
    use common\behaviors\StatusController;
    use common\components\CommonLib;
    use common\helpers\Image;
    use common\models\AccessRule;
    use common\models\Photo;
    use common\models\User;
    use Yii;
    use cms\models\TbCarousel;
    use cms\models\TbCarouselSearch;
    use yii\filters\AccessControl;
    use yii\helpers\Inflector;
    use yii\web\UploadedFile;
    use yii\widgets\ActiveForm;


    /**
     * CarouselController implements the CRUD actions for TbCarousel model.
     */
    class CarouselController extends \common\components\Controller
    {
       /* public function behaviors()
        {
            return [
                [
                    'class' => SortableController::className(),
                    'model' => TbCarousel::className()
                ],
                [
                    'class' => StatusController::className(),
                    'model' => TbCarousel::className()
                ],
                'access' => [
                    'class'      => AccessControl::className(),
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'only'       => ['index', 'view', 'create', 'edit', 'delete','clear-image','up','down','on','off'],
                    'rules'      => [
                        [
                            'actions' => [],
                            'allow'   => false,
                            'roles'   => [
                                User::ROLE_USER,
                            ],
                        ],
                        [
                            'actions' => ['index', 'view', 'create', 'edit', 'delete','clear-image','up','down','on','off'],
                            'allow'   => true,
                            'roles'   => [
                                User::ROLE_ADMIN,
                                User::ROLE_CLERK
                            ],
                        ],
                    ],
                ],
            ];
        }*/

        /**
         * Lists all TbCarousel models.
         * @return mixed
         */
        public function actionIndex()
        {
            $searchModel  = new TbCarouselSearch();
            $params       = Yii::$app->request->queryParams;
            $dataProvider = $searchModel->search($params);

            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'params'       => $params
            ]);
        }

        public function actionCreate()
        {
            $model = new TbCarousel();

            if ($model->load(Yii::$app->request->post())) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                } else {
                    if (($fileInstanse = UploadedFile::getInstance($model, 'image'))) {
                        $slug = !empty($model->title) ? Inflector::slug($model->title) : 'default';
                        $fileName = $slug.'-'.CommonLib::getRandomInt(10);
                        $model->image = $fileInstanse;
                        if ($model->image && $model->validate(['image'])) {
                            $model->image = Image::upload($model->image, $this->upload_image,null,null,false,$fileName);
                            $model->thumb = Image::thumb($model->image,Photo::PHOTO_THUMB_WIDTH,Photo::PHOTO_THUMB_HEIGHT,true,$this->upload_thumb,$fileName);
                            $model->status = TbCarousel::STATUS_ON;

                            if ($model->save()) {
                                $this->flash('success', 'Carousel created');
                                return $this->redirect(['/carousel/index']);
                            } else {
                                $this->flash('error', 'Create error. {0} ' . $model->formatErrors());
                            }
                        } else {
                            $this->flash('error', 'Create error. {0}' . $model->formatErrors());
                        }
                    } else {
                        $this->flash('error', '{attribute} cannot be blank.', ['attribute' => $model->getAttributeLabel('image')]);
                    }
                    return $this->refresh();
                }
            } else {
                return $this->render('create', [
                    'model' => $model
                ]);
            }
        }

        public function actionEdit($id)
        {
            $model = TbCarousel::findOne($id);

            if ($model === null) {
                $this->flash('error', 'Not found');
                return $this->redirect(['/carousel/']);
            }

            if ($model->load(Yii::$app->request->post())) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                } else {
                    if (($fileInstanse = UploadedFile::getInstance($model, 'image'))) {
                        $model->image = $fileInstanse;
                        $slug = !empty($model->title) ? Inflector::slug($model->title) : 'default';
                        $fileName = $slug.'-'.CommonLib::getRandomInt(10);
                        if ($model->image && $model->validate(['image'])) {
                            $model->image = Image::upload($model->image, $this->upload_image,null,null,false,$fileName);
                            $model->thumb = Image::thumb($model->image,Photo::PHOTO_THUMB_WIDTH,Photo::PHOTO_THUMB_HEIGHT,true,$this->upload_thumb,$fileName);
                        } else {
                            $this->flash('error', 'Update error' . $model->formatErrors());
                            return $this->refresh();
                        }
                    } else {
                        $model->image = $model->oldAttributes['image'];
                        $model->thumb = $model->oldAttributes['thumb'];
                    }

                    if ($model->save()) {
                        $this->flash('success', 'Carousel updated');
                    } else {
                        $this->flash('error', 'Update error. {0}' . $model->formatErrors());
                    }
                    return $this->refresh();
                }
            } else {
                return $this->render('edit', [
                    'model' => $model
                ]);
            }
        }

        public function actionDelete($id)
        {
            if (($model = TbCarousel::findOne($id))) {
                $model->delete();
            } else {
                $this->error = 'Not found';
            }
            return $this->formatResponse('Carousel item deleted');
        }

        public function actionClearImage($id)
        {
            $model = TbCarousel::findOne($id);
            if ($model === null) {
                $this->flash('error', 'Not found');
            } elseif ($model->image || $model->thumb) {
                $model->image = null;
                $model->thumb = null;
                if ($model->update()) {
                    @unlink(\Yii::getAlias('@upload_dir') . $model->image);
                    @unlink(\Yii::getAlias('@upload_dir') . $model->thumb);
                    $this->flash('success', 'Image cleared');
                } else {
                    $this->flash('error', 'Update error. {0}', $model->formatErrors());
                }
            }
            return $this->back();
        }

        public function actionUp($id)
        {
            return $this->move($id, 'up');
        }

        public function actionDown($id)
        {
            return $this->move($id, 'down');
        }

        public function actionOn($id)
        {
            return $this->changeStatus($id, TbCarousel::STATUS_ON);
        }

        public function actionOff($id)
        {
            return $this->changeStatus($id, TbCarousel::STATUS_OFF);
        }
    }
