<?php

    namespace common\models;

    use cms\models\TbCities;
    use cms\models\TbDistricts;
    use Yii;
    use yii\helpers\Html;
    use yii\helpers\Url;

    /**
     * This is the model class for table "tb_customers".
     *
     * @property integer $id
     * @property integer $userID
     * @property integer $userComplain
     * @property string $username
     * @property string $fullname
     * @property string $phone
     * @property string $email
     * @property string $auth_key
     * @property string $password_hidden
     * @property string $password_hash
     * @property string $password_reset_token
     * @property integer $group_id
     * @property integer $role
     * @property string $fb_id
     * @property string $fb_access_token
     * @property integer $email_verified
     * @property string $last_login
     * @property integer $by_admin
     * @property string $avatar
     * @property string $updated_at
     * @property string $gender
     * @property string $cityCode
     * @property integer $districtId
     * @property string $address
     * @property string $bankName
     * @property string $branch
     * @property string $shipAddress
     * @property string $billingAddress
     * @property integer $status
     * @property integer $deposit
     * @property integer $cny
     * @property double $weightFee
     * @property double $discountKg
     * @property integer $provinID
     * @property string $created_at
     * @property string $expire_at
     */
    class TbCustomers extends \common\components\ActiveRecord
    {
        public $totalResidual;
        public $multipleID;
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_customers';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['group_id', 'role', 'fb_id', 'email_verified', 'by_admin', 'districtId', 'deposit', 'status', 'userID', 'provinID', 'userComplain'], 'integer'],
                [['auth_key', 'password_hash'], 'required'],
                [['fb_access_token', 'phone' ,'multipleID'], 'string'],
                [['last_login', 'totalResidual', 'weightFee', 'cny','discountKg'], 'safe'],
                [['username', 'fullname', 'email', 'password_hash', 'password_hidden', 'password_reset_token'], 'string', 'max' => 255],
                [['auth_key'], 'string', 'max' => 32],
                [['avatar', 'gender'], 'string', 'max' => 200],
                [['updated_at', 'cityCode', 'created_at', 'expire_at'], 'string', 'max' => 255],
                [['address', 'bankName', 'branch', 'shipAddress', 'billingAddress'], 'string', 'max' => 300],
                [['userID', 'staffID'],'required','message'=>'{attribute} là bắt buộc', 'on' => 'assign_customer']
            ];
        }

        public function scenarios()
        {
            $scenarios = parent::scenarios();

            $arrSenarios =  [
                'assign_customer' => ['userID', 'staffID'],
            ];
            return array_merge($scenarios,$arrSenarios);

        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'userID' => 'Nhân viên kinh doanh',
                'staffID' => 'Nhân viên đặt hàng',
                'id'                   => 'ID',
                'username'             => 'Username',
                'fullname'             => 'Họ và Tên',
                'phone'                => 'Điện thoại',
                'email'                => 'Email',
                'auth_key'             => 'Auth Key',
                'password_hash'        => 'Password Hash',
                'password_reset_token' => 'Password Reset Token',
                'group_id'             => 'Group ID',
                'role'                 => 'Role',
                'fb_id'                => 'Fb ID',
                'fb_access_token'      => 'Fb Access Token',
                'email_verified'       => 'Email Verified',
                'last_login'           => 'Last Login',
                'by_admin'             => 'By Admin',
                'avatar'               => 'Avatar',
                'provinID'             => 'provinID',
                'updated_at'           => 'Updated At',
                'gender'               => 'Gender',
                'cityCode'             => 'Tỉnh/Tp',
                'districtId'           => 'Quận huyện',
                'address'              => 'Địa chỉ',
                'bankName'             => 'Tên ngân hàng',
                'branch'               => 'Chi nhánh',
                'shipAddress'          => 'Địa chỉ ship hàng',
                'billingAddress'       => 'Địa chỉ giao hàng',
                'status'               => 'Status',
                'created_at'           => 'Created At',
            ];
        }

        /*relation to  address*/
        public function getAddressShipping()
        {
            return $this->hasMany(TbAddressShipping::className(), ['customerID' => 'id'])->one();
        }

        /*customer admin*/
        public function getUser()
        {
            return $this->hasMany(User::className(), ['id' => 'userID'])->one();
        }


        /*customer city*/
        public function getCity()
        {
            return $this->hasMany(TbCities::className(), ['CityCode' => 'cityCode'])->one();
        }

        public function getDistrict()
        {
            return $this->hasMany(TbDistricts::className(), ['DistrictId' => 'districtId'])->one();
        }

        /*customer Accounting*/
        public function getAccounting()
        {
            return $this->hasMany(TbAccountBanking::className(), ['customerID' => 'id'])->one();
        }


        /*customer transaction*/
        public function getAccounTransaction()
        {
            return new TbAccountTransaction();
        }

        /*public function getAction()
        {
            $user = \Yii::$app->user->identity;
            $action = '';
            if($user->role != WAREHOUSE) {
                $statusClass = Html::encode($this->status == ACTIVE) ? 'glyphicon glyphicon-ok' : 'glyphicon glyphicon-ban-circle';
//                <li><a href="' . Url::to(['customer/delete', 'id' => $this->primaryKey]) . '" title="Xóa" aria-label="Delete" class="" data-confirm="Bạn có chắc chắn muốn xóa ?" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-trash"></span> Xóa</a></li>
                $action = '<li><a href="' . Url::to(['customer/update', 'id' => $this->primaryKey]) . '" title="Chỉnh sửa" aria-label="Update" class="" data-pjax="0"><span class="glyphicon glyphicon-edit"></span> Chỉnh sửa</a></li>
                    <li><a href="' . Url::to(['customer/change-user-password', 'id' => $this->primaryKey]) . '" title="Thay đổi mật khẩu"  data-pjax="0" class=""><i class="fa fa-key" aria-hidden="true"></i>Thay đổi mật khẩu</a></li>
                    <li><a id="ableToChangeStatus' . $this->id . '" class="ableToChangeStatus" href="javascript:void(0)" title="' . (($this->status) == ACTIVE ? 'Khóa' : 'Kích hoạt') . '" url="' . Url::to([Yii::$app->controller->id . "/status"]) . '"><span class="' . $statusClass . '"></span> ' . (($this->status) == ACTIVE ? 'Khóa' : 'Kích hoạt') . '</a></li>
                    ';

            }
            return '<div class="dropdown actions">
                <i id="dropdownMenu13" data-toggle="dropdown"  title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                    <li><a href="' . Url::to(['customer/view', 'id' => $this->primaryKey]) . '" title="Thông tin tài khoản" aria-label="Update" class="" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span> Thông tin tài khoản</a></li>
                    <li role="presentation" class="divider"></li>'.$action.'
                </ul>
            </div>';

        }*/

        public function getAction()
        {
            $statusClass = Html::encode($this->status == ACTIVE) ? 'glyphicon glyphicon-ok' : 'glyphicon glyphicon-ban-circle';
            return ' <div class="btn-group btn-group-sm" role="group">
                                <a href="' . Url::to(['customer/view', 'id' => $this->primaryKey]) . '" title="Thông tin tài khoản" aria-label="Update" class="btn btn-default" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
                                <a href="' . Url::to(['customer/update', 'id' => $this->primaryKey]) . '" title="Chỉnh sửa" aria-label="Update" class="btn btn-default" data-pjax="0"><span class="glyphicon glyphicon-edit"></span></a>
                                <a href="' . Url::to(['customer/change-user-password', 'id' => $this->primaryKey]) . '" title="Thay đổi mật khẩu"  data-pjax="0" class="btn btn-default"><i class="fa fa-key" aria-hidden="true"></i></a>
                                ' . Html::a('<span class="' . $statusClass . '"></span>', 'javascript:void(0)', ['class' => 'ableToChangeStatus btn btn-default', 'id' => 'ableToChangeStatus' . $this->id, 'url' => Url::to([Yii::$app->controller->id . "/status"]), 'title' => ($this->status) == ACTIVE ? 'Khóa' : 'Kích hoạt']) . '
                                
                            </div>';
            //<a href="' . Url::to(['customer/delete', 'id' => $this->primaryKey]) . '" title="Xóa" aria-label="Delete" class="btn btn-default" data-confirm="Bạn có chắc chắn muốn xóa ?" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-trash"></span></a>
        }


        public static function getInfoByConsdition($condition = [])
        {
            if (!is_array($condition)) return [];

            $cache = \Yii::$app->cache;
            $key = 'Key-customer-' . implode('-', $condition);

            $result = $cache->get($key);
            $result = false;
            if ($result === false) {
                $result = self::find()->select(['id', 'username', 'fullname'])->where($condition)->asArray()->all();
                $cache->set($key, $result, \Yii::$app->params['CACHE_TIME']['HOUR']);
            }
            return $result;
        }

        public static function getInfoByCondition($condition = [])
        {
            if (!is_array($condition)) return [];

            $cache = \Yii::$app->cache;
            $key = 'Key-customer-' . implode('-', $condition);

            $data = false;
            $cache->get($key);
            if ($data === false) {
                $result = self::find()->select(['id', 'username', 'fullname', 'phone'])->where($condition)->all();
                $data = [];
                if ($result) {
                    foreach ($result as $item) {
                        $data[$item['id']] = $item['username'];
                    }
                }


                $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
            }

            return $data;
        }
    }
