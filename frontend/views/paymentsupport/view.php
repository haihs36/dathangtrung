<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentSupport */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Chi tiết thanh toán hộ';
$this->params['breadcrumbs'][] = $this->title;
$amount_item = json_decode($model->attributes['dataAmount'],true);

?>
<div class="box payment-support-form" >
    <div class="box-header with-border">
        <h2 class="box-title"><?= $this->title ?></h2>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <div class="box-form">
        <div class="box-body ">
            <div id="fieldList">
                <div class="itemList">
                    <?php if(!empty($amount_item)){

                        foreach ($amount_item as $k=> $item){
                    ?>
                    <div class="rows">
                        <div class="col-md-4">
                            <span class="controld-label">Giá tiền (Tệ)</span>
                            <input disabled type="text" class="form-control textPrice" name="amount[<?= $k ?>][price]" value="<?= $item['price'] ?>" maxlength="300">
                        </div>
                        <div class="col-md-6">
                            <span class="controld-label">Nội dung</span>
                            <input disabled type="text" class="form-control" name="amount[<?= $k ?>][note]" value="<?= $item['note'] ?>" maxlength="300">
                        </div>
                    </div>
                    <?php } } ?>
                </div>
                <div class="col-sm-12" style="padding: 10px;">
                    <hr>
                </div>
            </div>
            <div style="padding-top: 20px">
                <div class="col-lg-12">
                    <div class="form-group">
                        <span class="control-label">Tổng tiền (Tệ)</span>
                        <input disabled type="text" class="form-control"  id="totalCNY" value="<?= $model->attributes['amount_total'] ?>" >
                    </div>
                    <div class="form-group">
                        <span class="control-label">Tổng tiền (VNĐ)</span>
                        <input disabled type="text" class="form-control" id="totalVN" value="<?= number_format($model->attributes['amount_total_vn']) ?>">
                    </div>
                    <div class="form-group">
                        <span class="control-label">Tỉ giá</span>
                        <input disabled type="text" class="form-control"  id="txtCny" value="<?= number_format($model->attributes['cny']) ?>" >
                    </div>
                    <div class="form-group">
                        <span class="control-label">Ghi chú</span>
                        <textarea  disabled name="note" class="form-control" rows="5"><?= $model->attributes['note'] ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-center">
            <div class="col-md-4 text-left"><label>Trạng thái:</label> <?= \common\components\CommonLib::getStatusPaymentTransport($model->status) ?></div>
            <div class="col-md-8 text-right"><a href="/danh-sach-thanh-toan-ho" class="btn btn-primary">Quay lại</a></div>
        </div>
    </div>
</div>

