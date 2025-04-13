<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

    $this->title = 'Thêm nhân viên';
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách nhân viên', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<?php echo \common\widgets\Alert::widget() ?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => false,
            'options'              => ['class' => "form-horizontal"]
        ]); ?>

        <?= $form->field($model, 'first_name',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Họ',['class'=>"col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'last_name',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Tên',['class'=>"col-sm-2 control-label"]) ?>
        <?= $form->field($model, 'username',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Tên đăng nhập',['class'=>"col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'email',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Email',['class'=>"col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'password',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Mật khẩu',['class'=>"col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'confirm_password',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Xác nhận mật khẩu',['class'=>"col-sm-2 control-label"]) ?>


        <?= $form->field($model, 'role', [
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->dropDownList(\common\components\CommonLib::getListRole(), [
            'class' => 'select2 form-control', 'prompt' => '','data-placeholder'=>'Chọn vai trò'])->label('Phân quyền:', ['class'=>"col-sm-2 control-label"])
        ?>

        <p class="text-right">
            <a class="btn btn-info" href="/user/index">Hủy</a>
            <?= Html::submitButton($model->isNewRecord ? 'Thêm' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </p>

        <?php ActiveForm::end(); ?>
    </div>
</div>


