<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
?>
<?php $form = ActiveForm::begin([
    'id'=>'frm-ajax-login',
    // 'validateOnChange' => false,
    //    'enableAjaxValidation' => true,
    //    'enableClientValidation' => true,
]); ?>
<?= $form->errorSummary($model,['header'=>'']); ?>
<div class="popup-login-regiter-form">
    <div class="left-popup">
        <!--tab-->
        <?php echo $this->render('@app/views/templates/_tab_function',['tab'=>'login']); ?>
        <!--end tab-->
        <?= $form->field($model, 'username', [
            'template' => "{label}\n{input}\n<span class=\"field-suffix\"><span class=\"fa fa-user\"></span></span>\n{hint}",
            'options' => ['class' => 'form-item form-type-textfield form-item-name']
        ])->textInput(['class'=>'form-text','placeholder'=>'Tên đăng nhập'])->label(false) ?>

        <?= $form->field($model, 'password', [
            'template' => "{label}\n{input}\n<span class=\"field-suffix\"><span class=\"fa fa-lock\"></span></span>\n{hint}",
            'options' => ['class' => 'form-item form-type-textfield form-item-name']
        ])->passwordInput(['class'=>'form-text','placeholder'=>'Mật khẩu'])->label(false) ?>

        <?= $form->field($model, 'rememberMe')->checkbox()->label('Duy trì đăng nhập') ?>
        <div class="form-group form-actions form-wrapper">
            <input type="hidden" name="form_id" value="frm-ajax-login">
            <?php echo Html::input('submit','login-button','Đăng nhập', ['class' => 'btn btn-primary form-submit','id'=>'btnSubmit']) ?>
        </div>
    </div>
<!--    --><?php //echo $this->render('@app/views/templates/_social_facebook'); ?>
</div>
<?php ActiveForm::end(); ?>


