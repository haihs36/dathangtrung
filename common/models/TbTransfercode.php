<?php

namespace common\models;

use common\components\CommonLib;
use Yii;

/**
 * This is the model class for table "tb_transfercode".
 *
 * @property string $id
 * @property integer $shopID
 * @property integer $businessID
 * @property string $identify
 * @property string $transferID
 * @property integer $orderID
 * @property integer $status
 * @property double $long
 * @property string $createDate
 * @property string $shipDate
 * @property string $payDate
 * @property double $wide
 * @property double $high
 * @property double $kg
 * @property double $kgChange
 * @property double $kgPay
 * @property double $totalPriceKg
 * @property double $kgFee
 * @property double $phidonggo
 * @property double $$phikiemhang
 * @property double $phiship
 * @property string $note
 * @property integer $shipStatus
 * @property integer $type
 * @property integer $quantity
 * @property integer $bagStatus
 */
class TbTransfercode extends \yii\db\ActiveRecord
{

    public $isCheck;
    public $isBox;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_transfercode';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['transferID'], 'required'],
            [['shopID', 'businessID', 'orderID', 'status', 'shipStatus','type','quantity','bagStatus'], 'integer'],
            [['long', 'wide', 'high', 'kg', 'kgChange', 'kgPay', 'totalPriceKg', 'phidonggo', 'phikiemhang', 'phiship', 'kgFee'], 'number'],
            [['createDate', 'shipDate', 'payDate','note'], 'safe'],
            [['identify', 'transferID'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shopID' => 'Shop ID',
            'businessID' => 'Business ID',
            'identify' => 'Identify',
            'transferID' => 'Transfer ID',
            'orderID' => 'Order ID',
            'status' => 'Status',
            'long' => 'Long',
            'createDate' => 'Create Date',
            'shipDate' => 'Ship Date',
            'payDate' => 'Pay Date',
            'wide' => 'Wide',
            'high' => 'High',
            'kg' => 'Kg',
            'type' => 'type',
            'kgChange' => 'Kg Change',
            'kgPay' => 'Kg Pay',
            'note' => 'Note',
            'shipStatus' => 'Ship Status',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if($this->isNewRecord){
                $this->createDate   = date('Y-m-d H:i:s');
            }

            return true;
        } else {
            return false;
        }
    }

    public function getUser(){
        return $this->hasMany(User::className(), ['id' => 'businessID'])->one();
    }

    /**
     * @author Haihs3
     * @deprecated:
     * @createDate: 4/26/2019
     */
    public static function getAllBarcodeByBarcode($barcode){
        $data = TbTransfercode::find()
                              ->select('a.id,a.shopID,a.shipStatus,a.quantity as cquantity,b.quantity,a.status,b.status as ostatus,b.customerID,b.isBox,b.isCheck,b.totalQuantity,b.noteOrder,a.transferID,a.wide,a.long,a.high,a.kg,a.kgChange,a.kgPay,a.note,b.orderID,b.identify,b.totalWeight,p.name,
                                b.totalWeightPrice,b.totalPayment,b.totalPaid,b.debtAmount,b.totalOrder,b.orderFee,b.incurredFee,b.totalShipVn,b.weightDiscount,
                                b.phikiemhang,b.phidonggo')
                              ->from(self::tableName().' a')
                              ->leftJoin(TbOrders::tableName().' b','a.orderID = b.orderID')
                              ->leftJoin(Province::tableName() . ' p', 'b.provinID = p.id')
                              ->where(['a.transferID'=>$barcode])
//                              ->andWhere(['<>','a.shipStatus', 5])
                              ->orderBy('a.createDate DESC')
                              ->asArray()->all();

        return $data;
    }

    //get all ma da ve kho vn
    public static function getAllBarcodeVnByCustomerId($customerId,$shipStatus = 3,$orderStatus = 9){
        $data = TbTransfercode::find()
            ->select('a.id,a.shopID,a.status,a.quantity,a.transferID,a.shipStatus,a.wide,a.long,a.high,a.kg,a.kgChange,a.kgPay,a.note,b.status as ostatus,b.orderID,b.identify,b.totalWeight,
                    b.totalWeightPrice,b.totalPayment,b.totalPaid,b.debtAmount,b.totalOrder,b.orderFee,b.incurredFee,b.totalShipVn,b.weightDiscount,
                    b.phikiemhang,b.phidonggo')
            ->from(TbTransfercode::tableName().' a')
            ->innerJoin(TbOrders::tableName().' b','a.orderID = b.orderID')
            ->where(['a.shipStatus'=>$shipStatus,'b.customerID'=>$customerId,'b.status'=>$orderStatus])
            ->orderBy('a.createDate DESC')
            ->asArray()->all();

        return $data;
    }

    /*
     * $status = 1; load tat ca ma co trang thai 1
     *  nguoc lai load all
     */
    public static function getAllOrderShipVnByCustomerId($customerId,$shipStatus = 3,$status = 0){
        $query = TbShippers::find()
            ->select('a.id,a.status,a.transferID,a.wide,a.long,a.high,a.kg,a.kgChange,a.kgPay,a.note,a.totalPriceKg,a.kgFee')
            ->from(TbShippers::tableName().' si')
            ->innerJoin(TbShipping::tableName().' s','si.id = s.`shipperID`')
            ->innerJoin(TbTransfercode::tableName().' a','s.tranID = a.`id`')
            ->where(['si.userID'=>$customerId,'a.shipStatus'=>$shipStatus,'a.type'=>SHIPPER_TYPE,'s.city'=>1]);
            if($status){
                $query->andWhere(['a.status'=>$status]);
            }

        $data = $query->orderBy('a.createDate DESC')->asArray()->all();

        return $data;
    }

    //get total kg, total price checked payment
    public static function getTotalKgofPrice($customerId){
        $sql = "SELECT `a`.`kgPay`,b.`debtAmount`,b.`orderID`
                                FROM `tb_transfercode` `a` 
                                INNER JOIN `tb_orders` `b` ON a.orderID = b.orderID 
                                WHERE `a`.`shipStatus`=3 AND `b`.`customerID`=$customerId AND `b`.`status`=9 AND a.`status` = 1";

        $data =  TbTransfercode::findBySql($sql)->asArray()->all();

        $arrItem = [
            'debt'=>0,
            'kgPay'=>0,
            'numCode'=>count($data)
        ];

        $exist = [];
        if($data){
            foreach ($data as $item){
                if(!in_array($item['orderID'],$exist)) {
                    $arrItem['debt'] += $item['debtAmount'];
                }

                $exist[] = $item['orderID'];
                $arrItem['kgPay'] += $item['kgPay'];
            }
        }

        return $arrItem;
    }

    public static function getAllBarcodeByOrderId($orderID){
        return self::find()->select('transferID,kg,shipStatus,kgPay')->where(['orderID'=>$orderID])->asArray()->all();
    }
}
