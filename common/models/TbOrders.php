<?php

namespace common\models;

use cms\models\Lo;
use cms\models\TbCities;
use cms\models\TbDistricts;
use cms\models\Warehouse;
use Yii;
use yii\data\Pagination;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_orders".
 *
 * @property integer $orderID
 * @property string $identify
 * @property integer $customerID
 * @property integer $businessID
 * @property integer $staffsID
 * @property integer $paymentID
 * @property double $totalOrder
 * @property double $totalOrderTQ
 * @property double $totalPayment
 * @property double $totalShipVn
 * @property double $totalShip
 * @property double $totalWeight
 * @property double $totalWeightPrice
 * @property double $totalPaid
 * @property double $totalIncurred
 * @property double $totalForfeit
 * @property integer $totalQuantity
 * @property double $cny
 * @property double $orderFee
 * @property double $discountDeals
 * @property double $weightCharge
 * @property double $weightDiscount
 * @property double $totalDiscount
 * @property double $totalDiscountVn
 * @property integer $shipperID
 * @property integer $orderNumber
 * @property integer $active
 * @property integer $status
 * @property integer $shippingStatus
 * @property string $noteIncurred
 * @property string $noteOrder
 * @property string $noteCoc
 * @property integer $perCent
 * @property double $discountRate
 * @property double $discountKg
 * @property double $discountBusiness
 * @property double $incurredFee
 * @property double $debtAmount
 * @property double $phikiemhang
 * @property double $phidonggo
 * @property string $orderDate
 * @property string $setDate
 * @property string $shipDate
 * @property string $deliveryDate
 * @property string $shippingDate
 * @property string $vnDate
 * @property string $paymentDate
 * @property string $finshDate
 * @property string $shipAddress 
  * @property string $image
 * @property integer $provinID
 * @property integer $isBox
 * @property integer $isCheck
 * @property integer $orderStaff
 * @property integer $quantity
 * @property integer $staffDiscount
 * @property double $staffdiscountTotal 
  * @property double $deposit
 */
class TbOrders extends \yii\db\ActiveRecord
{

