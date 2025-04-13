<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TbHotLink */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tb-hot-link-form">

    <?php $form = ActiveForm::begin([
//            'enableAjaxValidation' => true,
            'options'              => ['enctype' => 'multipart/form-data', 'class' => 'model-form']
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <div class="form-group field-category-title required <?= $model->cateid == null ? 'has-error' : ''; ?>">
        <label for="category-parent" class="control-label">Thư mục cha</label>
        <?= \common\components\CommonLib::DropDownList('TbHotLink[cateid]', \cms\models\TbCateProduct::find()->sort()->all(), $model->cateid, '-- Chọn mục dữ liệu --'); ?>
    </div>
    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'price')->textInput(['class' => 'form-control currency']) ?>
    <?= $form->field($model, 'image')->fileInput(['class'=>'form-control fileUp']) ?>
    <?php if (!empty($model->image)) : ?>
        <img style="max-width: 80px" src="<?=  Yii::$app->params['FileDomain'].$model->image ?>">
        <a href="<?= \yii\helpers\Url::to(['clear-image', 'id' => $model->id]) ?>" class="text-danger confirm-delete" title="Clear image">Xóa ảnh server</a>
    <?php endif; ?>
<br>
    <p class="text-right">
        <?= Html::submitButton($model->isNewRecord ? 'Thêm' : 'Sửa', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Hủy', ['/hotlink/index'], ['class'=>'btn btn-primary']) ?>
    </p>

    <?php ActiveForm::end(); ?>

</div>
