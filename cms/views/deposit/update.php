<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Deposit */

$this->title = 'Chỉnh sửa phí đặt cọc: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bảng phí đặt cọc', 'url' => ['index']];
?>
<div class="deposit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
