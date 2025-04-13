<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Bag */

$this->title = 'Cập nhật bao ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách bao', 'url' => ['index']];
?>
<div class="bag-update">

    <?= $this->render('_form', [
        'model' => $model,
        'total_kg' => $total_kg,
        'list_barcode' => $list_barcode,
    ]) ?>

</div>
