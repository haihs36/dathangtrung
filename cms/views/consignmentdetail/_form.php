<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model cms\models\ConsignmentDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="consignment-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'businessID')->textInput() ?>

    <?= $form->field($model, 'transferID')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'orderID')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'long')->textInput() ?>

    <?= $form->field($model, 'wide')->textInput() ?>

    <?= $form->field($model, 'high')->textInput() ?>

    <?= $form->field($model, 'kg')->textInput() ?>

    <?= $form->field($model, 'kgChange')->textInput() ?>

    <?= $form->field($model, 'kgPay')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'shipDate')->textInput() ?>

    <?= $form->field($model, 'payDate')->textInput() ?>

    <?= $form->field($model, 'totalPriceKg')->textInput() ?>

    <?= $form->field($model, 'phidonggo')->textInput() ?>

    <?= $form->field($model, 'phikiemdem')->textInput() ?>

    <?= $form->field($model, 'phiship')->textInput() ?>

    <?= $form->field($model, 'createDate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
