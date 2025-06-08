<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_orders_weight".
 *
 * @property string $id
 * @property integer $orderID
 * @property double $from
 * @property double $to
 * @property double $price
 * @property integer $provinID
 * @property string $createDate
 * @property string $identify
 */
class TbOrdersWeight extends \yii\db\ActiveRecord
{
    public $name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_orders_weight';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderID', 'from', 'to', 'price', 'identify'], 'required'],
            [['orderID', 'provinID'], 'integer'],
            [['from', 'to', 'price'], 'number'],
            [['createDate'], 'safe'],
            [['identify'], 'string', 'max' => 100],
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
            'from' => 'From',
            'to' => 'To',
            'price' => 'Price',
            'provinID' => 'Provin ID',
            'createDate' => 'Create Date',
            'identify' => 'Identify',
        ];
    }
}
