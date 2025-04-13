<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TbTransfercodeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tb-transfercode-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'shopID') ?>

    <?= $form->field($model, 'businessID') ?>

    <?= $form->field($model, 'identify') ?>

    <?= $form->field($model, 'transferID') ?>

    <?php // echo $form->field($model, 'orderID') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'shipStatus') ?>

    <?php // echo $form->field($model, 'createDate') ?>

    <?php // echo $form->field($model, 'shipDate') ?>

    <?php // echo $form->field($model, 'payDate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
