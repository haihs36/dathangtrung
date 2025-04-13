<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model cms\models\TbService */

$this->title = 'Sửa phí dịch vụ: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Phí dịch vụ', 'url' => ['index']];
/*$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];*/
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-service-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
