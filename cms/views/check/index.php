<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TbKgSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Giá phí kiểm đếm';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-kg-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="mb15 text-right">
        <?= Html::a('Thêm phí', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'=>true,
        'pjax' => true,
        'toolbar' =>false,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i> Quản lý giá cước </h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>false
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'      => 'Số lượng sản phẩm từ',
                'attribute'     => 'from',
                'filter' =>false,
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->from.' ';
                },
            ],
            /*'to',*/
            [
                'filter' =>false,
                'label'      => 'Đến',
                'attribute'     => 'to',
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->to .' ';
                },
            ],
            /*'price',*/
            [
                'label'      => 'Giá',
                'attribute'     => 'price',
                'format'         => 'raw',
                'filter' =>false,
                'value'          => function ($model) {
                    return '<b class="vnd-unit">'.number_format($model->price) .'</b> VNĐ';
                },
            ],
            // 'createDate',
            /*['class' => 'yii\grid\ActionColumn'],*/
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
