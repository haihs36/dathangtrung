<?php

namespace frontend\controllers;

use common\components\CaptchaAction;
use common\components\CController;
use common\components\CommonLib;
use common\helpers\GoogleTranslate;
use common\models\Custommer;
use common\models\LoginMember;
use common\models\SearchCode;
use common\models\TbOrders;
use common\models\TbOrdersSession;
use common\models\TbOrderSupplier;
use common\models\TbShippers;
use common\models\TbShipping;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use yii\base\InvalidParamException;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use Yii;
use common\models\Bag;
/**
 * Site controller
 */
class SiteController extends CController
{
    public $successUrl;

    public function actions()
    {
        return [
            'auth'    => [
                'class'           => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'AuthSuccess'],
            ],
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
//                'class'           => 'yii\captcha\CaptchaAction',
                'class' => CaptchaAction::className(), // change this as well in case of moving the class
               // 'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }



    public function behaviors()
    {
        return array_merge(parent::behaviors(), [

            // For cross-domain AJAX request
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    // restrict access to domains:
                    'Origin' => CommonLib::allowedDomains(),
                      'Access-Control-Request-Method'    => ['GET','POST'],
                      'Access-Control-Allow-Credentials' => true,
                      'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
                ],
            ],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        if (!self::checkOrigin()) {
            return false;
        }


       /* $id =   (int)TbOrders::find()->select(['orderID'=>'( MAX(`orderID`)+ 1) '])->one()->orderID;
        if(!$id)
            $id = 1;

        var_dump($id);die;*/


        $searchModel = new SearchCode();
        \frontend\widgets\SeoMeta::widget(); //set seo default
        return $this->render('index', [
            'searchModel' => $searchModel
        ]);
    }

    public static function checkOrigin()
    {
        $allowOrigin = [Yii::$app->params['baseUrl']];
        $origin      = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        if ($origin == '') {
            return true;
        } elseif (in_array($origin, $allowOrigin)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 3600');
            return true;
        }
        return false;
    }

    public function actionSearchcode()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $scode = Yii::$app->request->post('scode');
                $scode = trim($scode);
                if (!empty($scode) && strlen($scode) <= 20) {
                    $cache = \Yii::$app->cache;
                    $key   = 'Key-search-' . Html::encode(trim($scode));
                    $result = $cache->get($key);
                    if ($result === false) {
                        $shipping           = TbShipping::find()->select('a.shopID,a.`shippingCode`,a.`city`,a.`createDate`,o.identify,s.orderID')
                            ->from(TbShipping::tableName() . ' a')
                            ->leftJoin(TbOrderSupplier::tableName() . ' s', 'a.shopID = s.id')
                            ->leftJoin(TbOrders::tableName() . ' o', 's.orderID = o.orderID')
                            ->where(['shippingCode' => $scode])->asArray()->limit(5)->all();

                        $shipper            = TbShippers::find()->select('shippingCode,shippingStatus,status,createDate')->where(['shippingCode' => $scode])->asArray()->limit(5)->all();
                        $result['shipping'] = $shipping;
                        $result['shipper']  = $shipper;
                        $cache->set($key, $result, \Yii::$app->params['CACHE_TIME']['HOUR']);
                    }

                    $content = $this->renderPartial('_search_code', [
                        'result' => $result
                    ]);

                    return ['data'=>$content];
                }
            }
        }


        return ['data'=>false];
    }

    public function actionSearch()
    {
        if (!self::checkOrigin()) {
            return false;
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                ini_set('default_charset', 'UTF-8');

                $keyword  = Yii::$app->request->post('keyword');
                $type  = Yii::$app->request->post('type');

                if(!empty($keyword) && !empty($type)){
                    switch ($type){
                        case 1:
                            $website = 'https://s.taobao.com/search?q=';
                            $source = 'vi';
                            $target = 'zh-CN';
                            $text = trim($keyword);
                            $trans = new GoogleTranslate();
                            $result = $trans->translate($source, $target, $text);
                            return  array('status' => 200, 'content' => 'thành công', 'url' => $website . $result);
                            break;
                        case 2:
                            $website = 'https://s.1688.com/selloffer/offer_search.htm?_input_charset=utf-8&button_click=top&earseDirect=false&n=y&keywords=';
                            $source = 'vi';
                            $target = 'zh-CN';
                            $text = trim($keyword);
                            $trans = new GoogleTranslate();
                            $result = $trans->translate($source, $target, $text);
                            return  array('status' => 200, 'content' => 'thành công', 'url' => $website . urlencode($result));
                            break;
                        case 3:
                            $website = 'https://list.tmall.com/search_product.htm?type=p&spm=a220m.1000858.a2227oh.d100&from=.list.pc_1_searchbutton&q=';
                            $source = 'vi';
                            $target = 'zh-CN';
                            $text = trim($keyword);
                            $trans = new GoogleTranslate();
                            $result = $trans->translate($source, $target, $text);

                            return  array('status' => 200, 'content' => 'thành công', 'url' => $website . $result);
                            break;
                    }
                }

            }
        }


        return isset($data) ? $data : [];
    }

    public function actionConfig()
    {
        $setting = $this->setting;
        header('Access-Control-Allow-Origin: *');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            "exchange_rate"       => $setting['CNY'],
            "hotline"             => $setting['hotline'],
            "service_cost_1688"   => 12,
            "service_cost_taobao" => 10,
            "service_cost_tmall"  => 10,
        ];
    }

    public function actionGetCart()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $number_cart                = 0;
        if (\Yii::$app->request->isAjax) {
            if (!\Yii::$app->user->isGuest) {
                $number_cart = TbOrdersSession::find()->select('id')->where(['customerID' => \Yii::$app->user->id])->count();
                Yii::$app->session->set('num_cart', $number_cart);
            }
        }

        echo $number_cart;
    }

    public function actionGetInfo()
    {
        header('Access-Control-Allow-Origin: *');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['code' => 0];
        } else {
            return [
                'code'     => 1,
                'customer' => Yii::$app->user->identity->username
            ];
        }
    }

    /**
     * This function will be triggered when user is successfuly authenticated using some oAuth client.
     *
     * @param yii\authclient\ClientInterface $client
     * @return boolean|yii\web\Response
     */
    public function AuthSuccess($client)
    {
        $authclient = Yii::$app->request->get('authclient');
        // get user data from client
        $userAttributes = $client->getUserAttributes();
        $dataUser       = [];
        switch ($authclient) {
            case 'facebook':
                $email                = trim($userAttributes['email']);
                $dataUser['username'] = $email;
                $dataUser['fullname'] = $userAttributes['name'];
                $dataUser['email']    = $email;
                $dataUser['gender']   = isset($userAttributes['gender']) ? $userAttributes['gender'] : '';
                $dataUser['avatar']   = $userAttributes['picture']['data']['url'];
                break;
            case 'google':
                $email                = isset($userAttributes['emails'][0]['value']) ? trim($userAttributes['emails'][0]['value']) : '';
                $dataUser['username'] = $email;
                $dataUser['fullname'] = $userAttributes['displayName'];
                $dataUser['gender']   = isset($userAttributes['gender']) ? $userAttributes['gender'] : '';
                $dataUser['avatar']   = $userAttributes['image']['url'];
                $dataUser['email']    = $email;
                break;
        }

        if (!empty($dataUser['email'])) {
            $customer = Custommer::findByUsername($dataUser['email']);
            if (!empty($customer)) {
                Yii::$app->user->login($customer);
                return $this->redirect(['orders/index']);
            } else {
                //save attribute user
                $user                  = new Custommer();
                $user->scenario        = 'register';
                $user->username        = $dataUser['username'];
                $user->gender          = $dataUser['gender'];
                $user->fullname        = $dataUser['fullname'];
                $user->email           = $dataUser['email'];
                $user->avatar          = $dataUser['avatar'];
                $user->status          = Custommer::STATUS_ACTIVE;
                $password              = Yii::$app->security->generateRandomString(10);
                $user->password_hidden = CommonLib::encryptIt($password);
                $user->setPassword($password);
                $user->password = $password;
                $user->generateAuthKey();
                if ($user->save()) {
                    Yii::$app->user->login($user);
                    return $this->redirect(['orders/index']);
                    /*$temp
                                  = "Dear %s,<br/>Chào mừng bạn đã sử dụng dịch vụ của . <br/>
                                Tên đăng nhập và mật khẩu của bạn tại website <b>" . Yii::$app->params['SITE_NAME'] . "</b><br/> Username: <b>%s</b><br/>password: <b>%s</b>";
                    $mail_content = sprintf($temp, $user->fullname, $user->username, $password, Yii::$app->params['baseUrl']);
                    ContactForm::sendEmail('Thông tin đăng nhập tài khoản ', $user->email, $mail_content);*/
                } else {
                    \Yii::$app->session->setFlash('success', 'Tài khoản của bạn không hợp lệ. Vui lòng liên hệ lại với chúng tôi để được xử lý.');
                    return $this->redirect(['site/notifycation']);
                }
            }
        } else {
            \Yii::$app->session->setFlash('success', 'Tài khoản của bạn không hợp lệ. Vui lòng liên hệ lại với chúng tôi để được xử lý.');
            return $this->redirect(['site/notifycation']);
        }
    }

    public function actionNotifycation()
    {
        $data_seo['title'] = 'Thông báo |  LOGISTICS';
        \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);
        return $this->render('notify');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = 'login';
        $data_seo['title'] = 'Đăng nhập | Logistics';
        \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);

        if (!Yii::$app->user->isGuest) {
            $this->redirect(\Yii::$app->user->getReturnUrl());
        }

        $model = new LoginMember();
        if ($model->load(\Yii::$app->request->post())) {
            $grecaptcha = Yii::$app->request->post("g-recaptcha-response");
            if(!CommonLib::verifyCaptcha($grecaptcha)){
                Yii::$app->session->setFlash('danger','Mã captcha không hợp lệ.');

                return $this->render('login', [
                    'model' => $model,
                ]);
            }
            
            if($model->login()){

                    //update cache user login
                CommonLib::updateUserIdentify();


                return $this->redirect(['user/dashboard']);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);

    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $this->layout = 'login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $data_seo['title'] = 'Đăng ký |  LOGISTICS';
        \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);

        $model           = new SignupForm();
        if ($model->load(\Yii::$app->request->post())) {

            $grecaptcha = Yii::$app->request->post("g-recaptcha-response");

            if(!CommonLib::verifyCaptcha($grecaptcha)){
                Yii::$app->session->setFlash('danger','Mã captcha không hợp lệ.');

                return $this->render('signup', [
                    'model' => $model,
                ]);

            }

            if ($model->validate()) {
                
                $cus_id = str_pad(rand(0,99999), 3, "0", STR_PAD_LEFT);

                $id = Custommer::find()->select(['id'=>'( MAX(`id`)+ 1) '])->one()->id;
                if(is_null($id))
                    $id = 1;






                $custommer           = new Custommer();
                $custommer->scenario = 'register';
                $custommer->identify = 'A'.$cus_id.$id;
                $custommer->username = $model->username;
                $custommer->phone    = $model->phone;
                $custommer->fullname = $model->fullname;
                $custommer->email    = $model->email;
                $custommer->status   = Custommer::STATUS_ACTIVE;
                $custommer->password = $model->password;
                $custommer->password_hidden = CommonLib::encryptIt($model->password);
                $custommer->setPassword($model->password);
                $custommer->generateAuthKey();
                if ($custommer->save()) {
                    Yii::$app->session->setFlash('success','Chúc mừng bạn đã đăng ký thành công! Vui lòng click <a href="'.Url::toRoute(['site/login']).'" title="đăng nhập">Vào đây</a> để đăng nhập hệ thống.');
                    return $this->redirect(['site/notifycation']);
                }else{
                    var_dump($custommer->getErrors());die;
                }
            }
        }



        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionForgotPassword()
    {
        $model = new PasswordResetRequestForm();
        if (\Yii::$app->request->isAjax) {
            if ($model->load(\Yii::$app->request->post())) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                if (ActiveForm::validate($model)) {
                    return ['rs' => 'error', 'status' => 1, 'mess' => ActiveForm::validate($model)];
                }
                if ($model->sendEmail()) {
                    return ['rs' => 'success', 'status' => 0, 'mess' => 'Hãy kiểm tra email của bạn để được hướng dẫn thêm.'];
                }

                return ['rs' => 'error', 'status' => 0, 'mess' => 'Chúng tôi đã gửi một thống báo tới địa chỉ email của bạn. Vui lòng xác nhận lại.'];
            } else {
                return $this->renderAjax('forgot-password', [
                    'model' => $model,
                ]);
            }
        }

        throw new \yii\web\NotFoundHttpException("Your Error Message.");
    }


    /**
     * Resets new password
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            \Yii::$app->session->setFlash('success', 'Thay đổi mật khâu thành công! Hãy click <a class="use-modal" rel="/login" href="javascript:void(0)">Vào đây</a> để đăng nhập');
            return $this->redirect(['site/notifycation']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        
        \Yii::$app->user->logout();
        $cache = \Yii::$app->cache;
        $cache->flush();
        
        return $this->goHome();
    }

}
