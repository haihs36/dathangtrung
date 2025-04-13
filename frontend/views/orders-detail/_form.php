<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrdersDetail */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Chỉnh sửa sản phẩm: ' . ' ' . $model->product->name;
$this->params['breadcrumbs'][] = ['label' => 'Tất cả đơn hàng', 'url' => ['orders/index']];
$this->params['breadcrumbs'][] = ['label' => 'Xem đơn hàng', 'url' => ['orders/view', 'id' => $model->orderID]];
$this->params['breadcrumbs'][] = $this->title;

?>
<!--<script src="/admin/js/query.min.js"></script>-->
<!--<script src="/admin/js/tinymce/tinymce.min.js"></script>-->
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-edit" aria-hidden="true"></i> Chỉnh sửa sản phẩm</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="tab-content box-body">
                    <?php $form = ActiveForm::begin([
                        'enableAjaxValidation' => false,
                        'options' => ['class' => "form-horizontal", 'enctype' => 'multipart/form-data']
                    ]); ?>
                    <?= $form->field($model->product, 'productID')->hiddenInput()->label(false) ?>

                    <div class="form-group ">
                        <label class="col-sm-2 control-label">Ngày lên đơn: </label>
                        <div class="col-sm-4">
                            <?= date('d-m-Y H:i:s', strtotime($model->order->orderDate)) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tên shop: </label>
                        <div class="col-sm-4">
                            <?= $model->product->shop->shopName ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tên sản phẩm: </label>
                        <div class="col-sm-4">
                            <?= $model->product->name ?>
                        </div>
                    </div>

                    <?php if (!empty($model->image)) { ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Hình ảnh</label>
                            <div class="col-sm-4">
                                <img src="<?= $model->image ?>" width="200px" />
                                <a href="<?= \yii\helpers\Url::to(['clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="Xóa ảnh">Xóa ảnh</a>
                            </div>
                        </div>

                    <?php } else { ?>
                        <?= $form->field($model, 'image', [
                            'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                        ])->textInput(['maxlength' => true])->label('Nhập đường dẫn ảnh đại diện:', ['class' => "col-sm-2 control-label"]) ?>
                    <?php } ?>

                    <?= $form->field($model, 'size', [
                        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                    ])->textInput(['maxlength' => true])->label('Kích thước:', ['class' => "col-sm-2 control-label"]) ?>
                    <?= $form->field($model, 'color', [
                        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                    ])->textInput(['maxlength' => true])->label('Màu sắc:', ['class' => "col-sm-2 control-label"]) ?>
                    <?= $form->field($model, 'quantity', [
                        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                    ])->textInput(['maxlength' => true])->label('Số lượng:', ['class' => "col-sm-2 control-label"]) ?>
                    <?= $form->field($model, 'unitPrice', [
                        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                    ])->textInput(['maxlength' => true, 'readonly' =>  true])->label('Tiền TQ:', ['class' => "col-sm-2 control-label"]) ?>

                    <?= $form->field($model->order, 'cny', [
                        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                    ])->textInput(['maxlength' => true, 'readonly' =>  true])->label('Tỉ giá áp dụng:', ['class' => "col-sm-2 control-label"]) ?>

                    <?= $form->field($model, 'totalPriceVn', [
                        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                    ])->textInput(['maxlength' => true, 'readonly' =>  true])->label('Tổng tiền:', ['class' => "col-sm-2 control-label"]) ?>
                    <?= $form->field($model->product, 'link', [
                        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                    ])->textInput(['maxlength' => true])->label('Link sản phẩm:', ['class' => "col-sm-2 control-label"]) ?>

                    <?= $form->field($model, 'noteProduct', [
                        'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                    ])->textarea(['maxlength' => true,'row'=>5])->label('Ghi chú sản phẩm:', ['class' => "col-sm-2 control-label"]) ?>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" ></label>
                        <div class="col-sm-4 text-right">
                            <a class="btn btn-default mr10" href="<?= \yii\helpers\Url::toRoute(['orders/view', 'id' => $model->orderID]) ?>">Hủy</a>
                            <?= Html::submitButton($model->isNewRecord ? 'Thêm' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
