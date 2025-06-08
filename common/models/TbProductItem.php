<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_product_item".
 *
 * @property integer $id
 * @property integer $productID
 * @property string $name
 * @property string $value
 */
class TbProductItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_product_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productID'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['value'], 'string', 'max' => 1024]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'productID' => 'Product ID',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }
}
