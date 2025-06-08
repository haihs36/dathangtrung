<?php

namespace cms\controllers;

use common\models\AccessRule;
use Yii;
use common\models\Deposit;
use common\models\DepositSearch;
use common\components\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * DepositController implements the CRUD actions for Deposit model.
 */
class DepositController extends Controller
{
    public function behaviors()
    {

        return [
            'access' => [
                'class'      => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only'       => ['index','create', 'update', 'delete', 'status'],
                'rules'      => [
                    [
                        'actions' => [],
                        'allow'   => false,
                        'roles'   => [WAREHOUSE,BUSINESS,STAFFS],
                    ],
                    [
                        'actions' => ['index','create', 'update', 'delete', 'status'],
                        'allow'   => true,
                        'roles'   => [ADMIN],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Deposit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepositSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Deposit model.
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
     * Creates a new Deposit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Deposit();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Deposit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Deposit model.
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
     * Finds the Deposit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Deposit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Deposit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
