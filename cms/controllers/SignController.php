<?php

    namespace cms\controllers;



    use common\components\CommonLib;

    use common\models\LoginForm;

    use common\models\User;

    use common\models\UserMongo;



    class SignController extends \yii\web\Controller

    {

        public $enableCsrfValidation = false;



        public function actionIn()

        {



            $this->layout = 'login';



            if(!\Yii::$app->user->isGuest){

                return $this->redirect(\Yii::$app->user->getReturnUrl());

            }



            $model = new LoginForm();

            if (($model->load(\Yii::$app->request->post()))) {


                if($model->login()){

                    return $this->redirect(\Yii::$app->user->getReturnUrl());

                }

            }



            return $this->render('in', [

                'model' => $model,

            ]);

        }



        public function actionOut()

        {

            \Yii::$app->user->logout();

            return $this->redirect(\Yii::$app->homeUrl);

        }

    }