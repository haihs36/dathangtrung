<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_orders_detail".
 *
 * @property integer $id
 * @property integer $orderID
 * @property integer $productID
 * @property integer $orderSupplierID
 * @property integer $quantity
 * @property double $unitPrice
 * @property double $totalPrice
 * @property double $unitPriceVn
 * @property double $discount
 * @property double $totalPriceVn
 * @property string $size
 * @property string $color
 * @property string $image
 * @property string $noteProduct
 * @property string $createDate
 * @property integer $orderNumber
 * @property string $shipDate
 * @property string $billDate
 * @property integer $fulFilled
 * @property integer $status
   * @property integer $qty_receive 
   * @property string $note_receive
 */
class TbOrdersDetail extends \yii\db\ActiveRecord
{
    public $link;
    public $sourceName;
    public $name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_orders_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderID', 'productID'], 'required'],
            [['orderID', 'productID', 'quantity','orderSupplierID','status','qty_receive','note_receive', 'unitPriceVn', 'discount', 'totalPriceVn', 'orderNumber', 'fulFilled'], 'safe'],
            [['unitPrice', 'totalPrice'], 'number'],
            [['createDate', 'shipDate', 'billDate','noteProduct'], 'safe'],
            [['size', 'color'], 'string', 'max' => 200],
            ['quantity', 'compare', 'compareValue' => 0, 'operator' => '>','message'=>'Số lượng phải lớn hơn 0'],
            [['image'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderID' => 'Order ID',
            'productID' => 'Product ID',
            'quantity' => 'Quantity',
            'unitPrice' => 'Unit Price',
            'unitPriceVn' => 'Unit Price Vn',
            'discount' => 'Discount',
            'totalPrice' => 'Total Price',
            'totalPriceVn' => 'Total Price Vn',
            'size' => 'Size',
            'color' => 'Color',
            'image' => 'Image',
            'noteProduct' => 'Note Product',
            'createDate' => 'Create Date',
            'orderNumber' => 'Order Number',
            'shipDate' => 'Ship Date',
            'billDate' => 'Bill Date',
            'fulFilled' => 'Ful Filled',
        ];
    }

    public function getOrder()
    {
        return $this->hasMany(TbOrders::className(), ['orderID' => 'orderID'])->one();
    }

  

    public function getProduct()
    {
        return $this->hasMany(TbProduct::className(), ['productID' => 'productID'])->one();
    }
}
