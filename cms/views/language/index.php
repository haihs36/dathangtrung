<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel cms\models\TbLanguageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách từ khóa tìm kiếm';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-language-index">
    <div class="text-right mb15">
        <?= Html::a('Thêm từ khóa', ['create'], ['class' => 'btn btn-success']) ?>
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
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i> Thư viện từ khóa </h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>true
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'      => 'name',
                'filter'         => false,
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->name;
                }
            ],
            [
                'attribute'      => 'nameCN',
                'filter'         => false,
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->nameCN;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>

</div>
