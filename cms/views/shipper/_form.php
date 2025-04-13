<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Tbshippers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="">
    <div class="box-header with-border">
        <h3 class="box-title">Chỉnh sửa đơn</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
            <?php $form = ActiveForm::begin([
                'enableAjaxValidation' => false,
                'options' => ['class' => "form-horizontal", 'enctype' => 'multipart/form-data']
            ]); ?>
            <?= \common\widgets\Alert::widget() ?>


        <div class="form-group field-tbshippers-productname required">
            <label class="col-sm-2 control-label" >Khách hàng:</label>
            <div class="col-sm-4">
                <b><?= ($model->userID && $model->customer) ? '<a target="_blank" href="' . Url::to(['customer/view', 'id' => $model->customer->id]) . '">' . $model->customer->fullname . '</a>' : null ?></b>
            </div>
        </div>

            <?= $form->field($model, 'productName', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->label('Tên sản phẩm:', ['class' => "col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'quantity', [
            'template' => '{label}<div class="col-sm-2">{input} <span></span>{error}</div>'
        ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Số lượng:', ['class' => "col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'price', [
            'template' => '{label}<div class="col-sm-2 input-group">{input}<span class="input-group-addon">CNY</span>{error}</div>'
        ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Đơn giá:', ['class' => "col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'shippingCode', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['disabled' => true])->label('Mã vận chuyển:', ['class' => "col-sm-2 control-label"]) ?>


    
            <?= $form->field($model, 'weight', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->label('Cân nặng:', ['class' => "col-sm-2 control-label"]) ?>



            <?php if (!empty($model->image)) : ?>
                <div class="form-group field-tbshippers-productname required">
                    <label class="col-sm-2 control-label" >Hình ảnh:</label>
                    <div class="col-sm-4">
                        <?php
                        $img = (!empty($model->image) ? Yii::$app->params['FileDomain'] . $model->image : '../images/image-no-image.png');
                        ?>
                        <a rel="photos" title="" class="plugin-box " href="<?php echo $img ?>">
                            <?php
                            echo Html::img($img, ['style' => 'max-width: 200px;max-height: 200px', 'class' => 'thumb']);
                            ?>
                            <a href="<?= \yii\helpers\Url::to(['clear-image', 'id' => $model->id]) ?>"
                               class="text-danger confirm-delete" title="Clear image">Xóa ảnh</a>
                        </a>
                    </div>
                </div>
            <?php else: ?>

                <?= $form->field($model, 'image', [
                    'template' => '{label}<div class="col-sm-4">{input}<span class="red-color">(Max size: 2MB</span>){error}</div>'
                ])->fileInput(['maxlength' => true])->label('Ảnh đại diện:', ['class' => "col-sm-2 control-label"]) ?>


            <?php endif; ?>

            <?= $form->field($model, 'noteIncurred', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textarea(['maxlength' => true, 'rows' => 5, 'class' => 'mceEditor', 'style' => 'width:100%'])->label('Ghi chú phát sinh:', ['class' => "col-sm-2 control-label"]) ?>
            <?= $form->field($model, 'note', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textarea(['maxlength' => true, 'rows' => 5, 'class' => 'mceEditor', 'style' => 'width:100%'])->label('Ghi chú sản phẩm:', ['class' => "col-sm-2 control-label"]) ?>

        <?php //$model->shippingStatus = ($model->shippingStatus == 0 ? 1 : $model->shippingStatus); ?>
        <?= $form->field($model, 'shippingStatus', [
            'template' => '{label}<div class="col-sm-2">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->dropDownList(\common\components\CommonLib::statusShippingText(), [
            'class' => 'select2 form-control', 'prompt' => '', 'data-placeholder' => 'Tình trạng vận chuyển'])->label('Tình trạng vận chuyển:', ['class' => "col-sm-2 control-label"])
        ?>

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-4">
                    <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Chỉnh sửa', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

                    <a href="/shipper" class="btn btn-default">Hủy</a>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
    </div>
</div>
