<?php

    namespace common\models;

    use Yii;

    /**
     * This is the model class for table "tb_complain_reply".
     *
     * @property integer $id
     * @property integer $customerID
     * @property integer $userID
     * @property integer $orderID
     * @property integer $complainID
     * @property string $message
     * @property string $create_date
     */
    class TbComplainReply extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public $verifyCode;

        public static function tableName()
        {
            return 'tb_complain_reply';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['message'], 'required', 'message' => '{attribute} là bắt buộc'],
                //[['customerID', 'userID', 'complainID'], 'integer'],
                [['orderID'], 'integer'],
                [['message'], 'string', 'max' => 500],
                [['create_date'], 'safe'],
//                [['verifyCode'], 'captcha', 'skipOnEmpty' => true, 'on' => 'admin'],
//                [['verifyCode'], 'captcha', 'skipOnEmpty' => false, 'on' => 'member', 'message' => 'Mã xác nhận không hợp lệ']
            ];
        }

        public function getCustomer()
        {
            return $this->hasMany(TbCustomers::className(), ['id' => 'customerID'])->one();
        }

        public function getOrder()
        {
            return $this->hasMany(TbOrders::className(), ['orderID' => 'orderID'])->one();
        }

        public function getAdmin()
        {
            return $this->hasMany(User::className(), ['id' => 'userID'])->one();
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'verifyCode' => 'Mã xác nhận',
                'id'         => ' ID',
                'customerID'   => 'Member ID',
                'orderID'    => 'Mã đơn hàng',
                'userID'    => 'Admin ID',
                'complainID' => 'Complain ID',
                'message'    => 'Nội dung',
            ];
        }
    }
