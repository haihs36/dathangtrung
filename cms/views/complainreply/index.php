<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbComplainReplySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tb Complain Replies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-complain-reply-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Tb Complain Reply', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                    'headerOptions' => ['style' => 'width: 10%;'],
                    'label'         => 'Mã khiếu nại',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return $model->complainID;
                    }
            ],
            [
                    'headerOptions' => ['style' => 'width: 15%;'],
                    'label'         => 'Mã đơn hàng',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return $model->order->identify.'<br/><a target="_blank" href="/orders/'.$model->order->id.'">Xem đơn hàng</a>';
                    }
            ],
            [
                'headerOptions' => ['style' => 'width: 15%;'],
                'label'         => 'Khách hàng',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return $model->customer->fullname;
                }
            ],
            [
                    'headerOptions' => ['style' => 'width: 15%;'],
                    'label'         => 'NV Xử lý',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return !empty($model->admin) ? $model->admin->last_name .' ' . $model->admin->first_name : '';
                    }
            ],
            [
                    'headerOptions' => ['style' => 'width: 30%;'],
                    'label'         => 'Nội dung',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return $model->message;
                    }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
