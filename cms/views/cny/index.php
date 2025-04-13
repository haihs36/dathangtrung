<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \common\models\CnySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quản lý tỷ giá ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-kg-index">

    <div class="mb15 text-right">
        <?= Html::a('Thêm tỷ giá', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'=>true,
        'pjax' => true,
        'toolbar' =>false,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i> Quản lý tỷ giá </h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>false
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'      => 'Giá từ (tệ)',
                'attribute'     => 'from',
                'filter' =>false,
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->from;
                },
            ],
            /*'to',*/
            [
                'filter' =>false,
                'label'      => 'Đến (tệ)',
                'attribute'     => 'to',
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->to ;
                },
            ],
            [
                'label'      => 'Tỷ giá',
                'attribute'     => 'cny',
                'format'         => 'raw',
                'filter' =>false,
                'value'          => function ($model) {
                    return '<b class="vnd-unit">'.number_format($model->cny) .'</b>';
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
    <?php Pjax::end(); ?>

</div>
