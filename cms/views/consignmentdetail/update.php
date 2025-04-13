<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model cms\models\ConsignmentDetail */

$this->title = 'Update Consignment Detail: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Consignment Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="consignment-detail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
