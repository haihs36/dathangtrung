<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbHotLink */

$this->title = 'Chỉnh sửa link sản phẩm: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'QL link sản phẩm', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= \yii\helpers\Html::encode($this->title) ?></h3>
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