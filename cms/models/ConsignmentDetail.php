<?php

namespace cms\models;

use Yii;

/**
 * This is the model class for table "tb_consignment_detail".
 *
 * @property string $id
 * @property integer $consignID
 * @property string $barcode
 * @property double $long
 * @property double $wide
 * @property double $high
 * @property double $kg
 * @property double $kgChange
 * @property double $kgPay
 * @property string $note
 * @property double $totalPriceKg
 * @property double $incurredFee
 * @property double $kgFee
 * @property string $createDate
 * @property string $editDate
 */
class ConsignmentDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_consignment_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['consignID'], 'integer'],
            [['barcode'], 'required'],
            [['long', 'wide', 'high', 'kg', 'kgChange', 'kgPay', 'totalPriceKg', 'incurredFee', 'kgFee'], 'number'],
            [['note'], 'string'],
            [['createDate','editDate'], 'safe'],
            [['barcode'], 'string', 'max' => 250],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($this->isNewRecord) {
                $this->createDate = date('Y-m-d H:i:s');
            }else{
                $this->editDate = date('Y-m-d H:i:s');
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
            'consignID' => 'Consign ID',
            'barcode' => 'Barcode',
            'long' => 'Long',
            'wide' => 'Wide',
            'high' => 'High',
            'kg' => 'Kg',
            'kgChange' => 'Kg Change',
            'kgPay' => 'Kg Pay',
            'note' => 'Note',
            'totalPriceKg' => 'Total Price Kg',
            'incurredFee' => 'Incurred Fee',
            'kgFee' => 'Kg Fee',
            'createDate' => 'Create Date',
        ];
    }
}
