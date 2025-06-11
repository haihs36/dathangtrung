<?php

    namespace cms\controllers;

    use cms\models\TbChart;
    use common\components\CommonLib;
    use common\models\AccessRule;
    use common\models\TbAccountBanking;
    use common\models\TbOrderSearch;
    use Yii;
    use yii\filters\AccessControl;
    use yii\web\User;

    class ChartController extends \common\components\Controller
    {

        public function behaviors()
        {
            return [
                'access' => [
                    'class'      => AccessControl::className(),
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'only'       => ['index'],
                    'rules'      => [
                        [
                            'actions' => ['index'],
                            'allow'   => true,
                            'roles'   => [BUSINESS, ADMIN, STAFFS, CLERK],
                        ],
                        [
                            'allow' => false,
                            'roles' => [WAREHOUSE],
                        ],
                    ],
                ],
            ];
        }

        public function actionIndex()
        {


            $model = new TbChart();
            $role = '';
            $roleLogin = Yii::$app->user->identity->role;
            if(in_array($roleLogin,[BUSINESS,STAFFS])){
                $model->userId = Yii::$app->user->id;
                $role = $roleLogin;
            }

            if ($model->load(Yii::$app->request->get())) {
              //  var_dump($model->userId);die;
                if ($model->userId) {
                    $personnel = \common\models\User::findOne($model->userId);
                    if ($personnel) {
                        $role = $personnel->role;
                    }
                }
                switch ($model->charType) {
                    case 1: //doanh thu don hang khong co phi dv
                        $sum = CommonLib::getChart($model, 'totalOrder',$role);
                        $data = CommonLib::getChartQuarter(date('Y'), 'totalOrder', $model,$role);

                        $title = 'Doanh thu đơn hàng không có phí dịch vụ';
                        break;
                    case 2: //doanh thu % phi dv
                        $sum = CommonLib::getChart($model, 'orderFee',$role);
                        $data = CommonLib::getChartQuarter(date('Y'), 'orderFee', $model,$role);
                        $title = 'Tổng doanh thu % phí dịch vụ';
                        break;
                    case 3: //tong tien can nang cac don hang
                        $sum = CommonLib::getChart($model, 'totalWeightPrice',$role);
                        $data = CommonLib::getChartQuarter(date('Y'), 'totalWeightPrice', $model,$role);
                        $title = 'Tổng doanh thu cân nặng các đơn hàng';
                        break;
                    /*  case 4: //tien ship cho khach o vn
                          $sum = CommonLib::getChart($model,'totalShipVn');
                          $data = CommonLib::getChartQuarter(date('Y'),'totalShipVn',$model);
                          $title = 'Tổng tiền ship các đơn hàng';

                          break;*/
                    case 5: //phi ship noi dia TQ
                        $sum_china = CommonLib::getChart($model, 'totalShip',$role);
                        $data_china = CommonLib::getChartQuarter(date('Y'), 'totalShip', $model,$role);
                        $title = 'Thống kê phí ship nội địa';
                        break;
                    /*case 6: //tien boi thuong
                        $sum = CommonLib::getChart($model,'totalForfeit');
                        $data = CommonLib::getChartQuarter(date('Y'),'totalForfeit',$model);
                        $title = 'Tổng tiền bồi thường các đơn hàng';
                        break;*/
                    case 7: // tong chiet khau duoc
                        $sum_china = CommonLib::getChart($model, 'totalDiscount',$role);
                        $data_china = CommonLib::getChartQuarter(date('Y'), 'totalDiscount', $model,$role);
                        $title = 'Tổng tiền chiết khẩu được';
                        break;
                    case 8: //thong ke chiet khau don hang cho kinh doanh
                        $sum = 0;
                        $data = [];
                        if ($role == BUSINESS) {
                            $filed = 'discountBusiness';
                        }
                        if ($role == STAFFS) {
                            $filed = 'staffdiscountTotal';
                        }

                        if (isset($filed)) {
                            $sum = CommonLib::getChart($model, $filed,$role);
                            $data = CommonLib::getChartQuarter(date('Y'), $filed, $model,$role);
                        }

                        $title = 'Thống kê chiết khấu đơn hàng cho nhân viên';
                        break;
                    case 9: //thanh toan thuc te actualPayment
                        $sum_china = CommonLib::getChart($model, 'actualPayment',$role);
                        $sum_china = round($sum_china, 2);
                        $data_china = CommonLib::getChartQuarter(date('Y'), 'actualPayment', $model,$role);
                        $title = 'Thống kê thanh toán thực tế';
                        break;
                    case 10: //tong du vi
                        $query = (new \yii\db\Query())->from(TbAccountBanking::tableName());
                        $sum = $query->sum('totalResidual');
                        $data = [];
                        $title = 'Thống kê tổng số dư ví';
                        break;
                    case 11: //tong con no
                        $sum = CommonLib::getChart($model, 'debtAmount',$role);
                        $data = [];
                        $data = CommonLib::getChartQuarter(date('Y'), 'debtAmount', $model,$role);
                        $title = 'Thống kê tổng tiền nợ';
                        break;
                    default:
                        $sum = CommonLib::getChart($model, 'totalPayment',$role);
                        $data = CommonLib::getChartQuarter(date('Y'), 'totalPayment', $model,$role);

                        $title = 'Thống kê tổng';
                        break;

                }
            } else {
                $sum = CommonLib::getChart($model, 'totalPayment',$role);
                $title = 'Thống kê tổng';
                $data = CommonLib::getChartQuarter(date('Y'), 'totalPayment', $model,$role);
            }

            $searchModel = new TbOrderSearch();
            $params = Yii::$app->request->queryParams;
            $params['TbOrderSearch']['startDate'] = isset($params['TbChart']['startDate']) ? $params['TbChart']['startDate'] : null;
            $params['TbOrderSearch']['status'] = isset($params['TbChart']['status']) ? $params['TbChart']['status'] : null;
            $params['TbOrderSearch']['endDate'] = isset($params['TbChart']['endDate']) ? $params['TbChart']['endDate'] : null;

            $dataProvider = $searchModel->searchOrder($params,$model,$role);


            return $this->render('index', [
                'model'        => $model,
                'year'         => date('Y'),
                'sum'          => isset($sum) ? $sum : 0,
                'sum_china'    => isset($sum_china) ? $sum_china : 0,
                'title'        => $title,
                'data'         => isset($data) ? json_encode($data) : 0,
                'data_china'   => isset($data_china) ? json_encode($data_china) : 0,
                'searchModel'  => isset($searchModel) ? $searchModel : null,
                'dataProvider' => isset($dataProvider) ? $dataProvider : null,
            ]);
        }

    }
