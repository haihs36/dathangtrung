<?php
    namespace common\components;
    use common\models\TbComplain;
    use common\models\TbOrders;
    use common\models\TbOrdersSession;
        use common\models\LoginMember;
    use Yii;
    use yii\helpers\Url;

    class APPController extends \yii\web\Controller
    {
        public $error = null;
        public $title = null;
        public $setting;
        public $upload_image;
        public $upload_thumb;
        public $orderStatus;
        public $complainStatus;
        public $enableCsrfValidation = false;
        public $totalPriceUser = 0;
        public $CNY = 0;


        public function beforeAction($action)
        {
            $this->layout = '@app/views/layouts/customer';

            if (!parent::beforeAction($action))
                return false;


            $uerLogin = Yii::$app->session->get('uerLogin');
            if(!empty($uerLogin) && Yii::$app->user->isGuest){
                Yii::$app->user->login($uerLogin, LoginMember::EXPIRE_TIME);
                $this->setReturnUrl();
                return true;
            }

            if(Yii::$app->user->isGuest){
                return $this->redirect(['site/login'])->send();
            }
            else{
                $user = Yii::$app->user->identity;
                //update cache user login
                if ($user->expire_at < time()) {
                    \Yii::$app->user->logout();
                    $cache = \Yii::$app->cache;
                    $cache->flush();
                    return $this->redirect(['site/login'])->send();
                }

             
                Yii::$app->session->set('uerLogin',$user);

                $this->upload_image = Yii::$app->params['UPLOAD_IMAGE'];
                if($this->setting === null){
                    $this->setting = CommonLib::getAllSettings();
                }
                $this->CNY =  CommonLib::getCNY($this->setting['CNY'],Yii::$app->user->identity->cny);

                $this->orderStatus = \common\models\TbOrders::getOrderCount(null,true);
                $complain = TbComplain::find()->select(['id', 'status'])->where(['customerID' => \Yii::$app->user->id])->asArray()->all();
                $complainStatus = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0];
                if ($complain) {
                    foreach ($complain as $item) {
                        if ($item['status'] == 1) $complainStatus[1]++;
                        if ($item['status'] == 2) $complainStatus[2]++;
                        if ($item['status'] == 3) $complainStatus[3]++;
                        if ($item['status'] == 4) $complainStatus[4]++;
                        $complainStatus[0]++;
                    }
                }
                $this->complainStatus = $complainStatus;


                if($action->id === 'index'){
                    $this->setReturnUrl();
                }

                return true;
            }
        }

        public function back()
        {
            return $this->redirect(Yii::$app->request->referrer);
        }

        /**
         * Set return url for module in sessions
         * @param mixed $url if not set, returnUrl will be current page
         */
        public function setReturnUrl($url = null)
        {
            Yii::$app->getSession()->set($this->module->id . '_return', $url ? Url::to($url) : Url::current());
        }

        /**
         * Get return url for module from session
         * @param mixed $defaultUrl if return url not found in sessions
         * @return mixed
         */
        public function getReturnUrl($defaultUrl = null)
        {
            return Yii::$app->getSession()->get($this->module->id . '_return', $defaultUrl ? Url::to($defaultUrl) : Url::to('/admin/' . $this->module->id));
        }

        public function formatResponse($success = '', $back = true)
        {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if ($this->error) {
                    return ['result' => 'error', 'error' => $this->error];
                } else {
                    $response = ['result' => 'success'];
                    if ($success) {
                        if (is_array($success)) {
                            $response = array_merge(['result' => 'success'], $success);
                        } else {
                            $response = array_merge(['result' => 'success'], ['message' => $success]);
                        }
                    }
                    return $response;
                }
            } else {
                if ($this->error) {
                    $this->flash('error', $this->error);
                } else {
                    if (is_array($success) && isset($success['message'])) {
                        $this->flash('success', $success['message']);
                    } elseif (is_string($success)) {
                        $this->flash('success', $success);
                    }
                }

                return $back ? $this->back() : $this->refresh();
            }
        }

        public function flash($type, $message)
        {
            Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
        }

    }