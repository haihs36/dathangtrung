<?php

namespace common\models;

use common\components\CommonLib;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbOrders;

/**
 * TbOrderSearch represents the model behind the search form about `common\models\TbOrders`.
 */
class TbOrderSearch extends TbOrders
{
    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['orderID', 'shippingStatus', 'customerID', 'businessID', 'staffsID', 'paymentID', 'totalPayment', 'totalShip', 'totalWeight', 'totalPaid',
                'totalIncurred', 'totalForfeit', 'cny', 'orderFee', 'staffdiscountTotal', 'weightCharge', 'discountDeals', 'weightDiscount', 'shipperID', 'orderNumber', 'active', 'status'], 'integer'],
            [['image', 'identify', 'provinID', 'orderStaff', 'staffDiscount', 'orderDate', 'shipDate', 'paymentDate', 'noteIncurred', 'noteOrder', 'startDate', 'endDate', 'name', 'totalOrderTQ','note_company'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     * isBook = 1 don hang da dat
     * isBook = 0 don hang co canh bao
     */
    public function search($params, $isBook = null,$limit = 20)
    {

        $query = TbOrders::find()
            ->select(['d.image','o.requestPay','o.noteOrder','o.isBad','o.isCheck','o.categoryName', 'c.fullname as cfullname','o.note_company', 'o.isBox','o.orderFee','o.totalShipVn', 'o.phikiemhang', 'o.phidonggo',  'o.quantity', 'o.weightCharge', 'c.username as cusername', 'o.shipDate', 'o.debtAmount', 'o.paymentDate', 'p.name', 'su.sourceName', 'o.finshDate', 'o.vnDate', 'o.shippingDate', 'o.deliveryDate', 's.actualPayment',
                'o.businessID', 'o.totalIncurred', 'o.setDate', 'o.totalOrderTQ', 'o.customerID', 'o.orderID', 'o.identify', 'o.totalPayment', 'o.shippingStatus', 'o.status', 'o.orderStaff',
                'o.orderDate', 'o.totalQuantity', 'o.totalWeight', 'o.totalWeightPrice', 'o.totalOrder', 'o.totalPaid', 's.discount', 's.shipmentFee', 's.shopProductID'])

            ->from(self::tableName() . ' o')
            ->leftJoin(TbOrderSupplier::tableName() . ' s', 'o.orderID = s.orderID')
            ->leftJoin(TbCustomers::tableName() . ' c', 'o.customerID = c.id')
            ->leftJoin(TbSupplier::tableName() . ' su', 's.supplierID = su.supplierID')
            ->leftJoin(TbOrdersDetail::tableName() . ' d', 'o.orderID = d.orderID')
            // ->leftJoin(TbProduct::tableName() . ' pd', 'd.productID = pd.productID')
            ->leftJoin(Province::tableName() . ' p', 'o.provinID = p.id');

        if (isset($params['orderNumber']) && !empty($params['orderNumber'])) {
            $query->filterWhere(['o.identify' => trim($params['orderNumber'])]);
        }
        if (isset($params['barcode']) && !empty($params['barcode'])) {
            $query->leftJoin(TbTransfercode::tableName() . ' t', 'o.orderID = t.orderID')
                ->filterWhere(['t.transferID' => trim($params['barcode'])]);
        }
        if (isset($params['shopProductID']) && !empty($params['shopProductID'])) {
            $query->filterWhere(['like', 's.shopProductID', trim($params['shopProductID'])]);
        }

       /* if (isset($params['productname']) && !empty($params['productname'])) {
            $query->filterWhere(['like', 'pd.name', trim($params['productname'])]); 
        }*/

        $query = $query->groupBy('o.orderID');//bo sung hien thi anh 

        $status = $this->status;

        if(empty($status) || $status == 1){
            $sort = ['orderID' => SORT_DESC];
        }else{
             $sort = ['setDate' => SORT_DESC];
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $limit],
            'sort' => ['defaultOrder' => $sort]
        ]);

        $this->load($params);
        /*if (!$this->validate()) { 
            return $dataProvider;
        }*/

        $finterDate = '';
       
        switch ($status) {
            case 11:
                $finterDate = 'o.setDate';
                break;
            case 3:
                $finterDate = 'o.shipDate';
                break;
            case 4:
                $finterDate = 'o.deliveryDate';
                break;
            case 6:
                $finterDate = 'o.paymentDate';
                break;
            case 8:
                $finterDate = 'o.shippingDate';
                break;
            case 9:
                $finterDate = 'o.vnDate';
                break;

        }

        $startDate = $endDate = '';
        if (!empty($finterDate)) {
            if (!empty($this->startDate)) {
                $startDate = str_replace('/', '-', $this->startDate);
            }

            if (!empty($this->endDate)) {
                $endDate = str_replace('/', '-', $this->endDate);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($endDate) + 86400 - 1);
            } elseif (!empty($startDate) && empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($startDate) + 86400 - 1);
            }

            if (!empty($startDate) && !empty($endDate) && !empty($finterDate)) {
                $query->andFilterWhere(['>=', $finterDate, $startDate])
                    ->andFilterWhere(['<=', $finterDate, $endDate]);

               $status = isset($params['status']) ? $params['status'] : null;
            }
        }

        $user = \Yii::$app->user->identity;

        if ($user->role == BUSINESS) {
            $query->andFilterWhere(['o.businessID' => $user->id]);
        }

        if ($user->role == COMPLAIN) {
            $query->andFilterWhere(['o.userComplain' => $user->id]);
        }

        if ($user->role == STAFFS) {
            $query->andFilterWhere(['o.orderStaff' => $user->id]);
        }

        if (in_array($user->role, [ADMIN])) {
            $query->andFilterWhere(['or',
                ['o.orderStaff' => $this->businessID],
                ['o.businessID' => $this->businessID]
            ]);
        }

