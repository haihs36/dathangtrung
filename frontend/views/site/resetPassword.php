<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
?>
<div class="breadcrumb">
    <div class="container">
        <a href="/">Trang chủ</a><i class="fa fa-angle-right" aria-hidden="true"></i>
        <a href="javascript:void(0)" class="active">Reset password</a>
    </div>
</div>
<div class="container">
    <div class="quang-cao-content-top item-block"></div>
    <div class="content-include-search"></div>
    <?php echo $this->render('@app/views/templates/_support_online'); ?>
    <div class="group-content form-item-agree">
        <?php echo \common\widgets\Alert::widget() ?>
        <div class="site-reset-password">
            <h1><?= Html::encode($this->title) ?></h1>
            <p>Vui lòng chọn mật khẩu mới của bạn:</p>
            <div class="row">
                <div class="col-lg-5">
                    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                    <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>
                    <div class="form-group">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

    </div>
</div>