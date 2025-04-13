<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quản lý phí dịch vụ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-service-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i> Quản lý % phí dịch vụ </h3>',
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
                'label'      => '% Phí dịch vụ',
                'attribute'     => 'percent',
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->percent.' %' ;
                },
            ],
            [
                'header'        => 'Tỉnh Thành',
                'contentOptions' => ['data-th' => 'Tỉnh Thành'],
                'attribute' => 'provinID',
                'vAlign' => 'middle',
                'value' => function ($model) {
                    return $model->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Province::find()->select(['id','name'])->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Chọn..'],
                'format' => 'raw'
            ],
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
