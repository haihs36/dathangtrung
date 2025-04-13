<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrdersDetail */

$this->title = 'Update Tb Orders Detail: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tb Orders Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-orders-detail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
