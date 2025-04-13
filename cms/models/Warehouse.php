<?php

namespace cms\models;

use Yii;

/**
 * This is the model class for table "tb_warehouse".
 *
 * @property integer $id
 * @property integer $loID
 * @property integer $tran_id
 * @property integer $shopID
 * @property string $shippingCode
 * @property string $create
 * @property integer $orderID
 * @property integer $type
 * @property double $long
 * @property double $wide
 * @property double $high
 * @property double $kg
 * @property double $kgChange
 * @property double $kgPay
 * @property string $note
 */
class Warehouse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_warehouse';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loID', 'shopID', 'shippingCode'], 'required'],
            [['loID', 'shopID', 'orderID','type','tran_id'], 'integer'],
            [['create'], 'safe'],
            [['long', 'wide', 'high', 'kg', 'kgChange', 'kgPay'], 'number'],
            [['note'], 'string'],
            [['shippingCode'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'type',
            'loID' => 'Lo ID',
            'shopID' => 'Shop ID',
            'shippingCode' => 'Shipping Code',
            'create' => 'Create',
            'orderID' => 'Order ID',
            'long' => 'Long',
            'wide' => 'Wide',
            'high' => 'High',
            'kg' => 'Kg',
            'kgChange' => 'Kg Change',
            'kgPay' => 'Kg Pay',
            'note' => 'Note',
        ];
    }
}
