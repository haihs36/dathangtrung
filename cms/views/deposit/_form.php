<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model cms\models\TbService */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tb-service-form">

    <?php echo \common\widgets\Alert::widget() ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-user-secret" aria-hidden="true"></i> Cập nhật thông tin % cọc</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="container">
                <?php $form = ActiveForm::begin([
                    'enableAjaxValidation' => false,
                    'options'              => ['class' => "form-horizontal"]
                ]); ?>

                <?= $form->field($model, 'from', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['class' => 'currency form-control'])->label('Giá bắt đầu', ['class' => "col-sm-2 control-label"]) ?>

                <?= $form->field($model, 'to', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['class' => 'currency form-control'])->label('Giá đích ', ['class' => "col-sm-2 control-label"]) ?>

                <?= $form->field($model, 'percent', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => 'true'])->label('% Đặt cọc', ['class' => "col-sm-2 control-label"]) ?>


                <p class="text-center">
                    <a class="btn btn-info" href="/deposit">Hủy</a>
                    <?= Html::submitButton($model->isNewRecord ? 'Thêm' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </p>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
