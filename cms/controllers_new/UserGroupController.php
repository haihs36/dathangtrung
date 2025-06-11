<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 19/03/2016
     * Time: 9:17 SA
     */

    namespace cms\controllers;

    use common\models\AccessRule;
    use common\models\AuthItem;
    use common\models\AuthItemChild;
    use common\models\User;
    use Yii;
    use common\models\UserGroup;
    use yii\data\Pagination;
    use yii\filters\AccessControl;
    use yii\filters\VerbFilter;
    use yii\helpers\Url;

    class UserGroupController extends \common\components\Controller
    {

        /*public function behaviors()
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
                    // We will override the default rule config with the new AccessRule class
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'only'       => ['index', 'add', 'update', 'delete', 'my-profile', 'edit', 'status', 'change-password'],
                    'rules'      => [
                        [
                            'actions' => ['change-password'],
                            'allow'   => true,
                            // Allow users, moderators and admins to create
                            'roles'   => [
                                User::ROLE_USER,
                                User::ROLE_ADMIN
                            ],
                        ],
                        [
                            'actions' => ['index', 'add', 'update', 'delete', 'my-profile', 'edit', 'status', 'change-password'],
                            'allow'   => true,
                            // Allow admins to update
                            'roles'   => [
                                User::ROLE_ADMIN
                            ],
                        ],
                    ],
                ],
            ];
        }*/

        #################################### ADMIN FUNCTIONS ####################################

        /*
         * To show all the records (Users) listing
         * return the view of listing of records (Users)
         */
        public function actionIndex()
        {
            $results    = UserGroup::find()->onCondition(['type' => '1']);   //Type 1 is for Role (Type 2 is for permission)
            $pagination = new Pagination(['defaultPageSize' => DEFAULT_PAGE_SIZE, 'totalCount' => $results->count()]);
            $results    = $results->offset($pagination->offset)->limit($pagination->limit)->orderBy('name')->all();
            return $this->render('index', ['results' => $results, 'pagination' => $pagination]);
        }

        /**
         * To add a record into the model (User)
         * @return : view of add record (User) form
         */
        public function actionSave()
        {
            // if(!Yii::$app->user->isGuest){
            $model           = new UserGroup;
            $model->scenario = 'userGroup';
            if ($model->load(Yii::$app->request->post())) {
                $model->type = 1; // type 1 is for Role
                if ($model->validate()) {
                    $model->save(false) ? Yii::$app->session->setFlash('success', 'You have been registered successfully', true) : Yii::$app->session->setFlash('danger', 'Your registration was not successful.', true);
                    return $this->redirect(Url::to(['user-group/index']));
                }
            }
            return $this->render('save', ['model' => $model]);
            //  }
//        else{
//            Yii::$app->session->setFlash("danger", 'You have to be logged in to perform any private operation', true);
//            $this->redirect(Url::to(['user/login']));
//        }
        }

        /**
         * To see the particular record information (User Profile)
         * @param type $id : record id to fetch the particular user Profile Detail (user_id)
         * @return : view of record information (User Profile)
         */
        public function actionView($id = null)
        {
            if (!Yii::$app->user->isGuest) {
                $model = UserGroup::findOne($id);
                if (isset($model) && !empty($model)) {
                    return $this->render('view', ['model' => $model]);
                } else {
                    Yii::$app->session->setFlash("danger", 'Invalid role or role does not exist', true);
                    return $this->redirect(Url::to(['user-group/index']));
                }
            }
        }

        /**
         * To edit the record information (User Profile)
         * @param long $id : To get the particular user's id
         * @return : the view of edit User form
         */
        public function actionEdit($name = null)
        {
            $model = UserGroup::findOne($name);
            if (isset($model) && !empty($model)) {
                $model->scenario = 'userGroup';

                if ($model->load(\Yii::$app->request->post()) && $model->updateAll(['name' => $model->name], ['name' => $name])) {
                    \Yii::$app->session->setFlash("success", 'Role has been updated successfully', true);
                    return $this->redirect(Url::to(['user-group/index']));
                } else {
                    \Yii::$app->session->setFlash("success", 'Update not success', true);
                    return $this->render('edit', ['model' => $model]);
                }
            } else {
                \Yii::$app->session->setFlash("danger", 'Invalid Role', true);
                return $this->redirect(Url::to(['user/index']));
            }

        }

        #################################### AJAX FUNCTIONS ####################################

        public function actionDeleteRole()
        {
            $name = $_POST['id'];
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if (strtolower($name) == strtolower(SUPER_ADMIN_ROLE_NAME)) {
                    return ['status' => 'blocked', 'message' => 'This role can never be deleted as it is SuperAdmin(' . SUPER_ADMIN_ROLE_NAME . ')'];
                }
                if (isset($_POST['confirmed']) && !empty($_POST['confirmed'])) {
                    $model = AuthItem::findOne($name);
                    if (isset($model) && !empty($model)) {
                        return ($model->deleteAll(['name' => $model->name])) ? ['status' => 'success', 'recordDeleted' => DELETED] : ['status' => 'failure'];
                    }
                } else {
                    $modelChildren = AuthItemChild::getAllChildren($name);
                    $modelParent   = AuthItemChild::getAllParent($name);
                    if ((count($modelParent) != 0) || (count($modelChildren) != 0)) {
                        return ['status' => 'staged', 'childOrParent' => true, 'children' => count($modelChildren), 'parent' => count($modelParent)];
                    } else {
                        return ['status' => 'staged', 'childOrParent' => false];
                    }
                }

            }
        }
    }