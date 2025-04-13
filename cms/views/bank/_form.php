<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use common\models\TbCustomers;

    /* @var $this yii\web\View */
    /* @var $model common\models\TbAccountBanking */
    /* @var $form yii\widgets\ActiveForm */
    $customerID = Yii::$app->request->get('customerID', 0);
    if ($customerID) {
        $customerInfo = TbCustomers::findOne($customerID);
    }

    if (isset($model->customer) && $model->customer) {
        $customerInfo = $model->customer;
    }
?>
<?php echo \common\widgets\Alert::widget() ?>

<div class="row">
    <div class="col-lg-6">
        <table class="table table-striped table-bordered detail-view">
            <tbody>
            <?php if (isset($customerInfo) && $customerInfo) { ?>
                <tr>
                    <th>Họ tên:</th>
                    <td><b><?= $customerInfo->fullname ?></b></td>
                </tr>
                <tr>
                    <th>Tài khoản</th>
                    <td><b><?= $customerInfo->username ?></b></td>
                </tr>
                <tr>
                    <th>Điện thoại:</th>
                    <td><b><?= $customerInfo->phone ?></b></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><b><?= $customerInfo->email ?></b></td>
                </tr>
                <tr>
                    <th>Địa chỉ:</th>
                    <td><b><?= $customerInfo->address ?></b></td>
                </tr>
            <?php } ?>

            <tr>
                <th>Tổng tiền nạp</th>
                <td class="vnd-unit"><?php echo ($model->totalMoney > 0 ? number_format($model->totalMoney) : 0) ?> VND</td>
            </tr>
            <tr>
                <th>Tồng tiền thanh toán</th>
                <td class="vnd-unit"><?php echo($model->totalPayment > 0 ? number_format($model->totalPayment) : 0) ?> VND</td>
            </tr>
            <tr>
                <th>Tổng tiền hoàn lại</th>
                <td class="vnd-unit"><?php echo($model->totalRefund > 0 ? number_format($model->totalRefund) : 0) ?> VND</td>
            </tr>
            <tr>
                <th>Tổng tiền dư cuối</th>
                <td class="vnd-unit"><?php echo($model->totalResidual > 0 ? number_format($model->totalResidual) : 0) ?> VND</td>
            </tr>
            <tr>
                <th>Tổng tiền rút</th>
                <td class="vnd-unit"><?php echo($model->totalReceived > 0 ? number_format($model->totalReceived) : 0) ?> VND</td>
            </tr>
            <tr>
                <th>Ngày tạo</th>
                <td><?= date('d-m-Y H:i:s', strtotime($model->create_date)) ?></td>
            </tr>
            <tr>
                <th>Ngày cập nhật</th>
                <td><?= date('d-m-Y H:i:s', strtotime($model->edit_date)) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-lg-6">
        <?php $form = ActiveForm::begin([  ]); ?>
        <label class="control-label" for="tbaccountbanking-customerID"><b>Chọn tài khoản cần nạp tiền</b></label>
        <?php
            $customers = \yii\helpers\ArrayHelper::map(\common\models\TbCustomers::find()->select(['id', 'username'])->asArray()->all(), 'id', 'username');
        ?>
        <?= $form->field($model, 'customerID')->textInput(['maxlength' => true])->dropDownList($customers, [
            'class' => 'select2 form-control','disabled'=>true, 'prompt' => '','style'=>'width:100%','data-placeholder'=>'Chọn tài khoản nạp tiền..'])->label(false)
        ?>
        <label class="control-label" for="tbaccountbanking-customerID"><b>Số tiền cần nạp</b></label>
        <?php
            $model->totalMoney = '';
            echo $form->field($model, 'totalMoney', [
                'template' => "{label}\n{input}\n{error}",
            ])->textInput(['class' => 'currency form-control'])->label(false) ?>
        <?= $form->field($model, 'note', ['template' => '{label}<div class="controls">{input}{error}</div>'])->textarea(['maxlength' => true, 'rows' => 3])->label('Ghi chú:', ['class' => "control-label"]) ?>
        <?php echo $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
            'template' => '<div class="row"><div class="col-lg-5">{input}</div><div class="col-xs-5">{image}</div><div class="col-xs-2 text-right"><a href="javascript:void(0)" class="refresh-captcha"><i class="fa fa-refresh" aria-hidden="true"></i></a></div></div>',
            'imageOptions' => [
                'class' => 'my-captcha',
                'width' => 'auto'
            ]
        ]) ?>
        <div class="form-group text-right">
            <?= Html::submitButton('Nạp tiền', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
