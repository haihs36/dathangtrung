<?php

    namespace cms\controllers;

    use common\models\AccessRule;
    use common\models\User;
    use Yii;
    use common\models\TbComplainType;
    use common\models\TbComplainTypeSearch;

    use yii\filters\AccessControl;
    use yii\web\NotFoundHttpException;

    /**
     * ComplainTypeController implements the CRUD actions for TbComplainType model.
     */
    class ComplainTypeController extends \common\components\Controller
    {

      /*  public function behaviors()
        {

            return [
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
         * Lists all TbComplainType models.
         * @return mixed
         */
        public function actionIndex()
        {
            $searchModel  = new TbComplainTypeSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        /**
         * Displays a single TbComplainType model.
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
         * Creates a new TbComplainType model.
         * If creation is successful, the browser will be redirected to the 'view' page.
         * @return mixed
         */
        public function actionCreate()
        {
            $model = new TbComplainType();

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }

        /**
         * Updates an existing TbComplainType model.
         * If update is successful, the browser will be redirected to the 'view' page.
         * @param string $id
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
         * Deletes an existing TbComplainType model.
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
         * Finds the TbComplainType model based on its primary key value.
         * If the model is not found, a 404 HTTP exception will be thrown.
         * @param string $id
         * @return TbComplainType the loaded model
         * @throws NotFoundHttpException if the model cannot be found
         */
        protected function findModel($id)
        {
            if (($model = TbComplainType::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
    }
