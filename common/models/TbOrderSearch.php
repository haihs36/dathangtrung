<?php

    namespace common\models;

    use common\components\CommonLib;
    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use common\models\TbOrders;
use yii\db\Expression;

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
                [['image', 'identify', 'provinID', 'orderStaff', 'staffDiscount', 'orderDate', 'shipDate', 'paymentDate', 'noteIncurred', 'noteOrder', 'startDate', 'endDate', 'name', 'totalOrderTQ'], 'safe'],
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
        public function search($params, $isBook = null)
        {
    
            $query = TbOrders::find()
                ->alias('o')
                ->select([
                    'o.orderID',
                    'o.orderDate',
                    'o.status',
                    'o.totalOrder',
                    'o.totalPaid',
                    'o.totalQuantity',
                    'o.customerID',
                    'o.totalWeight',
                    'o.totalWeightPrice',
                    'o.totalOrderTQ',
                    'o.shippingStatus',
                    'o.totalPayment',
                    'o.finshDate',
                    'o.vnDate',
                    'o.shippingDate',
                    'o.deliveryDate',
                    'o.businessID',
                    'o.totalIncurred',
                    'o.setDate',
                    'o.weightCharge',
                    'o.note_company',
                    'o.quantity',
                    'o.isBox',
                    'o.updatetime',
                    'o.weightDiscount',
                    'o.buyDate',
                    'o.requestPay',
                    'o.isCheck',
                    'o.paymentDate',
                    'o.debtAmount',
                    'o.shipDate',
                    'o.orderStaff',
                    'o.identify',
                    'o.tag',
    
                    // Subquery lấy ảnh đầu tiên của order
                    new Expression("(
                    SELECT d.image 
                    FROM tb_orders_detail d 
                    WHERE d.orderID = o.orderID 
                    ORDER BY d.id ASC 
                    LIMIT 1
                ) AS image"),
    
                    // Subquery lấy username của customer
                    new Expression("(
                    SELECT c.username 
                    FROM tb_customers c 
                    WHERE c.id = o.customerID 
                    LIMIT 1
                ) AS cusername"),
    
                    // Subquery lấy sourceName
                    new Expression("(
                    SELECT su.sourceName 
                    FROM tb_order_supplier os 
                    LEFT JOIN tb_supplier su ON su.supplierID = os.supplierID 
                    WHERE os.orderID = o.orderID 
                    LIMIT 1
                ) AS sourceName"),
                ]);
    
            if (!empty($params['orderNumber'])) {
                $query->filterWhere(['o.identify' => trim($params['orderNumber'])]);
            }
    
            if (!empty($params['barcode'])) {
                $query->leftJoin(TbTransfercode::tableName() . ' t', 'o.orderID = t.orderID')
                    ->filterWhere(['t.transferID' => trim($params['barcode'])]);
            }
    
            if (!empty($params['shopProductID'])) {
                $query->filterWhere(['like', 's.shopProductID', trim($params['shopProductID'])]);
            }
    
            if (!empty($params['productname'])) {
                $query->filterWhere(['like', 'pd.name', trim($params['productname'])]);
            }
    
    //        $query->groupBy('o.orderID');
    
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => ['pageSize' => 15],
                'sort' => ['defaultOrder' => ['setDate' => SORT_DESC]]
            ]);
    
            $this->load($params);
    
            $status = $this->status;
            $finterDate = 'o.orderDate';
            $historyStatus = [];
    
            switch ($status) {
                case 1: $finterDate = 'o.orderDate';     $historyStatus = [1]; break;
                case 11:$finterDate = 'o.setDate';       $historyStatus = [11,2,3,4,8,9,6]; break;
                case 2: $finterDate = 'o.orderDate';     $historyStatus = [2,3,4,8,9,6]; break;
                case 3: $finterDate = 'o.shipDate';      $historyStatus = [3,4,8,9,6]; break;
                case 4: $finterDate = 'o.deliveryDate';  $historyStatus = [4,8,9,6]; break;
                case 8: $finterDate = 'o.shippingDate';  $historyStatus = [8,9,6]; break;
                case 9: $finterDate = 'o.vnDate';        $historyStatus = [9,6]; break;
                case 6: $finterDate = 'o.paymentDate';   $historyStatus = [6]; break;
                case 5: $finterDate = 'o.orderDate';     $historyStatus = [5]; break;
            }
    
            if (!empty($this->startDate)) {
                $start = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $this->startDate)));
                $end   = !empty($this->endDate)
                    ? date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $this->endDate)))
                    : date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $this->startDate)));
    
                $query->andFilterWhere(['between', $finterDate, $start, $end]);
            }
    
            $user = Yii::$app->user->identity;
    
            if ($user->role == BUSINESS) {
                $query->andFilterWhere(['o.businessID' => $user->id]);
            } elseif ($user->role == STAFFS) {
                $query->andFilterWhere(['o.orderStaff' => $user->id]);
            } elseif ($user->role == ADMIN) {
                if (!empty($this->orderStaff))  $query->andFilterWhere(['o.orderStaff' => $this->orderStaff]);
                if (!empty($this->businessID)) $query->andFilterWhere(['o.businessID' => $this->businessID]);
            }
    
            if ($isBook === 0) {
                $query->andFilterWhere(['o.active' => 1]);
            }
    
            if ($user->role == WAREHOUSE && !in_array($status, [3,4,6])) {
                $query->andFilterWhere(['o.status' => [3,4,6]]);
            } else {
                if (!empty($params['historyStatus']) && $params['historyStatus'] === 'yes') {
                    $query->andFilterWhere(['in', 'o.status', $historyStatus]);
                } else {
                    $query->andFilterWhere(['o.status' => $status]);
                }
            }
    
            if ($this->requestPay) {
                $query->andFilterWhere(['o.requestPay' => $this->requestPay, 'o.status' => 2]);
            }
    
            $query->andFilterWhere([
                'o.shippingStatus' => $this->shippingStatus,
                'o.customerID'     => $this->customerID,
                'o.provinID'       => $this->provinID,
            ]);
    
            if (!empty($params['cusId'])) {
                $query->andFilterWhere(['o.customerID' => trim($params['cusId'])]);
            }
    
            if (!empty($params['website'])) {
                $query->andFilterWhere(['su.sourceName' => trim($params['website'])]);
            }
    
            if (!empty($params['setOrderStaff'])) {
                if ($params['setOrderStaff'] == 1) {
                    $query->andWhere(['not', ['o.orderStaff' => null]]);
                } elseif ($params['setOrderStaff'] == 2) {
                    $query->andWhere(['o.orderStaff' => null]);
                }
            }
    
            if (!empty($this->tag)) {
                $tag = trim($this->tag);
                $query->andFilterWhere([
                    'or',
                    ['like', 'o.tag', $tag . ',%', false],
                    ['like', 'o.tag', '%,' . $tag, false],
                    ['like', 'o.tag', ',' . $tag . ',']
                ]);
            }
    
            return $dataProvider;
        }
    
        public function searchOld($params, $isBook = null)
        {

            $query = TbOrders::find()
                ->select(['d.image', 'o.isCheck', 'o.isBox', 'o.quantity', 'o.weightCharge', 'c.fullname as cfullname', 'o.shipDate', 'o.debtAmount', 'o.paymentDate', 'p.name', 'su.sourceName', 'o.finshDate', 'o.vnDate', 'o.shippingDate', 'o.deliveryDate', 's.actualPayment',
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


            $query = $query->groupBy('o.orderID');//bo sung hien thi anh


            $dataProvider = new ActiveDataProvider([
                'query'      => $query,
                'pagination' => ['pageSize' => 15],
                'sort'       => ['defaultOrder' => ['orderID' => SORT_DESC]]
            ]);

            $this->load($params);
            /*if (!$this->validate()) {
                return $dataProvider;
            }*/

            $finterDate = '';
            $status = $this->status;
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
                } else if (!empty($startDate) && empty($endDate)) {
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
            if ($user->role == STAFFS) {
                $query->andFilterWhere(['o.orderStaff' => $user->id]);
            }

            if (in_array($user->role, [ADMIN])) {
                $query->andFilterWhere(['or',
                    ['o.orderStaff' => $this->businessID],
                    ['o.businessID' => $this->businessID]
                ]);
            }

            if ($isBook === 0) {
                $query->andFilterWhere(['o.active' => 1]);//don hang co canh bao
            }
            if (Yii::$app->user->identity->role == WAREHOUSE && !in_array($status, [3, 4, 6])) {
                $query->andFilterWhere(['o.status' => [3, 4, 6]]);
            } else {
                $query->andFilterWhere(['o.status' => $status]);
            }

            $query->andFilterWhere([
                'o.shippingStatus' => $this->shippingStatus,
                'o.customerID'     => $this->customerID,
                'o.provinID'       => $this->provinID,
            ]);


            return $dataProvider;
        }

        /*thong ke tat ca don hang cua nhan vien kinh doanh*/
        public function searchOrder($params,$model,$role)
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

            $startDate = date("Y-m-d", strtotime(date('Y-01-01')));
            $endDate = date('Y-m-d 23:59:59');

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
                ->select(['d.image', 'o.isCheck', 'o.isBox', 'o.quantity', 'o.shipDate', 'o.deposit', 'o.noteCoc', 'o.paymentDate', 'o.debtAmount', 'o.orderFee', 'p.name', 'o.finshDate', 'o.vnDate', 'o.shippingDate', 'o.deliveryDate', 's.actualPayment', 'o.businessID', 'o.totalIncurred', 'o.setDate', 'o.customerID', 'o.orderID', 'o.identify', 'o.totalPayment', 'o.shippingStatus', 'o.status', 'o.orderDate', 'o.totalQuantity', 'o.totalWeight', 'o.totalWeightPrice', 'o.totalOrder', 'o.totalPaid'])
                ->from(self::tableName() . ' o')
                ->leftJoin(TbOrderSupplier::tableName() . ' s', 'o.orderID = s.orderID')
                ->leftJoin(TbOrdersDetail::tableName() . ' d', 'o.orderID = d.orderID')
                ->leftJoin(Province::tableName() . ' p', 'o.provinID = p.id')
                ->groupBy('o.orderID');//bo sung hien thi anh

            $dataProvider = new ActiveDataProvider([
                'query'      => $query,
                'pagination' => ['pageSize' => 20],
                'sort'       => ['defaultOrder' => ['orderID' => SORT_DESC]]
            ]);

            $this->load($params);

            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                // $query->where('0=1');
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
                } else if (!empty($startDate) && empty($endDate)) {
                    $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                    $endDate = date('Y-m-d H:i:s', strtotime($startDate) + 86400 - 1);
                    $query->andFilterWhere(['>=', $finterDate, $startDate])
                        ->andFilterWhere(['<=', $finterDate, $endDate]);
                }
            }


            $query->andFilterWhere([
                'o.orderID'        => $this->orderID,
                'o.customerID'     => $this->customerID,
                'o.paymentID'      => $this->paymentID,
                'o.shipperID'      => $this->shipperID,
                'o.orderNumber'    => $this->orderNumber,
                'o.shipDate'       => $this->shipDate,
                'o.paymentDate'    => $this->paymentDate,
                'o.shippingStatus' => $this->shippingStatus,
                'o.status'         => $this->status,
            ]);

            $query->andFilterWhere(['like', 'o.identify', $this->identify]);

            return $dataProvider;
        }


    }
