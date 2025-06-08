<?php

namespace cms\controllers;

use cms\models\TbCateProduct;
use cms\models\TbCateProductSearch;
use common\behaviors\SortableModel;
use common\components\CommonLib;
use common\helpers\Image;
use common\models\AccessRule;
use common\models\Photo;
use common\models\User;
use Yii;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * CageroryController implements the CRUD actions for TbCateProduct model.
 */
class CateproductController extends \common\components\Controller
{

    /*public function behaviors()
    {

        return [
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class'      => AccessControl::className(),
                // We will override the default rule config with the new AccessRule class
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
     * Lists all TbCateProduct models.
     * @return mixed
     */
    const LIMIT = 5;

    public function actionIndex()
    {
        $searchModel  = new TbCateProductSearch();
        $params       = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'params'       => $params,
        ]);
    }

    /**
     * Creates a new TbCateProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($parent = null)
    {
        $model = new TbCateProduct();

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                //update image
                /*if (isset($_FILES)) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if ($model->image && $model->validate(['image'])) {
                        $model->image = Image::upload($model->image, $this->getUniqueId());
                        $model->thumb = Image::thumb($model->image, Photo::PHOTO_THUMB_WIDTH);
                    } else {
                        $model->thumb = '';
                        $model->image = '';
                    }
                }*/


                $model->status    = $model::STATUS_ON;
                $parent           = Yii::$app->request->post('parent', null);
                $model->parent_id = $parent;
                 $parent           = (int)$parent;

                if ($model->parent_id > 0 && ($parentCategory = $model::findOne($model->parent_id))) {
                    $model->order_num = $parentCategory->order_num;
                    $model->appendTo($parentCategory);
                } else {
                    $model->attachBehavior('sortable', SortableModel::className());
                    $model->makeRoot();
                }

                $model->save();
                if (!$model->hasErrors()) {
                    $this->flash('success', ' created success');
                    return $this->redirect(['/cateproduct/create']);
                } else {
                    $this->flash('error', 'Create error. {0}', $model->formatErrors());
                    return $this->refresh();
                }
            }
        } else {
            return $this->render('create', [
                'model'  => $model,
                'parent' => $model->parent_id
            ]);
        }
    }

    /**
     * Edit form
     * @param $id
     * @return array|string|\yii\web\Response
     * @throws \yii\web\HttpException
     */
    public function actionEdit($id)
    {
        if (!($model = TbCateProduct::findOne($id))) {
            return $this->redirect(['/category/index']);
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                if (isset($_FILES)) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if ($model->image && $model->validate(['image'])) {
                        $model->image = Image::upload($model->image, $this->getUniqueId());
                        $model->thumb = Image::thumb($model->image, Photo::PHOTO_THUMB_WIDTH);
                    } else {
                        $model->image = $model->oldAttributes['image'];
                        $model->thumb = $model->oldAttributes['thumb'];
                    }
                }
                $parent = (int)Yii::$app->request->post('parent', null);

                if ($parent > 0 && ($parentCategory = TbCateProduct::findOne($parent))) {
                    $model->parent_id = ($parent > 0 ? $parent : null);
                    $model->order_num = $parentCategory->order_num;
                    $model->appendTo($parentCategory);
                } elseif ($model->parent_id !== null) {
                    $model->parent_id = ($parent > 0 ? $parent : null);
                    $model->attachBehavior('sortable', SortableModel::className());
                    $model->makeRoot();
                } else {
                    $model->save();
                }

                if (!$model->hasErrors()) {
                    $this->flash('success', 'update success');
                } else {
                    $this->flash('error', 'Update error. {0}' . $model->formatErrors());
                }
                return $this->refresh();
            }
        } else {
            return $this->render('edit', [
                'model'  => $model,
                'parent' => $model->parent_id
            ]);
        }
    }

    /**
     * Remove category image
     * @param $id
     * @return \yii\web\Response
     */
    public function actionClearImage($id)
    {
        //$class = $this->categoryClass;
        $model = TbCateProduct::findOne($id);
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
        if (($model = TbCateProduct::findOne($id))) {
            $children = $model->children()->all();
            $model->deleteWithChildren();
            foreach ($children as $child) {
                $child->afterDelete();
            }
        } else {
            $this->error = 'Not found';
        }
        return $this->formatResponse('deleted');
    }

    /**
     * Move category one level up up
     * @param $id
     * @return \yii\web\Response
     */
    public function actionUp($id)
    {
        return $this->MoveCate(TbCateProduct::className(), $id, 'up');
    }

    /**
     * Move category one level down
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDown($id)
    {
        return $this->MoveCate(TbCateProduct::className(), $id, 'down');
    }

    /**
     * Activate category action
     * @param $id
     * @return mixed
     */
    public function actionOn($id)
    {
        return $this->changeStatusCate(TbCateProduct::className(), $id, TbCateProduct::STATUS_ON);
    }

    /**
     * Activate category action
     * @param $id
     * @return mixed
     */
    public function actionOff($id)
    {
        return $this->changeStatusCate(TbCateProduct::className(), $id, TbCateProduct::STATUS_OFF);
    }

}
