<?php

    namespace common\models;

    use Yii;
    use yii\helpers\Url;

    /**
     * This is the model class for table "tb_account_transaction".
     *
     * @property string $id
     * @property integer $customerID
     * @property integer $type
     * @property string $sapo
     * @property integer $value
     * @property integer $balance
     * @property integer $accountID
     * @property integer $userID
     * @property integer $status
     * @property string $create_date
     */
    class TbAccountTransaction extends \yii\db\ActiveRecord
    {
        const TYPE_WITHDRAW = 2;
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_account_transaction';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['value'], 'required', 'message' => '{attribute} là bắt buộc.'],
//                [['value'], 'integer','message' => '{attribute} phải là số.'],
                [['create_date', 'status', 'sapo', 'value','userID','customerID', 'type', 'balance','accountID'], 'safe'],
                [['sapo'], 'string', 'max' => 500]
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id'          => 'ID',
                'accountID'   => 'accountID',
                'customerID'    => 'Member ID',
                'type'        => 'Type',
                'sapo'        => 'Thông tin giao dịch',
                'status'      => 'Trạng thái giao dịch',
                'value'       => 'Giá trị giao dịch',
                'balance'     => 'Số dư TK',
                'create_date' => 'Create Date',
            ];
        }

        /*relation to  member*/
        public function getCustomer()
        {
            return $this->hasMany(TbCustomers::className(), ['id' => 'customerID'])->one();
        }
        public function getUser()
        {
            return $this->hasMany(User::className(), ['id' => 'userID'])->one();
        }
        public function getBank()
        {
            return $this->hasMany(TbAccountBanking::className(), ['id' => 'accountID'])->one();
        }

        public function getAction()
        {
//            <a class="btn btn-default confirm-delete" href="' . Url::to(['account-transaction/delete', 'id' => $this->primaryKey]) . '" title="Bạn có chắc chắn muốn xóa lịch sử giao dịch" ><span class="glyphicon glyphicon-trash"></span></a>
            return ' <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn-default" href="' . Url::to(['account-transaction/view', 'id' => $this->primaryKey]) . '" title="Chi tiết giao dịch" aria-label="Update" data-pjax="0"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>                        
                    </div>';
        }

    }
