<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $model common\models\Bag */
    /* @var $form yii\widgets\ActiveForm */
?>
<div class="bag-form box">
    <div class="box-header with-border">
        <h3 class="box-title shop-title">
            <?= $this->title; ?>
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => false,
            'options'              => ['class' => "form-horizontal"]
        ]); ?>
        <div class="row clear-fix  mar-t-15">
            <div class="col-lg-4">
                <?= $form->field($model, 'kg', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true, 'onchange' => "Main.bagChange(this)"])->label('Cân nặng', ['class' => "col-sm-4 control-label"]) ?>
                <?= $form->field($model, 'long', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true, 'onchange' => "Main.bagChange(this)"])->label('Dài', ['class' => "col-sm-4 control-label"]) ?>
                <?= $form->field($model, 'wide', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true, 'onchange' => "Main.bagChange(this)"])->label('Rộng', ['class' => "col-sm-4 control-label"]) ?>
                <?= $form->field($model, 'high', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true, 'onchange' => "Main.bagChange(this)"])->label('Cao', ['class' => "col-sm-4 control-label"]) ?>

            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'kgChange', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true,])->label('Cân nặng quy đổi', ['class' => "col-sm-4 control-label"]) ?>

                <?= $form->field($model, 'kgPay', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true,])->label('Cân nặng tính tiền', ['class' => "col-sm-4 control-label"]) ?>
                <?= $form->field($model, 'm3', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true,])->label('Khối tính tiền (m3)', ['class' => "col-sm-4 control-label"]) ?>
                <?php
                    echo $form->field($model, 'status', [
                        'template' => '{label}<div class="col-sm-4">{input} <span></span>{error}</div>'
                    ])->dropDownList(\common\components\CommonLib::bagStatus(), [
                        'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn trạng thái'
                    ])->label('Trạng thái', ['class' => "col-sm-4 control-label"]);
                ?>
            </div>
            <div class="col-lg-4">
                <?php
                    echo $form->field($model, 'provinID', [
                        'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
                    ])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Province::find()->select(['id', 'name'])->all(), 'id', 'name'), [
                        'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn kho đích'
                    ])->label('Kho đích', ['class' => "col-sm-3 control-label"]);
                ?>
                <div class="form-group field-bag-long">
                    <label class="col-sm-3 control-label" for="bag-long">NV Xử lý</label>
                    <div class="col-sm-6">
                        <?php
                            $user = \Yii::$app->user->identity;
                            echo $user->fullname . '(' . $user->username . ') - ' . $user->email; ?>
                    </div>
                </div>
                <div class="form-group field-bag-long">
                    <label class="col-sm-3 control-label" for="bag-long">Ngày tạo</label>
                    <div class="col-sm-6">
                        <?php echo date('d/m/Y') ?>
                    </div>
                </div>
                <?= $form->field($model, 'note', [
                    'template' => '{label}<div class="col-sm-6">{input}{error}</div>'
                ])->textarea(['maxlength' => true, 'class' => 'form-control', 'rows' => 5])->label('Ghi chú', ['class' => "col-sm-3 control-label"]) ?>


            </div>
        </div>

        <div class="form-group text-center">
            <?= Html::submitButton($model->isNewRecord ? 'Lưu bao' : 'Cập nhật bao', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>


</div>
<?php if(!$model->isNewRecord){?>
<div class="bag-form box">
    <div class="box-body">  <br>
        <input style="width:30%" type="text" id="bag_barcode" class="form-control" name="barcode" value="" placeholder="Nhập mã VĐ....">
        <input type="hidden" value="<?php echo $model->id ?>" id="bag_id">
        <button type="submit" class="btn btn-primary btn-bag-submit">Xác nhận</button>

        <label class="pull-right">Tổng cân nặng kiện: <?= $total_kg ?> kg</label>
        <br><br>

        <div id="bag-result">
                <?php echo (isset($list_barcode) ? $list_barcode : '') ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
       Main.bagInit();
    });
</script>
<?php } ?>



