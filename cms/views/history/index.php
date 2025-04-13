<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lịch sử người dùng';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-history-index">
    <?php echo $this->render('_search', ['model' => $searchModel, 'params' => $params]); ?>
    <br>
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
            'heading'=>'<h3 class="panel-title"><i class="fa fa-history" aria-hidden="true"></i> Lịch sử người dùng </h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>true
        ],
        'columns' => [
            /*[
                    'class'         => 'yii\grid\SerialColumn',
                    'header'        => 'TT',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
            ],*/
            [
                    'headerOptions' => ['style' => 'width: 15%;'],
                    'contentOptions' => ['data-th' => 'Mã đơn hàng'],
                    'attribute' => 'orderID',
                    'label'     => 'Mã đơn hàng',
                    'format'    => 'raw',
                    'filter'    => '',
                    'value'     => function ($model) {
                        return isset($model->order->identify) ? $model->order->identify : null;
                    }
            ],
            [
//                    'headerOptions' => ['style' => 'width: 20%;'],
                    'attribute' => 'userID',
'contentOptions' => ['data-th' => 'Nhân viên'],
                    'label'     => 'Nhân viên tạo',
                    'format'    => 'raw',
                    'filter'    => '',
                    'value'     => function ($model) {
                        return isset($model->user) ? ' <a target="_blank"  title="Chi tiết nhân viên" href="'.\yii\helpers\Url::toRoute(['user/view','id'=>$model->userID]).'"><i class="fa fa-eye" aria-hidden="true"></i> '.($model->user->first_name.' '.$model->user->last_name).'(<i>'.$model->user->username.'</i>)</a>' : null;
                    }
            ],

            [
                    'headerOptions' => ['style' => 'width: 40%;'],
                    'contentOptions' => ['data-th' => 'Nội dung'],
                    'label'     => 'Nội dung',
                    'format'    => 'raw',
                    'value'     => function ($model) {
                        return $model->content;
                    }
            ],
            [
                'headerOptions' => ['style' => 'width: 15%;'],
                'attribute'     => 'createDate',
                'filter'         => false,
                'contentOptions' => ['data-th' => 'Ngày giờ'],
                'label'         => 'Ngày giờ',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return date('d-m-Y H:i:s', strtotime($model->createDate));
                }
            ],
            [
                    'label'          => 'Thao tác',
                    'headerOptions'  => ['class' => 'text-right','style' => 'width: 8%;'],
                    'contentOptions' => ['class' => 'text-right'],
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return $model->getAction();
                    },
            ],
           /* [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}'
            ],*/
        ],
    ]); ?>

</div>
