<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentSupport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-support-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'amount_total')->textInput() ?>

    <?= $form->field($model, 'amount_total_vn')->textInput() ?>

    <?= $form->field($model, 'cny')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <?= $form->field($model, 'dataAmount')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
