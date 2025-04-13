<?php

namespace cms\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_consignment".
 *
 * @property string $id
 * @property string $name
 * @property double $kg
 * @property double $amount
 * @property double $actualPayment
 * @property integer $userID
 * @property integer $customerID
 * @property string $create
 * @property integer $status
 * @property string $lastDate
 * @property string $address
 * @property string $images
 * @property string $note
 * @property integer $payStatus
 * @property double $shipFee
 */
class Consignment extends \yii\db\ActiveRecord
{

    public $customerName;
    public $username;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_consignment';
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$insert && $this->images != $this->oldAttributes['images'] && $this->oldAttributes['images']) {
                @unlink(\Yii::getAlias('@upload_dir') . $this->oldAttributes['images']);

            }

            if($this->isNewRecord) {
                $this->create = date('Y-m-d H:i:s');
            }else{
                $this->lastDate = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        @unlink(\Yii::getAlias('@upload_dir') . $this->images);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerID','payStatus', 'address', 'note'], 'required','message'=>'{attribute} là bắt buộc'],
            [['kg', 'amount', 'actualPayment', 'shipFee'], 'number'],
            [['userID', 'customerID', 'status', 'payStatus'], 'integer'],
            [['create', 'lastDate'], 'safe'],
            [['address', 'note','customerName','username'], 'string'],
            [['images'], 'image', 'extensions' => 'png,jpg,jpeg','maxSize' => 1024 * 1024 , 'message' => '{attribute} chỉ cho phép các tệp với các tiện ích này: jpg, png.'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'kg' => 'Cân nặng',
            'amount' => 'Tổng tiền',
            'actualPayment' => 'Thanh toán thực tế',
            'userID' => 'Nhân viên',
            'customerID' => 'Khách hàng',
            'create' => 'Create',
            'status' => 'Status',
            'lastDate' => 'Last Date',
            'address' => 'Địa chỉ giao hàng',
            'note' => 'Ghi chú',
            'payStatus' => 'Hình thức giao hàng',
            'shipFee' => 'Ship Fee',
        ];
    }

    public function getAction()
    {
        $del = '';
        //if($this->status == 0 || Yii::$app->user->identity->role == ADMIN){
            $del = '<a class="btn confirm-delete" href="' . Url::to(['consignment/delete', 'id' => $this->primaryKey]) . '"  title="Xóa phiếu: PXK-'.$this->id.'" ><span class="glyphicon glyphicon-trash"></span> Delete</a>';
       // }
        $print = '';
        $view = '';
       // if($this->customerID && $this->status){
            $print = '<a class="btn btn-print" href="javascript:void(0)"  data-url="' . Url::to(['consignment/print', 'loid' => $this->primaryKey]) . '" title="In phiếu"><i class="glyphicon glyphicon-print" aria-hidden="true"></i> Print</a>';
       // }

      //  if($this->status == 0) {
            $edit = '<a target="_blank" class="btn " href="' . Url::to(['consignment/update', 'id' => $this->primaryKey]) . '" title="' . $this->name . '" aria-label="Update" data-pjax="0"><i class="glyphicon glyphicon-edit"></i> Update</a>';
       // }else{
         //   $edit = '';
       // }

        return ' <div class="btn-group btn-group-sm" role="group">                        
                        '.$view.$print.$edit.$del.'
                    </div>';

    }

    public function getStatus(){
        $str = '';
        switch ($this->status){
            case 2:
                $str = '<span class="label label-primary">Thanh toán tiền mặt</span>';
                break;
            case 1:
                $str = '<span class="label label-finish">Thanh toán ngân hàng</span>';
                break;
            case 0:
            default:
                $str = '<span class="label label-warning">Chưa thanh toán</span>';
                break;
        }

        return $str;
    }
}