    public $image;
    public $name;
    public $actualPayment;
    public $sourceName;
    public $shopName;
    public $discount;
    public $shipmentFee;
    public $shopProductID;
    public $cusername;
    public $cfullname;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_orders';
    }
    public function rules()
    {
        return [
            [['identify', 'customerID'], 'required'],
            [['weightDiscount', 'discountKg','cny'], 'customRequired', 'skipOnError' => false, 'skipOnEmpty' => false],

            [['isBox','isCheck','customerID','orderStaff','provinID', 'businessID', 'staffsID', 'paymentID', 'totalQuantity', 'shipperID', 'orderNumber', 'active', 'status', 'shippingStatus', 'perCent','quantity'], 'integer'],
            [['totalOrder', 'totalOrderTQ','staffdiscountTotal', 'totalPayment', 'totalShipVn', 'totalShip', 'totalWeight', 'totalWeightPrice', 'totalPaid', 'totalIncurred', 'totalForfeit', 'orderFee',
                'discountDeals', 'weightCharge','staffDiscount', 'totalDiscount', 'totalDiscountVn', 'discountRate','discountBusiness', 'incurredFee', 'debtAmount', 'phikiemhang', 'phidonggo','deposit'], 'number'],
            [['noteIncurred', 'noteOrder', 'noteCoc'], 'string'],
            [['orderDate', 'setDate', 'shipDate', 'deliveryDate','finshDate', 'shippingDate', 'vnDate', 'paymentDate','sourceName','discount'], 'safe'],
            [['identify','shipAddress','name','image'], 'string'],
            //['weightDiscount', 'customRequired', 'skipOnError' => false, 'skipOnEmpty' => false],
//            [['discountKg','weightDiscount'], 'isPrice']weightDiscount
        ];
    }

    /*public function isPrice( $attribute, $params ) {
        $this->discountKg = str_replace(['.',','],'',$this->discountKg);

        if(!is_numeric($this->discountKg)){
            $this->addError('price', 'Tiền chiết khấu cân nặng không hợp lệ');
            return false;
        }
        return true;
    }*/

    public function customRequired( $attribute, $params ) {
        $this->discountKg = str_replace(['.',','],'',$this->discountKg);
        if (!empty($this->discountKg) && (!is_numeric($this->discountKg) || $this->discountKg < 0)) {
            $this->addError('discountKg', 'Tiền chiết khấu cân nặng không hợp lệ.');
            return false;
        }

        $this->weightDiscount = str_replace(['.',','],'',$this->weightDiscount);
        if(!empty($this->weightDiscount) && (!is_numeric($this->weightDiscount) || $this->discountKg < 0)){
            $this->addError('weightDiscount', 'Giá cân nặng không hợp lệ');
            return false;
        }
        $this->cny = str_replace(['.',','],'',$this->cny);
        if(!empty($this->cny) && (!is_numeric($this->cny) || $this->cny < 0)){
            $this->addError('weightDiscount', 'Tỷ giá không hợp lệ');
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'orderID' => 'Order ID',
            'identify' => 'Identify',
            'incurredFee' => 'incurredFee',
            'customerID' => 'Customer ID',
            'businessID' => 'Business ID',
            'staffsID' => 'Staffs ID',
            'paymentID' => 'Payment ID',
            'totalPayment' => 'Total Payment',
            'totalShip' => 'Total Ship',
            'totalWeight' => 'Total Weight',
            'totalPaid' => 'Total Paid',
            'totalIncurred' => 'Total Incurred',
            'totalForfeit' => 'Total Forfeit',
            'cny' => 'Cny',
            'orderFee' => 'Order Fee',
            'weightCharge' => 'Weight Charge',
            'discountDeals' => 'Discount Deals',
            'weightDiscount' => 'Weight Discount',
            'shipperID' => 'Shipper ID',
            'orderNumber' => 'Order Number',
            'orderDate' => 'Order Date',
            'shipDate' => 'Ship Date',
            'requiredDate' => 'Required Date',
            'paymentDate' => 'Payment Date',
            'active' => 'Active',
            'shippingStatus' => 'shipping Status',
            'status' => 'Status',
            'noteIncurred' => 'Note Incurred',
            'noteOrder' => 'Note Order',
            'totalOrder' => 'total Orderr',
        ];
    }

    public function getOrderSupplier()
    {
        return $this->hasOne(TbOrderSupplier::className(), ['orderID' => 'orderID'])->one();
    }
    public function getProvin()
    {
        return $this->hasOne(Province::className(), ['id' => 'provinID'])->one();
    }

    //don khieu nai
    public function getComplain()
    {
        return $this->hasMany(TbComplain::className(), ['orderID' => 'orderID'])->one();
    }

    /*relation to  banking*/
    public function getBanking()
    {
        return $this->hasMany(TbAccountBanking::className(), ['customerID' => 'customerID'])->one();
    }

    /*relation to  orderdetail*/
    public function getOdersDetail()
    {
        return $this->hasMany(TbOrdersDetail::className(), ['orderID' => 'orderID'])->all();
    }


    /*relation to  member*/
    public function getCustomer()
    {
        return $this->hasMany(TbCustomers::className(), ['id' => 'customerID'])->one();
    }

    /*kd*/
    public function getBusiness()
    {
        return $this->hasMany(User::className(), ['id' => 'businessID'])->one();
    }

    /*relation to  ke toan*/
    public function getStaff()
    {
        return $this->hasMany(User::className(), ['id' => 'staffsID'])->one();
    }

    public static function getStatus($status = 0)
    {
        switch ($status) {
            case 1:
                return '<span class="label label-warning">Chờ đặt cọc</span>';
                break;
            case 2:
                return '<span class="label label-info">Đang đặt hàng</span>';
                break;
            case 3:
                return '<span class="label label-success">Đã đặt hàng</span>';
                break;
            case 4:
                return '<span class="label label-danger">Shop xưởng giao</span>';
                break;
            case 5:
                return '<span class="label label-default">Đã Hủy</span>';
                break;
            case 6:
                return '<span class="label label-finish">Đã trả hàng</span>';
                break;
            case 7:
                return '<span class="label label-warning">Chờ báo giá</span>';
                break;
            case 8:
                return '<span class="label label-success">Đang vận chuyển</span>';
                break;
            case 9:
                return '<span class="label label-primary">Kho VN nhận</span>';
                break;
            case 11:
                return '<span class="label label-wait">Đã đặt cọc</span>';
                break;
        }

        return null;
    }


    public function getAction()
    {
        $user = \Yii::$app->user->identity;
        $del = '';
        if($user->username == ADMINISTRATOR){
            $del .= ' <li>
                            <a href="' . Url::to(['orders/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete" title="đơn hàng '.$this->identify.'" data-toggle="tooltip" data-original-title="Xóa đơn hàng"><i class="glyphicon glyphicon-remove font-12"></i> Xóa đơn hàng</a>
                        </li>';
        }

        $isBookOrder = '';
        if($this->status == 2 && $user->role == ADMIN){
            $isBookOrder = '<li><a class="booking" href="' . Url::to(['orders/booking', 'id' => $this->primaryKey]) . '" data-toggle="tooltip" data-original-title="Đã đặt hàng"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Đã đặt hàng</a></li>';
        }
          $complain = '<li><a href="' . Url::to(['complain/create', 'id' => $this->primaryKey]) . '" title="Nộp đơn khiếu nại"  data-toggle="tooltip"   class="khieunai" id="khieunai" data-uk-tooltip=""><i class="fa fa-warning text-yellow"></i> Nộp đơn khiếu nại</a></li>';


        $sendSms = '';
        $sendSms .= ' <li>
                            <a href="javascript:void(0)" data-id="'.$this->primaryKey.'" class="send-sms" data-toggle="tooltip" data-original-title="Gửi thông báo"><i class="fa fa-fw fa-commenting"></i> Gửi thông báo</a>
                        </li>';

        return '<div class="dropdown actions">    
                    <span style="cursor: pointer"  data-toggle="dropdown" aria-expanded="true"><i class="glyphicon glyphicon-menu-hamburger"></i> Tùy chọn</span>        
                    
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                        <li><a target="_blank" href="' . Url::to(['orders/view', 'id' => $this->primaryKey]) . '" data-toggle="tooltip" data-original-title="Chi tiết đơn hàng"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Chi tiết đơn hàng</a></li>                        
                        <li><a target="_blank" href="' . Url::to(['orders/costs', 'id' => $this->primaryKey]) . '" data-toggle="tooltip" data-original-title="Cài đặt phí"><i class="fa fa-mail-forward" aria-hidden="true"></i> Cài đặt phí</a></li>                        
                        <li><a target="_blank" href="' . Url::to(['orders/approval', 'id' => $this->primaryKey]) . '" data-toggle="tooltip" data-original-title="Duyệt đơn hàng"><i class="fa fa-mail-forward" aria-hidden="true"></i> Duyệt đơn hàng</a></li>                        
                       
                        <li role="presentation" class="divider"></li>      
                                  
                        ' . $isBookOrder . '
                        ' . $del . '
                         ' . $complain . '
                        ' . $sendSms . '
                       
                    </ul>
                          
                   
                </div>';// <a class="pl5" href=""> <button type="button" class="btn bg-orange margin">Chốt đơn</button></a>

    }

    public function actionOut()
    {
        $complain = '<a href="' . Url::to(['complain/create', 'id' => $this->primaryKey]) . '" data-toggle="tooltip"  data-original-title="Khiếu nại đơn hàng"  title="Khiếu nại đơn hàng"  class="btn bg-purple btn-flat margin"><i class="fa fa-fw fa-frown-o"></i> Khiếu nại</a>';
//        if ($this->status == 3) {
//            //$complain = '';
//        }
        $message = '';
        //<a href="' . Url::to(['orders/export', 'id' => $this->primaryKey]) . '" data-uk-tooltip="" title="Thông báo"><i class="fa fa-bell-o" aria-hidden="true"></i><span class="label label-warning">1</span></a>

        $del = '';
        if ($this->status == 1) {
            $del = '<a href="' . Url::to(['orders/delete', 'id' => $this->primaryKey]) . '" title="Xóa đơn hàng" data-toggle="tooltip" onclick="return confirm(\'Bạn có chắc chắn muốn xóa đơn hàng này không ?\')" class="btn bg-olive margin"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> Hủy đơn</a>';
        }
        /*<a href="' . Url::to(['orders/export', 'id' => $this->primaryKey],true) . '" data-uk-tooltip="" title="Xuất excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>*/

        return '<div class="btn-group btn-group-sm" role="group">
                        <a target="_blank" href="' . Url::toRoute(['orders/view', 'id' => $this->primaryKey]) . '" data-toggle="tooltip"  data-original-title="Chi tiết đơn hàng" title="Chi tiết đơn hàng" class="btn bg-aqua-active color-palette margin"><i class="fa fa-fw fa-eye"></i> Chi tiết</a>                       
                         ' . $complain . '
                         ' . $del . '
                        
                    </div>
                ';
    }

    //danh sach kho da ban
    /*
     * status = 1; da thanh toan
     *        = 4; da tra hang
     * */
    public static function getShopPayment($customerID, $loID, $status = 1, $limit = 100)
    {
        $query = new \yii\db\Query;
        $query->select('`a`.`id`,d.id as did, `a`.`orderID`, `s`.`supplierID`, `d`.`unitPrice`, `d`.`totalPrice`, `d`.`unitPriceVn`,
                      a.status as sstatus,a.shopProductID,a.billLadinID,a.weight,a.incurredFee,a.shipmentFee,a.actualPayment,
                      a.shopPriceTQ,a.shipmentVn,a.kgFee,a.shippingStatus,a.quantity as osquantity,a.shopPrice,a.shopPriceKg,a.shopPriceTotal,
                     o.identify,o.customerID,o.status as ostatus,o.perCent,o.discountDeals,    
                     `d`.`totalPriceVn`, `d`.`image`, `d`.`color`, `d`.`size`, `d`.`noteProduct`, `d`.`quantity`, `p`.`name` AS `title`, 
                     `p`.`link`, `d`.status as dstatus,`d`.status, `s`.`shopID`, `s`.`shopName`')
            ->from('tb_order_supplier a')
            ->leftJoin('tb_orders_detail d', 'a.id = d.`orderSupplierID`')
            ->leftJoin('tb_product p', 'd.productID = p.`productID`')
            ->leftJoin('tb_supplier s', 'a.supplierID = s.supplierID')
            ->leftJoin('tb_orders o', 'a.orderID = o.orderID')
            ->where(['a.isSelected' => 1, 'o.customerID' => $customerID]);
//            ->where(['a.status'=>$status,'a.isSelected'=> 1,'o.customerID'=>$customerID]);
        if ($loID) {
            $query->leftJoin(Warehouse::tableName() . ' w', 'a.id = w.shopID')
                ->andWhere(['w.loID' => $loID]);
        }


        return $query->orderBy('a.paymentDate desc')->limit($limit)->all();

    }

    public static function getOrderDetailView($orderID, $userID = 0, $statusProduct = false)
    {
        $query = new \yii\db\Query;
        $query->select('d.id,o.orderID,d.qty_receive,d.note_receive,s.supplierID ,d.unitPrice,d.totalPrice,d.unitPriceVn,d.totalPriceVn,d.image,d.color,d.size,s.sourceName,p.shopProductID,d.noteProduct,d.quantity,p.name as title,p.link,d.status,s.shopID,s.shopName')
              ->from('tb_orders o')
              ->leftJoin('tb_orders_detail d', 'o.orderID = d.orderID')
              ->leftJoin('tb_product p', 'd.productID = p.productID')
              ->leftJoin('tb_supplier s', 'p.supplierID = s.supplierID')
              ->where(['o.orderID' => (int)$orderID]);

        if ($userID) {
            $query->andWhere(['o.customerID' => $userID]);
        }
        $user = \Yii::$app->user->identity;
        if (!in_array($user->role, [ADMIN, WAREHOUSE, STAFFS,CLERK])) {
            $query->andWhere(['o.businessID' => $user->id]);
        }

        if ($statusProduct) {
            $query->andWhere('d.status != 3');
        }

        return $query->createCommand()->queryAll();
    }

    //order detail
    public static function getOrderDetail($orderID, $userID = 0, $statusProduct = false)
    {
        $query = new \yii\db\Query;
        $query->select('d.id,o.orderID,s.supplierID ,d.unitPrice,d.totalPrice,d.unitPriceVn,d.totalPriceVn,d.image,d.color,d.size,d.noteProduct,d.quantity,p.name as title,p.link,d.status,s.shopID,s.shopName')
            ->from('tb_orders o')
            ->leftJoin('tb_orders_detail d', 'o.orderID = d.orderID')
            ->leftJoin('tb_product p', 'd.productID = p.productID')
            ->leftJoin('tb_supplier s', 'p.supplierID = s.supplierID')
            ->where(['o.orderID' => (int)$orderID]);

        if ($userID) {
            $query->andWhere(['o.customerID' => $userID]);
        }
        /*else{
            $user = \Yii::$app->user->identity;
            if (!in_array($user->role, [ADMIN, WAREHOUSE, STAFFS])) {
                $query->andWhere(['o.businessID' => $user->id]);
            }
        }*/



        if ($statusProduct) {
            $query->andWhere('d.status != 3');
        }

        return $query->createCommand()->queryAll();
    }


    public static function getOrderCount($isBook = null, $isCustomer = false)
    {
        $user = Yii::$app->user->identity;

        $query = self::find()
            ->select([
                'status',
                "SUM(CASE WHEN requestPay = 1 AND status = 2 THEN 1 ELSE 0 END) AS requestPayCount",
                "COUNT(*) as total"
            ])
            ->groupBy('status');

        // Lọc theo trạng thái đơn hàng
        if ($isBook || $user->role == WAREHOUSE) {
            $query->andFilterWhere(['status' => [3, 4, 6]]);
        } elseif ($isBook === 0) {
            $query->andFilterWhere(['active' => 1]);
        }

        // Lọc theo vai trò người dùng
        switch ($user->role) {
            case BUSINESS:
                $query->andFilterWhere(['businessID' => (int)$user->id]);
                break;
            case STAFFS:
                $query->andFilterWhere(['orderStaff' => (int)$user->id]);
                break;
            case COMPLAIN:
                $query->andFilterWhere(['userComplain' => (int)$user->id]);
                break;
        }

        if ($isCustomer) {
            $query->andFilterWhere(['customerID' => (int)$user->id]);
        }

        $raw = $query->asArray()->all();

        // Khởi tạo mảng kết quả
        $orderStatus = array_fill(0, 13, 0);

        foreach ($raw as $row) {
            $status = (int)$row['status'];
            $count = (int)$row['total'];
            $reqPay = (int)$row['requestPayCount'];

            if (isset($orderStatus[$status])) {
                $orderStatus[$status] += $count;
            }

            $orderStatus[12] += $reqPay;  // đơn yêu cầu thanh toán
            $orderStatus[0] += $count;    // tổng số đơn
        }

        return $orderStatus;
    }


    public static function getOrderCount1($isBook = null,$isCustomer = false)
    {
        $query = self::find()->select(['status']);
        /*
         * isBook = 1; da dat hang
         *        = 0 co canh bao
         * */
        $user = Yii::$app->user->identity;

        if ($isBook || $user->role == WAREHOUSE) {
            $query->andFilterWhere(['status' => [3, 4, 6]]);
        } else if ($isBook === 0) {
            $query->andFilterWhere(['active' => 1]);
        }

        switch ($user->role) {
            case BUSINESS: //KD
                $query->andFilterWhere(['businessID' => $user->id]);
                break;
            /*   case STAFFS: //vn Thanh toan
                   $query->andFilterWhere(['staffsID' => $user->id]);
                   break;*/

        }

        if($isCustomer){
            $query->andFilterWhere(['customerID' => $user->id]);
        }

        $order = $query->asArray()->all();
        $orderStatus = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0,11 => 0,10=>0];
        if ($order) {
            foreach ($order as $item) {
                if ($item['status'] == 1) $orderStatus[1]++;
                if ($item['status'] == 2) $orderStatus[2]++;
                if ($item['status'] == 3) $orderStatus[3]++;
                if ($item['status'] == 4) $orderStatus[4]++;
                if ($item['status'] == 5) $orderStatus[5]++;
                if ($item['status'] == 6) $orderStatus[6]++;
                if ($item['status'] == 7) $orderStatus[7]++;
                if ($item['status'] == 8) $orderStatus[8]++;
                if ($item['status'] == 9) $orderStatus[9]++;
                if ($item['status'] == 10) $orderStatus[10]++;
                if ($item['status'] == 11) $orderStatus[11]++;

                $orderStatus[0]++;
            }
        }

        return $orderStatus;
    }

    public static function getTotal($provider, $columnName)
    {
        $total = 0;
        foreach ($provider as $item) {
            $total += $item[$columnName];
        }
        return '<label class="vnd-unit">' . number_format($total) . '<em>đ</em></label>';
    }

    public static function getTotalCoc($provider, $columnName)
    {
        $total = 0;
        foreach ($provider as $item) {
            if ($item['status'] == 1) {
                $perCent    = \common\components\CommonLib::getPercentDeposit($item[$columnName],$item['customerID'],$item['deposit']);
//                var_dump($item[$columnName]);
//                var_dump($perCent);die;
                $coc_money  = ($item[$columnName] * $perCent / 100);
                $total +=  $coc_money;
            }

        }
        return '<label class="vnd-unit">' . number_format($total) . '<em>đ</em></label>';
    }
}
