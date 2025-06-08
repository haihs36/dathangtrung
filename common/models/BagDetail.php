<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_bag_detail".
 *
 * @property string $id
 * @property integer $bagID
 * @property integer $transferID
 * @property integer $orderID
 * @property integer $status
 * @property string $createDate
 * @property string $barcode
 */
class BagDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_bag_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bagID', 'transferID'], 'required'],
            [['bagID', 'transferID','orderID','status'], 'integer'],
            [['createDate','barcode'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bagID' => 'Bag ID',
            'status' => 'status',
            'transferID' => 'Tranfer ID',
            'createDate' => 'Create Date',
        ];
    }

    public static function getBagDetailByBagId($bagId){
        $dataDetail = self::find()->select('a.id,b.totalPriceKg,b.transferID,b.note,b.orderID,b.identify,b.long,b.wide,b.high,b.kg,b.kgChange,b.kgPay,a.createDate,a.status')
                               ->from(self::tableName().' a')
                               ->leftJoin(TbTransfercode::tableName().' b','a.transferID = b.id')
                              // ->leftJoin(TbOrders::tableName().' o','b.orderID = o.orderID')
                               ->where(['a.bagID' => $bagId])->asArray()->all();

        return $dataDetail;
    }
}
