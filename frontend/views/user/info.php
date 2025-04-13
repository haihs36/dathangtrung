<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    $this->title = 'Thông tin cá nhân';
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
<?php \yii\widgets\Pjax::begin(['enablePushState' => false]); ?>
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
        <h3 class="box-title"> Thông tin cá nhân</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?= $form->field($model, 'fullname', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Họ và Tên:', ['class' => "col-sm-2 control-label"]) ?>
        <?= $form->field($model, 'email', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('email:', ['class' => "col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'phone', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('phone:', ['class' => "col-sm-2 control-label"]) ?>

        <?php
        echo $form->field($model, 'provinID',[
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Province::find()->select(['id', 'name'])->all(), 'id', 'name'), [
            'class' => 'form-control select2','style'=>'width:100%','prompt'=>'','data-placeholder'=>'Chọn kho đích'])->label('Kho đích:', ['class' => "col-sm-2 control-label"]);
        ?>

        <?= $form->field($model, 'billingAddress', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->textarea(['maxlength' => true, 'class' => 'form-control','rows'=>3])->label('Địa chỉ:', ['class' => "col-sm-2 control-label"]) ?>

    </div>
    <div class="box-footer text-center">
        <button type="submit" class="btn btn-primary"><?= $model->isNewRecord ? 'Gửi đơn' : 'Cập nhật' ?></button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php \yii\widgets\Pjax::end(); ?>

