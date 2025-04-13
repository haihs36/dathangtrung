<?php
    use yii\widgets\ActiveForm;

?>
<div class="tb-complain-search">
    <?php $form = ActiveForm::begin([
            'id'      => 'complain-search-form',
            'action'  => ['index'],
            'method'  => 'get',
    ]); ?>

     <div class="rows">
            <div class="form-text ext-full">
            <?php echo  $form->field($model, 'identify')->textInput(['placeholder' => 'Mã đơn hàng', 'class' => 'form-control'])->label(false) ?>
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
        <!--<div class="form-text ext-full">
            <?php
/*                $model->status = $model->status > 0 ? $model->status : 0;
                echo $form->field($model, 'status')->dropDownList(\common\models\TbComplain::getStatus(), [
                    'class' => 'form-control select2','style'=>'width:100%','prompt'=>'','data-placeholder'=>'Trạng thái đơn'])->label(false);
            */?>
        </div>-->
           <button type="submit" name="op" value="Tìm kiếm" class="btn btn-primary btn-flat">
               <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
           </button>
     </div>
<!-- finish -->

    <?php ActiveForm::end(); ?>
</div>


