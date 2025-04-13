<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentSupport */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="box payment-support-form" >
    <div class="box-header with-border">
        <h2 class="box-title">Gửi yêu cầu thanh toán hộ </h2>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <form id="frmPayment"  method="post" action="/tao-thanh-toan-ho" enctype="multipart/form-data" accept-charset="UTF-8">
        <div class="box-body box-form">
                <div id="fieldList">
                    <div class="itemList">
                        <div class="rows">
                            <div class="col-md-4">
                                <span class="controld-label">Giá tiền (Tệ)</span>
                                <input required type="text" class="form-control textPrice" name="amount[0][price]" value="0" maxlength="300">
                            </div>
                            <div class="col-md-6">
                                <span class="controld-label">Nội dung</span>
                                <input type="text" class="form-control" name="amount[0][note]" value="" maxlength="300">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="padding-top: 10px;">
                        <hr>
                        <div class="text-right">
                            <input type="button" class="btn btn-primary" id="addMore" value="+ Thêm hóa đơn thanh toán hộ">
                        </div>
                    </div>
                </div>
                <div style="padding-top: 20px">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <span class="control-label">Tổng tiền (Tệ)</span>
                            <input disabled type="text" class="form-control" id="totalCNY">
                            <input  type="hidden"  name="totalCNY" value="">
                        </div>
                        <div class="form-group">
                            <span class="control-label">Tổng tiền (VNĐ)</span>
                            <input disabled type="text" class="form-control" id="totalVN" >
                            <input  type="hidden" name="totalVN" value="" >
                        </div>
                        <div class="form-group">
                            <span class="control-label">Tỉ giá</span>
                            <input disabled type="text" class="form-control" id="txtCny" >
                            <input  type="hidden" name="txtCny" value="" >
                        </div>
                        <div class="form-group">
                            <span class="control-label">Ghi chú</span>
                            <textarea name="note" class="form-control" rows="5"></textarea>
                        </div>
                    </div>
                </div>
        </div>
        <div class="box-footer text-center">
            <button class="btn btn-primary btnRequest">Gửi đơn</button>
            <a href="/danh-sach-thanh-toan-ho" class="btn btn-default">Hủy</a>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        main.update_payment_transport();

    });
</script>
