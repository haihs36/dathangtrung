<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbNewsSearchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quản lý link sản phẩm';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-hot-link-index">
    <div class="text-right mb15">
        <?= Html::a('Thêm link sản phẩm', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'=>true,
        'pjax' => true,
        'toolbar' => [
            ['content'=>
                 Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
            ],
        ],
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i>'.$this->title.'</h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>true
        ],
        'columns' => [
                [
                        'headerOptions' => ['style' => 'width: 15%;'],
                        'label'         => 'Hình ảnh',
                        'format'        => 'raw',
                        'value'         => function ($model) {
                            return '<img style="width:50px" src="' . (!empty($model->image) ? Yii::$app->params['FileDomain'] . $model->image : '') . '">';
                        }
                ],
                [
                    'label'      => 'Chuyên mục',
                    'headerOptions'  => [ 'style' => 'width: 20%;'],
                    'contentOptions' => ['class' => 'hot'],
                    'filter'         => \common\components\CommonLib::DropDownList('TbHotLinkSearch[cateid]', \cms\models\TbCateProduct::find()->sort()->all(), isset($params['TbHotLinkSearch']['cateid']) ? $params['TbHotLinkSearch']['cateid'] : '', '-- Tất cả --'),
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return $model->cateproduct ? $model->cateproduct->title : null;
                    }
                ],
                [
                        'headerOptions' => ['style' => 'width: 25%;'],
                        'label'         => 'Tên sản phẩm',
                        'format'        => 'raw',
                        'value'         => function ($model) {
                            return $model->name;
                        }
                ],
                [
                        'headerOptions' => ['style' => 'width: 15%;'],
                        'label'         => 'Giá',
                        'format'        => 'raw',
                        'value'         => function ($model) {
                            return '<strong class="currency">'.(!empty($model->price) ? $model->price : 0).'</strong><em>đ</em>';
                        }
                ],


                [
                        'headerOptions' => ['style' => 'width: 8%;'],
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                ],
        ],
    ]); ?>

</div>
