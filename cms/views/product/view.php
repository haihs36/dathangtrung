<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model cms\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->productID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->productID], [
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
            'productID',
            'supplierID',
            'shopProductID',
            'shopID',
            'sourceName',
            'md5',
            'name',
            'quantity',
            'unitPrice',
            'image',
            'link',
            'slug',
            'description:ntext',
            'text:ntext',
            'thumb',
            'time:datetime',
            'is_hot',
            'status',
            'views',
            'create_date',
            'color',
            'size',
        ],
    ]) ?>

</div>
