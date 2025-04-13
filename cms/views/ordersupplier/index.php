<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbOrderSupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trả hàng';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-order-supplier-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'orderID',
            'supplierID',
            'billLadinID',
            'cny',
            // 'quantity',
            // 'shopProductID',
            // 'shopPriceKg',
            // 'shopPriceTQ',
            // 'shopPrice',
            // 'shopPriceTotal',
            // 'actualPayment',
            // 'discount',
            // 'orderFee',
            // 'weightCharge',
            // 'discountDeals',
            // 'weightDiscount',
            // 'freeCount',
            // 'shipmentFee',
            // 'shipmentVn',
            // 'weight',
            // 'totalWeight',
            // 'noteInsite',
            // 'noteOther',
            // 'isCheck',
            // 'shippingStatus',
            // 'status',
            // 'link',
            // 'incurredFee',
            // 'kgFee',
            // 'isStock',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
