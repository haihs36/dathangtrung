<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbOrdersMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách các thông báo cho đơn hàng';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-orders-message-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout' =>'{items}{summary}{pager}',
        'tableOptions' => ['class' => 'table  table-bordered table-hover table-striped'],
        'rowOptions'=>function ($model, $key, $index, $grid){
            $class=$index%2?'success':'warning';
            return array('key'=>$key,'index'=>$index,'class'=>$class);
        },

        'columns'      => [

            [
                'headerOptions' => ['style' => 'width: 10%;'],
                'label'      => 'ID',
                'format'         => 'raw',
                'attribute'         => 'title',
                'value'          => function ($model) {
                    return $model->id;
                }
            ],
            [
                'label'      => 'Mã đơn hàng',
                'format'         => 'raw',
                'attribute'         => 'title',
                'value'          => function ($model) {
                    return $model->identify;
                }
            ],
            [
                'label'      => 'Người gửi',
                'format'         => 'raw',
                'attribute'         => 'userID',
                'value'          => function ($model) {
                    return $model->user->username;
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 40%;'],
                'label'      => 'Tiêu đề',
                'format'         => 'raw',
                'attribute'         => 'title',
                'value'          => function ($model) {
                    return $model->title;
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 10%;'],
                'label'      => 'Trạng thái',
                'format'         => 'raw',
                'attribute'         => 'status',
                'value'          => function ($model) {
                    return $model->status == 1 ? 'Chưa xem': 'Đã xem';
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
