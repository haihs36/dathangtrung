<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

$user = \Yii::$app->user->identity;
$role  = $user->role;
/* @var $this yii\web\View */
/* @var $searchModel common\models\TbOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Xử lý đặt hàng';
$this->params['breadcrumbs'][] = $this->title;
$businuss = \common\components\CommonLib::listUser(0, [ADMIN, WAREHOUSE, WAREHOUSETQ]);
?>
<div class="tb-orders-index">
    <?php echo $this->render('_search', ['model' => $searchModel, 'params' => $params, 'businuss' => $businuss,'status' => $status, 'isBook' => null, 'action' => 'orders/index']); ?>
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
            'contentOptions' => ['data-th' => 'Mã NCC'],
            'attribute'     => 'shopProductID',
            'label'         => 'Mã NCC',
            'format'        => 'raw',
            'filter'    => false,
            'value'         => function ($model) {
                return $model->shopProductID;
            }
        ],

        [
            'attribute'      => 'totalPayment',
            'contentOptions' => ['data-th' => '∑ Tiền hàng(¥)'],
            'label'          => '∑ Tiền hàng(¥)',
            'format'         => 'raw',
            'filter'         => false,
            'value'          => function ($model) {
                return '<label><b>' . $model->totalOrderTQ . '</b></label>';
            }
        ],
        [
            'attribute'      => 'actualPayment',
            'headerOptions'  => ['style' => 'width: 10%;'],
            'contentOptions' => ['data-th' => 'TT Thực(¥)', 'class' => 'text-center'],
            'label'          => 'TT Thực(¥)',
            'format'         => 'raw',
            'filter'         => false,
            'value'          => function ($model) use ($role) {
                //BUSINESS
                if($role !== BUSINESS){
                    return '<b>' . ($model->actualPayment) . '</b>';
                }

                return '---';
            }
        ],
        [
            'attribute'      => 'discount',
            'contentOptions' => ['data-th' => 'CK được(¥)'],
            'label'          => 'CK được(¥)',
            'format'         => 'raw',
            'filter'         => false,
            'value'          => function ($model) {
                return '<label><b>' . $model->discount . '</b></label>';
            }
        ],
        [
            'attribute'      => 'shipmentFee',
            'contentOptions' => ['data-th' => 'Phí ship(¥)'],
            'label'          => 'Phí ship(¥)',
            'format'         => 'raw',
            'filter'         => false,
            'value'          => function ($model) {
                return '<label><b>' . $model->shipmentFee . '</b></label>';
            }
        ],
        [
            'header'        => ' Kho đích',
            'contentOptions' => ['data-th' => 'Kho đích'],
            'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 16%;'],
            'attribute' => 'provinID',
            'vAlign' => 'middle',
            'value' => function ($model) {
                $dgo = $model->isBox ? '<input type="checkbox" checked disabled/> Đóng gỗ' : '';
                $kdem = $model->isCheck ? '<input type="checkbox" checked disabled/> Kiểm đếm' : '';
                return '<div style="display: inline-block;width: 180px">'.$model->name.'<br/>'.$dgo.' '.$kdem.'</div>';
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \yii\helpers\ArrayHelper::map(\common\models\Province::find()->select(['id','name'])->all(), 'id', 'name'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Chọn..'],
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
            'class' => 'table-hover text-center'
        ],
        'layout'       => '<div class="text-right">{summary}</div>{items}{pager}',
        'dataProvider' => $dataProvider,
        'filterModel'  => false,
        'columns'      => $gridColumns,
        'pjax' => true,
        'toolbar'      => [
            ['content' =>
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax' => false, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
            ],
            '{export}',
            // '{toggleData}'
        ],
        'panel'        => [
            'heading'    => '<h3 class="panel-title"><i class="fa fa-cart-arrow-down"></i> Danh sách đơn hàng </h3>',
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
                'filename'        => 'Don-hang-' . date('d-m-Y H:i'),
                'alertMsg'        => 'created',
                'options'         => ['title' => 'Semicolon -  Separated Values'],
                'mime'            => 'application/excel',
                'config'          => [
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
