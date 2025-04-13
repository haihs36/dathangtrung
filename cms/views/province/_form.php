<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Province */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="province-form">
    <?php echo \common\widgets\Alert::widget() ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-user-secret" aria-hidden="true"></i> Cập nhật thông tin bảng giá</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <?php $form = ActiveForm::begin([
                'enableAjaxValidation' => false,
                'options' => ['class' => "form-horizontal"]
            ]); ?>

            <?= $form->field($model, 'name', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->label('Tên thành phố', ['class' => "col-sm-2 control-label"]) ?>

            <?= $form->field($model, 'note', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textarea(['maxlength' => true])->label('Ghi chú', ['class' => "col-sm-2 control-label"]) ?>
            <p class="text-center">
                <a class="btn btn-info" href="/province">Hủy</a>
                <?= Html::submitButton($model->isNewRecord ? 'Thêm' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </p>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
