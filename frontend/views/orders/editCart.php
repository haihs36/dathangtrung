<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

    $this->title = 'Chỉnh sửa sản phẩm có mã: ' . ' ' . $model->id;
    $this->params['breadcrumbs'][] = ['label' => 'Giỏ hàng', 'url' => ['orders/cart']];
    $this->params['breadcrumbs'][] = $this->title;
    $setting = \Yii::$app->controller->setting;
?>

<div class="tb-orders-detail-form">
    <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => true,
            'options'              => ['class' => "form-horizontal", 'enctype' => 'multipart/form-data']
    ]); ?>
    <div class="row">
        <div class="box-body">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Chỉnh sửa sản phẩm: <b><?= $model->id ?></b></h3>
                </div>
                <div class="panel-body">
                    <div class="rows">
                        <div class="col-lg-12">
                            <?= \common\widgets\Alert::widget() ?>
                            <div class="form-group">
                                <label class="control-label"  style="padding-top: 0px;padding-bottom: 10px">
                                    Tên shop:  <?= $model->shop_name ?>
                                </label>
                            </div>
                            <?= $form->field($model, 'title', [
                                'template' => '{label}<div class="controls">{input}{error}</div>'
                            ])->textInput(['class' => 'input-xslarge form-control'])->label('Tên sản phẩm', ['class' => "control-label"]) ?>

                            <?php if ($model->image){ ?>
                                <div class="form-group">
                                    <label class="control-label"  style="padding-top: 0px;padding-bottom: 10px">
                                        Hình ảnh:
                                    </label>
                                    <div class="controls">
                                        <img src=" <?= $model->image ?>" width="200px" />
                                        <a href="<?= \yii\helpers\Url::to(['orders/clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete"  title="Xóa ảnh">Xóa ảnh</a>
                                    </div>
                                </div>
                                <br/>
                            <?php }else{ ?>
                                <?= $form->field($model, 'image', [
                                    'template' => '{label}<div class="controls">{input}{error}</div>'
                                ])->textInput(['class' => 'input-xlarged form-control'])->label('Ảnh đại diện', ['class' => "control-label"]) ?>
                            <?php } ?>
                            <?= $form->field($model, 'size', [
                                'template' => '{label}<div class="controls">{input}{error}</div>'
                            ])->textInput(['class' => 'input-xlarge form-control'])->label('Kích thước', ['class' => "control-label"]) ?>
                            <?= $form->field($model, 'color', [
                                'template' => '{label}<div class="controls">{input}{error}</div>'
                            ])->textInput(['class' => 'input-xlarge form-control'])->label('Màu sắc', ['class' => "control-label"]) ?>

                            <?= $form->field($model, 'quantity', [
                                'template' => '{label}<div class="controls">{input}{error}</div>'
                            ])->textInput(['class' => 'input-xlarge form-control'])->label('Số lượng', ['class' => "control-label"]) ?>

                            <?= $form->field($model, 'unitPrice', [
                                'template' => '{label}<div class="controls">{input}{error}</div>'
                            ])->textInput(['class' => 'input-xlarge form-control','disabled'=>true])->label('Tiền TQ (¥)', ['class' => "control-label"]) ?>

                            <?= $form->field($model, 'link', [
                                'template' => '{label}<div class="controls">{input}{error}</div>'
                            ])->textInput(['class' => 'form-control'])->label('Link sản phẩm:', ['class' => "control-label"]) ?>

                            <?= $form->field($model, 'noteProduct', [
                                'template' => '{label}<div class="controls">{input}{error}</div>'
                            ])->textarea(['maxlength' => true, 'class' => 'mceEditor form-control','rows'=>5])->label('Ghi chú sản phẩm:', ['class' => "control-label"]) ?>

                            <div class="control-group text-right">
                                    <a href="<?= \yii\helpers\Url::toRoute(['orders/cart']) ?>" class="btn btn-success">Hủy</a>
                                    <?= Html::submitButton('Lưu', ['class' => 'btn btn-primary']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

