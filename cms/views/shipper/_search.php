<?php

use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin([
    'id'      => 'search-form',
    'method'  => 'get',
    'action' => '/shipper/index',
    'options' => [
        'class' => 'form-common',
        'data-pjax' => 1
    ],
]); ?>
<div class="col-sm-2 ext-full" style="padding-left: 0px">
    <?php
    $customers = \common\models\TbCustomers::getInfoByCondition(['status' => 1]);
    echo $form->field($model, 'userID')->dropDownList($customers, [
        'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => 'Tất cả', 'data-placeholder' => 'Tìm khách hàng'])->label(false);
    ?>
</div>
<div class="col-sm-2 ext-full" style="padding-left: 0px">
    <?= $form->field($model, 'shippingCode', [
        'template' => '{label}{input}'
    ])->textInput(['placeholder' => 'Mã vận chuyển..', 'class' => 'form-control', 'id' => 'orderNumbers'])->label(false) ?>
</div>
<div class="col-sm-2 ext-full">
    <?= $form->field($model, 'startDate', [
        'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
    ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'id' => 'startDate'])->label(false) ?>
</div>
<div class=" col-sm-2 ext-full">
    <?= $form->field($model, 'endDate', [
        'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
    ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'id' => 'endDate'])->label(false) ?>
</div>
<div class=" col-sm-2 ext-full">
    <?php
    echo $form->field($model, 'shippingStatus')->dropDownList(\common\components\CommonLib::statusShippingText(), [
        'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => 'Tất cả', 'data-placeholder' => 'Tình trạng ship'])->label(false);
    ?>
</div>
<div class=" col-sm-1 ext-full">
    <button type="submit" id="btn-search-tran" name="op" value="Tìm kiếm" class="btn btn-primary btn-flat">
        <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
    </button>
</div>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs("$('ready pjax:success').ready(function(){
        $('.select2').select2({
            placeholder: function () {
                $(this).data('placeholder');
            },
            allowClear: true
        });
        $('#startDate').datepicker({
        format: 'dd/mm/yyyy'
        });
        $('#endDate').datepicker({
            format: 'dd/mm/yyyy'
        });
    });
");
?>


