<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    /* @var $this yii\web\View */
    /* @var $model common\models\TbComplain */
    /* @var $form yii\widgets\ActiveForm */
?>

<div class="tb-complain-form">
    <div class="alert alert-danger">
        <i class="fa fa-warning"></i> Quý khách lưu ý điền
        <b>đầy đủ</b> thông tin để nhận được sự hỗ trợ sớm nhất. Trân trọng !
    </div>
    <?php if (isset($_SERVER['HTTP_REFERER'])) { ?>
    <div class="text-right mb15">
        <a class="btn btn-success" href="<?= $_SERVER['HTTP_REFERER'] ?>"><i class="fa fa-mail-reply" aria-hidden="true"></i> Quay lại</a>
    </div>
<?php } ?>
    <div>
        <?php echo \common\widgets\Alert::widget(); ?>
    </div>
    <div class="box">
        <?php $form = ActiveForm::begin([
                'id'      => 'frm-ajax',
                'fieldConfig' => ['template' => "{label}\n{input}\n{hint}"],
                'options' => [
                        'class' => 'form-khieunai',
                        'enctype' => 'multipart/form-data'
                ],
        ]); ?>
        <div class="box-body">
            <input type="hidden" name="orderID" value="<?= $tborder->orderID ?>" id="orderID">
            <div class="col-lg-4 khieunai">
                <div class="khieunai-title"><span>Lựa chọn loại khiếu nại (*)</span></div>
                <?php $dataType = \common\models\TbComplainType::find()->where(['type'=>1])->select(['id','name'])->asArray()->all();?>
                <?= $form->field($model, 'type')->radioList(ArrayHelper::map($dataType, 'id', 'name'))->label(false); ?>
            </div>
            <div class="col-lg-8 khieunai">
                <div class="khieunai-title"><span>Chi tiết khiếu nại</span></div>
                <?= $form->errorSummary($model,['header'=>'']); ?>
                <div class="form-item form-type-textfield form-item-order-code">
                    <?= $form->field($model, 'orderID')->hiddenInput(['readonly'=>1,'maxlength' => 128,'class'=>'form-text','size'=>58])->label(false) ?>
                    <?php $model->orderID = $tborder->orderID; ?>
                    <div class="form-group field-tbcomplain-orderid required">
                        <label class="control-label" for="tbcomplain-orderid">Đơn hàng khiếu nại</label>
                        <input type="text"  class="form-control" name="identify" value="<?= $model->order->identify ?>" disabled >
                    </div>
                    <div class="form-group description text-red">(<span class="red">*</span>) Lưu ý quý khách chỉ được chọn một mã đơn hàng khiếu nại </div>
                </div>
                <div id="khieunai-item">
                    <div id="edit-container" class="form-wrapper">
                        <label for="edit-tien-boi-thuong">Số tiền bồi thường </label>
                        <?= Html::activeTextarea($model,'content',['placeholder' => 'Mô tả chi tiết nội dung tại đây', 'class' => 'form-control','cols'=>60,'rows'=>5]) ?>
                        <?php echo Html::error($model,'content', ['class' => 'help-block']); ?>
                    </div>
                </div>
                <div class="form-item form-type-textfield form-item-tien-boi-thuong">
                    <?= $form->field($model, 'compensation')->textInput(['maxlength' => 128,'class'=>'currency form-control hide','style'=>''])->label(false) ?>
                </div>
                <div class="col-sm-4">
                    <div class="form-item img form-type-managed-file form-item-file-upload">
                        <label class="control-label" for="imgInp">Ảnh vận đơn</label>
                        <div class="img" style=" overflow: hidden; position: relative;">
                            <?php echo $form->field($model, 'image')->fileInput(['onchange'=>"Main.readURL(this,'vd')"])->label(false);?>
                            <a href="javascript:void(0)" data-id="vd">
                                <img src="/images/image-no-image.png" width="100" height="100"><br>
                                <span style="background:#3182c1; color: #fff; padding: 0px 8px">Tải</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <input class="tao-yeu-cau btn btn-primary"  name="op" value="Tạo yêu cầu" type="submit">
                </div>
               <!-- <div id="edit-file-upload-ajax-wrapper">
                    <div class="form-item form-type-managed-file form-item-file-upload">
                        <?php /*echo $form->field($model, 'image')->fileInput()->label($model->getAttributeLabel('image'));*/?>
                    </div>
                </div>-->

            </div>
        </div>
        <?php ActiveForm::end(); ?>

        <div style="display: none">
            <?= Html::beginForm(\yii\helpers\Url::to(['ajax/upload']), 'post', ['enctype' => 'multipart/form-data']) ?>
            <?= Html::fileInput('', null, [
                    'id' => 'photo-file',
                    'class' => 'hidden',
                    'multiple' => 'multiple',
            ])
            ?>
            <input type="file" name="Photo[image]">
            <input type="hidden" name="pid">
            <?php Html::endForm() ?>
        </div>
    </div>
</div>
