<?php
    use yii\widgets\ActiveForm;

?>
<div class="tb-complain-search">
    <?php $form = ActiveForm::begin([
            'id'      => 'complain-search-form',
            'action'  => ['index'],
            'method'  => 'post',
    ]); ?>

     <div class="rows">
            <div class="form-text ext-full">
            <?php echo  $form->field($model, 'orderID')->textInput(['placeholder' => 'Mã đơn hàng', 'class' => 'form-control'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($model, 'startDate',[
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'id' => 'startDate'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($model, 'endDate',[
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'id' => 'endDate'])->label(false) ?>
        </div>
         <div class="form-text ext-full ">
             <?php
             echo $form->field($model, 'businessID')->dropDownList(\common\components\CommonLib::listUser(0,[ADMIN,WAREHOUSE,WAREHOUSETQ]), [
                 'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn nhân viên'])->label(false);
             ?>
         </div>
         <div class="form-text ext-full ">
             <?php
             $customers = \yii\helpers\ArrayHelper::map(\common\models\TbCustomers::find()->select(['id', 'username'])->where(['status' => 1])->all(), 'id', 'username');
             echo $form->field($model, 'customerID')->dropDownList($customers, [
                 'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn khách hàng'])->label(false);
             ?>
         </div>
        <div class="form-text ext-full">
            <?php
                $model->status = $model->status > 0 ? $model->status : 0;
                echo $form->field($model, 'status')->dropDownList(\common\models\TbComplain::getStatus(), [
                    'class' => 'form-control select2','style'=>'width:100%','prompt'=>'','data-placeholder'=>'Trạng thái đơn'])->label(false);
            ?>
        </div>
           <button type="submit" id="btn-search-order" name="op" value="Tìm kiếm" class="btn btn-primary btn-flat">
               <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
           </button>
     </div>
<!-- finish -->

    <?php ActiveForm::end(); ?>
</div>
<script src="/scripts/moment-with-locales.js"></script>
<script src="/scripts/bootstrap-datetimepicker.js"></script>
<script type="text/javascript">
    $(function () {
        $('#btn-search-order').on('click', function (e) {
            e.preventDefault();
            var status = $('#tbcomplainsearch-status').val();
            $('#complain-search-form').attr('action', "/danh-sach-khieu-nai"+(status ? '-'+status : '')).submit();
        });

    });
</script>
