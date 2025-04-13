<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TbKg */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tb-kg-form">
    <?php echo \common\widgets\Alert::widget() ?>
    <div class="">

        <div class="box-body">
            <div class="container">
                <?php $form = ActiveForm::begin([
                    'enableAjaxValidation' => false,
                    'options'              => ['class' => "form-horizontal"]
                ]); ?>

                <?= $form->field($model, 'from', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true])->label('Giá tệ từ', ['class' => "col-sm-2 control-label"]) ?>

                <?= $form->field($model, 'to', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true])->label('Đến', ['class' => "col-sm-2 control-label"]) ?>

                <?= $form->field($model, 'cny', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['class' => 'currency form-control'])->label('Tỷ giá', ['class' => "col-sm-2 control-label"]) ?>


                <div class="text-center">
                    <a class="btn btn-info" href="/cny">Hủy</a>
                    <?= Html::submitButton($model->isNewRecord ? 'Thêm' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
