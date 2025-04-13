<?php use yii\bootstrap\ActiveForm; ?>
<div id="myModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog login-pop-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">
                    <img src="/file/media/avatar/default_user_icon.png" class="img-thumbnail img-circle img-responsive" height="30px" width="30px" /> Login
                </h1>
                <a class="close" data-dismiss="modal" aria-hidden="true">×</a>
            </div>
            <div class="modal-body">
                <div id="modal-content">
                    <div class="login-box">
                        <div class="login-box-body">
<!--                            <p class="login-box-msg"></p>-->
                             <?php echo \common\widgets\Alert::widget() ?>
                            <?php $form = ActiveForm::begin([
                                'id' => 'frm-ajax-login',
                                // 'validateOnChange' => false,
//                                    'enableAjaxValidation' => true,
//                                    'enableClientValidation' => true,

                            ]); ?>
                            <?= $form->field($model, 'username', [
                                'template' => "{label}\n{input}\n<span class=\"glyphicon glyphicon-user form-control-feedback\"></span>\n{hint}\n{error}",
                                'options'  => ['class' => 'form-group has-feedback']
                            ])->textInput(['class' => 'form-control', 'placeholder' => 'Tên đăng nhập'])->label(false) ?>
                            <?= $form->field($model, 'password', [
                                'template' => "{label}\n{input}\n<span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>\n{hint}\n{error}",
                                'options'  => ['class' => 'form-group has-feedback']
                            ])->passwordInput(['class' => 'form-control', 'placeholder' => 'Mật khẩu'])->label(false) ?>

                            <div class="form-group field-signupform-verifycode">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="g-recaptcha" data-sitekey="<?php echo Yii::$app->params['CAPTCHA_SITE_KEY'] ?>"></div>
                                        <!--js-->
                                        <script src='https://www.google.com/recaptcha/api.js'></script>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-xs-7">
                                    <?= $form->field($model, 'rememberMe', [
                                        'template' => "{input}\n{label}\n{hint}\n{error}",
                                        'options'  => ['class' => 'checkbox icheck']
                                    ])->checkbox()->label('Ghi nhớ đăng nhập') ?>

                                </div><!-- /.col -->
                                <div class="col-xs-5">
                                    <button type="submit"  class="btn btn-primary btn-block btn-flat">Đăng nhập</button>
                                </div><!-- /.col -->
                            </div>
                            <div class="row">
                                 <div class="col-xs-12">
                                        <a  href="/register">Đăng ký</a> nếu bạn chưa có tài khoản
                                    </div>
                            </div>
                            <?php ActiveForm::end(); ?>

                        </div><!-- /.login-box-body -->
                    </div><!-- /.login-box -->
                </div>
            </div>
        </div>
    </div>
</div>
