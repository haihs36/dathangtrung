<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbSupport */

$this->title = 'Cập nhật ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Hỗ trợ trực tuyến', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Cập nhật';
?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
