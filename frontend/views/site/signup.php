<?php use yii\bootstrap\ActiveForm; ?>
<div id="myModal" class="modal " role="dialog" tabindex="-1" style="display: block">
    <div class="modal-dialog login-pop-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">
                    <img src="/file/media/avatar/default_user_icon.png" class="img-thumbnail img-circle img-responsive" height="30px" width="30px" /> Đăng ký thành viên
                </h1>
                <a class="close" data-dismiss="modal" aria-hidden="true">×</a>
            </div>
            <div class="modal-body">
                <div id="modal-content">
                    <div class="register-box">
                        <div class="register-box-body">
                             <?php echo \common\widgets\Alert::widget() ?>
                            <?php $form = ActiveForm::begin([
                                                                'id' => 'frm-ajax-login',
                                                            ]); ?>
                            <?= $form->field($model, 'username', [
                                'template' => "{label}\n{input}\n<span class=\"glyphicon glyphicon-user form-control-feedback\"></span>\n{hint}\n{error}",
                                'options'  => ['class' => 'form-group has-feedback']
                            ])->textInput(['class' => 'form-control', 'placeholder' => 'Tên đăng nhập'])->label(false) ?>

                            <?= $form->field($model, 'password', [
                                'template' => "{label}\n{input}\n<span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>\n{hint}\n{error}",
                                'options'  => ['class' => 'form-group has-feedback']
                            ])->passwordInput(['class' => 'form-control', 'placeholder' => 'Mật khẩu'])->label(false) ?>
                            <?= $form->field($model, 'confirm_password', [
                                'template' => "{label}\n{input}\n<span class=\"glyphicon glyphicon-log-in form-control-feedback\"></span>\n{hint}\n{error}",
                                'options'  => ['class' => 'form-group has-feedback']
                            ])->passwordInput(['class' => 'form-control', 'placeholder' => 'Xác nhận mật khẩu'])->label(false) ?>
                            <?= $form->field($model, 'fullname', [
                                'template' => "{label}\n{input}\n<span class=\"glyphicon glyphicon-user form-control-feedback\"></span>\n{hint}\n{error}",
                                'options'  => ['class' => 'form-group has-feedback']
                            ])->textInput(['class' => 'form-control', 'placeholder' => 'Họ và tên'])->label(false) ?>

                            <?= $form->field($model, 'email', [
                                'template' => "{label}\n{input}\n<span class=\"glyphicon glyphicon-envelope form-control-feedback\"></span>\n{hint}\n{error}",
                                'options'  => ['class' => 'form-group has-feedback']
                            ])->textInput(['class' => 'form-control', 'placeholder' => 'Email'])->label(false) ?>
                            <?= $form->field($model, 'phone', [
                                'template' => "{label}\n{input}\n<span class=\"glyphicon glyphicon-earphone form-control-feedback\"></span>\n{hint}\n{error}",
                                'options'  => ['class' => 'form-group has-feedback']
                            ])->textInput(['class' => 'form-control', 'placeholder' => 'Số điện thoại'])->label(false) ?>

                            <?php echo  $form->field($model, 'agree',[
                                'template' => "{label}\n{error}",
                                'options'=>['class'=>'form-item-agree']
                            ])->checkbox()->label('Tôi đã đọc và đồng ý với <a target="_blank" href="/chinh-sach-va-quy-dinh-chung-51">Quy định và Chính sách</a> của '. \Yii::$app->params['SITE_NAME']) ?>
                            <div class="form-group field-signupform-verifycode">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="g-recaptcha" data-sitekey="<?php echo Yii::$app->params['CAPTCHA_SITE_KEY'] ?>"></div>
                                        <!--js-->
                                        <script src='https://www.google.com/recaptcha/api.js'></script>
                                    </div>
                                </div>
                            </div>
                            <?php /*echo $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
                                'template' => '<div class="row"><div class="col-lg-6">{input}</div><div class="col-md-4">{image}</div><div class="col-md-2"><a href="javascript:void(0)" class="refresh-captcha"><i class="fa fa-refresh" aria-hidden="true"></i></a></div></div>',
                                'imageOptions' => [
                                    'class' => 'my-captcha',
                                    'placeholder' => 'Mã xác nhận'
                                ]
                            ])->label(false) */?>

                            <div class="row">
                                <div class="col-xs-8">
                               
                                 </div><!-- /.col -->
                                <div class="col-xs-4">
                                    <button disabled type="submit" class="btn btn-primary btn-block btn-flat">Đăng ký</button>
                                </div><!-- /.col -->
                            </div>
                            <?php ActiveForm::end(); ?>
                            <div class="social-auth-links text-center">
                                <p>- OR -</p>
                                <p class="facebook">
                                    <a href="/login">Đăng nhập</a> nếu bạn đã có tài khoản
                                </p>
                            </div>
                        </div><!-- /.form-box -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
