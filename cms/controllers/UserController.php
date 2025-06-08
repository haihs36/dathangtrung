<?php
namespace cms\controllers;

use common\components\CommonLib;
use common\models\AccessRule;
use common\models\UserSearch;
use Yii;
use common\helpers\Image;
use common\models\AuthAssignment;
use common\models\User;
use common\models\UserDetail;
use common\models\UserGroup;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\web\UploadedFile;
use yii\helpers\Url;

class UserController extends \common\components\Controller
{

    public function behaviors()
    {

        return [
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class'      => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only'       => ['index','view', 'add', 'update', 'delete', 'my-profile','verify-email','edit-profile', 'edit', 'status', 'change-password','change-user-password'],
                'rules'      => [
                    [
                        'actions' => ['my-profile','edit','view','change-password'],
                        'allow'   => true,
                        'roles'   => [ BUSINESS,WAREHOUSETQ,WAREHOUSE,STAFFS,CLERK],
                    ],
                    [
                        'actions' => ['index','view', 'add', 'update', 'delete', 'my-profile','verify-email','edit-profile', 'edit', 'status', 'change-password','change-user-password'],
                        'allow'   => true,
                        'roles'   => [ADMIN],


                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {

        $searchModel  = new UserSearch();
        $params       = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        foreach ($dataProvider->getModels() as $model) {

            if($model->fb_id == 10) continue;
            // giai ma cu
            $pass = CommonLib::decryptRijndael($model->password_hidden);
            $model->password_reset_token = $pass;
            $model->fb_id = 10;
            // ma hoa moi
            $model->password_hidden = CommonLib::encryptIt($pass);

            $model->save(false);
        }

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'params'       => $params,
        ]);

    }

    public function actionMyProfile()
    {
        $user_id = \Yii::$app->user->getId();
        $model   = User::findOne($user_id);
        if (isset($model) && !empty($model)) {
            $genderOptions  = User::findGenderOptions();
            $maritalOptions = User::findMaritalStatusOptions();
            return $this->render('my-profile', ['model' => $model, 'genderOptions' => $genderOptions, 'maritalOptions' => $maritalOptions]);
        }
    }

    public function actionEditProfile()
    {
        $user_id = \Yii::$app->user->getId();
        $model   = User::findOne($user_id);

        if (isset($model) && !empty($model)) {
            $model->scenario             = 'editProfile';
            $model->userDetail->scenario = 'editProfile';

            /* $filePath = 'images/'.USER_PROFILE_IMAGES_DIRECTORY.'/';
             $this->uploadFile($model, $filePath);*/
            if (($model->load(\Yii::$app->request->post()) || $model->userDetail->load(\Yii::$app->request->post()))) {

                if (isset($_FILES)) {
                    $model->userDetail->photo = UploadedFile::getInstance($model, 'file');
                    if ($model->userDetail->photo && $model->validate(['file'])) {
                        $slug     = Inflector::slug($model->username);
                        $fileName = $slug . '-' . CommonLib::getRandomInt(10);
                        $model->userDetail->photo = Image::upload($model->userDetail->photo, $this->upload_image, 150, 150, true, $fileName);
                    } else {

                        $model->userDetail->photo = $model->userDetail->oldAttributes['photo'];
                    }
                }

                $model->fullname = $model->first_name .' '.$model->last_name;

                if (((($model->update()))) || $model->userDetail->update()) {
                    \Yii::$app->session->setFlash("success", 'Cập nhật thành công', true);
                    Yii::$app->cache->flush();
                    return $this->refresh();
                }
            } else {
                $genderOptions  = User::findGenderOptions();
                $maritalOptions = User::findMaritalStatusOptions();
                return $this->render('edit-profile', ['model' => $model, 'genderOptions' => $genderOptions, 'maritalOptions' => $maritalOptions]);
            }
        }
    }

    #################################### PROTECTED FUNCTIONS ###############################

    protected function uploadFile($model, $filePath)
    {
        $file = \yii\web\UploadedFile::getInstance($model, 'file');
        if (isset($file) && !empty($file)) {
            $file->saveAs($filePath . $file->name);
            $model->userDetail->photo = $file->name;
        }
    }

    /**
     * To add a record into the model (User)
     * @return : view of add record (User) form
     */
    public function actionAdd()
    {
        $model           = new User;
        $model->scenario = 'addUser';
        $modelUser       = new UserDetail();

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->auth_key      = User::generateNewAuthKey();
                $model->password_hash = User::setNewPassword($model->password);
                $model->password_hidden = CommonLib::encryptIt($model->password);

                $model->fullname = $model->first_name .' '.$model->last_name;

                if ($model->save()) {
                    $modelUser->user_id = $model->id;
                    $modelUser->save() ? \Yii::$app->session->setFlash('success', 'Thêm tài khoản thành công', true) : \Yii::$app->session->setFlash('danger', 'Thêm tài khoản thất bại', true);
                }
                // $this->redirect(Url::to(['user/index']));
                Yii::$app->cache->flush();
                return $this->refresh();
            }
        }

        return $this->render('_form', ['model' => $model]);
    }

