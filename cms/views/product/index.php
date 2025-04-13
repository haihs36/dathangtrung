<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel cms\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
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
            'toolbar' => [
                ['content'=>
                     Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
//            '{export}',
            ],
            'panel' => [
                'heading'=>'<h3 class="panel-title"><i class="fa fa-fw fa-users" aria-hidden="true"></i> Danh sách sản phẩm </h3>',
                'type' => GridView::TYPE_PRIMARY,
                'showFooter'=>true
            ],
            'columns' => [
                [
                    'headerOptions' => ['style' => 'width: 10%;'],
                    'label'         => 'avatar',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return '<img width="80" height="80" class="img-thumbnail "  src="'.(!empty($model->image) ? $model->image : Yii::$app->homeUrl.'images/'.USER_PROFILE_IMAGES_DIRECTORY.'/'.USER_PROFILE_DEFAULT_IMAGE) .'">';
                    }
                ],
                [
                    'header'        => 'Tên sản phẩm',
                    'attribute' => 'id',
                    'vAlign' => 'middle',
                    'width' => '180px',
                    'value' => function ($model) {
                        return $model->name;
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
                    'headerOptions' => ['style' => 'width: 15%;'],
                    'label'         => 'Link',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return $model->link;
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
                    'headerOptions' => ['style' => 'width: 8%;'],
                    'label'         => 'Giá',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return $model->unitPrice .' ¥';
                    }
                ],


            ],
        ]);?>
</div>
