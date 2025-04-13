<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

    $this->title                   = 'Duyệt đơn hàng - ' . $model->identify;
    $this->params['breadcrumbs'][] = ['label' => 'Tất cả đơn hàng', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
    $business                      = $model->Business;
    $disable =  ''; //da tra hang disable all form
?>


<?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'options' => ['class' => "form-horizontal"]
]); ?>
<div class="col-lg-7">
    <?php
        echo $form->field($model, 'businessID', [
            'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
        ])->dropDownList(\common\components\CommonLib::listUser(10,[ADMIN,WAREHOUSE,WAREHOUSETQ]), [
            'class' => 'form-control select2', 'disabled' => (!empty($disable) ? true : false), 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Nhân viên kinh doanh'])->label('Nhân viên kinh doanh', ['class' => "col-sm-4 control-label"]);
    ?>
    <?= $form->field($model, 'discountRate', [
        'template' => '{label}<i>(%)</i><div class="col-sm-3">{input}{error}</div>'
    ])->textInput(['class' => 'form-control','placeholder'=>'% Chiết khấu'])->label('Chiết khấu dịch vụ', ['class' => "col-sm-4 control-label"]) ?>

    <?= $form->field($model, 'discountKg', [
        'template' => '{label}<i>(VNĐ)</i><div class="col-sm-3">{input}{error}</div>'
    ])->textInput(['class' => 'form-control currency','placeholder'=>'Chiết khấu cân nặng'])->label('Chiết khấu cân nặng', ['class' => "col-sm-4 control-label"]) ?>

    <?php
    echo $form->field($model, 'orderStaff', [
        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
    ])->dropDownList(\common\components\CommonLib::listUser(4,[ADMIN,WAREHOUSE,WAREHOUSETQ]), [
        'class' => 'form-control select2', 'disabled' => (!empty($disable) ? true : false), 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Nhân viên đặt hàng'])->label('Nhân viên đặt hàng', ['class' => "col-sm-4 control-label"]);
    ?>

    <?= $form->field($model, 'staffDiscount', [
        'template' => '{label}<i>(%)</i><div class="col-sm-3">{input}{error}</div>'
    ])->textInput(['class' => 'form-control','placeholder'=>'Chiết khấu đặt hàng'])->label('Chiết khấu đặt hàng', ['class' => "col-sm-4 control-label"]) ?>


    <div class="form-group field-tborders-weightdiscount">
        <label class="col-sm-4 control-label"></label>
        <div class="col-sm-2">
            <button type="submit" class="btn btn-primary">Xác nhận</button>
        </div>
    </div>
</div>
<div class="col-lg-5">
    <div class="callout callout-danger">
        <h4>Thông báo!</h4>
        <p>
            % Dịch vụ áp dụng cho KD:<b class="text-bold"><?= $model->discountRate ?> %</b><br>
            Phí cân nặng áp dụng cho KD: <b class="text-bold"><?= number_format($model->discountKg) ?> VND/1kg</b>
            <hr style="border: 1px dotted #CCCCCC">
            % Dịch vụ cài đặt cho KD:<b class="text-bold"><?= isset($business->discountRate) ? $business->discountRate : '' ?> %</b><br>
            Phí cân nặng cài đặt cho KD:: <b class="text-bold"><?= isset($business->discountKg) ? number_format(round($business->discountKg)) : '' ?> VND/1kg</b>
        </p>
    </div>
</div>

<?php ActiveForm::end(); ?>
