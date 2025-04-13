<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TbOrderSupplier */

$this->title = 'Create Tb Order Supplier';
$this->params['breadcrumbs'][] = ['label' => 'Tb Order Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-order-supplier-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
