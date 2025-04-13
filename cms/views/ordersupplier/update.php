<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrderSupplier */

$this->title = 'Update Tb Order Supplier: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tb Order Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-order-supplier-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
