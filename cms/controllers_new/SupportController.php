<?php

namespace cms\controllers;

use common\components\CommonLib;
use common\helpers\Image;
use common\models\AccessRule;
use common\models\Photo;
use common\models\User;
use Yii;
use common\models\TbSupport;
use common\models\TbSupportSearch;
use common\components\Controller;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * SupportController implements the CRUD actions for TbSupport model.
 */
class SupportController extends Controller
{
   /* public function behaviors()
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
                'only'       => ['index', 'view', 'create', 'update', 'delete','clear-image'],
                'rules'      => [
                    [
                        'actions' => [],
                        'allow'   => false,
                        'roles'   => [
                            User::ROLE_USER,
                        ],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete','clear-image'],
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
     * Lists all TbSupport models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TbSupportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TbSupport model.
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
     * Creates a new TbSupport model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TbSupport();
        if ($model->load(Yii::$app->request->post())) {
            if (isset($_FILES)) {
                $slug     = Inflector::slug($model->name);
                $fileName = $slug . '-' . CommonLib::getRandomInt(10);
                $model->image = UploadedFile::getInstance($model, 'image');
                if (!empty($model->image) && $model->validate(['image'])) {
                    $model->image = Image::upload($model->image, $this->upload_image, null, null, false, $fileName);
                }
                $model->thumb = UploadedFile::getInstance($model, 'thumb');
                if (!empty($model->thumb) && $model->validate(['thumb'])) {
                    $model->thumb = Image::upload($model->thumb, $this->upload_image, null, null, false, 'thumb'.$fileName);
                }
            }
            if($model->save()){
                return $this->redirect(['index']);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing TbSupport model.
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
                $model->thumb = UploadedFile::getInstance($model, 'thumb');
                if (!empty($model->thumb) && $model->validate(['thumb'])) {
                    $model->thumb = Image::upload($model->thumb, $this->upload_image, null, null, false, 'thumb'.$fileName);
                }

            }
            if(empty($model->image)) {
                $model->image = $model->oldAttributes['image'];
            }
            if(empty($model->thumb)) {
                $model->thumb = $model->oldAttributes['thumb'];
            }

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
    public function actionClearImage($id=null,$type=null)
    {
       if(is_null($id)) $this->refresh();
        $model = TbSupport::findOne($id);

        if ($model === null) {
            $this->flash('error', 'Not found');
        } else {
            if($type=='image'){
                $model->image = '';
            }else if($type=='thumb'){
                $model->thumb = '';
            }

            if ($model->update()) {
                if($type=='image'){
                    @unlink(Yii::getAlias('@upload_dir') . $model->image);
                }else if($type=='thumb'){
                    @unlink(Yii::getAlias('@upload_dir') . $model->thumb);
                }
                $this->flash('success', 'Image cleared');
            } else {
                $this->flash('error', 'Update error. {0}', $model->formatErrors());
            }
        }
        return $this->back();
    }

    /**
     * Deletes an existing TbSupport model.
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
     * Finds the TbSupport model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TbSupport the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbSupport::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
