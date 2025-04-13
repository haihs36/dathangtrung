<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model cms\modules\coupons\models\Chietkhau */

$this->title = 'Update Chietkhau: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Chietkhaus', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="chietkhau-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