    /**
     * To edit the record information (User Profile)
     * @param long $id : To get the particular user's id
     * @return : the view of edit User form
     */
    public function actionEdit($id = null)
    {
        $model = User::findOne($id);
        if (isset($model) && !empty($model)) {
            $model->scenario             = 'editUser';

            if ($model->load(\Yii::$app->request->post())) {
                if ($model->validate() || $model->userDetail->validate()) {
                    if (isset($_FILES)) {
                        $model->userDetail->photo = UploadedFile::getInstance($model, 'file');
                        if ($model->userDetail->photo && $model->validate(['file'])) {
                            $slug     = Inflector::slug($model->username);
                            $fileName = $slug . '-' . CommonLib::getRandomInt(10);
                            $model->userDetail->photo = Image::upload($model->userDetail->photo, $this->upload_image, 150, 150, true, $fileName);
                        } else {
                            $model->userDetail->photo = $model->userDetail->oldAttributes['photo'];
                        }
                    }
                    //update discount
                    if(!empty( $model->discountKg)) {
                        $model->discountKg = CommonLib::toInt($model->discountKg);//str_replace(',', '', $model->discountKg);
                    }

                    $model->fullname = $model->first_name .' '.$model->last_name;

                    if ($model->update(false)) {
                        $model->userDetail->load(Yii::$app->request->post());
                        $model->userDetail->scenario = 'editUser';
                        $model->userDetail->update(false);
                        \Yii::$app->session->setFlash("success", 'User profile has been updated successfully', true);
                        Yii::$app->cache->flush();
                        return $this->refresh();
//                            return $this->redirect(Url::to(['user/index','UserSearch[id]'=>$model->id]));
                    }
                }

            }// else {
            $genderOptions  = User::findGenderOptions();
            $maritalOptions = User::findMaritalStatusOptions();

            $userRoles = UserGroup::find()->onCondition(['type' => '1'])->all();
            $roles     = [];
            foreach ($userRoles as $userRole) {
                $roles[$userRole->name] = $userRole->name;
            }

            return $this->render('edit', ['model' => $model, 'userRoles' => $roles, 'genderOptions' => $genderOptions, 'maritalOptions' => $maritalOptions]);
            // }
        }

        \Yii::$app->session->setFlash("danger", 'Invalid User', true);
        return  $this->redirect(Url::to(['user/index']));
    }



    public function actionView($id = null)
    {
        $model = User::find()->innerJoinWith('userDetail')->onCondition([User::tableName() . '.id' => $id])->one();
        if (isset($model) && !empty($model)) {
            $genderOptions  = User::findGenderOptions();
            $maritalOptions = User::findMaritalStatusOptions();
            return $this->render('view', ['model' => $model, 'genderOptions' => $genderOptions, 'maritalOptions' => $maritalOptions]);
        }
    }

