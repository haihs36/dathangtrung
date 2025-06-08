<?php

    namespace cms\controllers;

    class HspanelController extends \common\components\Controller
    {

        public function actionDashboard()
        {

            $role = \Yii::$app->user->identity->role;
            switch ($role){
                case WAREHOUSE:
                case WAREHOUSETQ:
                    return $this->redirect(['shipping/index']);
                    break;
                case CLERK:
                    return $this->redirect(['chart/index']);
                    break;
                default:
                    return $this->render('dashboard');

            }

        }

    }

