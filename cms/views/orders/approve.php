<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel common\models\TbOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Các đơn hàng '.($isBook ==0 ?'Cảnh báo':'đã đặt');
$this->params['breadcrumbs'][] = $this->title;
$businuss = \common\components\CommonLib::listUser(0, [ADMIN, WAREHOUSE, WAREHOUSETQ]);
?>
<div class="tb-orders-index">
    <?php  echo $this->render('_search', ['model' => $searchModel,'params'=>$params,'status' => $status,'businuss' => $businuss,'isBook'=>$isBook,'action'=>'orders/'.Yii::$app->controller->action->id]); ?>
    <div class="clear mt15 mb15">
        <?php echo \common\widgets\Alert::widget(); ?>
    </div>

    <?php
    $gridColumns = [
        [
            'headerOptions'  => ['style' => 'width: 12%;'],
            'contentOptions' => ['data-th' => 'Mã ĐH'],
            'label'          => 'Mã ĐH',
            'attribute'      => 'identify',
            'format'=>'raw',
            'filter'    => false,
            //'noWrap'=>true,
            'value'          => function ($model) {
                /*$isOderNumber = (int)\common\components\CommonLib::isOderNumber($model->orderID);
                $isTQ = (int)\common\components\CommonLib::isNotTQ($model->orderID);
                $class            = '';


                if(!$isTQ && !$isOderNumber && $model->status != 3){
                    $class = 'blue';
                }elseif (!$isOderNumber && $isTQ && ($model->status != 3 || $model->status == 3)){
                    $class = 'green';
                }
                elseif (((!$isTQ || $isTQ) && $isOderNumber) && ($model->status == 3 || $model->status == 2 || $model->status == 4)) {

                    $class = 'yellow';
                }

                if(isset($model->complain->id) && $model->complain->id) {
                    $class = 'red';
                }

                $alertOrder = \common\components\CommonLib::checkOrderAlert($model->orderID,$model->orderDate);*/
                return '<div class="identify"><a href="'. \yii\helpers\Url::to(['orders/view', 'id' => $model->orderID]).'"><b>' . (isset($model->identify) ? $model->identify : null) .'</b></a></div>';
            }
        ],
        [
            'headerOptions' => ['style' => 'width: 10%;'],
            'attribute'     => 'orderDate',
            'label'         => 'Ngày gửi',
            'format'        => 'raw',
            'filter'    => false,
            'value'         => function ($model) {
                return date('d-m-Y H:i:s', strtotime($model->orderDate));
            }
        ],
        [
            'contentOptions' => ['data-th' => 'Số lượng'],
            'attribute'     => 'totalQuantity',
            'label'         => '∑ SP',
            'format'        => 'raw',
            'filter'    => false,
            'value'         => function ($model) {
                return $model->totalQuantity ? $model->totalQuantity : 0;
            }
        ],
        [
            'attribute' => 'totalOrder',
            'contentOptions' => ['data-th' => '∑ Tiền hàng'],
            'label'     => '∑ Tiền hàng',
            'format'    => 'raw',
            'filter'    => false,
            'value'     => function ($model) {
                return '<label class="vnd-unit"><b>' . number_format(round($model->totalOrder)) . '</b></label>';
            }
        ],
        [
            'attribute' => 'totalPayment',
            'contentOptions' => ['data-th' => '∑ Tiền đơn'],
            'label'     => '∑ Tiền đơn',
            'format'    => 'raw',
            'filter'    => false,
            'value'     => function ($model) {
                return '<label class="vnd-unit"><b>' . number_format(round($model->totalPayment)) . '</b></label>';
            }
        ],
        [
            'contentOptions' => ['data-th' => 'Đã đặt cọc'],
            'label'  => 'Đã đặt cọc',
            'attribute' => 'totalPaid',
            'filter' => false,
            'format' => 'raw',
            'value'  => function ($model) {
                return '<label class="vnd-unit"><b>' . number_format(round($model->totalPaid)) . '</b></label>';
            }
        ],
        [
            'contentOptions' => ['data-th' => 'Còn thiếu'],
            'label'  => '∑ Còn thiếu',
            'format' => 'raw',
            'value'  => function ($model) {
                $totalOrder = $model->totalPayment;
                $totalPaid1 = ($model->totalPaid < $model->totalPayment) ? $model->totalPayment - $model->totalPaid : 0;
                $totalPaid2 = ($model->totalPaid > $totalOrder) ? $model->totalPaid - $totalOrder : 0;
                $str = $totalPaid2 > $totalPaid1 ? 'Còn thừa<br><label class="vnd-unit"><b>'.(number_format(round($totalPaid2))).'</b></label>' : 'còn nợ<br><label class="vnd-unit"><b>'.number_format(round($totalPaid1)).'</b></label>';
                return $str;//'<label class="vnd-unit"><b>'.number_format(round($totalPaid)).'</b></label>';
            }
        ],
        [
            'contentOptions' => ['data-th' => 'Tình trạng đơn'],
            'headerOptions' => ['style' => 'width: 10%;'],
            'label'         => 'Tình trạng đơn',
            'format'        => 'raw',
            'attribute' => 'status',
            'filter' => false,
            //                            'filter' => Html::dropDownList('TbOrderSearch[status]', isset($params['TbOrderSearch']['status']) ? $params['TbOrderSearch']['status'] : '', \common\components\CommonLib::statusText(), ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
            'value'         => function ($model) {
                return $model->getStatus($model->status);

            }
        ],
        //            [
        //                'headerOptions' => ['style' => 'width: 11%;'],
        //                'attribute' => 'shippingStatus',
        //                'label'         => 'Tình trạng vận chuyển',
        //                'format'        => 'raw',
        //                'filter' => false,
        //                //                            'filter' => Html::dropDownList('TbOrderSearch[shippingStatus]', isset($params['TbOrderSearch']['shippingStatus']) ? $params['TbOrderSearch']['shippingStatus'] : '', \common\components\CommonLib::statusShippingText(), ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
        //                'value'         => function ($model) {
        //                    return \common\components\CommonLib::getStatusShipping($model->shippingStatus);
        //                }
        //            ],
        [
            'contentOptions' => ['data-th' => 'Thao tác'],
            'class' => 'kartik\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '<div class="dropdown actions">
                    <i id="dropdownMenu13" data-toggle="dropdown" aria-expanded="true" title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                                {view}{costs}{approval}{message}{delete}
                            </ul>
                    </div>',

            'buttons' => [
                'costs' => function ($url, $model) {
                    return '<li><a target="_blank" href="' .$url . '"><i class="fa fa-mail-forward" aria-hidden="true"></i> Cài đặt phí</a></li>';
                },
                'approval' => function ($url, $model) {
                    return '<li><a target="_blank" href="' .$url . '"><i class="fa fa-mail-forward" aria-hidden="true"></i> Duyệt đơn hàng</a></li>';
                },
                'view' => function ($url, $model) {
                    return '<li><a target="_blank" href="' . $url . '" ><i class="fa fa-shopping-cart" aria-hidden="true"></i> Chi tiết đơn hàng</a></li>';
                },
                'message' => function ($url, $model) {
                    return '<li><a href="javascript:void(0)"  class="send-sms" data-id="'.$model->orderID.'"><i class="fa fa-fw fa-commenting"></i> Gửi thông báo</a></li>';
                },
                'delete' => function ($url, $model) {
                    return '<li><a href="' . $url. '" class="confirm-delete" title="'.$model->identify.'"><i class="glyphicon glyphicon-remove font-12"></i> Xóa đơn hàng</a></li>';
                }
            ],
        ]
    ];
    ?>
    <div class="clear-fix"></div>
    <?php
    echo GridView::widget([
        'tableOptions' => [
            'id' => 'tbl_manager',
            'class' => 'table-hover'
        ],
        'layout'       => '<div class="text-right">{summary}</div>{items}{pager}',
        'dataProvider' => $dataProvider,
        'filterModel'  => false,
        'columns' => $gridColumns,
                     'pjax' => true,
        'toolbar' => [
            ['content'=>
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
            ],
            '{export}',
            // '{toggleData}'
        ],
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="fa fa-cart-arrow-down"></i> Danh sách đơn hàng </h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>false
        ],
        'export' => [
            'fontAwesome' => true,
        ],
        'responsive'=>false,
        'exportConfig'=> [
            GridView::EXCEL =>[
                'label' => 'To Excel',
                'icon' => 'file-excel-o',
                'iconOptions' => '',
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => 'Don-hang-'.date('d-m-Y H:i'),
                'alertMsg' => 'created',
                'options' => ['title' => 'Semicolon -  Separated Values'],
                'mime' => 'application/excel',
                'config' => [
                    'colDelimiter' => ";",
                    'rowDelimiter' => "\r\n",
                ],
            ],
            /* GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'File_Name-'.date('d-M-Y')],
             GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'File_Name -'.date('d-M-Y')],
             GridView::PDF => ['label' => 'Export as PDF', 'filename' => 'File_Name -'.date('d-M-Y')],
             GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'File_Name -'.date('d-M-Y')],
             GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'File_Name -'.date('d-M-Y')],*/
        ],
    ]);
    ?>

</div>

