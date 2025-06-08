<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_shipping".
 *
 * @property string $id
 * @property integer $shopID
 * @property integer $userID
 * @property integer $shipperID
 * @property integer $tranID
 * @property string $shippingCode
 * @property integer $status
 * @property integer $city
 * @property string $createDate
 * @property string $editDate
 * @property string $setDate
 * @property string $barcode
 */
class TbShipping extends \yii\db\ActiveRecord
{
    public $identify;
    public $orderID;
    public $username;
    public $name;
    public $isCheck;
    public $isBox;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_shipping';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userID', 'shippingCode'], 'required'],
            [['shopID', 'userID','tranID','shipperID','orderID', 'status','city'], 'integer'],
            [['createDate','identify','editDate','setDate','username','isCheck','isBox',], 'safe'],
            [['shippingCode','barcode','name'], 'string']
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if($this->isNewRecord){
                $this->createDate   = date('Y-m-d H:i:s');
                $this->editDate   = $this->createDate;
            }else{
                $this->editDate   = date('Y-m-d H:i:s');
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shopID' => 'Shop ID',
            'userID' => 'User ID',
            'shippingCode' => 'Shipping Code',
            'status' => 'Status',
            'createDate' => 'Create Date',
            'setDate' => 'ngay kho ban',
            'editDate' => 'ngay cap nhat',
            'isCheck' => 'isCheck',
        ];
    }

    public function getTransfer(){
        return $this->hasMany(TbTransfercode::className(), ['id' => 'tranID'])->one();
    }

    public function getUser(){
        return $this->hasMany(User::className(), ['id' => 'userID'])->one();
    }

    public function getAction()
    {
        if($this->status != 1) {
            return ' <div class="btn-group btn-group-sm" role="group">
                        <a href="' . Url::to(['shipping/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete" title="Delete item"><span class="glyphicon glyphicon-remove"></span> Delete</a>
                    </div>';
        }
        return null;
    }
}
