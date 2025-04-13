<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OrdersFast */
/* @var $form yii\widgets\ActiveForm */

$setting = Yii::$app->controller->setting;
$baseUrl = Yii::$app->params['baseUrl'];
/*seo*/

$data_seo['title']          = 'Đặt hàng nhanh';

\frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);
?>
<main id="home">
    <section id="firm-services" class="services">
        <div class="all">
            <h4 class="sec__title center-txt"> <span id="ContentPlaceHolder1_lblTitle">Đặt hàng nhanh</span></h4>
            <div class="primary-form">
                <div class="order-tool clearfix">
                    <div class="tool-detail">
                        <div class="">
                            <div class="clear"></div>
                            <div class="form-search-product bbb">
                                <div class="orders-fast-form">

                                    <?php
                                    if (Yii::$app->session->hasFlash('success')):
                                        ?>
                                        <script>
                                            $(function () {
                                                swal({
                                                        title: "Thông báo",
                                                        text: "<?= Yii::$app->session->getFlash('success') ?>",
                                                        type: "success",
                                                        showCancelButton: false,
                                                        confirmButtonColor: "#DD6B55",
                                                        confirmButtonText: "Ok",
                                                        closeOnConfirm: false,
                                                        closeOnCancel: false,
                                                        confirmButtonClass: "btn-success"
                                                    },
                                                    function(isConfirm){
                                                        if (isConfirm) {
                                                            location.reload();
                                                        }
                                                    });

                                            });
                                        </script>
                                    <?php endif; ?>

                                    <div class="box box-info">
                                        <?php $form = ActiveForm::begin([
                                            'id'      => 'search-form',
                                            'enableAjaxValidation' => false,
                                            'method'  => 'post',
                                            'action' => '/dat-hang-nhanh',
                                            'options' => [
                                                'class' => 'form-horizontal',
                                            ],

                                        ]);
                                        ?>
                                        <div class="box-body">
                                            <?= $form->field($model, 'link', [
                                                'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
                                            ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge','placeholder'=>'Nhập link sản phẩm: taobao, 1688, tmall'])->label('Nhập link sản phẩm', ['class' => "col-sm-2 control-label"]) ?>
                                            <?= $form->field($model, 'fullname', [
                                                'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
                                            ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge','placeholder'=>'Họ và tên'])->label('Họ và tên:', ['class' => "col-sm-2 control-label"]) ?>

                                            <?= $form->field($model, 'mobile', [
                                                'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
                                            ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge','placeholder'=>'Số điện thoại'])->label('Số điện thoại:', ['class' => "col-sm-2 control-label"]) ?>


                                            <?= $form->field($model, 'note', [
                                                'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
                                            ])->textarea(['maxlength' => true, 'class' => 'form-control','rows'=>3,'placeholder'=>'Ghi chú'])->label('Ghi chú:', ['class' => "col-sm-2 control-label"]) ?>

                                        </div>
                                        <div class="form-search-right text-center">
                                            <button type="submit" class="pill-btn btn btn-search btn main-btn hover">Gửi đơn</button>
                                        </div>
                                        <?php ActiveForm::end(); ?>
                                    </div>


                                </div>
                            </div>
                            <div class="clear"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</main>

