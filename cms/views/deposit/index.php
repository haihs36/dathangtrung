<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bảng phí đặt cọc đơn hàng';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-service-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="mb15 text-right">
        <?= Html::a('Thêm mới', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'=>true,
        'pjax' => true,
        'toolbar' =>false,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i> Bảng phí đặt cọc đơn hàng </h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>false
        ],
        'columns' => [
            /*'from',*/
            [
                'filter' =>false,
                'label'      => 'Từ',
                'attribute'     => 'from',
                'format'         => 'raw',
                'value'          => function ($model) {
                    return '<b class="vnd-unit">'.number_format($model->from) .'</b> VNĐ';
                },
            ],
            /*'to',*/
            [
                'filter' =>false,
                'label'      => 'Đến',
                'attribute'     => 'to',
                'format'         => 'raw',
                'value'          => function ($model) {
                    return '<b class="vnd-unit">'.number_format($model->to) .'</b> VNĐ';
                },
            ],
            /*'percent',*/
            [
                'filter' =>false,
                'label'      => '% Đặt cọc',
                'attribute'     => 'percent',
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->percent.' %' ;
                },
            ],
            [
                'label'      => 'Hành Động',
                'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 15%;'],
                'contentOptions' => ['class' => 'text-center'],
                'filterOptions'  => ['class' => 'text-center'],
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->getAction();
                },
            ],
        ],
    ]); ?>
</div>
