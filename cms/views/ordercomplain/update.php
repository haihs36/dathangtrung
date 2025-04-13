<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model cms\models\TbOrderComplain */

$this->title = 'Update Tb Order Complain: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tb Order Complains', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-order-complain-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
