<?php

namespace cms\models;

use common\models\Custommer;
use common\models\User;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_lo".
 *
 * @property integer $id
 * @property string $name
 * @property double $kg
 * @property double $amount
 * @property double $shipFee
 * @property integer $userID
 * @property integer $customerID
 * @property string $create
 * @property string $lastDate
 * @property string $note
 * @property string $address
 * @property integer $status
 * @property integer $payStatus
 * @property integer $payType
 */
class Lo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_lo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerID','payStatus','payType'], 'required','message'=> '{attribute} là bắt buộc'],
            [['userID','customerID','payStatus', 'status'], 'integer'],
            [['create','kg', 'amount','note','address','lastDate','shipFee'], 'safe'],
            [['name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tên Lô',
            'kg' => 'Kg',
            'payStatus' => 'Hình thức giao hàng',
            'payType' => 'Phương thức thanh toán',
            'customerID' => 'Khách hàng',
            'amount' => 'Tổng tiền',
            'userID' => 'Người tạo',
            'create' => 'Ngày tạo',
            'status' => 'Trạng thái',
            'shipFee' => 'Phí ship',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($this->isNewRecord) {
                $this->create = date('Y-m-d H:i:s');
                $this->lastDate = $this->create;
            }else{
                $this->lastDate = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'userID'])->one();
    }

    public function getCustomer(){
        return $this->hasOne(Custommer::className(),['id'=>'customerID'])->one();
    }

    public function getAction()
    {
        $del = '';
        if($this->status == 0 || Yii::$app->user->identity->role == ADMIN){
            $del = '<a class="btn confirm-delete" href="' . Url::to(['lo/delete', 'id' => $this->primaryKey]) . '"  title="Phiếu xuất: PXK-'.$this->id.'" ><span class="glyphicon glyphicon-trash"></span></a>';
        }
        $print = '';
        $view = '';
        if($this->customerID && $this->status){
            $print = '<a class="btn btn-print" href="javascript:void(0)"  data-url="' . Url::to(['payment/print', 'loid' => $this->primaryKey]) . '" title="In Lô"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></a>';
            $view  = '<a target="_blank" class="btn" href="' . Url::to(['lo/view', 'id' => $this->primaryKey]) . '" title="Xem chi chiết"><span class="glyphicon glyphicon-eye-open"></span></a>';
        }

        if($this->status == 0) {
            $edit = '<a target="_blank" class="btn " href="' . Url::to(['lo/update', 'id' => $this->primaryKey]) . '" title="' . $this->name . '" aria-label="Update" data-pjax="0">Xuất phiếu</a>';
        }else{
            $edit = '';
        }

        return ' <div class="btn-group btn-group-sm" role="group">                        
                        '.$view.$print.$edit.$del.'
                    </div>';

    }

    //check lo
    public static function checkLoClose($loID,$shippingCode){
        $result = self::find()->select('b.loID,b.shippingCode')->from(self::tableName().' a')
            ->innerJoin(Warehouse::tableName().' b','a.id = b.loID')
            ->where(['a.id'=>$loID,'a.status'=>0,'b.shippingCode'=>$shippingCode])
            ->asArray()->one();
        return $result;
    }
}
