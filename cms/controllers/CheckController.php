<?php

namespace cms\controllers;

use common\models\AccessRule;
use common\models\CheckSearch;
use common\models\Check;
use Yii;
/*use yii\web\Controller;*/

use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * KgController implements the CRUD actions for Check model.
 */
class CheckController extends \common\components\Controller
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
     * Lists all Check models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CheckSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Check model.
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
     * Creates a new Check model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Check();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Check model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->flash('success','Cập nhật thành công');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Check model.
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
     * Finds the Check model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Check the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Check::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
