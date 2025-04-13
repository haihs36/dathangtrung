<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

/**
 * Customer model
 *
 * @property integer $id
 * @property integer $userID
 * @property string $username
 * @property string $password_hidden
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property string $fullname
 * @property string $avatar
 * @property string $gender
 * @property string $phone
 * @property integer $role
 * @property integer $provinID
 * @property integer $staffID
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deposit
 * @property double $weightFee
 * @property double $cny
 * @property double $expire_at
 * @property string $password write-only password
 * @property string $access_token
 */
class Custommer extends ActiveRecord implements IdentityInterface
{

    #################################### MODEL BASE ####################################
    const ACTIVE_ACOUNT_KEY = 'CUSTOMER:ACTIVE';
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const ROLE_USER = 10;

    /*Fields not the part of database fields...declare them public*/
    public $password;
    public $old_password;
    public $new_password;
    public $confirm_password;

    // public $verifyCode;
    public $file;

    /**
     * To tell the model which table to use for this model
     * @return string : the table name with to use for this model (with auto prefix)
     */
    public static function tableName()
    {
        return '{{%customers}}';
    }

    /**
     * To specify the behaviors to use for this model
     * @return : behaviors to use for this model
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userID'])->one();
    }

    /**
     * To validate the input fields
     * @return : the validation rules to validate and respective error messages
     */
    public function rules()
    {
        $useCaptcha = 1 ? ['register'] : [];
        return [
            [['fullname', 'username', 'email', 'password', 'phone'], 'required', 'message' => '{attribute} là bắt buộc'],    //default
            [['userID', 'provinID', 'staffID','deposit'], 'integer'],
            ['phone', 'integer', 'message' => 'Số điện thoại phải là chữ số'],
            [['phone'], 'match', 'pattern' => '/^\d{9,11}$/', 'message' => 'Số điện thoại không hợp lệ'],
            ['phone', 'filter', 'filter' => 'trim'],
            ['email', 'email', 'message' => 'Bạn phải nhập một địa chỉ email hợp lệ'],    //default
            ['email', 'unique', 'targetClass' => '\common\models\Custommer', 'message' => 'Địa chỉ email đã được đăng ký'],
            ['username', 'unique', 'targetClass' => '\common\models\Custommer', 'message' => 'Tên đăng nhập đã được đăng ký'],
            ['username', 'string', 'min' => 6, 'max' => 200, 'tooShort' => 'Tên đăng nhập có ít nhất 6 ký tự.'],
            ['username', 'match', 'pattern' => '/^(?:[a-zA-Z0-9_-]{5,20}|[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})$/i', 'message' => 'Tên đăng nhập của bạn chỉ có thể chứa các ký tự chữ số, dấu gạch dưới và dấu gạch ngang.'],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Mấu khẩu có ít nhất 6 ký tự.'],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'message' => 'Mật khẩu xác nhận không chính xác'],  //default
            [['updated_at', 'created_at','expire_at'], 'string', 'max' => 255],
            [['old_password', 'password', 'confirm_password'], 'required', 'message' => '{attribute} không được để trống', 'on' => 'changePassword'],         //for user and admin both
            ['old_password', 'verifyOldPassword', 'on' => 'changePassword'],                                  //for user and admin both
            [['fullname', 'phone', 'username', 'email'], 'required', 'on' => 'editProfile'],
            [['password', 'confirm_password'], 'required', 'on' => 'changeUserPassword'],         //for admin only ,'expire_at','totalResidual'
            [['username','password','fullname','confirm_password','email','phone'],'filter','filter'=>'strip_tags'],
            ######Default values to go
            //['verifyCode', 'captcha', 'on'=>$useCaptcha],
            ['status', 'in', 'range' => [0, 1]],
            //['role', 'default', 'value' => self::ROLE_USER],
            ['role', 'in', 'range' => [self::ROLE_USER]],
        ];
    }

