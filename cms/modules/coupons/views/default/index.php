<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel cms\modules\coupons\models\ChietkhauSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Chietkhaus';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chietkhau-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Chietkhau', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'product_id',
            'price',
            'source',

            [
                'attribute'      => 'coupon_short_url',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                'contentOptions' => ['class' => 'text-center'],
                'format'         => 'raw',
                'value'          => function ($model) {
                    return '<a href="'.$model->coupon_short_url.'">Link CK</a>';
                }
            ],
            'create_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
