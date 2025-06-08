<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_orders_session".
 *
 * @property integer $id
 * @property string $md5
 * @property integer $customerID
 * @property string $shopProductID
 * @property string $source_site
 * @property string $shop_id
 * @property string $shop_name
 * @property string $shop_address
 * @property string $title
 * @property string $link
 * @property integer $quantity
 * @property double $unitPrice
 * @property double $unitPriceVn
 * @property double $discounts
 * @property string $size
 * @property string $color
 * @property double $totalPrice
 * @property double $totalPriceVn
 * @property integer $isCheck
 * @property string $image
 * @property string $noteProduct
 * @property string $createDate
 */
class TbOrdersSession extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_orders_session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['md5','image', 'customerID', 'title', 'quantity', 'unitPrice', 'link'], 'required', 'message'=>'{attribute} là bắt buộc'],
            [['customerID', 'quantity','isCheck'], 'integer'],
            [['unitPrice', 'totalPrice', 'unitPriceVn', 'discounts', 'totalPriceVn'], 'number'],
            [['createDate'], 'safe'],
            ['image', 'safe'],
            [['shop_name', 'title', 'link', 'image'], 'string', 'max' => 500],
            [['shopProductID'], 'string', 'max' => 250],
            [['source_site'], 'string', 'max' => 1000],
            [['md5'], 'string', 'max' => 1000],
            [['shop_id', 'size', 'color'], 'string', 'max' => 200],
            [['shop_address'], 'string', 'max' => 300],
            [['noteProduct'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'md5' => 'Md5',
            'customerID' => 'Customer ID',
            'shopProductID' => 'Shop Product ID',
            'source_site' => 'Source Site',
            'shop_id' => 'Shop ID',
            'shop_name' => 'Shop Name',
            'shop_address' => 'Shop Address',
            'title' => 'Tên sản phẩm',
            'link' => 'Đường dẫn',
            'quantity' => 'Số lượng',
            'unitPrice' => 'Tiền TQ',
            'unitPriceVn' => 'Unit Price Vn',
            'discounts' => 'Discounts',
            'size' => 'Kích thướng',
            'color' => 'Màu sắc',
            'totalPrice' => 'Total Price',
            'totalPriceVn' => 'Total Price Vn',
            'image' => 'Ảnh đại diện',
            'noteProduct' => 'Ghi chú',
            'createDate' => 'Create Date',
        ];
    }
}
