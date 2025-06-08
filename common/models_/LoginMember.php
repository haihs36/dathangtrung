<?php

    namespace common\models;

    use Yii;
    use yii\base\Model;

    /**
     * LoginForm is the model behind the login form.
     *
     * @property Custommer|null $user This property is read-only.
     *
     */
    class LoginMember extends Model
    {
        public $username;
        public $password;
        public $rememberMe = true;

        protected $_user = false;

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                // username and password are both required
                [['username', 'password'], 'required', 'message' => '{attribute} không được để trống'],
                //                ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_-]$/', 'message' => 'Tên người dùng của bạn chỉ có thể chứa các ký tự chữ số, dấu gạch dưới và dấu gạch ngang.'],
                //                ['username', 'string', 'min' => 5, 'max' => 255,'message' => 'Tên đăng nhập tối thiếu 5 kí tự'],
                // rememberMe must be a boolean value
                ['rememberMe', 'boolean'],
                [['username','password'],'filter','filter'=>'strip_tags'],
                // password is validated by validatePassword()
                ['password', 'validatePassword'],
                ['password', 'isNotBlocked'],
            ];
        }

        public function attributeLabels()
        {
            return [
                'username'   => 'Tên đăng nhập',
                'password'   => 'Mật khẩu',
                'rememberMe' => 'Duy trì đăng nhập',
            ];
        }

       
        /**
         * Validates the password.
         * This method serves as the inline validation for password.
         *
         * @param string $attribute the attribute currently being validated
         * @param array $params the additional name-value pairs given in the rule
         */
        public function validatePassword($attribute, $params)
        {
            if (!$this->hasErrors()) {
                $user = $this->getUser();
                if (!$user || !$user->validatePassword($this->password)) {
                    $this->addError($attribute, 'Tên đăng nhập hoặc mật khẩu không hợp lệ.');
                }
            }
        }

        /**
         * Validates the password.
         * This method serves as the inline validation for password.
         *
         * @param string $attribute the attribute currently being validated
         * @param array $params the additional name-value pairs given in the rule
         */
        public function isNotBlocked($attribute, $params)
        {
            if (!$this->hasErrors()) {
                $user = $this->getUser();
                if (!$user || $user->status == 0) {
                    $this->addError($attribute, 'Tài khoản của bạn chưa được kích hoạt.');
                }
            }
        }

        /**
         * Logs in a user using the provided username and password.
         *
         * @return boolean whether the user is logged in successfully
         */
        const CACHE_KEY = 'SIGNIN_TRIES_HOME';
        Const EXPIRE_TIME = 1440000; //token expiration time, 4h

        public function login()
        {
            $cache = Yii::$app->cache;
//            if (($tries = (int)$cache->get(self::CACHE_KEY)) > 3) {
//                $this->addError('username', 'You tried to login too often. Please wait 5 minutes.');
//                return false;
//            }

            if ($this->validate()) {
                if ($this->_user->expire_at < time()) {
                    $access_token = Yii::$app->security->generateRandomString(100);
                    $this->_user->access_token = $access_token;
                    $this->_user->expire_at = time() + static::EXPIRE_TIME;
                    $this->_user->save(false);

                    //return Yii::$app->user->login($this->_user, static::EXPIRE_TIME);
                }

                return  Yii::$app->user->login($this->_user, static::EXPIRE_TIME);

            }

//                $cache->set(self::CACHE_KEY, ++$tries, 300);
             return false;
        }



        /**
         * Logs in a user using the provided username and password.
         *
         * @return boolean whether the user is logged in successfully
         */
        public function loginApi()
        {
            if ($this->validate()) {
                if ($this->_user->expire_at < time()) {
                    $access_token = Yii::$app->security->generateRandomString(100);
                    $this->_user->access_token = $access_token;
                    $this->_user->expire_at = time() + static::EXPIRE_TIME;
                    $this->_user->save(false);

                    //return Yii::$app->user->login($this->_user, static::EXPIRE_TIME);
                }

                return  Yii::$app->user->login($this->_user, static::EXPIRE_TIME);

            }

            return false;
        }



        /**
         * Finds user by [[username]]
         *
         * @return Custommer|null
         */
        public function getUser()
        {
            if ($this->_user === false) {
                $this->_user = Custommer::findByUsername($this->username);
            }

            return $this->_user;
        }
    }
