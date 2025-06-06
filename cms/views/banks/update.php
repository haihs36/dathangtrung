<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbBank */

$this->title = 'Update Tb Bank: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tb Banks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-bank-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
