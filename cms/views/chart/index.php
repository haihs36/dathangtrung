<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;
    use kartik\grid\GridView;
    $setting                       = Yii::$app->controller->setting;
    $this->title                   = $title;
    $this->params['breadcrumbs'][] = $this->title;
    $user                          = \Yii::$app->user->identity;
    $disabled                      = false;
    if ($user->role == BUSINESS || $user->role == STAFFS) {
        $disabled = true;
    }
?>

<?=  \common\widgets\Alert::widget(); ?>
<div class="boxd">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-pie-chart"></i> <?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php

            $form = ActiveForm::begin([
                'enableAjaxValidation' => false,
                'method'               => 'get',
                'options'              => [
                    'id'    => "form",
                    'class' => "form-horizontal",
                ]
            ]); ?>

        <?= $form->field($model, 'businessID', [
            'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
        ])->dropDownList(\common\components\CommonLib::listUserByUsername(0,[ADMIN,WAREHOUSE,WAREHOUSETQ]), [
            'class' => 'select2 form-control','disabled'=>$disabled, 'prompt' => '','data-placeholder'=>'Chọn nhân viên thống kê'])->label('Nhân viên', ['class'=>"col-sm-2 control-label"])
        ?>
        <?= $form->field($model, 'charType', [
            'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->dropDownList(\cms\models\TbChart::getStatisticalType(), [
            'class' => 'select2 form-control', 'prompt' => '','data-placeholder'=>'Chọn hình thức thống kê'])->label('Hình thức thống kê', ['class'=>"col-sm-2 control-label"])
        ?>
        <?= $form->field($model, 'startDate', [
            'template' => '{label}<div class="input-group col-sm-4"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
        ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'id' => 'startDate'])->label('Từ ngày',['class'=>"col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'endDate', [
            'template' => '{label}<div class="input-group col-sm-4"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
        ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'id' => 'endDate'])->label('Đến ngày', ['class' => "col-sm-2 control-label"]) ?>

        <div class="form-group mt15 mb15">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-4 text-right">
                <button type="submit" class="btn btn-primary">Thống kê</button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <div class="mt15">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pie-chart"></i> <?= $title ?></h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="title">Tổng tiền:</label>
                                    <?php if($sum_china){?>
                                        <div class="col-sm-3">
                                            <input disabled type="text" value="<?= round($sum_china,2); ?>" class="form-control">
                                        </div>
                                        <div class="pull-left"><b class="vnd">¥</b></div>
                                    <?php } ?>
                                <?php if($sum){?>
                                        <div class="col-sm-3">
                                            <input disabled type="text" value="<?= number_format(round($sum)) ?>" class="form-control vnd-unit">
                                        </div>
                                    <div class="pull-left"><b class="vnd-unit">VNĐ</b></div>
                                <?php } ?>
                            </div>
                            <div class="chart-content clear">
                                <div class="box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Biểu đồ</h3>
                                    </div>
                                    <div class="box-body chart-responsive">
                                        <div class="chart" id="line-chart" style="height: 300px;"></div>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(isset($searchModel) && $searchModel) { ?>
<div class="clear box">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Danh sách đơn hàng</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php
        $businuss = \common\components\CommonLib::listUser(0,[ADMIN,WAREHOUSE,WAREHOUSETQ]);
            $gridColumns = [
                /*[
                    'header'        => 'image',
                    'filter'    => false,
                    'format'    => 'raw',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value'          => function ($model) {
                        return '<img width="50" height="50" src="'.htmlspecialchars($model->image).'">';
                    }
                ],*/
                [
                    'headerOptions'  => ['style' => 'width: 7%;','class' => 'text-center'],
                    'contentOptions' => ['data-th' => 'Mã ĐH','class' => 'text-center'],
                    'label'          => 'Mã ĐH',
                    'attribute'      => 'identify',
                    'format'=>'raw',
//                    'filter'    => false,
                    //'noWrap'=>true,
                    'value'          => function ($model) {
                        //$isOderNumber = (int)\common\components\CommonLib::isOderNumber($model->orderID);
                       // $isTQ = (int)\common\components\CommonLib::isNotTQ($model->orderID);
                        $class            = '';
                        /*if(!$isTQ && !$isOderNumber && $model->status != 3){
                            $class = 'blue';
                        }elseif (!$isOderNumber && $isTQ && ($model->status != 3 || $model->status == 3)){
                            $class = 'green';
                        }
                        elseif (((!$isTQ || $isTQ) && $isOderNumber) && ($model->status == 3 || $model->status == 2 || $model->status == 4)) {
                            $class = 'yellow';
                        }

                        if(isset($model->complain->id) && $model->complain->id) {
                            $class = 'red';
                        }*/
                        $alertOrder = $dateCoc = '';
                        /*if(!empty($model->setDate)) {
                            $alertOrder = \common\components\CommonLib::checkOrderAlert($model->orderID, $model->setDate);
                            $dateCoc = date('d/m/Y H:i:s', strtotime($model->setDate));
                        }*/
                        return '<div class="identify"><a target="_blank" href="'. \yii\helpers\Url::to(['orders/view', 'id' => $model->orderID]).'"><b>' . (isset($model->identify) ? $model->identify : null) .$alertOrder.'</b></a></div>'.$dateCoc;
                    }
                ],

//                [
//                    'headerOptions' => ['class' => 'text-center'],
//                    'attribute'     => 'customerID',
//                    'label'         => 'Khách hàng',
//                    'format'        => 'raw',
////                    'filter'    => false,
//                    'value'         => function ($model) use ($businuss) {
//                        if($model->customerID){
//                            return '<a target="_blank" title="'.$model->customer->username.'" href="'.Url::toRoute(['customer/view','id'=>$model->customerID]).'"><b>'.$model->customer->username.'</b><br><i>'.(isset($businuss[$model->businessID]) ? '('.$businuss[$model->businessID].')' : '').'</i></a>';
//                        }
//                        return null;
//                    }
//                ],
                [
                    'header'              => 'Khách hàng',
                    'contentOptions'      => ['data-th' => 'Khách hàng'],
                    'attribute'           => 'customerID',
                    'vAlign'              => 'middle', 'filter'    => false,
                    'width'               => '180px',
                    'value'               => function ($model) use ($businuss) {
                        return '<a target="_blank" title="'.$model->customer->username.'" href="'.Url::toRoute(['customer/view','id'=>$model->customerID]).'"><b>'.$model->customer->username.'</b><br><i>'.(isset($businuss[$model->businessID]) ? '('.$businuss[$model->businessID].')' : '').'</i></a>';
                    },
                   // 'filterType'          => GridView::FILTER_SELECT2,
                   // 'filter'              => \yii\helpers\ArrayHelper::map(\common\models\Custommer::find()->select(['id', 'username'])->where(['status' => 1])->all(), 'id', 'username'),
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions'  => ['placeholder' => 'Chọn..'],
                    'format'              => 'raw'
                ],
                [
                    'contentOptions' => ['data-th' => 'SL','class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'attribute'     => 'totalQuantity',
                    'label'         => 'SL',
                    'format'        => 'raw',
                    'filter'    => false,
                    'value'         => function ($model) {
                        return $model->totalQuantity ? $model->totalQuantity : 0;
                    }
                ],
                [
                    'attribute'     => 'totalWeight',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['data-th' => 'Kg','class' => 'text-center'],
                    'label'         => 'Kg',
                    'format'        => 'raw',
                    'filter'        => false,
                    'value'         => function ($model) {
                        return ($model->totalWeight ? $model->totalWeight : 0);
                    }
                ],
                [
                    'attribute' => 'totalWeightPrice',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['data-th' => 'Tiền Kg','class'=>'text-center'],
                    'label'     => 'Tiền Kg',
                    'format'    => 'raw',
                    'filter'    => false,
                    'value'     => function ($model) {
                        return '<label class="vnd-unit"><b>' . number_format(round($model->totalWeightPrice)) . '</b></label>';
                    }
                ],
                [
                    'attribute' => 'totalOrder',
                    'headerOptions' => ['class' => 'text-center','style'=>'width:6%'],
                    'contentOptions' => ['data-th' => 'Thực tế (¥)','class'=>'text-center'],
                    'label'     => 'Thực tế (¥)',
                    'format'    => 'raw',
                    'filter'    => false,
                    'value'     => function ($model) {
                        return  $model->actualPayment ;
                    }
                ],[
                    'attribute' => 'totalDiscount',
                    'headerOptions' => ['class' => 'text-center' ,'style'=>'width:6%'],
                    'contentOptions' => ['data-th' => 'CK được (¥)','class'=>'text-center'],
                    'label'     => 'CK được (¥)',
                    'format'    => 'raw',
                    'filter'    => false,
                    'value'     => function ($model) {
                        return  $model->totalDiscount ;
                    }
                ],
                [
                    'attribute' => 'totalPayment',
                    'contentOptions' => ['data-th' => 'Phí DV','class' => 'text-center'],
                    'headerOptions'  => ['class' => 'text-center'],
                    'label'     => 'Phí DV',
                    'format'    => 'raw',
                    'filter'    => false,
                    'value'     => function ($model) {
                        return '<label class="vnd-unit"><b>' . number_format($model->orderFee) . '</b></label>';
                    }
                ], [
                    'attribute' => 'totalPayment',
                    'contentOptions' => ['data-th' => 'Tổng tiền','class' => 'text-center'],
                    'headerOptions'  => ['class' => 'text-center'],
                    'label'     => 'Tổng tiền',
                    'format'    => 'raw',
                    'filter'    => false,
                    'value'     => function ($model) {
                        return '<label class="vnd-unit"><b>' . number_format(round($model->totalPayment)) . '</b></label>';
                    }
                ],



                [
                    'label'     => 'CK KG',
                    'contentOptions' => ['class' => 'text-center','data-th' => 'CK KG'],
                    'attribute' => 'discountKg',
                    'filter'    => false,
                    'format'    => 'raw',
                    'value'     => function ($model) {
                        return '<label class="vnd-unit"><b>' . number_format(round($model->discountKg)) . '</b></label>';
                    }
                ],
                [
                    'label'          => '% KPI',
                    'attribute'      => 'discountRate',
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['data-th' => '% KPI','class' => 'text-center'],
                    'filter'         => false,
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return ($model->discountRate > 0? $model->discountRate : 0) . ' %';
                    }
                ],
                [
                    'attribute'     => 'discountBusiness',
                    'label'         => 'Tổng CK',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['data-th' => 'Tổng CK','class' => 'text-center'],
                    'format'        => 'raw',
                    'filter'        => false,
                    'value'         => function ($model) {
                        return '<label class="vnd-unit"><b>' . number_format(round($model->discountBusiness)) . '</b></label>';
                    }
                ],
                [
                    'contentOptions' => ['data-th' => 'Tình trạng','class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center','style'=>'width:10%'],
                    'label'         => 'Tình trạng',
                    'format'        => 'raw',
                    'attribute' => 'status',
                    'filter' => false,
                    'value'          => function ($model) {
                        $html = '
                            <div class="collapse navbar-collapse" >
                                <ul class="nav navbar-nav navbar-right">
                                    <li class="dropdown">
                                        <a href="#" style="padding: 5px" class="dropdown-toggle" data-toggle="dropdown">' . $model->getStatus($model->status) . '<br/>' . \common\components\CommonLib::getDateByOrderStatus($model) . '</a>';
                                    if(!in_array($model->status,[1,11])) {
                                        $html .= '<ul class="dropdown-menu">';
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
                                    }

                                    $html .= ' </li>
                                </ul>                    
                            </div>';

                        return $html;
                    }
                ],
                [
                    'contentOptions' => ['data-th' => 'Thao tác'],
                    'class' => 'kartik\grid\ActionColumn',
                    'header' => 'Thao tác',
                    'template' => '{approval}',
                    'buttons' => [
                        'approval' => function ($url, $model) {
                            return '<a target="_blank" href="' . Url::toRoute(['orders/approval', 'id' => $model->orderID], true) . '"><i class="fa fa-mail-forward" aria-hidden="true"></i> Sửa</a>';
                        },
                    ],
                ]
            ];
        ?>
        <div class="clear"></div>
        <?php
            echo GridView::widget([
                'tableOptions' => [
                    'id' => 'tbl_manager',
                    'class' => 'table-hover'
                ],
                'layout'       => '<div class="text-right">{summary}</div>{items}{pager}',
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'columns'      => $gridColumns,
                'toolbar'      => [
                    '{export}',
                    // '{toggleData}'
                ],

                'panel'        => [
                    'type'       => GridView::TYPE_PRIMARY,
                    'showFooter' => false
                ],
                'export'       => [
                    'fontAwesome' => true,
                ],
                'responsive'   => false,
                'exportConfig' => [
                    GridView::EXCEL => [
                        'label'           => 'To Excel',
                        'icon'            => 'file-excel-o',
                        'iconOptions'     => '',
                        'showHeader'      => true,
                        'showPageSummary' => true,
                        'showFooter'      => true,
                        'showCaption'     => true,
                        'filename'        => 'Thong-ke-don-hang-' . date('d-m-Y'),
                        'alertMsg'        => 'Bạn có chắc chắn muốn export ra excel?',
                        'options'         => ['title' => 'Export Exce;'],
                        'mime'            => 'application/excel',
                        'config'          => [
                            'colDelimiter' => ";",
                            'rowDelimiter' => "\r\n",
                        ],
                    ],
                ],
            ]);
        ?>
    </div>
</div>
<?php  }?>

