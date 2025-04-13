<?php

namespace cms\models;

use Yii;

/**
 * This is the model class for table "tb_product".
 *
 * @property integer $productID
 * @property integer $supplierID
 * @property string $shopProductID
 * @property string $shopID
 * @property string $sourceName
 * @property string $md5
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
class Product extends \yii\db\ActiveRecord
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
            [['supplierID', 'quantity', 'time', 'is_hot', 'status', 'views'], 'integer'],
            [['name'], 'required'],
            [['unitPrice'], 'number'],
            [['description', 'text'], 'string'],
            [['create_date'], 'safe'],
            [['shopProductID'], 'string', 'max' => 250],
            [['shopID', 'sourceName', 'name', 'link'], 'string', 'max' => 200],
            [['md5'], 'string', 'max' => 300],
            [['image', 'thumb'], 'string', 'max' => 150],
            [['slug', 'color', 'size'], 'string', 'max' => 128],
        ];
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
            'shopID' => 'Shop ID',
            'sourceName' => 'Source Name',
            'md5' => 'Md5',
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
