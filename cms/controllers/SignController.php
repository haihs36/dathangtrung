<?php
    namespace cms\controllers;

    use common\components\CommonLib;
    use common\models\LoginForm;

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
                // $grecaptcha = \Yii::$app->request->post("g-recaptcha-response");
                // if(!CommonLib::verifyCaptcha($grecaptcha)){
                //     \Yii::$app->session->setFlash('danger','Mã captcha không hợp lệ.');

                //     return $this->render('in', [
                //         'model' => $model,
                //     ]);
                // }

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