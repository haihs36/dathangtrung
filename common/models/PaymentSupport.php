<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_payment_support".
 *
 * @property string $id
 * @property double $amount_total
 * @property double $amount_total_vn
 * @property double $cny
 * @property integer $status
 * @property integer $customerID
 * @property string $note
 * @property string $dataAmount
 * @property string $create_time
 * @property string $update_time
 */
class PaymentSupport extends \yii\db\ActiveRecord
{
    public $fullname;
    public $username;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_payment_support';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount_total', 'amount_total_vn', 'cny'], 'required'],
            [['amount_total', 'amount_total_vn', 'cny'], 'number'],
            [['status','customerID'], 'integer'],
            [['note','dataAmount'], 'string'],
            [['note'],'filter','filter'=>'\yii\helpers\HtmlPurifier::process'],
            [['create_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount_total' => 'Amount Total',
            'amount_total_vn' => 'Amount Total Vn',
            'cny' => 'Cny',
            'status' => 'Status',
            'note' => 'Note',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /*customer admin*/
    public function getCustomer()
    {
        return $this->hasMany(Custommer::className(), ['id' => 'customerID'])->one();
    }


    public function getAction()
    {
        $action = '';

        switch ($this->status){
            case 0: //chua thanh toan
                $action = '<a href="javascript:"   data-id="'.$this->primaryKey.'"  title="Hủy" class="btn bg-olive margin btnHuy"><i class="fa fa-fw fa-ban"></i> Hủy</a>';
                break;
            case 2:
                $action = '<a href="javascript:"   data-id="'.$this->primaryKey.'" title="Thanh toán"  class="btn bg-orange-active margin btnPay"><i class="fa fa-fw fa-credit-card"></i> Duyệt</a>
                        <a href="javascript:"   data-id="'.$this->primaryKey.'"  title="Hủy" class="btn bg-olive margin btnHuy"><i class="fa fa-fw fa-ban"></i> Hủy</a>';
                break;

        }


        return ' <div class="btn-group btn-group-sm" role="group">
                        <a href="' . Url::to(['paymentsupport/view', 'id' => $this->primaryKey]) . '"  title="Chi tiết" class="btn bg-purple margin"><i class="fa fa-fw fa-eye"></i> Chi tiết</a>
                        '.$action.'
                        
                    </div>';
    }

    public function getActionOut()
    {
        $action = '';

        switch ($this->status){
            case 0: //chua thanh toan
                $action = '<a href="javascript:"   data-id="'.$this->primaryKey.'" title="Thanh toán"  class="btn bg-orange margin btnPay"><i class="fa fa-fw fa-credit-card"></i> Thanh toán</a>
                        <a href="javascript:"   data-id="'.$this->primaryKey.'"  title="Hủy" class="btn bg-olive margin btnHuy"><i class="fa fa-fw fa-ban"></i> Hủy</a>';

        }


        return ' <div class="btn-group btn-group-sm" role="group">
                        <a href="' . Url::to(['paymentsupport/view', 'id' => $this->primaryKey]) . '"  title="Chi tiết" class="btn bg-aqua-active color-palette margin"><i class="fa fa-fw fa-eye"></i> Chi tiết</a>
                        '.$action.'
                        
                    </div>';
    }
}
