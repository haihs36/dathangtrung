<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model cms\models\Consignment */
/* @var $form yii\widgets\ActiveForm */
$disable = $model->isNewRecord ? '' : 'disabled';
?>

<div class="consignment-form">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options' => ['class' => "form-horizontal"]
    ]); ?>
    <div class="box clear">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $this->title ?></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="col-sm-8">
                <?php $customers = \yii\helpers\ArrayHelper::map(\common\models\TbCustomers::find()->select(['id', 'username'])->asArray()->all(), 'id', 'username');
                echo $form->field($model, 'customerID', [
                    'template' => '{label}<div class="col-sm-8">{input}{error}</div>'
                ])->dropDownList($customers, [
                    'disabled' => ($disable ? true : false),
                    'id' => 'lo-customerid',
                    'class' => 'select2 form-control', 'prompt' => '', 'style' => 'width:100%', 'data-placeholder' => 'Chọn tài khách hàng'
                ])->label('Khách hàng', ['class' => "col-sm-4 control-label"]);
                ?>

                <?php
                echo $form->field($model, 'payStatus', [
                    'template' => '{label}<div class="col-sm-8">{input}{error}</div>'
                ])->dropDownList(\common\components\CommonLib::paymentStatus(), [
                    'disabled' => ($disable ? true : false),
                    'class' => 'select2 form-control', 'prompt' => '', 'style' => 'width:100%', 'data-placeholder' => 'Hình thức giao hàng'
                ])->label('Hình thức giao hàng', ['class' => "col-sm-4 control-label"]);
                ?>

                <?= $form->field($model, 'address', [
                    'template' => '{label}<div class="col-sm-8">{input}{error}</div>'])->textarea([
                    'id' => 'editor1', 'class' => 'editor form-control', 'rows' => "2",
                    'disabled' => ($disable ? true : false),
                ])->label('Địa chỉ giao hàng:', ['class' => "col-sm-4 control-label"]) ?>

                <?= $form->field($model, 'note', [
                    'template' => '{label}<div class="col-sm-8">{input}{error}</div>'])->textarea([
                    'id' => 'editor2', 'class' => 'editor form-control', 'rows' => "2",
                    'disabled' => ($disable ? true : false),
                ])->label('Ghi chú:', ['class' => "col-sm-4 control-label"]) ?>
                <?php if($model->isNewRecord){ ?>
                  <label class="control-label pull-right">
                        <?= Html::submitButton('Lưu phiếu xuất', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        <a href="<?= \yii\helpers\Url::toRoute(['consignment/index']) ?>" class="btn btn-danger">Hủy</a>
                    </label>
                <?php } ?>

            </div>
            <div class="col-sm-4">
                <?php  if ($model->customerID) { ?>
                    <script>
                        Main.load_customer('<?= (int)$model->customerID ?>');
                    </script>
                <?php } ?>
                <div class="cusomerInfo">

                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <?php if(!empty($disable)){ ?>
    <div class="box">
        <div class="box-body">
            <div class="col-sm-8">
                <div class="form-group">
                    <label class="col-sm-4 control-label text-right" for="orderNumber">Nhập mã vận đơn</label>
                    <div class="col-sm-4">
                        <input type="text" id="orderNumber" class="form-control" name="Consignment[name]"
                               placeholder="Nhập mã vận đơn...." style="width:100%" aria-invalid="false">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-primary btn-submit">Xác nhận</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="box">
            <div class="box-body">
               <div id="list-barcode">

               </div>
            </div>
        </div>
    <?php } ?>
</div>
<input type="hidden" id="pxk" value="<?php echo $model->id ?>">
<input type="hidden" id="customer_id" value="<?php echo $model->customerID ?>">
<script>
    $(function (e) {
        var txtInput = $("#orderNumber");
        txtInput.focus();

        $('.btn-submit').on('click',function () {
            Main.insertBarcode();
        });

        //load all barcode
        var pid = $('#pxk').val();
        var cusID = $('#customer_id').val();
        if(pid){
            $.ajax({
                url: '/get-all-barcode',
                type: 'post',
                data: {pid:pid,cusID:cusID},
                dataType: 'json',
                beforeSend: function () {
                   // $('#myModal').modal('show').find('.modal-title').html('Hệ thống đang xử lý');
                   // $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/></div>');
                },
                success: function (rs) {
                   // $('#myModal').modal('hide');
                    $('#list-barcode').html(rs.html);
                }
            });
        }
    });
    $(document).keypress(function(e) {
        if(e.which == 13) {
            Main.insertBarcode();
        }
    });


</script>