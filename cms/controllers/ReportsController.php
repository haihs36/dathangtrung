<?php
/**
 * Created by PhpStorm.
 * User: haihs3
 * Date: 12/11/2020
 * Time: 4:17 PM
 */

namespace cms\controllers;

use common\components\Controller;
use common\models\TbCustomers;
use common\models\TbOrderSearch;
use common\models\User;
use common\models\TbOrders;
use common\models\TbAccountBanking;
use common\models\TbOrderSupplier;
use yii\db\Query;
use Yii;
use yii\filters\AccessControl;
use common\models\AccessRule;
use yii\filters\VerbFilter;

class ReportsController extends Controller
{


    public function behaviors()
    {

        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'create', 'update', 'delete','return'],
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => [STAFFS],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete','return'],
                        'allow' => true,
                        'roles' => [ADMIN,CLERK],
                    ],
                ],
            ],
        ];
    }

    //filter status
    private $listStatus = [
        '6' => 'Đã trả hàng',
        '11' => 'Đã đặt cọc'
    ];

    /**
     * @param $params
     * @return array
     * @author cuonghh
     * @function handle time use query
     */
    public function handleTime($params)
    {
        //default time 1 month
        $startDate = date('Y-m-d 00:00:00', strtotime("-1 months"));
        $endDate = date('Y-m-d 23:59:59');
        //handle time
        if (!empty($params['startDate'])) {
            $startDate = str_replace('/', '-', $params['startDate']);
        }
        if (!empty($params['endDate'])) {
            $endDate = str_replace('/', '-', $params['endDate']);
        }
        if (!empty($startDate) && !empty($endDate)) {
            $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
            $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
        }
        if (!empty($params['startDate']) && empty($params['endDate'])) {
            $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
            $endDate = date('Y-m-d 23:59:59', strtotime($startDate));
        }
        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

    /**
     * @return string
     * @author cuonghh
     * @function report revenue
     */
    public function actionIndex()
    {
        //get data response method get
        $reqData = Yii::$app->request->get();
        //time query
        $queryTime = $this->handleTime($reqData);
        $startDate = $queryTime['startDate'];
        $endDate = $queryTime['endDate'];
        $status = empty($reqData['status']) ? 6 : $reqData['status'];
        //get query
        $listOrder = (new Query())
            ->select([
                'u.id',
                'u.fullname',
                'o.businessID',
                'sum(o.totalPayment) as totalPayment',
                'sum(o.totalOrder) as totalOrder',
                'sum(o.totalShipVn) as totalShipVn',
                'sum(o.phidonggo) as phidonggo',
                'sum(o.orderFee) as orderFee',
                'sum(o.phikiemhang) as phikiemhang',
                'sum(o.totalWeightPrice) as totalWeightPrice'
            ])
            ->from(TbOrders::tableName() . ' o')
            ->innerJoin(User::tableName() . ' u', 'o.businessID = u.id')
            ->where(['o.status' => $status])
            ->andFilterWhere(['>=', 'o.orderDate', $startDate])
            ->andFilterWhere(['<=', 'o.orderDate', $endDate])
            ->groupBy('o.businessID')
            ->all();
        $sumOrder = (new Query())
            ->select([
                'sum(o.totalPayment) as totalPayment',
                'sum(o.totalOrder) as totalOrder',
                'sum(o.totalShipVn) as totalShipVn',
                'sum(o.phidonggo) as phidonggo',
                'sum(o.orderFee) as orderFee',
                'sum(o.phikiemhang) as phikiemhang',
                'sum(o.totalWeightPrice) as totalWeightPrice'
            ])
            ->from(TbOrders::tableName() . ' o')
            ->innerJoin(User::tableName() . ' u', 'o.businessID = u.id')
            ->where(['o.status' => $status])
            ->andFilterWhere(['>=', 'o.orderDate', $startDate])
            ->andFilterWhere(['<=', 'o.orderDate', $endDate])
            ->all();
        // param search form
        $params = [
            'startDate' => date('d/m/Y', strtotime($startDate)),
            'endDate' => date('d/m/Y', strtotime($endDate)),
            'status' => $status
        ];
        return $this->render('index', [
            'params' => $params,
            'listOrder' => $listOrder,
            'sumOrder' => $sumOrder,
            'listStatus' => $this->listStatus
        ]);

    }

    /**
     * @author cuonghh
     * @function chart revenue
     */
    public function actionAjaxChartRevenue()
    {
        //get data response method post
        $reqData = Yii::$app->request->post();
        if($reqData){
            //query
            $queryTime = $this->handleTime($reqData);
            $startDate = $queryTime['startDate'];
            $endDate = $queryTime['endDate'];
            $status = $reqData['status'];
            //get data
            $listOrder = (new Query())
                ->select([
                    'u.id',
                    'u.fullname',
                    'o.businessID',
                    'sum(o.totalPayment) as totalPayment',
                    'DATE(o.orderDate) as orderDate'
                ])
                ->from(TbOrders::tableName() . ' o')
                ->innerJoin(User::tableName() . ' u', 'o.businessID = u.id')
                ->where(['o.status' => $status])
                ->andFilterWhere(['>=', 'o.orderDate', $startDate])
                ->andFilterWhere(['<=', 'o.orderDate', $endDate])
                ->groupBy(['o.businessID', 'DATE(o.orderDate)'])
                ->all();
            $date = $dataHandle = [];
            $listBusinessID = array_values(array_unique(array_values((array_column($listOrder, 'businessID')))));
            foreach($listBusinessID as $keyBusinessID => $businessID){
                $i = 0;
                for($d = strtotime($startDate); $d < strtotime($endDate); $d = strtotime('+1 day', $d)){
                    $date[] = date('d/m/Y', $d);
                    $dataHandle[$keyBusinessID]['data'][$i] = 0;
                    foreach($listOrder as $key => $order){
                        if( strtotime($order['orderDate']) == $d && $businessID == $order['businessID']){
                            $dataHandle[$keyBusinessID]['name'] = $order['fullname'];
                            $dataHandle[$keyBusinessID]['data'][$i] = (int)$order['totalPayment'];
                            unset($listOrder[$key]);
                        }
                    }
                    $i++;
                }
            }
            $resData = [
                'code' => '00',
                'date' => $date,
                'dataHandle' => $dataHandle
            ];
        } else {
            $resData = [
                'code' => '01',
                'error' => 'Method Invalid'
            ];
        }
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_JSON,
            'data' => $resData
        ]);
    }

    /**
     * @return string
     * @author cuonghh
     * @function report customer
     */
    public function actionCustomers()
    {
        //get data response method get
        $reqData = Yii::$app->request->get();
        //time query
        $queryTime = $this->handleTime($reqData);
        $startDate = $queryTime['startDate'];
        $endDate = $queryTime['endDate'];
        //convert to timestamp
        $timestampSD = strtotime($startDate);
        $timestampED = strtotime($endDate);
        //get user sale
        $listSale = (new Query())
            ->from(User::tableName() . ' o')
            ->where(['o.role' => '10'])
            ->all();
        $arrIdSale = array_values((array_column($listSale, 'id')));
        //count order
        $listOrder = (new Query())
            ->select([
                'o.businessID',
                'o.status',
                'COUNT(o.status) as totalStatus',
            ])
            ->from(TbOrders::tableName() . ' o')
            ->innerJoin(User::tableName() . ' u', 'o.businessID = u.id')
            ->where(['in', 'o.businessID', $arrIdSale])
            ->andFilterWhere(['>=', 'o.orderDate', $startDate])
            ->andFilterWhere(['<=', 'o.orderDate', $endDate])
            ->groupBy(['o.businessID', 'o.status'])
            ->all();
        //count create customer
        $listCus = (new Query())
            ->select([
                'c.userID',
                'COUNT(c.id) as totalCustomer',
            ])
            ->from(TbCustomers::tableName() . ' c')
            ->innerJoin(User::tableName() . ' u', 'c.userID = u.id')
            ->andFilterWhere(['>=', 'c.created_at', $timestampSD])
            ->andFilterWhere(['<=', 'c.created_at', $timestampED])
            ->groupBy('c.userID')
            ->all();
        //count customer topup
        $listCusTopup = (new Query())
            ->select([
                'c.userID',
                'COUNT(ab.customerID) as totalCusTopup',
            ])
            ->from(TbCustomers::tableName() . ' c')
            ->innerJoin(User::tableName() . ' u', 'c.userID = u.id')
            ->innerJoin(TbAccountBanking::tableName() . ' ab', 'c.id = ab.customerID')
            ->andFilterWhere(['>=', 'ab.create_date', $startDate])
            ->andFilterWhere(['<=', 'ab.create_date', $endDate])
            ->groupBy('c.userID')
            ->all();
        //data response
        $resData = [];
        /*1 - Chờ đặt cọc, 11 - Đã đặt cọc, 2 - Đang đặt hàng, 3 - Đã đặt hàng, 4 - Shop xưởng giao,
        5 - Đã hủy, 6 - Đã trả hàng, 8 - Đang vận chuyển, 9 - Kho VN nhận*/
        //sum
        $sRegister = $sTopup = $sChoCoc = $sDaCoc = $sDangDH = $sDaDH = $sShopGiao = $sDangVC = $sKhoVN = $sDaTraHang = $sDaHuy = 0;
        //data sale
        $stt = 1;
        foreach ($listSale as $sale) {
            $register = $topup = $choCoc = $daCoc = $dangDH = $daDH = $shopGiao = $dangVC = $khoVN = $daTraHang = $daHuy = $tongDon = 0;
            foreach ($listOrder as $key => $data) {
                if ($data['businessID'] == $sale['id']) {
                    if (!empty($data['status'])) {
                        switch ($data['status']) {
                            case '1':
                                $sChoCoc += $data['totalStatus'];
                                $choCoc = $data['totalStatus'];
                                unset($listOrder[$key]);
                                break;
                            case '11':
                                $sDaCoc += $data['totalStatus'];
                                $daCoc = $data['totalStatus'];
                                unset($listOrder[$key]);
                                break;
                            case '2':
                                $sDangDH += $data['totalStatus'];
                                $dangDH = $data['totalStatus'];
                                unset($listOrder[$key]);
                                break;
                            case '3':
                                $sDaDH += $data['totalStatus'];
                                $daDH = $data['totalStatus'];
                                unset($listOrder[$key]);
                                break;
                            case '4':
                                $sShopGiao += $data['totalStatus'];
                                $shopGiao = $data['totalStatus'];
                                unset($listOrder[$key]);
                                break;
                            case '5':
                                $sDaHuy += $data['totalStatus'];
                                $daHuy = $data['totalStatus'];
                                unset($listOrder[$key]);
                                break;
                            case '6':
                                $sDaTraHang += $data['totalStatus'];
                                $daTraHang = $data['totalStatus'];
                                unset($listOrder[$key]);
                                break;
                            case '8':
                                $sDangVC += $data['totalStatus'];
                                $dangVC = $data['totalStatus'];
                                unset($listOrder[$key]);
                                break;
                            case '9':
                                $sKhoVN += $data['totalStatus'];
                                $khoVN = $data['totalStatus'];
                                unset($listOrder[$key]);
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
            $tongDon = $choCoc + $daCoc + $dangDH + $daDH + $shopGiao + $dangVC + $khoVN + $daTraHang + $daHuy;
            foreach ($listCus as $keyC => $customer) {
                if ($customer['userID'] == $sale['id']) {
                    $sRegister += $customer['totalCustomer'];
                    $register = $customer['totalCustomer'];
                    unset($listCus[$keyC]);
                }
            }
            foreach ($listCusTopup as $keyCTP => $cusTopup) {
                if ($cusTopup['userID'] == $sale['id']) {
                    $sTopup += $cusTopup['totalCusTopup'];
                    $topup = $cusTopup['totalCusTopup'];
                    unset($listCusTopup[$keyCTP]);
                }
            }
            $resData[$stt] = [
                'id' => isset($sale['id']) ? $sale['id'] : '',
                'sale' => isset($sale['fullname']) ? $sale['fullname'] : '',
                'register' => $register,
                'topup' => $topup,
                'choCoc' => $choCoc,
                'daCoc' => $daCoc,
                'dangDH' => $dangDH,
                'daDH' => $daDH,
                'shopGiao' => $shopGiao,
                'dangVC' => $dangVC,
                'khoVN' => $khoVN,
                'daTraHang' => $daTraHang,
                'daHuy' => $daHuy,
                'tongDon' => $tongDon,
            ];
            $stt++;
        }
        //data sum
        $sTongDon = $sChoCoc + $sDaCoc + $sDangDH + $sDaDH + $sShopGiao + $sDangVC + $sKhoVN + $sDaTraHang + $sDaHuy;
        $resData[0] = [
            'id' => '',
            'sale' => 'Tổng',
            'register' => $sRegister,
            'topup' => $sTopup,
            'choCoc' => $sChoCoc,
            'daCoc' => $sDaCoc,
            'dangDH' => $sDangDH,
            'daDH' => $sDaDH,
            'shopGiao' => $sShopGiao,
            'dangVC' => $sDangVC,
            'khoVN' => $sKhoVN,
            'daTraHang' => $sDaTraHang,
            'daHuy' => $sDaHuy,
            'tongDon' => $sTongDon,
        ];
        //sort key asc
        ksort($resData);
        // param search form
        $params = [
            'startDate' => date('d/m/Y', strtotime($startDate)),
            'endDate' => date('d/m/Y', strtotime($endDate)),
        ];
        return $this->render('customers', [
            'params' => $params,
            'resData' => $resData
        ]);

    }

    public function actionBargain()
    {
        //get data response method get
        $reqData = Yii::$app->request->get();
        //time query
        $queryTime = $this->handleTime($reqData);
        $startDate = $queryTime['startDate'];
        $endDate = $queryTime['endDate'];
        $status = empty($reqData['status']) ? 6 : $reqData['status'];
        $customer = empty($reqData['customer']) ? 0 : $reqData['customer'];
        $params = [
            'startDate' => date('d/m/Y', strtotime($startDate)),
            'endDate' => date('d/m/Y', strtotime($endDate)),
            'status' => $status,
            'customer' => $customer
        ];

        //query
        $query = (new Query())
            ->select([
                'sum(o.totalOrder) as totalOrder',
                'sum(o.phidonggo) as phidonggo',
                'sum(o.totalShipVn) as totalShipVn',
                'sum(o.totalPayment) as totalPayment',
                'sum(os.shopPrice) as shopPrice',
            ])
            ->from(TbOrders::tableName() . ' o')
            ->innerJoin(TbOrderSupplier::tableName() . ' os', 'o.orderID = os.orderID')
            ->where(['o.status' => $status])
            ->andFilterWhere(['>=', 'o.orderDate', $startDate])
            ->andFilterWhere(['<=', 'o.orderDate', $endDate]);
        if (!empty($customer))
            $query->andFilterWhere(['o.customerID' => $customer]);
        //get list order
        $sumOrder = $query->all();
        $sumOrder = current($sumOrder);
        $tienSP = $sumOrder['totalOrder'] + $sumOrder['phidonggo'] + $sumOrder['totalShipVn'];
        $bargainPrice = $sumOrder['totalPayment'] - $tienSP;
        if($tienSP == 0)
            $rate = 0;
        else
            $rate = round($bargainPrice / $tienSP, 2) . '%';
        $bargainPrice = number_format($bargainPrice);
        $tienSP = number_format($tienSP);
        $shopPrice = number_format($sumOrder['shopPrice']);
        $sum = [
            'tienSP' => $tienSP,
            'bargainPrice' => $bargainPrice,
            'rate' => $rate,
            'shopPrice' => $shopPrice
        ];

        return $this->render('bargain', [
            'params' => $params,
            'listStatus' => $this->listStatus,
            'sum' => $sum
        ]);
    }

    public function actionAjaxBargain()
    {
        //get data response method get
        $reqData = Yii::$app->request->post();
        //time query
        $queryTime = $this->handleTime($reqData);
        $startDate = $queryTime['startDate'];
        $endDate = $queryTime['endDate'];
        $status = empty($reqData['status']) ? 6 : $reqData['status'];
        $customer = empty($reqData['customer']) ? 0 : $reqData['customer'];

        //pagination data table
        $draw = (int)$reqData['draw'];
        $limit = (int)$reqData['length'];
        $offset = (int)$reqData['start'];
        $data = [];
        //query
        $query = (new Query())
            ->select([
                'o.orderID',
                'o.identify',
                'o.totalOrder',
                'o.phidonggo',
                'o.totalShipVn',
                'o.totalPayment',
                'o.totalOrderTQ',
                'o.totalShip',
                'os.shopPrice',
                'os.actualPayment',
                'os.discount',//tien mac ca
            ])
            ->from(TbOrders::tableName() . ' o')
            ->innerJoin(TbOrderSupplier::tableName() . ' os', 'o.orderID = os.orderID')
            ->where(['o.status' => $status])
            ->andFilterWhere(['>=', 'o.orderDate', $startDate])
            ->andFilterWhere(['<=', 'o.orderDate', $endDate]);

        if (!empty($customer))
            $query->andFilterWhere(['o.customerID' => $customer]);
        //count list
        $total = $query->count();
        //get list order
        $listOrder = $query->orderBy(['o.orderID' => SORT_DESC])->limit($limit)->offset($offset)->all();

        foreach ($listOrder as $key => $order) {
            $tienhang = $order['totalOrderTQ'] + $order['totalShip'] ;// + $order['totalShipVn'];
            $bargainPrice = round($order['discount'],2); //tien mac ca
            $rate = round($bargainPrice / $tienhang, 2) . '%';
            $bargainPrice = number_format($bargainPrice);

            $data[] = [$order['identify'], $tienhang, $bargainPrice , $rate, $order['actualPayment']];
        }

        $arrData = [
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        ];
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_JSON,
            'data' => $arrData,
        ]);
    }
    /**
     * @author cuonghh6
     */
    public function actionWallet()
    {
        //get data response method get
        $reqData = Yii::$app->request->get();
        //time query
        $queryTime = $this->handleTime($reqData);
        $startDate = $queryTime['startDate'];
        $endDate = $queryTime['endDate'];
        $customer = empty($reqData['customer']) ? 0 : $reqData['customer'];
        $params = [
            'startDate' => date('d/m/Y', strtotime($startDate)),
            'endDate' => date('d/m/Y', strtotime($endDate)),
            'customer' => $customer
        ];
        return $this->render('wallet', [
            'params' => $params
        ]);
    }

    /**
     * @author cuonghh6
     */
    public function actionAjaxWallet()
    {
        //get data response method get
        $reqData = Yii::$app->request->post();
        //time query
        $queryTime = $this->handleTime($reqData);
        $startDate = $queryTime['startDate'];
        $endDate = $queryTime['endDate'];
        $customer = empty($reqData['customer']) ? 0 : $reqData['customer'];

        //pagination data table
        $draw = (int)$reqData['draw'];
        $limit = (int)$reqData['length'];
        $offset = (int)$reqData['start'];
        $data = [];
        //query
        $query = (new Query())
            ->select([
                'c.fullname',
                'c.username',
                'ab.customerID',
                'ab.totalMoney',
                'ab.totalResidual',
                'ab.totalReceived',
                'ab.create_date'
            ])
            ->from(TbAccountBanking::tableName() . ' ab')
            ->innerJoin(TbCustomers::tableName() . ' c', 'ab.customerID = c.id')
            ->andFilterWhere(['>=', 'ab.create_date', $startDate])
            ->andFilterWhere(['<=', 'ab.create_date', $endDate]);
        if (!empty($customer))
            $query->andFilterWhere(['ab.customerID' => $customer]);
        //count list
        $total = $query->count();
        //get list order
        $listWallet = $query->orderBy(['ab.customerID' => SORT_DESC])->limit($limit)->offset($offset)->all();
        foreach ($listWallet as $key => $wallet) {
            $name = $wallet['fullname']. " (" . $wallet['username'] . ")";
            $totalMoney = number_format($wallet['totalMoney']);
            $totalResidual = number_format($wallet['totalResidual']);
            $totalReceived = number_format($wallet['totalReceived']);
            $data[] = [$wallet['customerID'], $name, $totalMoney, $totalReceived, $totalResidual, $wallet['create_date']];
        }
        $arrData = [
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        ];
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_JSON,
            'data' => $arrData,
        ]);
    }
}