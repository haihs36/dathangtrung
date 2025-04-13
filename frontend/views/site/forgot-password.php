<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
?>
<?php $form = ActiveForm::begin([
        'id'      => 'frm-ajax-register',
        'fieldConfig' => ['template' => "{label}\n{input}\n{hint}"],
        'options' => [
            'class' => 'ctools-use-modal-processed'
        ],
]); ?>
<div class="popup-login-regiter-form">
    <?php echo $form->errorSummary($model, ['header' => '']);?>
    <div class="left-popup">
        <?php echo $this->render('@app/views/templates/_tab_function',['tab'=>'forgot']); ?>
        <?= $form->field($model, 'email', [
                'template' => "{label}\n{input}\n<span class=\"field-suffix\"><span class=\"fa fa-envelope\"></span></span>\n{hint}",
                'options'  => ['class' => 'form-item form-type-textfield form-item-name']
        ])->textInput(['class' => 'form-text', 'placeholder' => 'Thư điện tử (*)'])->label(false) ?>

        <div class="form-actions">
            <?= Html::input('submit','login-button','Lấy mã xác nhận', ['class' => 'btn btn-primary form-submit','id'=>'btnSubmit']) ?>
        </div>
    </div>
    <?php echo $this->render('@app/views/templates/_social_facebook'); ?>
</div>
<?php ActiveForm::end(); ?>