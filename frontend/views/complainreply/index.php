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

            'customerID',
            'adminID',
            'complainID',
            'message',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
