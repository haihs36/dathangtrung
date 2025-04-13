<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel cms\models\ConsignmentDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Consignment Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consignment-detail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Consignment Detail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'businessID',
            'transferID',
            'orderID',
            'status',
            // 'long',
            // 'wide',
            // 'high',
            // 'kg',
            // 'kgChange',
            // 'kgPay',
            // 'note:ntext',
            // 'shipDate',
            // 'payDate',
            // 'totalPriceKg',
            // 'phidonggo',
            // 'phikiemdem',
            // 'phiship',
            // 'createDate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
