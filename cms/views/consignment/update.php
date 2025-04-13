<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model cms\models\Consignment */

$this->title = 'Cập nhật phiếu: PXK-' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Quản lý phiếu xuất', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consignment-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
