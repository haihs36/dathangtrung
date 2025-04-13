<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TbAccountBankingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php echo \common\widgets\Alert::widget() ?>
<div class="tb-account-banking-search">
    <?php $form = ActiveForm::begin([
//        'enableAjaxValidation' => true,
    ]); ?>
    <table class="table table-hover table-bordered" >
        <tr>
            <td>
                <div class="tb-account-banking-form">
                    <label class="control-label" for="tbaccountbanking-customerID"><b>Chọn tài khoản cần nạp tiền</b></label>
                    <?php
                        $customers = \yii\helpers\ArrayHelper::map(\common\models\TbCustomers::find()->select(['id', 'username'])->asArray()->all(), 'id', 'username');
                    ?>
                    <?= $form->field($model, 'customerID')->textInput(['maxlength' => true])->dropDownList($customers, [
                        'class' => 'select2 form-control', 'prompt' => '','style'=>'width:100%','data-placeholder'=>'Chọn tài khoản nạp tiền..'])->label(false)
                    ?>
                    <label class="control-label" for="tbaccountbanking-customerID"><b>Số tiền cần nạp</b></label>
                    <?php
                        echo $form->field($model, 'totalMoney', [
                            'template' => "{label}<i class='vnd-unit'>VNĐ</i>\n<div class='col-sm-4 pl0 pr0'>{input}</div>\n<div class='clear'>{error}</div>"])->textInput(['class' => 'currency form-control','placeholder'=>'Số tiền nạp'])->label(false) ?>

                    <div class="isCheck">
                        <label class="checkbox checkbox-primary">
                            <input class="pay-check-item" value="2" name="type" id="paymentReturn" type="checkbox">
                            <label for="paymentReturn">Chọn giao dịch rút tiền</label>
                        </label>
                    </div>

                    <?= $form->field($model, 'note', ['template' => '{label}<div class="controls">{input}{error}</div>'])->textarea(['maxlength' => true, 'rows' => 3])->label('Ghi chú:', ['class' => "control-label"]) ?>
                    <?php echo $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
                        'template' => '<div class="row"><div class="col-lg-5">{input}</div><div class="col-xs-5">{image}</div><div class="col-xs-2 text-right"><a href="javascript:void(0)" class="refresh-captcha"><i class="fa fa-refresh" aria-hidden="true"></i></a></div></div>',
                        'imageOptions' => [
                            'class' => 'my-captcha',
                            'width' => 'auto'
                        ]
                    ]) ?>
                    <div class="form-group ">
                        <?= Html::submitButton('Xác nhận', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <?php ActiveForm::end(); ?>
   
</div>
