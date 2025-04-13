<?php

use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin([
    'id'      => 'search-form',
    'method'  => 'get',
    'options' => [
        'class' => 'form-common',
        'data-pjax' => 1
    ],
]); ?>
    <div class="col-sm-3 ext-full">
        <?= $form->field($model, 'orderNumber', [
            'template' => '{label}{input}'
        ])->textInput(['placeholder' => 'Mã đơn hàng..', 'class' => 'form-control', 'id' => 'orderNumbers'])->label(false) ?>
    </div>
    <div class="col-sm-3 ext-full">
        <?= $form->field($model, 'startDate', [
            'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
        ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'id' => 'startDate'])->label(false) ?>
    </div>
    <div class=" col-sm-3 ext-full">
        <?= $form->field($model, 'endDate', [
            'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
        ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'id' => 'endDate'])->label(false) ?>
    </div>
    <div class=" col-sm-3 ext-full">
        <?php
        echo $form->field($model, 'type')->dropDownList(\common\components\CommonLib::rechargeType(), [
            'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Trạng thái'])->label(false);
        ?>
    </div>
    <div class="clear text-right mr15">
        <button type="submit" id="btn-search-tran" name="op" value="Tìm kiếm" class="btn btn-primary btn-flat">
            <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
        </button>
    </div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(function () {
        $('#btn-search-tran').on('click', function (e) {
//            e.preventDefault();
//            var type = $('#tbaccounttransactionsearch-status').val();
//            $('#search-form').attr('action', "/lich-su-giao-dich" + (type ? '-' + type : '')).submit();
        });
    });
</script>
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