<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model cms\models\LoSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="tb-orders-search clear">
    <div class="tb-orders-search">
        <?php
        $form = ActiveForm::begin([
            'method' => 'get',
        ]); ?>

        <div class="form-text ext-full">
            <?= $form->field($model, 'startDate', [
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'id' => 'startDate','autocomplete'=>'off'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($model, 'endDate', [
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'id' => 'endDate','autocomplete'=>'off'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?php
            echo $form->field($model, 'status')->dropDownList([0=>'Chờ xuất',1=>'Hoàn thành'], [
                'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Trạng thái'])->label(false);
            ?>
        </div>
        <div class="form-text ext-full">
            <?php
            $customers = \yii\helpers\ArrayHelper::map(\common\models\TbCustomers::find()->select(['id', 'username'])->where(['status' => 1])->all(), 'id', 'username');
            echo $form->field($model, 'customerID')->dropDownList($customers, [
                'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn khách hàng'])->label(false);
            ?>
        </div>
        <div class="form-text ext-full">
            <button type="submit" id="btn-search-order" name="op" value="Tìm kiếm" class="btn btn-primary">
                <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
            </button>
        </div>
        <div class="pull-right">
            <?= Html::a('Tạo phiếu xuất', ['consignment/create'], ['class' => 'btn btn-success btn-sm','target'=>'_blank']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
