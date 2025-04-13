<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbAccountBanking */

$this->title = 'Nạp tiền vào tài khoản' . ': ' . $model->customer->username;
$this->params['breadcrumbs'][] = ['label' => 'Ví điện tử', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box clear">
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
