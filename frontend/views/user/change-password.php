<?php
    use yii\widgets\ActiveForm;

    $this->title = 'Thay đổi mật khẩu';
    $this->params['breadcrumbs'][] = $this->title;
?>
<?php
if (Yii::$app->session->hasFlash('success')):
    ?>
    <script>
        $(function () {
            swal({
                    title: "Thông báo",
                    text: "<?= Yii::$app->session->getFlash('success') ?>",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Ok",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    confirmButtonClass: "btn-success"
                },
                function(isConfirm){
                    if (isConfirm) {
                        location.reload();
                    }
                });

        });
    </script>
<?php endif; ?>
<div class="box box-info">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'form-horizontal',
        ]
    ]);
    ?>
    <div class="box-header with-border">
        <h3 class="box-title"> Thay thổi mật khẩu</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?= $form->field($model, 'old_password', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->passwordInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Mật khẩu cũ:', ['class' => "col-sm-2 control-label"]) ?>
        <?= $form->field($model, 'password', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->passwordInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Mật khẩu mới:', ['class' => "col-sm-2 control-label"]) ?>
        <?= $form->field($model, 'confirm_password', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->passwordInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Xác nhận mật khẩu:', ['class' => "col-sm-2 control-label"]) ?>

    </div>
    <div class="box-footer text-center">
        <button type="submit" class="btn btn-primary"><?= $model->isNewRecord ? 'Gửi đơn' : 'Cập nhật' ?></button>
    </div>
    <?php ActiveForm::end(); ?>
</div>