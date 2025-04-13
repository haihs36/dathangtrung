<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model cms\models\TbOrderComplain */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tb-order-complain-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'shopID')->textInput() ?>

    <?= $form->field($model, 'orderID')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
