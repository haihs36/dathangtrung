<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

$user = \Yii::$app->user->identity;
$role  = $user->role;
/* @var $this yii\web\View */
/* @var $searchModel common\models\TbOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tất cả đơn hàng';
$this->params['breadcrumbs'][] = $this->title;
$businuss = \common\components\CommonLib::listUser(0, [ADMIN, WAREHOUSE, WAREHOUSETQ]);
?>
<?php //Pjax::begin(['enablePushState' => false]); ?>
<div class="tb-orders-index">
    <?php echo $this->render('_search', ['model' => $searchModel, 'params' => $params, 'businuss' => $businuss, 'status' => $status, 'isBook' => null, 'action' => 'orders/index']); ?>
    <div class="clear mt15 mb15">
        <?php echo \common\widgets\Alert::widget(); ?>
    </div>
    <?php


    $gridColumns = [
        ['class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => ['onclick' => 'js:addItems(this.value, this.checked)']],
        /*[
            'class' => 'kartik\grid\SerialColumn',
            'contentOptions' => ['class' => 'kartik-sheet-style'],
            'width' => '36px',
            'header' => '',
            'headerOptions' => ['class' => 'kartik-sheet-style']
        ],*/
        [
            'header'         => 'image',
            'filter'         => false,
            'format'         => 'raw',
            'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 5%;'],
            'contentOptions' => ['class' => 'text-center'],
            'value'          => function ($model) {
                return '<img width="50" height="50" src="' . htmlspecialchars($model->image) . '">';
            }
        ],
        [
            'headerOptions'  => ['style' => 'width: 10%;'],
            'contentOptions' => ['data-th' => 'Mã ĐH'],
            'label'          => 'Mã ĐH',
            'attribute'      => 'identify',
            'format'         => 'raw',
            'filter'         => false,
            'value'          => function ($model) {
                $img = \common\components\CommonLib::getIconBySource($model->sourceName);

                return '<div class="identify"><a target="_blank" href="' . \yii\helpers\Url::to(['orders/view', 'id' => $model->orderID]) . '"><b>' . (isset($model->identify) ? $model->identify : null)  . '</b></a><br>'.$img.'</div>' ;
            }
        ],
        [
            'headerOptions' => ['style' => 'width: 10%;'],
            'attribute'     => 'customerID',
            'label'         => 'Khách hàng',
            'format'        => 'raw',
            'filter'        => false,
            'value'         => function ($model) use ($businuss) {
                if ($model->customerID) {
                    return '<a style="text-decoration:underline;" target="_blank" title="' . $model->cusername . '" href="' . Url::toRoute(['customer/view', 'id' => $model->customerID]) . '"><b>' . $model->cusername .
                        '</b></a>'.(isset($businuss[$model->businessID]) ?  '<br/><label class="label label-success"> <i>KD:'.$businuss[$model->businessID].'</i></label>' : '').
                        (isset($businuss[$model->orderStaff]) ?  '<br/><label class="label label-danger"> <i>ĐH:'.$businuss[$model->orderStaff].'</i></label>' : '');
                }
                return null;
            }
        ],
        [
            'contentOptions' => ['data-th' => 'Số lượng'],
            'headerOptions'  => ['style' => 'width: 10%;'],
            'attribute'     => 'totalQuantity',
            'label'         => 'SL',
            'format'        => 'raw',
            'filter'    => false,
            'value'         => function ($model) {
                $txt = '_';
                if($model->quantity)
                    $txt = (int)$model->quantity;
                return $model->totalQuantity .' / '.$txt;
            }
        ],
        [
            'headerOptions'  => ['style' => 'width: 10%;'],
            'attribute'     => 'totalWeightPrice',
            'label'         => 'Phí VC',
            'format'        => 'raw',
            'filter'    => false,
            'value'         => function ($model) {
               
                return  '<span class="vnd-unit">'.number_format($model->totalWeightPrice) .'</span> / '.$model->totalWeight.'/<span class="vnd-unit">'.number_format($model->weightCharge).'</span>';
            }
        ],
        [
            'attribute'      => 'actualPayment',
            'headerOptions'  => ['style' => 'width: 10%;'],
            'contentOptions' => ['data-th' => 'Thanh toán TT', 'class' => 'text-center'],
            'label'          => 'Thanh toán TT',
            'format'         => 'raw',
            'filter'         => false,
            'value'          => function ($model) use ($role) {
                //BUSINESS
                if(!in_array($role,[BUSINESS,WAREHOUSE,WAREHOUSETQ])){
                    return '<b>' . ($model->actualPayment) . '</b>';
                }

                return '---';
            }
        ],
        [
            'attribute'      => 'totalPayment',
            'contentOptions' => ['data-th' => '∑ Tiền đơn'],
            'label'          => '∑ Tiền đơn',
            'format'         => 'raw',
            'filter'         => false,
            'value'          => function ($model) {
                return '<label class="vnd-unit"><b>' . number_format(round($model->totalPayment)) . '</b></label>';
            }
        ],
        [
            'contentOptions' => ['data-th' => 'Đã đặt cọc'],
            'label'          => 'Đã đặt cọc',
            'attribute'      => 'totalPaid',
            'filter'         => false,
            'format'         => 'raw',
            'value'          => function ($model) {
                return '<label class="vnd-unit"><b>' . number_format(round($model->totalPaid)) . '</b></label>';
            }
        ],
        [
            'contentOptions' => ['data-th' => 'Còn thiếu'],
            'label'          => '∑ Còn thiếu',
            'attribute'      => 'debtAmount',
            'format'         => 'raw',
            'value'          => function ($model) {
                $label = '';
                $money = 0;
                if ($model->status == 1) {
                    //tong tien don hang + phi phat sinh neu co
                    $totalPayment = $model->totalPayment;
                    $money = $totalPayment - $model->totalPaid;
                    $money = number_format($money);
                }else if($model->status != 1){
                    $money = number_format($model->debtAmount);
                }
                if($money > 0){
                    $label = '';
                }

                return $label . ' <b class="vnd-unit">' .$money . '</b>';
            }
        ],
        [
            'headerOptions'  => ['style' => 'width: 10%;'],
            'header'        => ' Kho đích',
            'attribute' => 'provinID',
            'value' => function ($model) {
                $dgo = $model->isBox ? '<label class=" btn-block btn-danger btn-xs"> Đóng gỗ</label>' : '';
                $kdem = $model->isCheck ? '<label class=" btn-block btn-warning btn-xs"> Kiểm đếm</label>' : '';
                return '<div style=""><b>'.$model->name.'<br/>'.$dgo.' '.$kdem.'</div>';
            },
            'format' => 'raw'
        ],
        [
            'contentOptions' => ['data-th' => 'Tình trạng đơn'],
            'headerOptions'  => ['style' => 'width: 10%;'],
            'label'          => 'Tình trạng đơn',
            'format'         => 'raw',
            'attribute'      => 'status',
            'filter'         => false,
            //                            'filter' => Html::dropDownList('TbOrderSearch[status]', isset($params['TbOrderSearch']['status']) ? $params['TbOrderSearch']['status'] : '', \common\components\CommonLib::statusText(), ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
            'value'          => function ($model) {
                $html = '
                <div class="collapse navbar-collapse" >
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" style="padding: 5px" class="dropdown-toggle" data-toggle="dropdown">' . $model->getStatus($model->status) . '<br/>' . \common\components\CommonLib::getDateByOrderStatus($model) . '</a>';
                       // if(!in_array($model->status,[1,11])) {
                            $html .= '<ul class="dropdown-menu">';

                            if (!empty($model->orderDate) && (strtotime($model->orderDate) > 0)) {
                                $html .= '<li><a href="#"><span class="label label-warning">Ngày đặt</span> ' . date('d/m/Y H:i', strtotime($model->orderDate)) . '&nbsp &nbsp </a> </li>';
                            }
                            if (!empty($model->setDate) && (strtotime($model->setDate) > 0)) {
                                $html .= '<li><a href="#">' . $model->getStatus(11) . ' ' . date('d/m/Y H:i', strtotime($model->setDate)) . '</a></li>';
                            }
                            if (!empty($model->shipDate) && strtotime($model->shipDate) > 0) {
                                $html .= '<li><a href="#">' . $model->getStatus(3) . ' ' . date('d/m/Y H:i', strtotime($model->shipDate)) . '</a></li>';
                            }
                            if (!empty($model->deliveryDate) && strtotime($model->deliveryDate) > 0) {
                                $html .= '<li><a href="#" rel="'. strtotime($model->deliveryDate).'">' . $model->getStatus(4) .' '. date('d/m/Y H:i', strtotime($model->deliveryDate)) . '</a></li>';
                            }
                            if (!empty($model->shippingDate) && strtotime($model->shippingDate) > 0) {
                                $html .= '<li><a href="#">' . $model->getStatus(8) .' '. date('d/m/Y H:i', strtotime($model->shippingDate)) . '</a></li>';
                            }
                            if (!empty($model->vnDate)  && strtotime($model->vnDate) > 0) {
                                $html .= '<li><a href="#" rel="'. strtotime($model->vnDate).'">' . $model->getStatus(9) .' '. date('d/m/Y H:i', strtotime($model->vnDate)) . '</a></li>';
                            }
                            if (!empty($model->paymentDate)  && strtotime($model->paymentDate) > 0) {
                                $html .= '<li><a href="#" rel="'. strtotime($model->paymentDate).'" data-count="'.$model->paymentDate.'">' . $model->getStatus(6) .' '. date('d/m/Y H:i', strtotime($model->paymentDate)) . '</a></li>';
                            }
                            if (!empty($model->finshDate)  && strtotime($model->finshDate) > 0) {
                                $html .= '<li><a href="#" data-count="'.$model->finshDate.'">' . $model->getStatus(5) .' '. date('d/m/Y H:i', strtotime($model->finshDate)) . '</a></li>';
                            }

                            $html .= ' </ul>';
                       // }

                    $html .= ' </li>
                    </ul>                    
                </div>';

                return $html;
            }
        ],
        [
            'label'      => 'Thao tác',
            'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 10%;'],
            'contentOptions' => ['class' => 'text-center'],
            'filterOptions'  => ['class' => 'text-center'],
            'format'         => 'raw',
            'value'          => function ($model) {
                return $model->getAction();
            },
        ],
    ];
    ?>
    <div class="clear"></div>
    <?php
    echo GridView::widget([
        'tableOptions' => [
            'id'    => 'tbl_manager',
            'class' => 'table-hover text-center dataTable'
        ],
        'layout' => "{summary}\n{items}\n<div class='text-center'>{pager}</div>",
//        'layout'       => '<div class="text-right">{summary}</div>{items}{pager}',
        'dataProvider' => $dataProvider,
        'filterModel'  => false,
        'columns'      => $gridColumns,
        'pjax' => false,
        'toolbar'      => [
            ['content' =>
                 Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax' => false, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
            ],
            '{export}',
            // '{toggleData}'
        ],

        'export'       => [
            'fontAwesome' => true,
        ],
        'responsive'=>false,
        'hover'=>false,
        'exportConfig' => [
            GridView::EXCEL => [
                'label' => 'To Excel',
                'icon' => 'file-excel-o',
                'iconOptions' => '',
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => 'Don-hang-' . date('d-m-Y'),
                'alertMsg' => 'created',
                'options' => ['title' => 'Semicolon -  Separated Values'],
                'mime' => 'application/excel',
                'config' => [
                    'colDelimiter' => ";",
                    'rowDelimiter' => "\r\n",
                ],
            ],
        ],
    ]);
    ?>
</div>
<?php //Pjax::end(); ?>
<script>
    $(function () {
        $('.booking').on('click',function (e) {
            if(confirm('Bạn có chắc chắn muốn chuyển trạng thái sang đã đặt hàng không?')){
                e.preventDefault();
                $('#myModal').modal('show');
                $.ajax({
                    url: $(this).attr('href'),
                    type: 'get',
                    beforeSend: function () {
                        $('.modal-container').html('<div style="text-align: center;padding-top: 150px"><img src="/images/loader.gif"/></div>');
                    },
                    success: function (res) {
                        $('.modal-container').html(res.alert);
                        setTimeout(function () {// wait for 5 secs(2)
                            location.reload(); // then reload the page.(3)
                        },2000);
                    }
                });
            }

            return false;
        });

    });
</script>
