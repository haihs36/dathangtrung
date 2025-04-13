<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbSupplier */

$this->title = 'Update Tb Supplier: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tb Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-supplier-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
