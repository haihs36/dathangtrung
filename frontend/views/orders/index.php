<?php

use yii\grid\GridView;

$this->title = 'Tất cả đơn hàng';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['orders/index']];


$uLogin = Yii::$app->user->identity;
$orderStatus = Yii::$app->controller->orderStatus;
$totalResidual = isset($uLogin->accounting) ? $uLogin->accounting->totalResidual : 0;
$complainStatus = Yii::$app->controller->complainStatus;
$cusId = \Yii::$app->user->id;
$total_dang_dat_thieu = \common\models\TbOrders::find()->where(['customerID' => $cusId, 'status' => [2, 3, 4, 8, 11]])->sum('debtAmount');
$total_kho_vn_thieu = \common\models\TbOrders::find()->where(['customerID' => $cusId, 'status' => 9])->sum('debtAmount');

$allCoc = \common\models\TbOrders::find()->where(['customerID' => $cusId, 'status' => 1])->asArray()->all();
$total_coc = 0;
if ($allCoc) {
    foreach ($allCoc as $item) {
        $perCent = \common\components\CommonLib::getPercentDeposit($item['totalOrder'], $item['customerID'], $item['deposit']);
        $coc_money = ($item['totalOrder'] * $perCent / 100);
        $total_coc += $coc_money;
    }
}

?>
<?php \yii\widgets\Pjax::begin(['enablePushState' => false]); ?>
<div class="contailer">
    <div class="row">

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <div class="form-group">
                        <label>Chờ đặt cọc</label><br>
                        <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                            <?= $orderStatus[1] ?> đơn
                        </h4>
                    </div>
                    <div class="form-group">
                        <label>Số tiền cần cọc</label><br>
                        <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                            <?= number_format($total_coc) ?> <em>đ</em>
                        </h4>
                    </div>
                </div>
                <div class="icon"><i class="fa fa-usd"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <div class="form-group">
                        <label>Đơn hàng đang đặt</label><br>
                        <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                            <?= $orderStatus[11] + $orderStatus[2] + $orderStatus[3] + $orderStatus[4] + $orderStatus[8] ?>
                            đơn
                        </h4>
                    </div>
                    <div class="form-group">
                        <label>Tổng tiền còn thiếu</label><br>
                        <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                            <?= number_format($total_dang_dat_thieu) ?> <em>đ</em>
                        </h4>
                    </div>
                </div>
                <div class="icon"><i class="fa fa-fw fa-taxi"></i></div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-blue">
                <div class="inner">
                    <div class="form-group">
                        <label>Kho vn nhận</label><br>
                        <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                            <?= $orderStatus[9] ?> đơn
                        </h4>
                    </div>
                    <div class="form-group">
                        <label>Tổng tiền còn thiếu</label><br>
                        <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                            <?= number_format($total_kho_vn_thieu) ?>
                        </h4>
                    </div>
                </div>
                <div class="icon">
                    <i class="ion fa fa-fw fa-database"></i>
                </div>

            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h4><?= $complainStatus[0] ?> đơn</h4>
                    <p>Khiếu nại</p>
                    <br>
                </div>
                <div class="icon">
                    <i class="fa fa-frown-o"></i>
                </div>

            </div>
        </div>
    </div>
</div>
<br><br>

