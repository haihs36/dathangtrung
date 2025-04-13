<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbSupportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-support-index">
    <div class="text-right mb15">
        <?= Html::a('Thêm mới', ['create'], ['class' => 'btn btn-success']) ?>
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
            //            '{export}',
        ],
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i>'.$this->title.'</h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>true
        ],
        'columns' => [
                        [
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 15%;'],
                    'label'         => 'Image',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return '<img style="max-width:80px" src="' . (!empty($model->image) ? Yii::$app->params['FileDomain'] . $model->image : '') . '">';
                    }
            ],
            [
//                    'headerOptions' => ['style' => 'width: 15%;'],
                    'label'         => 'Họ và tên',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return $model->name;
                    }
            ],
            [
//                    'headerOptions' => ['style' => 'width: 15%;'],
                    'label'         => 'Số điệnt thoại',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return $model->mobile;
                    }
            ],
//            [
//                    'label'         => 'Mã QCODE',
//                    'format'        => 'raw',
//                    'value'         => function ($model) {
//                        return $model->nameCode;
//                    }
//            ],

            [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
            ],
        ],
    ]); ?>

</div>
