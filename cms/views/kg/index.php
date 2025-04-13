<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TbKgSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Giá cước cân nặng';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-kg-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="mb15 text-right">
        <?= Html::a('Thêm phí cân nặng', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'=>true,
        'pjax' => true,
        'toolbar' =>false,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i> Quản lý giá cước cân nặng </h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>false
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'      => 'Cân nặng từ',
                'attribute'     => 'from',
                'filter' =>false,
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->from.' kg';
                },
            ],
            /*'to',*/
            [
                'filter' =>false,
                'label'      => 'Đến (kg)',
                'attribute'     => 'to',
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->to .' kg';
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
