<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2/5/2018
 * Time: 11:07 AM
 */

namespace cms\models;


use yii\base\Model;

class Payment extends Model
{
    public $shippingCode;
    public $customerID;
    public $loID;

    public function rules()
    {
        return [
            [['shippingCode'], 'required','message'=>'{attribute} là bắt buộc'],
            [['customerID','loID'], 'integer'],
            [['shippingCode'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customerID' => 'Tài khoản khách hàng',
            'loID' => 'Lô',
            'shippingCode' => 'Mã vận đơn',
        ];
    }

}