    /**
     * To calculate the attribute label names
     * @return : the attribute label names (tranlatable in other language)
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'fullname'          => 'Họ và Tên',
            'phone'             => 'Số điện thoại',
            'email'             => 'Thư điện tử',
            'username'          => 'Tên đăng nhập',
            'password'          => 'Mật khẩu',
            'old_password'      => 'Mật khẩu cũ',
            'confirm_password'  => 'Xác nhận mật khẩu',
            'fb_id'             => 'Fb ID',
            'provinID'          => 'provinID',
            'fb_access_token'   => 'Fb Access Token',
            'twt_id'            => 'Twt ID',
            'twt_access_token'  => 'Twt Access Token',
            'twt_access_secret' => 'Twt Access Secret',
            'ldn_id'            => 'Ldn ID',
            'status'            => 'Trạng thái',
            'email_verified'    => 'Email Verified',
            'last_login'        => 'Last Login',
            'by_admin'          => 'By Admin',
            'created'           => 'Created',
            'modified'          => 'Modified',
            'avatar'            => 'avatar',
            'gender'            => 'gender',
            'cityCode'          => 'Tỉnh/Tp',
            'districtId'        => 'Quận huyện',
            'address'           => 'Địa chỉ',
            'bankName'          => 'Tên ngân hàng',
            'branch'            => 'Chi nhánh',
            'shipAddress'       => 'Địa chỉ ship hàng',
            'billingAddress'    => 'Địa chỉ giao hàng',
            'created_at'        => 'Created At',
            'discountRate'      => 'discountRate',
        ];
    }

    /**
     * To define scenarios for this model (for validation purposes)
     * @return : different scenarios to use for this model
     */
    public function scenarios()
    {
        return [
            'login'              => ['email', 'username', 'password'],
            'register'           => ['fullname', 'password_hidden', 'email', 'username', 'phone', 'password', 'confirm_password', 'status', 'by_admin', 'role'],
            'changePassword'     => ['old_password', 'password', 'confirm_password'],
            'addUser'            => ['fullname','deposit','weightFee','cny', 'provinID','staffID', 'discountRate', 'discountKg', 'password_hidden', 'userID', 'shipAddress', 'address', 'phone', 'cityCode', 'billingAddress', 'shipAddress', 'districtId', 'username', 'password', 'confirm_password', 'email', 'status', 'by_admin'],
            'editUser'           => ['fullname','deposit','weightFee','cny','staffID', 'provinID', 'discountRate', 'discountKg', 'password_hidden', 'userID', 'shipAddress', 'address', 'phone', 'cityCode', 'billingAddress', 'districtId', 'email', 'status', 'by_admin'],
            'statusChange'       => ['status'],
            'changeUserPassword' => ['password', 'confirm_password'],
            'emailVerification'  => ['email_verified'],
            #######Password reset
            'resetPassword'      => ['email', 'password_reset_token'],
            'resetPass'          => ['password'],
        ];

    }

    /*
     * To Associate this model to another model(here associating with "UserDetail" Model)
     * @return : the relation with model
     */
    /*  public function getUserDetail()
      {
          return $this->hasOne(UserDetail::className(), ['user_id'=>'id']);
      }*/

    #################################### STATIC ARRAY VALUES FUNCTIONS ####################################

    /**
     * To get all the gender options
     * @return array : array of all the gender options
     */
    public static function findGenderOptions()
    {
        return [
            'M' => 'Male',
            'F' => 'Female',
            'O' => 'Any Other',
        ];
    }

    /**
     * To get all the marital status options
     * @return array : array of marital status options
     */
    public static function findMaritalStatusOptions()
    {
        return [
            'M' => 'Married',
            'U' => 'Unmarried',
            'D' => 'Divorced',
            'W' => 'Widowed',
        ];
    }

    /**
     * To get the identity of the user WITH STATUS
     * @param type $id : the user having this id
     * @return type record Object(User object)
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /*Customer Accounting*/
    public function getAccounting()
    {
        return $this->hasOne(TbAccountBanking::className(), ['customerID' => 'id'])->one();
    }

    /*Customer transaction*/
    public function getAccounTransaction()
    {
        return $this->hasOne(TbAccountTransaction::className(), ['customerID' => 'id'])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken_($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = static::find()->where(['access_token' => $token, 'status' => self::STATUS_ACTIVE])->one();
        if (!$user) {
            return false;
        }
        if ($user->expire_at < time()) {
            throw new UnauthorizedHttpException('the access - token expired ', -1);
        }
        return $user;
    }

    /*find user by id*/
    public static function findById($id)
    {
        return self::find()->select(['fullname', 'phone', 'email', 'username'])->where(['id' => $id])->asArray()->one();
    }

    public static function findByUsername($username)
    {
        return static::find()->onCondition('(username = :username or email = :email) and status = :status', [':username' => $username, ':email' => $username, ':status' => self::STATUS_ACTIVE])->one();//'status' => ACTIVE
    }

    public static function findByAuthor($username)
    {
        return static::find()->select(['u.fullname', 'u.id', 'u.username'])->from(self::tableName() . ' u')->innerJoinWith('userDetail')->onCondition('username = :username and status = :active', [':username' => $username, ':active' => 1])->asArray()->one();//'status' => ACTIVE
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
            'status'               => self::STATUS_ACTIVE,
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

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
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
     * To set the password for the registering user
     * @param type string password
     * @return type string password_hash (generated)
     */
    public static function setNewPassword($password = null)
    {
        return Yii::$app->security->generatePasswordHash($password);
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
     * Generates "reCustomer me" authentication key
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
     * To set the auth_key for registering user
     * @return type string auth_key(generated)
     */
    public static function generateNewAuthKey()
    {
        return Yii::$app->security->generateRandomString();
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
                $this->addError($attribute, "Mật khẩu hiện tại không chính xác");
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

}
