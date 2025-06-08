<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_supplier".
 *
 * @property integer $supplierID
 * @property string $shopProductID
 * @property string $shopID
 * @property string $shopName
 * @property string $shopUrl
 * @property string $sourceName
 * @property string $address
 * @property string $email
 * @property string $phone
 * @property string $fax
 * @property integer $status
 * @property string $create_date
 */
class TbSupplier extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shopID', 'shopName'], 'required'],
            [['status'], 'integer'],
            [['create_date','shopProductID'], 'safe'],
            [['shopProductID'], 'string', 'max' => 250],
            [['shopID', 'shopName', 'shopUrl', 'sourceName'], 'string', 'max' => 200],
            [['address'], 'string'],
            [['email', 'phone', 'fax'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'supplierID' => 'Supplier ID',
            'shopProductID' => 'Shop Product ID',
            'shopID' => 'Shop ID',
            'shopName' => 'Shop Name',
            'shopUrl' => 'Shop Url',
            'sourceName' => 'Source Name',
            'address' => 'Address',
            'email' => 'Email',
            'phone' => 'Phone',
            'fax' => 'Fax',
            'status' => 'Status',
            'create_date' => 'Create Date',
        ];
    }

    public static function getSupplierByID($supplierID)
    {
        return self::find()->select(['shopName'])->where(['supplierID' => $supplierID])->asArray()->one();
    }

    public static function getSupplierByShopId($shop_id)
    {
        return self::find()->where(['shopID' => $shop_id])->one();
    }
}
