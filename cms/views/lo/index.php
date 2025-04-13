<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel cms\models\LoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quản lý phiếu xuất kho';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lo-index clear">
    <?php    //search
        echo $this->render('_search', ['model' => $searchModel, 'params' => $params]);
    ?>
    <br>
    <div class="clear mt15"></div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
         'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'=>true,
        //        'pjax' => true,
        'toolbar' => [
            ['content'=>
                 Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['lo/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
            ],
            '{export}',
        ],
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="fa fa-cart-arrow-down"></i> Danh sách phiếu xuất kho</h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>false
        ],
        'columns' => [
            // [
            //     'class' => 'kartik\grid\SerialColumn',
            //     'contentOptions' => ['class' => 'kartik-sheet-style'],
            //     'width' => '36px',
            //     'header' => '',
            //     'headerOptions' => ['class' => 'kartik-sheet-style']
            // ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'contentOptions' => ['data-th' => 'Xem chi tiết'],
                'width' => '50px',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detailUrl' => \yii\helpers\Url::to(['lo/detail']),
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'expandOneOnly' => true
            ],
            [               
                'label'          => 'Mã phiếu',
                'contentOptions' => ['data-th' => 'Mã phiếu'],
                'attribute'      => 'id',
                'filter'        => false,
                'format'         => 'raw',
                'value'          => function ($model) use ($pxks) {
                    $total_code = 0;
                    if(isset($pxks[$model->id]))
                        $total_code = count($pxks[$model->id]);
                    return 'PXK-'.$model->id.'<br/>Tổng mã: '.$total_code;
                }
            ],
            [
//                'headerOptions'  => [ 'style' => 'width: 20%;'],
                'contentOptions' => ['data-th' => 'Người trả hàng'],
                'label'          => 'Người trả hàng',
                'attribute'      => 'name',
                'filter'        => false,
                'format'         => 'raw',
                'value'          => function ($model) {
                    return isset($model->user->username) ? $model->user->username : null;
                }
            ],
            [
                //                'headerOptions'  => [ 'style' => 'width: 20%;'],
                 'contentOptions' => ['data-th' => 'Khách hàng'],
                'label'          => 'Khách hàng',
                'attribute'      => 'customerID',
                'filter'        => false,
                'format'         => 'raw',
                'value'          => function ($model) {
                    return isset($model->customer->username) ? $model->customer->username : null;
                }
            ],
            [
//                'headerOptions'  => ['style' => 'width: 10%;'],
                 'contentOptions' => ['data-th' => 'Cân nặng'],
                'attribute'     => 'kg',
                'label'         => 'kg',
                'format'        => 'raw',
                'filter'        => false,
                'value'         => function ($model) {
                    return ($model->kg > 0 ? $model->kg : 0) .'kg';
                }
            ],
            [
//                'headerOptions'  => ['style' => 'width: 10%;'],
                 'contentOptions' => ['data-th' => 'Tổng tiền'],
                'attribute'     => 'amount',
                'label'         => 'Tổng tiền',
                'filter'        => false,
                'format'        => 'raw',
                'value'         => function ($model) {
                    return '<label class="vnd-unit">'.number_format(round($model->amount)).' <em class="red-color">đ</em></label>';
                }
            ],
            [
//                'headerOptions'  => ['style' => 'width: 10%;'],
                  'contentOptions' => ['data-th' => 'Ngày xuất'],
                'attribute'     => 'lastDate',
                'label'         => 'Ngày xuất',
                'filter'        => false,
                'format'        => 'raw',
                'value'         => function ($model) {
                    return date('d-m-Y H:i:s', strtotime($model->lastDate));
                }
            ],
            [
                'headerOptions'  => ['class' => 'text-center'],
                'attribute'     => 'status',
                'contentOptions' => ['class' => 'text-center','data-th' => 'Trạng thái'],
                'label'         => 'Trạng thái',
                'filter'         => false,
                'format'        => 'raw',
                'value'         => function ($model) {
                   return \common\components\CommonLib::getStatusExportWarehouse($model->status);
                }
            ],
            [
                'label'          => 'Thao tác',
                 'contentOptions' => ['data-th' => 'Thao tác','class' => 'text-right'],
                'headerOptions'  => ['style' => 'min-width: 10%;','class' => 'text-right'],
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->getAction();
                },
            ],
        ],

    ]); ?>

</div>
