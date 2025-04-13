<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrdersDetail */
/* @var $form yii\widgets\ActiveForm */
    $this->title = 'Chỉnh sửa sản phẩm';
    $this->params['breadcrumbs'][] = ['label' => 'Tất cả đơn hàng', 'url' => ['orders/index']];
    $this->params['breadcrumbs'][] = 'Xem đơn hàng';

?>
<!--<script src="/admin/js/query.min.js"></script>-->
<!--<script src="/admin/js/tinymce/tinymce.min.js"></script>-->
<div class="box box-info">
    <?= \common\widgets\Alert::widget() ?>
    <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => false,
            'options'              => [
                'class' => "form-horizontal",
                'enctype' => 'multipart/form-data'
            ]
    ]); ?>
    <?= $form->field($model->product, 'productID')->hiddenInput()->label(false) ?>
    <div class="box-header with-border">
        <h3 class="box-title">Thông tin sản phẩm</h3>
        <div class="pull-right">
            <?= Html::a('Thoát', ['orders/view','id'=>$model->orderID, '#' => 'w1'], ['class' => 'btn btn-default']) ?>
            <?= Html::submitButton( 'Lưu', ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-primary']) ?>
        </div>
    </div>
    <div class="box-body">
        <?= $form->field($model->product, 'name',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Tên sản phẩm',['class'=>"col-sm-2 control-label"]) ?>

        <?php if (!empty($model->image)){ ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="tbmenus-title">Ảnh đại diện</label>
                <div class="col-sm-10">
                    <img src="<?= $model->image ?>" width="100px" height="100px">
                    <a href="<?= Url::to(['clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="Clear image">Xóa ảnh</a>
                </div>
            </div>
        <?php }else{ ?>
            <?= $form->field($model, 'image',[
                'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->label('Nhập đường dẫn ảnh đại diện',['class'=>"col-sm-2 control-label"]) ?>
        <?php } ?>
        <?= $form->field($model, 'size',[
            'template' => '{label}<div class="col-sm-2">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Kích thước',['class'=>"col-sm-2 control-label"]) ?>
        <?= $form->field($model, 'color',[
            'template' => '{label}<div class="col-sm-3">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Màu sắc',['class'=>"col-sm-2 control-label"]) ?>
        <?= $form->field($model, 'quantity',[
            'template' => '{label}<div class="col-sm-2">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Số lượng',['class'=>"col-sm-2 control-label"]) ?>
        <?= $form->field($model, 'unitPrice',[
            'template' => '{label}<div class="col-sm-2">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Tiền TQ',['class'=>"col-sm-2 control-label"]) ?>
        <?= $form->field($model->order, 'cny',[
            'template' => '{label}<div class="col-sm-2">{input}{error}</div>'
        ])->textInput(['disabled' => true,'class'=>'currency vnd-unit form-control'])->label('Tỷ giá',['class'=>"col-sm-2 control-label"]) ?>
        <?= $form->field($model, 'totalPriceVn',[
            'template' => '{label}<div class="col-sm-3">{input}{error}</div>'
        ])->textInput(['disabled' => true,'class'=>'currency vnd-unit form-control'])->label('Tổng tiền',['class'=>"col-sm-2 control-label"]) ?>
        <?= $form->field($model->product, 'link',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['disabled' => true])->label('Link sản phẩm:',['class'=>"col-sm-2 control-label"]) ?>

        <?php
        echo $form->field($model, 'status',[
            'template' => '{label}<div class="col-sm-3">{input}{error}</div>'
        ])->dropDownList(\common\components\CommonLib::statusProduct())->label('Tình trạng hàng',['class'=>"col-sm-2 control-label"]);
        ?>
        <?= $form->field($model, 'noteProduct',[
            'template' => '{label}<div class="col-sm-5">{input}{error}</div>'
        ])->textarea(['maxlength' => true])->label('Ghi chú sản phẩm:',['class'=>"col-sm-2 control-label"]) ?>
    </div>
    <div class="box-footer">
        <label class="control-label pull-right">
            <?= Html::a('Thoát', ['orders/view','id'=>$model->orderID, '#' => 'w1'], ['class' => 'btn btn-default']) ?>
            <?= Html::submitButton('Lưu', ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-primary']) ?>
        </label>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<!--<script>
    tinyMCE.init({
        mode: "specific_textareas",
        height:'200px',
        editor_selector: /(mceEditor)/,
        fontsize_formats: '8px 10px 12px 13px 14px 15px 16px 18px 20px 25px 28px 35px 40px',
        plugins: ['advlist  lists link image charmap print preview hr anchor pagebreak', 'searchreplace visualblocks visualchars code', 'insertdatetime media nonbreaking save table contextmenu directionality', 'template paste textcolor wordcount'],
        toolbar: 'code | fullscreen fontsizeselect fontselect styleselect | undo | redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor',
        toolbar_items_size: 'small',
        theme_advanced_buttons3_add: "preview",
        plugin_preview_width: "1000",
        plugin_preview_height: "500",
        convert_urls: false,
        verify_html: false,
        //document_base_url: '',
        relative_urls: false,
        image_advtab: false,
        remove_script_host: false
    });
</script>-->
