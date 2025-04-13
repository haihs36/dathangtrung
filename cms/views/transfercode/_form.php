<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TbTransfercode */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tb-transfercode-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'shopID')->textInput() ?>

    <?= $form->field($model, 'businessID')->textInput() ?>

    <?= $form->field($model, 'identify')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transferID')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'orderID')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'shipStatus')->textInput() ?>

    <?= $form->field($model, 'createDate')->textInput() ?>

    <?= $form->field($model, 'shipDate')->textInput() ?>

    <?= $form->field($model, 'payDate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
