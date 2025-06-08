<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel cms\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'order detail';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'=>true,
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

        'toolbar' => [
            '{export}',
            '{toggleData}',
        ],

        'columns' => [
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'label'         => 'avatar',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return '<img width="80" height="80" class="img-thumbnail "  src="'.(!empty($model->image) ? $model->image : Yii::$app->homeUrl.'images/'.USER_PROFILE_IMAGES_DIRECTORY.'/'.USER_PROFILE_DEFAULT_IMAGE) .'">';
                }
            ],
            [
                'header'        => 'Tên sản phẩm',
                'headerOptions' => ['style' => 'width: 15%;'],
                'attribute' => 'id',
                'vAlign' => 'middle',
                'value' => function ($model) {
                    return '<a href="'.$model->link.'"> '.$model->name.'</a>';
                },
                'format' => 'raw'
            ],
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'header'         => 'Nguồn',
                'format'        => 'raw',
                'filter'         =>false,
                'value'         => function ($model) {
                    return $model->sourceName;
                }
            ],

            [
                'headerOptions' => ['style' => 'width: 10%;'],
                'label'         => 'Thuộc tính',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return 'Màu sắc: '.$model->color.'<br/>Kích thước: '.$model->size;
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 6%;'],
                'label'         => 'Số lượng',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return $model->quantity;
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 8%;'],
                'label'         => 'Giá',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return $model->unitPrice .' ¥';
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 8%;'],
                'label'         => 'Tổng tiền',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return $model->totalPrice.' ¥';
                }
            ],


        ],
    ]);?>
</div>
