<?php

namespace cms\controllers;

use common\models\AccessRule;
use common\models\TbKg;
use common\models\TbKgSearch;
use Yii;
/*use yii\web\Controller;*/

use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * KgController implements the CRUD actions for TbKg model.
 */
class KgController extends \common\components\Controller
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
     * Lists all TbKg models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TbKgSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TbKg model.
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
     * Creates a new TbKg model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TbKg();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TbKg model.
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
     * Deletes an existing TbKg model.
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
     * Finds the TbKg model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TbKg the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbKg::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
