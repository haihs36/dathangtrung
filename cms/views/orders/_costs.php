<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

    $this->title                   = 'Cài đặt phí đơn hàng - ' . $model->identify;
    $this->params['breadcrumbs'][] = ['label' => 'Tất cả đơn hàng', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;

    $customer = $model->customer;
    $phidv    = 0;
    $phikg    = 0;
    if (isset($customer->discountRate)) {
        $phidv = $customer->discountRate;
        $phikg = $customer->discountKg;
    }
    $disable = ($model->status == 6) ? 'disabled' : ''; //da tra hang disable all form

?>
<!--ti gia-->
<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options'              => ['class' => "form-horizontal", 'enctype' => 'multipart/form-data']
]); ?>
<div class="col-lg-7">
    <?= $form->field($model, 'provinID', [
        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
    ])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Province::find()->select('id,name')->asArray()->all(),'id','name'), [
        'class' => 'form-control select2', 'disabled' => (!empty($disable) ? true : false), 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn kho đích'])->label('Chọn kho đích', ['class' => "col-sm-4 control-label"]);
    ?>
    <?= $form->field($model, 'cny', [
        'template' => '{label}<i class="vnd-unit">(VNĐ)</i><div class="col-sm-2">{input}{error}</div>'
    ])->textInput(['class' => 'currency form-control','placeholder'=>'Tỉ giá', 'disabled' => (!empty($disable) ? true : false)])->label('Tỉ giá đơn hàng:', ['class' => "col-sm-4 control-label"]) ?>

    <?= $form->field($model, 'discountDeals', [
        'template' => '{label}<i>(%)</i><div class="col-sm-3">{input}{error}</div>'
    ])->textInput(['maxlength' => true,'placeholder'=>'% Dịch vụ', 'disabled' => (!empty($disable) ? true : false)])->label('Ưu đãi giảm giá % dịch vụ:', ['class' => "col-sm-4 control-label"]) ?>

    <?= $form->field($model, 'weightDiscount', [
        'template' => '{label}<i class="vnd-unit">(VNĐ)</i><div class="col-sm-4">{input}{error}</div>'
    ])->textInput(['class' => 'currency form-control','placeholder'=>'Ưu đãi cân nặng', 'disabled' => (!empty($disable) ? true : false)])->label('Ưu đãi giảm giá cân nặng:', ['class' => "col-sm-4 control-label"]) ?>


     <?= $form->field($model, 'deposit', [
        'template' => '{label}<i class="vnd-unit">(%)</i><div class="col-sm-4">{input}{error}</div>'
    ])->textInput(['class' => 'currency form-control','placeholder'=>'% Cọc đơn hàng', 'disabled' => (!empty($disable) ? true : false)])->label('% Cọc đơn hàng:', ['class' => "col-sm-4 control-label"]) ?>



    <div class="form-group field-tborders-weightdiscount">
        <label class="col-sm-4 control-label"></label>
        <div class="col-sm-2">
            <button <?= $disable ?> type="submit" class="btn btn-primary">Xác nhận</button>
        </div>
    </div>
</div>
<div class="col-lg-5">
    <div class="callout callout-danger">
        <h4>Thông báo!</h4>
        <div>
            % Dịch vụ đang áp dụng: <b class="text-bold"><?= $model->discountDeals ?> %</b><br>
            Ưu đãi cân nặng đang áp dụng:  <b class="text-bold"><?= number_format(round($model->weightCharge)) ?> VND/1kg</b>
            <hr style="border: 1px dotted #CCCCCC">
            % Dịch vụ cài đặt cho khách hàng: <b class="text-bold"><?= $phidv ?> %</b><br>
            Ưu đãi cân nặng cài đặt cho khách hàng:
            <b class="text-bold"><?= number_format(round($phikg)) ?> VND/1kg</b>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

