<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin([
    'id'      => 'frm-ajax-register',
    'fieldConfig' => ['template' => "{label}\n{input}\n{hint}"],
    //    'method'=>'post',
    //    'enableAjaxValidation' => true,
    //    'enableClientValidation' => false,
    'options' => [
        'class' => 'don-hang-dang-ky-form'
    ],
]); ?>
<div class="popup-login-regiter-form">
    <?php echo $form->errorSummary($model, ['header' => '']);?>
    <div class="left-popup">
        <?php echo $this->render('@app/views/templates/_tab_function',['tab'=>'register']); ?>
        <?= $form->field($model, 'username', [
            'template' => "{label}\n{input}\n<span class=\"field-suffix\"><span class=\"fa fa-user\"></span></span>\n{hint}",
            'options'  => ['class' => 'form-item Custommer form-item-name']
        ])->textInput(['class' => 'form-text', 'placeholder' => 'Tên đăng nhập (*)'])->label(false) ?>

        <?= $form->field($model, 'password', [
            'template' => "{label}\n{input}\n<span class=\"field-suffix\"><span class=\"fa fa-lock\"></span></span>\n{hint}",
            'options'  => ['class' => 'form-item Custommer form-item-name']
        ])->passwordInput(['class' => 'form-text', 'placeholder' => 'Mật khẩu (*)'])->label(false) ?>

        <?php echo $form->field($model, 'email', [
            'template' => "{label}\n{input}\n<span class=\"field-suffix\"><span class=\"fa fa-envelope\"></span></span>\n{hint}",
            'options'  => ['class' => 'form-item Custommer form-item-name']
        ])->textInput(['class' => 'form-text', 'placeholder' => 'Thư điện tử (*)'])->label(false) ?>

        <?php echo $form->field($model, 'fullname', [
            'template' => "{label}\n{input}\n<span class=\"field-suffix\"><span class=\"fa fa-blind\"></span></span>\n{hint}",
            'options'  => ['class' => 'form-item Custommer form-item-name']
        ])->textInput(['class' => 'form-text', 'placeholder' => 'Họ và tên (*)'])->label(false) ?>

        <?php echo $form->field($model, 'phone', [
            'template' => "{label}\n{input}\n<span class=\"field-suffix\"><span class=\"fa fa-phone-square\"></span></span>\n{hint}",
            'options'  => ['class' => 'form-item Custommer form-item-name']
        ])->textInput(['class' => 'form-text', 'placeholder' => 'Số điện thoại (*)'])->label(false) ?>

        <?php /* $form->field($model, 'agree',[
                'template' => "{label}\n{error}",
                'options'=>['class'=>'form-item-agree']
        ])->checkbox()->label('Tôi đã đọc và đồng ý với <a href="/quy-dinh-ve-ky-gui-hang">điều khoản</a> của ') */?>

        <?php echo $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
            'template' => '<div class="row"><div class="col-lg-6">{input}</div><div class="col-md-4">{image}</div><div class="col-md-2"><a href="javascript:void(0)" class="refresh-captcha"><i class="fa fa-refresh" aria-hidden="true"></i></a></div></div>',
            'imageOptions' => [
                'class' => 'my-captcha'
            ]
        ]) ?>

        <div class="form-group form-actions form-wrapper">
            <?= Html::input('submit','login-button','Đăng ký', ['class' => 'btn btn-primary form-submit','id'=>'btnSubmit']) ?>
        </div>
    </div>
<!--    --><?php //echo $this->render('@app/views/templates/_social_facebook'); ?>
</div>
<?php ActiveForm::end(); ?>


