<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Tbshippers */

$this->title = 'Mã vận chuyển: ' . ' ' . $model->shippingCode;
$this->params['breadcrumbs'][] = ['label' => 'Đơn ký gửi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->shippingCode, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbshippers-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
