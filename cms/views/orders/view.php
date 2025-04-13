<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrders */

$this->title = 'Cập nhật đơn hàng: ' . ' ' . $model->identify;
$this->params['breadcrumbs'][] = ['label' => 'Tb Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-orders-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
