<?php

namespace cms\controllers;

use common\components\CommonLib;
use common\helpers\Image;
use common\models\AccessRule;
use common\models\User;
use Yii;
use common\models\TbHotLink;
use common\models\TbHotLinkSearch;
use common\components\Controller;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * HotlinkController implements the CRUD actions for TbHotLink model.
 */
class HotlinkController extends Controller
{
    /*public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class'      => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only'       => ['index', 'view', 'create', 'update', 'delete'],
                'rules'      => [
                    [
                        'actions' => [],
                        'allow'   => false,
                        'roles'   => [
                            User::ROLE_USER,
                        ],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
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
     * Lists all TbHotLink models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TbHotLinkSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'params' => $params,
        ]);
    }

    /**
     * Displays a single TbHotLink model.
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
     * Creates a new TbHotLink model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TbHotLink();

        if ($model->load(Yii::$app->request->post())) {
            if (isset($_FILES)) {
                $slug     = Inflector::slug($model->name);
                $fileName = $slug . '-' . CommonLib::getRandomInt(10);
                $model->image = UploadedFile::getInstance($model, 'image');
                if (!empty($model->image) && $model->validate(['image'])) {
                    $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                }
            }

            $model->price =  CommonLib::toInt($model->price);
            if($model->save()){
                return $this->redirect(['index']);
            }
        }

            return $this->render('create', [
                'model' => $model,
            ]);

    }

    /**
     * Updates an existing TbHotLink model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if (isset($_FILES) && !empty($_FILES)) {
                $slug     = Inflector::slug($model->name);
                $fileName = $slug . '-' . CommonLib::getRandomInt(10);
                $model->image = UploadedFile::getInstance($model, 'image');
                if (!empty($model->image) && $model->validate(['image'])) {
                    $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                }
            }
            if(empty($model->image)) {
                $model->image = $model->oldAttributes['image'];
            }
            $model->price =  CommonLib::toInt($model->price);
            if($model->save()){
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /* clearn image
         * */
    public function actionClearImage($id)
    {
        $model = TbHotLink::findOne($id);

        if ($model === null) {
            $this->flash('error', 'Not found');
        } else {
            $model->image = '';
            if ($model->update()) {
                @unlink(Yii::getAlias('@upload_dir') . $model->image);
                $this->flash('success', 'Image cleared');
            } else {
                $this->flash('error', 'Update error.');
            }
        }
        return $this->back();
    }

    /**
     * Deletes an existing TbHotLink model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TbHotLink model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TbHotLink the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbHotLink::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
