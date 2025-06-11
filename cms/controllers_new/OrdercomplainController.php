<?php

namespace cms\controllers;

use common\models\AccessRule;
use Yii;
use cms\models\TbOrderComplain;
use cms\models\TbOrderComplainSearch;
use common\components\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * OrdercomplainController implements the CRUD actions for TbOrderComplain model.
 */
class OrdercomplainController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class'      => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only'       => ['index', 'view', 'delete','update'],
                'rules'      => [
                    [
                        'actions' => ['index', 'view', 'delete','update'],
                        'allow'   => true,
                        'roles'   => [ADMIN],
                    ],
                    [
                        'actions' => [],
                        'allow'   => false,
                        'roles'   => [BUSINESS,WAREHOUSE],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TbOrderComplain models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TbOrderComplainSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TbOrderComplain model.
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
     * Creates a new TbOrderComplain model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TbOrderComplain();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TbOrderComplain model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
     * Deletes an existing TbOrderComplain model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TbOrderComplain model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TbOrderComplain the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbOrderComplain::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
