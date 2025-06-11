<?php

namespace cms\controllers;

use common\models\AccessRule;
use common\models\TbOrders;
use Yii;
use common\models\TbOrdersMessage;
use common\models\TbOrdersMessageSearch;
use common\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * MessageController implements the CRUD actions for TbOrdersMessage model.
 */
class MessageController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class'      => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only'       => ['index', 'view', 'delete','create','update'],
                'rules'      => [
                    [
                        'actions' => [],
                        'allow'   => false,
                        'roles'   => [WAREHOUSE,WAREHOUSETQ],
                    ],
                    [
                        'actions' => ['index', 'view', 'delete','update','create'],
                        'allow'   => true,
                        'roles'   => [BUSINESS, ADMIN]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TbOrdersMessage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TbOrdersMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TbOrdersMessage model.
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
     * Creates a new TbOrdersMessage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new TbOrdersMessage();
        if (\Yii::$app->request->isAjax) {
            if ($model->load(\Yii::$app->request->post())) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                if (ActiveForm::validate($model)) {
                    return ActiveForm::validate($model);//['rs' => 'error', 'status' => 1, 'mess' => ActiveForm::validate($model)];
                }
                if($order = TbOrders::findOne($id)) {
                    $model->orderID = (int)$id;
                    $model->identify = $order->identify;
                    $model->userID  = Yii::$app->user->id;
                    if ($model->save()) {
                        return ['mess' => 'Gửi thông báo thành công!'];
                    }
                }
            } else {

                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TbOrdersMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['message/index']);
        } else {
            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TbOrdersMessage model.
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
     * Finds the TbOrdersMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TbOrdersMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TbOrdersMessage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
