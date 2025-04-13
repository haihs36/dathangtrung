<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OrdersFastSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Đơn hàng nhanh';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-fast-index">
    <?php Pjax::begin(['enablePushState' => false]); ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php  //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'format'         => 'raw',
                'attribute'         => 'link',
                'headerOptions'  => ['style' => 'width: 20%;'],
                'value'          => function ($model) {
                    return '<div style="display: block">'.Html::encode($model->link).'</div>';
                }
            ],
            'mobile',
            'fullname',
            'note',
            'create_time',
            [
                'label'          => 'Thao tác',
                'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 15%;'],
                'contentOptions' => ['class' => 'text-center'],
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->getAction();

                },
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
