<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

    $fileDomain = Yii::$app->params['FileDomain'];
?>
<div class="tb-support-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options'              => ['enctype' => 'multipart/form-data', 'class' => 'model-form']
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nameCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'skype')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->fileInput(['class'=>'fileUp']) ?>
    <?php if (!empty($model->image)) : ?>
        <img src="<?= $fileDomain.$model->image ?>">
        <a href="<?= \yii\helpers\Url::to(['clear-image', 'id' => $model->id,'type'=>'image']) ?>" class="text-danger confirm-delete" title="Clear image">Xóa ảnh server</a>
    <?php endif; ?>
    <br>
    <?= $form->field($model, 'thumb')->fileInput(['class'=>'fileUp']) ?>
    <?php if (!empty($model->thumb)) : ?>
        <img src="<?= $fileDomain.$model->thumb ?>">
        <a href="<?= \yii\helpers\Url::to(['clear-image', 'id' => $model->id,'type'=>'thumb']) ?>" class="text-danger confirm-delete" title="Clear image">Xóa ảnh server</a>
    <?php endif; ?>

    <p class="text-right">
        <?= Html::submitButton($model->isNewRecord ? 'Thêm mới' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Hủy', ['/support/index'], ['class'=>'btn btn-primary']) ?>
    </p>

    <?php ActiveForm::end(); ?>

</div>

