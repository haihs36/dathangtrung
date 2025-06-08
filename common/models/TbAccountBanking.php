<?php

    namespace common\models;

    use Yii;
    use yii\helpers\Url;

    /**
     * This is the model class for table "tb_account_banking".
     *
     * @property integer $id
     * @property integer $customerID
     * @property integer $totalMoney
     * @property integer $totalReceived
     * @property integer $totalPayment
     * @property integer $totalRefund
     * @property integer $totalResidual
     * @property string $create_date
     * @property string $edit_date
     * @property string $note
     */
    class TbAccountBanking extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public $verifyCode;

        public static function tableName()
        {
            return 'tb_account_banking';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['customerID', 'totalMoney'], 'required', 'message' => '{attribute} là bắt buộc'],
                [['totalMoney','customerID', 'totalReceived', 'totalPayment', 'totalRefund', 'totalResidual'], 'safe'],
                [['create_date', 'edit_date','note'], 'safe'],

            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id'            => 'ID',
                'customerID'      => 'Tài khoản',
                'totalMoney'    => 'Tổng tiền nạp',
                'totalReceived' => 'Tổng tiền rút',
                'totalPayment'  => 'Tồng thanh toán',
                'totalRefund'   => 'Tổng hoàn lại',
                'totalResidual' => 'Tổng dư TK',
                'create_date'   => 'Ngày tạo',
                'edit_date'     => 'Ngày cập nhật',
                'note'     => 'Ghi chú',
                'verifyCode'       => 'Nhập mã xác nhận',
            ];
        }

        /*relation to  member*/
        public function getCustomer()
        {
            return $this->hasMany(TbCustomers::className(), ['id' => 'customerID'])->one();
        }

        public function getAction()
        {
            return ' <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn-default" href="' . Url::to(['bank/view', 'id' => $this->primaryKey]) . '" title="Thông tin tài khoản" aria-label="Update" data-pjax="0"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                        <a class="btn btn-default" href="' . Url::to(['bank/update', 'id' => $this->primaryKey]) . '" title="Nạp tiền tài khoản" aria-label="Update" data-pjax="0"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
                        <a class="btn btn-default" href="' . Url::to(['bank/delete', 'id' => $this->primaryKey]) . '" title="Xóa" aria-label="Delete" data-confirm="Are you sure you want to delete this item?" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>';
        }
    }