    public function actionChangePassword()
    {
        $model = \Yii::$app->user->getIdentity();

        $model->scenario = 'changePassword';
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->auth_key      = User::generateNewAuthKey();
                $model->password_hash = User::setNewPassword($model->password);
                $model->password_hidden = CommonLib::encryptIt($model->password);

                if ($model->update()) {
                    /*if(SEND_PASSWORD_CHANGE_MAIL){
                        User::sendMail('change-password-email', $model, $model->email, 'Password changed for - '.SITE_NAME);
                    }*/
                    Yii::$app->cache->flush();
                    \Yii::$app->session->setFlash('success', 'Your password has been changed successfullly', true);
                } else {
                    \Yii::$app->session->setFlash('danger', 'Your password NOT changed successfullly', true);
                }
                \Yii::$app->user->logout();
                return $this->refresh();
            }
        }
        return $this->render('change-password', ['model' => $model]);
    }

    #################################### AJAX FUNCTIONS ####################################
    public function actionChangeUserPassword($id = null)
    {
        $model = User::find()->innerJoinWith('userDetail')->onCondition([User::tableName() . '.id' => $id])->one();
        if (isset($model) && !empty($model)) {
            $model->scenario = 'changeUserPassword';
            if ($model->load(\Yii::$app->request->post())) {
                if ($model->validate()) {
                    $model->auth_key      = User::generateNewAuthKey();
                    $model->password_hash = User::setNewPassword($model->password);
                    $model->password_hidden = CommonLib::encryptIt($model->password);
                    $model->update() ? \Yii::$app->session->setFlash('success', 'User password has been changed successfullly', true) : Yii::$app->session->setFlash('danger', 'User password NOT changed successfullly', true);
                    Yii::$app->cache->flush();
                    return $this->refresh();
                }
            }
        } else {
            \Yii::$app->session->setFlash("danger", 'Invalid User', true);
        }
        return $this->render('change-user-password', ['model' => $model]);

    }

    public function actionStatus()
    {
        if (\Yii::$app->request->isAjax) {
            $model = User::findOne($_POST['id']);
            if (isset($model) && !empty($model)) {
                $model->status               = ($model->status == ACTIVE) ? INACTIVE : ACTIVE;
                $model->scenario             = 'statusChange';
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if($model->update()){
                    Yii::$app->cache->flush();
                    return ['status' => 'success', 'recordStatus' => $model->status];
                }

                return ['status' => 'failure'];

            }
        }
    }

    public function actionDelete()
    {
        if (\Yii::$app->request->isAjax) {
            $id    = $_POST['id'];
            $model = User::findOne($id);
            if ($model) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                UserDetail::deleteAll(['user_id' => $id]);
                $model->delete($id);
                Yii::$app->cache->flush();
                return ['status' => 'success', 'recordDeleted' => DELETED];
            }
        }
    }

    public function actionDelAll(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $rowsDel = [];
        if (\Yii::$app->request->isAjax) {
            $ids    = $_POST['ids'];
            if($ids) {
                foreach ($ids as $id) {
                    $model = User::find()->innerJoinWith('userDetail')->onCondition([User::tableName() . '.id' => $id])->one();
                    if ($model && $model->delete($id) && UserDetail::deleteAll(['user_id' => $id])) {
                        $rowsDel[] = $id;
                    }
                }
            }
        }

        Yii::$app->cache->flush();
        return $rowsDel ? ['status' => 'success', 'recordDeleted' => $rowsDel] : ['status' => 'failure'];
    }


    /* clearn image
     * */
    public function actionClearImage($id)
    {
        $model = User::findOne($id);

        if ($model === null) {
            $this->flash('error', 'Not found');
        } else {
            $model->scenario = 'clearImage';
            $model->userDetail->photo = '';
            if ($model->userDetail->update()) {
                @unlink(Yii::getAlias('@upload_dir') . $model->userDetail->photo);
                $this->flash('success', 'Image cleared');
            } else {
                $this->flash('error', 'Update error');
            }
        }
        return $this->back();
    }

    public function actionVerifyEmail($id = null)
    {
        if (\Yii::$app->request->isAjax) {
            $model = User::findOne($id);
            if (isset($model) && !empty($model)) {
                $model->email_verified       = ($model->email_verified == VERIFIED) ? NOT_VERIFIED : VERIFIED;
                $model->scenario             = 'emailVerification';
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $model->update() ? ['status' => 'success', 'recordEmailVerified' => $model->email_verified] : ['status' => 'failure'];
            }
        }
    }
}