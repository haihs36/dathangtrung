<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_product".
 *
 * @property integer $productID
 * @property integer $supplierID
 * @property string $shopProductID
 * @property string $md5
 * @property string $sourceName
 * @property string $shopID
 * @property string $name
 * @property integer $quantity
 * @property double $unitPrice
 * @property string $image
 * @property string $link
 * @property string $slug
 * @property string $description
 * @property string $text
 * @property string $thumb
 * @property integer $time
 * @property integer $is_hot
 * @property integer $status
 * @property integer $views
 * @property string $create_date
 * @property string $color
 * @property string $size
 */
class TbProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supplierID', 'quantity','time', 'is_hot', 'status', 'views'], 'integer'],
            [['name'], 'required'],
            [['unitPrice'], 'number'],
            [['description', 'text','shopProductID'], 'string'],
            [['create_date'], 'safe'],
            [['md5'], 'string', 'max' => 300],
            [['sourceName', 'shopID', 'name', 'link'], 'string', 'max' => 200],
            [['image', 'thumb'], 'string', 'max' => 500],
            [['slug', 'color', 'size'], 'string', 'max' => 200]
        ];
    }

    public function getShop()
    {
        return $this->hasMany(TbSupplier::className(), ['supplierID' => 'supplierID'])->one();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'productID' => 'Product ID',
            'supplierID' => 'Supplier ID',
            'shopProductID' => 'Shop Product ID',
            'md5' => 'Md5',
            'sourceName' => 'source Name',
            'shopID' => 'Shop ID',
            'name' => 'Name',
            'quantity' => 'Quantity',
            'unitPrice' => 'Unit Price',
            'image' => 'Image',
            'link' => 'Link',
            'slug' => 'Slug',
            'description' => 'Description',
            'text' => 'Text',
            'thumb' => 'Thumb',
            'time' => 'Time',
            'is_hot' => 'Is Hot',
            'status' => 'Status',
            'views' => 'Views',
            'create_date' => 'Create Date',
            'color' => 'Color',
            'size' => 'Size',
        ];
    }
}
