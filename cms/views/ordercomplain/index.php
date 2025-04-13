<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel cms\models\TbOrderComplainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tb Order Complains';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-order-complain-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <!-- <p>
        <?/*= Html::a('Create Tb Order Complain', ['create'], ['class' => 'btn btn-success']) */?>
    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'       => '{items}{summary}{pager}',
       // 'tableOptions' => ['class' => 'grid-order table  table-bordered table-hover table-striped'],
        'rowOptions'   => function ($model, $key, $index, $grid) {
            $class = $index % 2 ? 'success' : 'warning';
            return array('key' => $key, 'index' => $index, 'class' => $class);
        },
        'columns' => [
            [
                'class'         => 'yii\grid\SerialColumn',
                'header'        => 'TT',
                'headerOptions' => ['style' => 'width: 10%;'],
            ],
            [
                'headerOptions' => ['style' => 'width: 25%;'],
                'attribute'     => 'orderID',
                'label'         => 'Mã đơn hàng',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return '<a target="_blank" title="Chi tiết đơn hàng" href="'.\yii\helpers\Url::toRoute(['orders/view','id'=>$model->orderID]).'"><i class="fa fa-eye" aria-hidden="true"></i>'.($model->order->identify).'</a>';
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 20%;'],
                'attribute'     => 'title',
                'label'         => 'Tiêu đề',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return $model->title;
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 30%;'],
                'attribute'     => 'content',
                'label'         => 'Nội dung',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return $model->content;
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
