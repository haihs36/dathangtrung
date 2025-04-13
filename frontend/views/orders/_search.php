<?php

use yii\widgets\ActiveForm;

?>
<div class="box-search">
    <?php $form = ActiveForm::begin([
        'id'     => 'order-search-form',
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="rows">
        <div class="form-text ext-full">
            <button class="btn btn-danger btnDepositAll">Cọc tất cả đơn hàng</button>
        </div>
        <div class="form-text ext-full">
            <?php echo $form->field($model, 'identify')->textInput(['placeholder' => 'Mã đơn hàng', 'class' => 'form-control'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($model, 'startDate', [
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'id' => 'startDate'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($model, 'endDate', [
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'id' => 'endDate'])->label(false) ?>
        </div>
        <div class="form-text ext-full ">
            <?php
            echo $form->field($model, 'status')->dropDownList(\common\components\CommonLib::statusText(), [
                'class' => 'form-control select2',  'prompt' => 'Tất cả', 'data-placeholder' => 'Tình trạng đơn hàng'])->label(false);
            ?>
        </div>

        <button type="submit" id="btn-search-order" name="op" value="Tìm kiếm" class="btn btn-primary btn-flat">
            <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
        </button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs("$('ready pjax:success').ready(function(){
        $('#startDate').datepicker({
        format: 'dd/mm/yyyy'
        });
        $('#endDate').datepicker({
            format: 'dd/mm/yyyy'
        });
    });
");
?>

