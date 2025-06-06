<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrderSupplier */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tb Order Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-order-supplier-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'orderID',
            'supplierID',
            'billLadinID',
            'cny',
            'quantity',
            'shopProductID',
            'shopPriceKg',
            'shopPriceTQ',
            'shopPrice',
            'shopPriceTotal',
            'actualPayment',
            'discount',
            'orderFee',
            'weightCharge',
            'discountDeals',
            'weightDiscount',
            'freeCount',
            'shipmentFee',
            'shipmentVn',
            'weight',
            'totalWeight',
            'noteInsite',
            'noteOther',
            'isCheck',
            'shippingStatus',
            'status',
            'link',
            'incurredFee',
            'kgFee',
            'isStock',
        ],
    ]) ?>

</div>