<!-- Morris.js charts -->
<link rel="stylesheet" href="<?= Yii::$app->params['adminUrl'] ?>/plugins/morris/morris.css">
<script src="<?= Yii::$app->params['adminUrl'] ?>/plugins/morris/morris.min.js"></script><!--<script src="../js/raphael-min.js"></script>-->
<script src="<?= Yii::$app->params['adminUrl'] ?>/plugins/morris/raphael-min.js"></script><!--<script src="../js/raphael-min.js"></script>-->


<!-- page script -->
<script>
    $(function () {
        "use strict";
        // LINE CHART
        // LINE CHART
        var line = new Morris.Line({
            element: 'line-chart',
            resize: true,
            data: <?= ($data) ? $data : $data_china ?>,
            /*[
            {y: '2011 Q1', item1: 2666},
            {y: '2011 Q2', item1: 2778},
            {y: '2011 Q3', item1: 4912},
            {y: '2011 Q4', item1: 3767},
            {y: '2012 Q1', item1: 6810},
            {y: '2012 Q2', item1: 5670},
            {y: '2012 Q3', item1: 4820},
            {y: '2012 Q4', item1: 15073},
            {y: '2013 Q1', item1: 10687},
            {y: '2013 Q2', item1: 8432}
        ],*/
            xkey: 'y',
            ykeys: ['value'],
            labels: ['total'],
            lineColors: ['#3c8dbc'],
            hideHover: 'auto'
        });
    });
</script>