//        if ($isBook === 0) {
//            $query->andFilterWhere(['o.active' => 1]);//don hang co canh bao
//        }


        $role = Yii::$app->user->identity->role;

        switch ($role){
            case WAREHOUSE: //nhan vien kho vn
                if(!in_array($status, [3, 4, 6])){
                    $query->andFilterWhere(['o.status' => [3, 4, 6]]);
                }
                break;
            case CARE_TQ: //cskh TQ
                if(in_array($status,[1,2,11,5]))
                    $status = 3;

                $query->andFilterWhere(['o.status' => $status]);

                break;
            default:
                $query->andFilterWhere(['o.status' => $status]);
                break;
        }

        $query->andFilterWhere([
            'o.shippingStatus' => $this->shippingStatus,
            'o.customerID' => $this->customerID,
            'o.provinID' => $this->provinID,
        ]);


        return $dataProvider;
    }
    /*thong ke tat ca don hang cua nhan vien kinh doanh*/
    public function searchOrder($params, $model, $role = '')
    {
        $query = TbOrders::find()
            ->select([
                'o.isCheck', 'o.isBox', 'o.shipDate','o.debtAmount', 'o.orderFee', 'o.orderStaff', 'o.discountKg', 'o.staffdiscountTotal', 'o.discountBusiness', 'o.discountRate', 'o.totalDiscount', 'o.paymentDate', 'p.name', 'o.finshDate', 'o.vnDate', 'o.shippingDate', 'o.deliveryDate', 's.actualPayment',
                'o.businessID', 'o.totalIncurred', 'o.setDate', 'o.customerID', 'o.orderID', 'o.identify', 'o.totalPayment', 'o.shippingStatus', 'o.status',
                'o.orderDate', 'o.totalQuantity', 'o.totalWeight', 'o.totalWeightPrice', 'o.totalOrder', 'o.totalPaid'
            ])
            ->from(self::tableName() . ' o')
            ->leftJoin(TbOrderSupplier::tableName() . ' s', 'o.orderID = s.orderID')
            ->leftJoin(Province::tableName() . ' p', 'o.provinID = p.id')
            ->groupBy('o.orderID');


        if(!empty($model->status)) {
           // $model->status = array_map('intval', $model->status);
            $query->filterWhere(['o.status' => $model->status]);
        }

        if ($model->businessID) {
            $query->andFilterWhere(['o.businessID' => $model->businessID]);
        }

        if ($model->staffID) {
            $query->andFilterWhere(['o.orderStaff' => $model->staffID]);
        }

        $startDate = '';
        $endDate ='';

        if (!empty($model->startDate)) {
            $startDate = str_replace('/', '-', $model->startDate);
            $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        }

        if (!empty($model->endDate)) {
            $endDate = str_replace('/', '-', $model->endDate);
            $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
        }

        /*$arrStatus = [1,11,2,3,4,8,9,5,6];
       $startFilter = '';
       $endFilter = '';

       if(!empty($model->status)){
           foreach($arrStatus as $id){
               switch ($id) {
                   case 1:
                       if(in_array($id,$model->status)){
                           if(empty($startFilter))
                               $startFilter = 'o.orderDate';

                           $endFilter = 'o.orderDate';
                       }
                       break;
                   case 11:
                       if(in_array($id,$model->status)){
                           if(empty($startFilter))
                               $startFilter = 'o.setDate';

                           $endFilter = 'o.setDate';
                       }
                       break;
                   case 2:
                       if(in_array($id,$model->status)){
                           if(empty($startFilter))
                               $startFilter = 'o.buyDate';

                           $endFilter = 'o.buyDate';
                       }
                       break;
                   case 3:
                       if(in_array($id,$model->status)){
                           if(empty($startFilter))
                               $startFilter = 'o.shipDate';

                           $endFilter = 'o.shipDate';
                       }
                       break;
                   case 4:
                       if(in_array($id,$model->status)){
                           if(empty($startFilter))
                               $startFilter = 'o.deliveryDate';

                           $endFilter = 'o.deliveryDate';
                       }
                       break;

                   case 8:
                       if(in_array($id,$model->status)){
                           if(empty($startFilter))
                               $startFilter = 'o.shippingDate';

                           $endFilter = 'o.shippingDate';
                       }
                       break;
                   case 9:
                       if(in_array($id,$model->status)){
                           if(empty($startFilter))
                               $startFilter = 'o.vnDate';

                           $endFilter = 'o.vnDate';
                       }
                       break;
                   case 6:
                       if(in_array($id,$model->status)){
                           if(empty($startFilter))
                               $startFilter = 'o.paymentDate';

                           $endFilter = 'o.paymentDate';
                       }
                       break;
                   case 5:
                       if(in_array($id,$model->status)){
                           if(empty($startFilter))
                               $startFilter = 'o.finshDate';

                           $endFilter = 'o.finshDate';
                       }
                       break;

               }
           }
       }

       if(empty($startFilter) && empty($endFilter)){
           $startFilter = 'o.paymentDate';
           $endFilter = 'o.paymentDate';
       }*/

        $finterDate = '';
        switch ($model->status) {
            case 11:
                $finterDate = 'o.setDate';
                break;
            case 2:
                $finterDate = 'o.buyDate';
                break;
            case 3:
                $finterDate = 'o.shipDate';
                break;
            case 4:
                $finterDate = 'o.deliveryDate';
                break;
            case 5:
                $finterDate = 'o.finshDate';
                break;
            case 6:
                $finterDate = 'o.paymentDate';
                break;
            case 8:
                $finterDate = 'o.shippingDate';
                break;
            case 9:
                $finterDate = 'o.vnDate';
                break;
//            default:
//                $finterDate = 'o.paymentDate';
//                break;
        }

        if (isset($startDate) && isset($endDate) &&  !empty($finterDate)) {
            $query->andFilterWhere(['between', $finterDate, $startDate , $endDate]);
           /* $query->andFilterWhere(['or',
                    ['between', $startFilter, $startDate , $endDate],
                    ['between', $endFilter, $startDate , $endDate],
            ]);*/
        }

        $query->andFilterWhere([
            'o.identify'   => $this->identify,
            'o.customerID' => $this->customerID
        ]);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 20],
            'sort'       => ['defaultOrder' => ['orderID' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        return $dataProvider;
    }


  /*thong ke tat ca don hang cua nhan vien kinh doanh*/
    public function searchOrder2($params,$model,$role)
    {

        $query = TbOrders::find()
            ->select(['o.isCheck', 'o.isBox', 'o.shipDate', 'o.orderFee', 'o.orderStaff', 'o.discountKg', 'o.staffdiscountTotal', 'o.discountBusiness', 'o.discountRate', 'o.totalDiscount', 'o.paymentDate', 'p.name', 'o.finshDate', 'o.vnDate', 'o.shippingDate', 'o.deliveryDate', 's.actualPayment',
                'o.businessID', 'o.totalIncurred', 'o.setDate', 'o.customerID', 'o.orderID', 'o.identify', 'o.totalPayment', 'o.shippingStatus', 'o.status',
                'o.orderDate', 'o.totalQuantity', 'o.totalWeight', 'o.totalWeightPrice', 'o.totalOrder', 'o.totalPaid'])
            ->from(self::tableName() . ' o')
            ->leftJoin(TbOrderSupplier::tableName() . ' s', 'o.orderID = s.orderID')
            ->leftJoin(Province::tableName() . ' p', 'o.provinID = p.id')
            ->groupBy('o.orderID');

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 20],
            'sort'       => ['defaultOrder' => ['orderID' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->filterWhere(['o.status' => $model->status]);
        if (!empty($role)) {
            if ($role == BUSINESS) {
                $query->andFilterWhere(['o.businessID' => $model->userId]);
            }
            if ($role == STAFFS) {
                $query->andFilterWhere(['o.orderStaff' => $model->userId]);
            }
        }

       // $startDate = date("Y-m-d", strtotime(date('Y-01-01')));
       // $endDate = date('Y-m-d 23:59:59');

         $finterDate = 'o.paymentDate';
        $startDate = $endDate = ''; 


        if (!empty($model->startDate)) {
            $startDate = str_replace('/', '-', $model->startDate);
            $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        }

        if (!empty($model->endDate)) {
            $endDate = str_replace('/', '-', $model->endDate);
            $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
        }

        switch ($model->status) {
            case 11:
                $finterDate = 'o.setDate';
                break;
            case 2:
                $finterDate = 'o.buyDate';
                break;
            case 3:
                $finterDate = 'o.shipDate';
                break;
            case 4:
                $finterDate = 'o.deliveryDate';
                break;
            case 5:
                $finterDate = 'o.finshDate';
                break;
            case 6:
                $finterDate = 'o.paymentDate';
                break;
            case 8:
                $finterDate = 'o.shippingDate';
                break;
            case 9:
                $finterDate = 'o.vnDate';
                break;
            default:
                $finterDate = 'o.paymentDate';
                break;
        }

        if(isset($startDate) && isset($endDate)) {
            $query->andFilterWhere(['>=', $finterDate, $startDate])->andFilterWhere(['<=', $finterDate, $endDate]);
        }

        $query->andFilterWhere([
            'o.identify'   => $this->identify,
            'o.customerID' => $this->customerID
        ]);


        return $dataProvider;
    }
    
    public function searchHome($params)
    {

        $query = TbOrders::find()
            ->select(['d.image','o.isBad', 'o.isCheck','o.noteOrder', 'o.isBox', 'o.quantity', 'o.shipDate', 'o.deposit', 'o.noteCoc', 'o.paymentDate', 'o.debtAmount', 'o.orderFee', 'p.name', 'o.finshDate', 'o.vnDate', 'o.shippingDate', 'o.deliveryDate', 's.actualPayment', 'o.businessID', 'o.totalIncurred', 'o.setDate', 'o.customerID', 'o.orderID', 'o.identify', 'o.totalPayment', 'o.shippingStatus', 'o.status', 'o.orderDate',
                'o.totalQuantity', 'o.totalWeight', 'o.totalWeightPrice', 'o.totalOrder', 'o.totalPaid', 'su.sourceName', 'su.shopName'])
            ->from(self::tableName() . ' o')
            ->leftJoin(TbOrderSupplier::tableName() . ' s', 'o.orderID = s.orderID')
            ->leftJoin(TbOrdersDetail::tableName() . ' d', 'o.orderID = d.orderID')
            ->leftJoin(Province::tableName() . ' p', 'o.provinID = p.id')
            ->leftJoin(TbSupplier::tableName() . ' su', 's.supplierID = su.supplierID')
            ->groupBy('o.orderID');//bo sung hien thi anh

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 30],
            'sort' => ['defaultOrder' => ['orderID' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $finterDate = 'o.orderDate';
        $startDate = $endDate = '';
        if (!empty($finterDate)) {
            if (!empty($this->startDate)) {
                $startDate = str_replace('/', '-', $this->startDate);
            }
            if (!empty($this->endDate)) {
                $endDate = str_replace('/', '-', $this->endDate);
            }


            if (!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($endDate) + 86400 - 1);

                $query->andFilterWhere(['>=', $finterDate, $startDate])
                    ->andFilterWhere(['<=', $finterDate, $endDate]);
            } elseif (!empty($startDate) && empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($startDate) + 86400 - 1);
                $query->andFilterWhere(['>=', $finterDate, $startDate])
                    ->andFilterWhere(['<=', $finterDate, $endDate]);
            }
        }

        $query->andFilterWhere([
            'o.orderID' => $this->orderID,
            'o.customerID' => \Yii::$app->user->id,
            'o.paymentID' => $this->paymentID,
            'o.shipperID' => $this->shipperID,
            'o.orderNumber' => $this->orderNumber,
            'o.shipDate' => $this->shipDate,
            'o.paymentDate' => $this->paymentDate,
            'o.shippingStatus' => $this->shippingStatus,
            'o.status' => $this->status,
            'o.identify' => $this->identify,
        ]);

        //$query->andFilterWhere(['like', 'o.identify', $this->identify]);

        return $dataProvider;
    }

    public function searchHomeApi($params, $offset = 0, $limit = 10)
    {

        $query = TbOrders::find()
            ->select(['d.image', 'o.orderID', 'o.identify','o.totalPayment','o.totalPaid','o.debtAmount', 'o.status', 'o.orderDate', 'o.deposit',
                'o.totalQuantity', 'o.totalOrder', 'su.sourceName', 'su.shopName'])
            ->from(TbOrders::tableName() . ' o')
            // ->leftJoin(TbOrders::tableName() . ' o', 'o.orderID = t.orderID')
            ->leftJoin(TbOrderSupplier::tableName() . ' s', 'o.orderID = s.orderID')
            ->leftJoin(TbOrdersDetail::tableName() . ' d', 'o.orderID = d.orderID')
            ->leftJoin(TbSupplier::tableName() . ' su', 's.supplierID = su.supplierID')
            ->groupBy('o.orderID');//bo sung hien thi anh

//
        $this->load($params);


        $finterDate = 'o.orderDate';
        $startDate = $endDate = '';
        if (!empty($finterDate)) {
            if (!empty($this->startDate)) {
                $startDate = str_replace('/', '-', $this->startDate);
            }
            if (!empty($this->endDate)) {
                $endDate = str_replace('/', '-', $this->endDate);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($endDate) + 86400 - 1);

                $query->andFilterWhere(['>=', $finterDate, $startDate])
                    ->andFilterWhere(['<=', $finterDate, $endDate]);
            } elseif (!empty($startDate) && empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($startDate) + 86400 - 1);
                $query->andFilterWhere(['>=', $finterDate, $startDate])
                    ->andFilterWhere(['<=', $finterDate, $endDate]);
            }
        }


        $query->andFilterWhere([
            'o.orderID' => $this->orderID,
            'o.customerID' => \Yii::$app->user->id,
            //  'o.paymentID' => $this->paymentID,
            // 'o.shipperID' => $this->shipperID,
            'o.orderNumber' => $this->orderNumber,
            // 'o.shipDate' => $this->shipDate,
            // 'o.paymentDate' => $this->paymentDate,
            'o.shippingStatus' => $this->shippingStatus,
            'o.status' => $this->status,
        ]);

        $query->andFilterWhere(['or',
            ['like', 'o.identify', $this->identify],
            // ['like', 't.transferID', $this->identify]
        ]);


        return $query->orderBy(['orderID' => SORT_DESC])->offset($offset)->limit($limit)->all();


//          return $dataProvider;
    }

}