<?php echo $this->render('@app/views/templates/_menu_order', ['status' => $status, 'orderStatus' => $orderStatus]); ?>
<br><br>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<div style="margin-top: 20px">
    <div class="grid-order">

        <?= GridView::widget([
            'id' => 'gridview',
            'showFooter' => true,
            'footerRowOptions' => ['style' => 'font-weight:bold;text-decoration: underline;'],
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'layout' => '<div class="text-right">{items}{summary}{pager}</div>',
            'tableOptions' => ['class' => 'table table-bordered table-hover dataTable text-center', 'id' => 'tbl_manager'],

            'columns' => [
                [
                    'headerOptions' => ['style' => 'width: 3%;'],
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function($model) {
                            if ($model->status == 1) {
                                return ['value' => $model->orderID, 'class' => 'checkbox-row'];
                            }else{
                                return ['disabled' =>true]; // OR ['disabled' => true]
                            }
                     }

                ],
                [
                    'headerOptions' => ['style' => 'width: 5%;'],
                    'label' => 'ID',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<div class="identify"><a target="_blank" href="' . \yii\helpers\Url::to(['orders/view', 'id' => $model->orderID]) . '"><b>' . (isset($model->identify) ? $model->identify : null) . '</b></a></div>';
                    }
                ],
                [
                    'header' => 'Ảnh',
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function ($model) {
                        return '<img width="50" height="50" src="' . htmlspecialchars($model->image) . '">';
                    }
                ],

                [
                    'header' => 'Website',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function ($model) {
                        return \common\components\CommonLib::getIconBySource($model->sourceName);
                    }
                ],

                [
                    'headerOptions' => ['style' => 'width: 10%;'],
                    'contentOptions' => ['data-th' => 'Tổng tiền'],
                    'label' => 'Tổng tiền',
                    'format' => 'raw',
                    'footer' => \common\models\TbOrders::getTotal($dataProvider->models, 'totalOrder'),
                    'value' => function ($model) {
                        return '<label class="vnd-unit">' . number_format($model->totalOrder) . '<em>đ</em></label>';
                    }
                ],

                [
                    'headerOptions' => ['style' => 'width: 14%;'],
                    'contentOptions' => ['data-th' => 'Đặt cọc'],
                    'label' => 'Đặt cọc',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->status == 1) {
                            //xac nhan dat coc tien hang
                            $perCent = \common\components\CommonLib::getPercentDeposit($model->totalOrder, $model->customerID, $model->deposit);
                            $coc_money = ($model->totalOrder * $perCent / 100);

                            $strPercent = '';
                            if ($perCent > 0) {
                                $strPercent = '<div class="txt_coc_' . $model->orderID . '">Đặt cọc: ' . $perCent . '% = <b class="vnd-unit">' . number_format($coc_money) . ' <em>đ</em></b><br><a data-identify="' . $model->identify . '" onclick="main.order_deposit(this,' . $model->orderID . ',1);" class=" btn-deposit-' . $model->orderID . ' order-part btn bg-orange margin ">Đặt cọc ' . $perCent . '%</a>';
                            }

                            $strPercent .= '&nbsp<a data-identify="' . $model->identify . '" onclick="main.order_deposit(this,' . $model->orderID . ',2);" class=" btn-deposit-' . $model->orderID . ' order-full btn bg-maroon ">Đặt cọc 100%</a></div>';

                            return $strPercent;
                        } elseif ($model->totalPaid && $model->debtAmount) {
                            return $model->noteCoc;
                        } elseif (!$model->debtAmount) {
                            return '<b class="vnd-unit">' . number_format($model->totalPaid) . '<em>đ</em></b>';
                        } else {
                            return 'Chưa cọc';
                        }
                    }
                ],
                [
                    'headerOptions' => ['style' => 'width: 10%;'],
                    'contentOptions' => ['data-th' => 'Tiền đã cọc'],
                    'label' => 'Tiền đã cọc',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<b class="vnd-unit">' . number_format($model->totalPaid) . '<em>đ</em></b>';

                    }
                ],
                [
                    'headerOptions' => ['style' => 'width: 8%;'],
                    'contentOptions' => ['data-th' => 'Ngày đặt'],
                    'label' => 'Ngày đặt',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return date('d/m/Y', strtotime($model->orderDate));

                    }
                ],

