<?php

namespace cms\controllers;

use cms\models\TbSetting;
use common\components\CommonLib;
use common\models\AccessRule;
use common\models\User;
use Yii;
use cms\models\TbSettings;
use cms\models\TbSettingsSearch;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SettingController implements the CRUD actions for TbSettings model.
 */
class SettingController extends \common\components\Controller
{
    /* public function behaviors()
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
                    'only'       => ['index', 'view', 'create', 'edit','update', 'delete','list'],
                    'rules'      => [
                        [
                            'allow'   => false,
                            'roles'   => [
                                User::ROLE_USER
                            ],
                        ],
                        [
                            'actions' => ['index', 'view', 'create', 'edit','update', 'delete','list'],
                            'allow'   => true,
                            'roles'   => [
                                User::ROLE_ADMIN,
                                User::ROLE_CLERK,
                            ],
                        ],
                    ],
                ],
            ];
        }*/

    /**
     * Lists all TbSettings models.
     * @return mixed
     */
    public function actionIndex()
    {

        $model      = new TbSettings();
        $results    = $model->find();
        $pagination = new Pagination(['defaultPageSize' => 20, 'totalCount' => $results->count()]);
        $results    = $results->offset($pagination->offset)->limit($pagination->limit)->orderBy('id')->all();
        return $this->render('index', ['results' => $results, 'model' => $model, 'pagination' => $pagination]);
    }

    public function actionList()
    {
        $searchModel  = new TbSettingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * To edit the record information (User Profile)
     * @param long $id : To get the particular user's id
     * @return : the view of edit User form
     */
    public function actionEdit($id = null)
    {
        $model = TbSettings::findOne(['id' => $_POST['id']]);
        if (isset($model) && !empty($model)) {
            $model->value               = $_POST['value'];
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $model->save(false) ? ['status' => 'success'] : ['status' => 'failure'];
        } else {
            Yii::$app->session->setFlash("danger", 'Invalid Setting', true);
            $this->refresh();
        }
    }

    /**
     * Displays a single TbSettings model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TbSettings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TbSettings();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->flash('success', 'Tạo mới thành công.');
            return $this->redirect(['create']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TbSettings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $page = null)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['list', 'page' => $page]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionStatus()
    {
        $params = Yii::$app->request->post();
        return $this->changeStatus(TbSettings::className(), $params['id'], 'status');
    }

    /**
     * Deletes an existing TbSettings model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['list']);
    }

    /**
     * Finds the TbSettings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TbSettings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbSettings::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
