<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbChatMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tb Chat Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-chat-message-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Tb Chat Message', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'chat_id',
            'to_user_id',
            'from_user_id',
            'order_id',
            'message:ntext',
            // 'status',
            // 'type',
            // 'timestamp',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
