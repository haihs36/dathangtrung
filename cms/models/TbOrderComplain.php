<?php

namespace cms\models;

use common\models\TbOrders;
use common\models\TbOrderSupplier;
use common\models\TbSupplier;
use Yii;

/**
 * This is the model class for table "tb_order_complain".
 *
 * @property integer $id
 * @property integer $shopID
 * @property integer $orderID
 * @property integer $userID
 * @property string $title
 * @property string $content
 */
class TbOrderComplain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_order_complain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shopID', 'orderID', 'title'], 'required'],
            [['shopID', 'orderID','userID'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 200]
        ];
    }
    public function getShop(){
        return $this->hasOne(TbSupplier::className(),['supplierID'=>'shopID'])->one();
    }
    public function getOrder(){
        return $this->hasOne(TbOrders::className(),['orderID'=>'orderID'])->one();
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shopID' => 'Shop ID',
            'orderID' => 'Order ID',
            'title' => 'Title',
            'content' => 'Content',
        ];
    }
}