//                        [
//                            'headerOptions'  => ['style' => 'width: 13%;'],
//                            'label'  => 'Còn thiếu',
//                            'attribute' => 'debtAmount',
//                            'format' => 'raw',
//                            'value'  => function ($model) {
//                                //tong tien don hang + phi phat sinh neu co
//                                $totalPayment = $model->totalPayment;
//                                $label = '';
//                                $money = 0;
//                                if($totalPayment > $model->totalPaid){
//                                    $money = $totalPayment - $model->totalPaid;
//                                    $money = number_format($money);
//                                    $label = '-';
//                                }else if($model->totalPaid > $totalPayment){
//                                    $money = $model->totalPaid - $totalPayment;
//                                    $label = '+';
//                                }
//
//                                return ''.$label.'<b class="vnd-unit">'. ($model->debtAmount).'</b>';
//                            }
//                        ],
//                        [
//                            'contentOptions' => ['data-th' => 'Số lượng'],
//                            'headerOptions'  => ['style' => 'width: 8%;'],
//                            'attribute'     => 'totalQuantity',
//                            'label'         => 'SL',
//                            'format'        => 'raw',
//                            'filter'    => false,
//                            'value'         => function ($model) {
//                                $txt = '_';
//                                if($model->quantity)
//                                    $txt = (int)$model->quantity;
//                                return $model->totalQuantity .' / '.$txt;
//                            }
//                        ],
//
//                        [
//                            'headerOptions'  => ['style' => 'width: 10%;'],
//                            'header'        => ' Kho đích',
//                            'attribute' => 'provinID',
//                            'value' => function ($model) {
//                                $dgo = $model->isBox ? '<label class=" btn-block btn-danger btn-xs"> Đóng gỗ</label>' : '';
//                                $kdem = $model->isCheck ? '<label class=" btn-block btn-warning btn-xs"> Kiểm đếm</label>' : '';
//                                return '<div style=""><b>'.$model->name.'<br/>'.$dgo.' '.$kdem.'</div>';
//                            },
//                            'format' => 'raw'
//                        ],
                [
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
//                            'attribute'      => 'status',  navbar-right
                    'label' => 'Trạng thái',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $html = '
                                        <div class=" navbar-collapse" >
                                            <ul class="nav" style="margin: 0;padding: 0">
                                                <li class="dropdown">
                                                    <a href="#" style="padding: 5px" class="dropdown-toggle" data-toggle="dropdown">' . $model->getStatus($model->status) . '<br/><br/>' . \common\components\CommonLib::getDateByOrderStatus($model) . '</a>';
                        //if(!in_array($model->status,[1,11])) {
                        $html .= '<ul class="dropdown-menu">';


                        if (!empty($model->orderDate) && (strtotime($model->orderDate) > 0)) {
                            $html .= '<li><a href="#"><span class="label label-warning">Ngày đặt</span> ' . date('d/m/Y', strtotime($model->orderDate)) . '&nbsp &nbsp </a> </li>';
                        }
                        if (!empty($model->setDate) && (strtotime($model->setDate) > 0)) {
                            $html .= '<li><a href="#">' . $model->getStatus(11) . ' ' . date('d/m/Y', strtotime($model->setDate)) . '</a></li>';
                        }
                        if (!empty($model->shipDate) && strtotime($model->shipDate) > 0) {
                            $html .= '<li><a href="#">' . $model->getStatus(3) . ' ' . date('d/m/Y', strtotime($model->shipDate)) . '</a></li>';
                        }
                        if (!empty($model->deliveryDate) && strtotime($model->deliveryDate) > 0) {
                            $html .= '<li><a href="#" rel="' . strtotime($model->deliveryDate) . '">' . $model->getStatus(4) . ' ' . date('d/m/Y', strtotime($model->deliveryDate)) . '</a></li>';
                        }
                        if (!empty($model->shippingDate) && strtotime($model->shippingDate) > 0) {
                            $html .= '<li><a href="#">' . $model->getStatus(8) . ' ' . date('d/m/Y', strtotime($model->shippingDate)) . '</a></li>';
                        }
                        if (!empty($model->vnDate) && strtotime($model->vnDate) > 0) {
                            $html .= '<li><a href="#" rel="' . strtotime($model->vnDate) . '">' . $model->getStatus(9) . ' ' . date('d/m/Y', strtotime($model->vnDate)) . '</a></li>';
                        }
                        if (!empty($model->paymentDate) && strtotime($model->paymentDate) > 0) {
                            $html .= '<li><a href="#" rel="' . strtotime($model->paymentDate) . '" data-count="' . $model->paymentDate . '">' . $model->getStatus(6) . ' ' . date('d/m/Y', strtotime($model->paymentDate)) . '</a></li>';
                        }
                        if (!empty($model->finshDate) && strtotime($model->finshDate) > 0) {
                            $html .= '<li><a href="#" data-count="' . $model->finshDate . '">' . $model->getStatus(5) . ' ' . date('d/m/Y', strtotime($model->finshDate)) . '</a></li>';
                        }

                        $html .= ' </ul>';
                        //  }

                        $html .= ' </li>
                                            </ul>                    
                                        </div>';

                        return $html;
                    }
                ],
                [
                    'headerOptions' => ['class' => 'text-center order-action', 'style' => 'width: 10%;'],
                    'contentOptions' => ['class' => 'text-center order-action'],
                    'label' => 'Thao tác',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->actionOut();
                    },
                ],
                //            ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>


    </div>
</div>
<script>
    $(document).ready(function () {
        $('ul.nav li.dropdown').hover(function () {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
        }, function () {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
        });
        //checkbox
        main.order_checkbox();
        main.depositAll();
    });

</script>
<?php \yii\widgets\Pjax::end(); ?>
