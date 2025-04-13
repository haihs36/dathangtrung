<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrdersDetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tb-orders-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'orderID') ?>

    <?= $form->field($model, 'productID') ?>

    <?= $form->field($model, 'quantity') ?>

    <?= $form->field($model, 'unitPrice') ?>

    <?php // echo $form->field($model, 'totalPrice') ?>

    <?php // echo $form->field($model, 'unitPriceVn') ?>

    <?php // echo $form->field($model, 'totalPriceVn') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'size') ?>

    <?php // echo $form->field($model, 'color') ?>

    <?php // echo $form->field($model, 'image') ?>

    <?php // echo $form->field($model, 'noteProduct') ?>

    <?php // echo $form->field($model, 'createDate') ?>

    <?php // echo $form->field($model, 'orderNumber') ?>

    <?php // echo $form->field($model, 'shipDate') ?>

    <?php // echo $form->field($model, 'billDate') ?>

    <?php // echo $form->field($model, 'fulFilled') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
