<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrderSupplier */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tb-order-supplier-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'orderID')->textInput() ?>

    <?= $form->field($model, 'supplierID')->textInput() ?>

    <?= $form->field($model, 'billLadinID')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cny')->textInput() ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'shopProductID')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shopPriceKg')->textInput() ?>

    <?= $form->field($model, 'shopPriceTQ')->textInput() ?>

    <?= $form->field($model, 'shopPrice')->textInput() ?>

    <?= $form->field($model, 'shopPriceTotal')->textInput() ?>

    <?= $form->field($model, 'actualPayment')->textInput() ?>

    <?= $form->field($model, 'discount')->textInput() ?>

    <?= $form->field($model, 'orderFee')->textInput() ?>

    <?= $form->field($model, 'weightCharge')->textInput() ?>

    <?= $form->field($model, 'discountDeals')->textInput() ?>

    <?= $form->field($model, 'weightDiscount')->textInput() ?>

    <?= $form->field($model, 'freeCount')->textInput() ?>

    <?= $form->field($model, 'shipmentFee')->textInput() ?>

    <?= $form->field($model, 'shipmentVn')->textInput() ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <?= $form->field($model, 'totalWeight')->textInput() ?>

    <?= $form->field($model, 'noteInsite')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'noteOther')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shippingStatus')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'incurredFee')->textInput() ?>

    <?= $form->field($model, 'kgFee')->textInput() ?>

    <?= $form->field($model, 'isStock')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
