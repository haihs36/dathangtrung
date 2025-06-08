<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_order_supplier".
 *
 * @property string $id
 * @property integer $orderID
 * @property integer $supplierID
 * @property string $billLadinID
 * @property string $shopProductID
 * @property double $cny
 * @property integer $quantity
 * @property double $shopPriceKg
 * @property double $shopPriceTQ
 * @property double $shopPrice
 * @property double $shopPriceTotal
 * @property double $actualPayment
 * @property double $discount
 * @property double $orderFee
 * @property double $incurredFee
 * @property double $weightCharge
 * @property double $discountDeals
 * @property double $weightDiscount
 * @property double $freeCount
 * @property double $shipmentFee
 * @property double $shipmentVn
 * @property double $weight
 * @property double $totalWeight
 * @property string $noteInsite
 * @property string $noteOther
 * @property string $paymentDate
 * @property string $setDate
 * @property integer $shippingStatus
 * @property integer $status
 * @property double $kgFee
 * @property integer $isStock
 * @property integer $isSelected
 * @property string $link
 * * @property double $totalPaid
 */
class TbOrderSupplier extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_order_supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderID'], 'required'],
            [['supplierID','orderID', 'cny', 'quantity','shopPriceTotal', 'shopPrice', 'orderFee','incurredFee',
              'weightCharge', 'discountDeals', 'weightDiscount', 'freeCount',
              'shipmentVn',  'shippingStatus', 'status','kgFee','isStock','isSelected'], 'safe'],
            [['shopPriceKg', 'shopPriceTQ', 'actualPayment', 'discount', 'shipmentFee', 'weight', 'totalWeight','totalPaid'], 'number'],
            [['billLadinID', 'link','shopProductID'], 'string'],
            [['noteInsite', 'noteOther'], 'string', 'max' => 500],
            [['paymentDate','setDate'], 'safe'],
            //['shopProductID', 'validOrderNumber'],
            //['billLadinID', 'validBillLadin'],
        ];
    }
//lay ma van don da thanh toan
    public static function getSupplierofCustomer($customerID,$orderID,$shopID){
        $query = TbOrderSupplier::find()->select('s.*')->from(self::tableName().' s')
                                ->innerJoin(TbOrders::tableName().' o','s.orderID = o.orderID')
                                ->where(['s.orderID' => $orderID, 's.id' => $shopID,'o.customerID'=>$customerID]);// 's.status' => 1,
        return $query->one();

    }
    public static function CheckBillLadin($orderID,$billLadinID,$supplierID){
       $db = TbOrderSupplier::find()
            ->where(['billLadinID' => trim($billLadinID), 'orderID' => $orderID])
            ->andWhere(['!=','supplierID',$supplierID])
            ->one();

       return $db;
    }
    public static function CheckOrderNumber($orderID,$orderNumber,$supplierID){
        $db = TbOrderSupplier::find()
            ->where(['shopProductID' => trim($orderNumber), 'orderID' => $orderID])
            ->andWhere(['!=','supplierID',$supplierID])
            ->one();
        return $db;
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->billLadinID = trim($this->billLadinID);
            $this->shopProductID = trim($this->shopProductID);

            return true;
        } else {
            return false;
        }
    }

    /*public function scenarios()
    {
        $scenarios = [
            'orderNumber' => ['shopProductID'],
        ];
        return array_merge(parent::scenarios(), $scenarios);
    }*/

    /*public function validOrderNumber($attribute, $params)
    {
        if(!empty($this->shopProductID)) {
            if($db = TbOrderSupplier::find()->where(['shopProductID'=>trim($this->shopProductID),'orderID'=>$this->orderID])->exists()){
                var_dump($this->billLadinID);die;
                $this->addError($attribute, 'Mã orderNumber: <b>'.$this->shopProductID.'</b> đã tồn tại ở shop');
                return false;
            }
        }

        return true;
    }
    public function validBillLadin($attribute, $params)
    {
        if(!empty($this->billLadinID)) {
            if(TbOrderSupplier::find()->where(['billLadinID'=>trim($this->billLadinID),'orderID'=>$this->orderID])->exists()){
                $this->addError($attribute, 'Mã vận đơn: <b>'.$this->billLadinID.'</b> đã tồn tại ở shop khác');
                return false;
            }
        }

        return true;
    }*/

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shopPriceTotal' => 'shopPriceTotal',
            'paymentDate' => 'ngay tra hang',
            'orderID' => 'Order ID',
            'incurredFee' => 'incurredFee',
            'supplierID' => 'Supplier ID',
            'billLadinID' => 'Bill Ladin ID',
            'cny' => 'Cny',
            'quantity' => 'Quantity',
            'shopProductID' => 'Ma order_number',
            'shopPriceKg' => 'Shop Price Kg',
            'shopPriceTQ' => 'Shop Price Tq',
            'shopPrice' => 'Shop Price',
            'actualPayment' => 'Actual Payment',
            'discount' => 'Discount',
            'orderFee' => 'Order Fee',
            'weightCharge' => 'Weight Charge',
            'discountDeals' => 'Discount Deals',
            'weightDiscount' => 'Weight Discount',
            'freeCount' => 'Free Count',
            'shipmentFee' => 'Shipment Fee',
            'shipmentVn' => 'Shipment Vn',
            'weight' => 'Weight',
            'totalWeight' => 'Total Weight',
            'noteInsite' => 'Note Insite',
            'noteOther' => 'Note Other',
            'shippingStatus' => 'Shipping Status',
            'status' => 'Status',
            'link' => 'Link',
        ];
    }

    public function getOders()
    {
        return $this->hasMany(TbOrders::className(), ['orderID' => 'orderID'])->one();
    }

    public function getSupplier()
    {
        return $this->hasMany(TbSupplier::className(), ['supplierID' => 'supplierID'])->one();
    }
}
