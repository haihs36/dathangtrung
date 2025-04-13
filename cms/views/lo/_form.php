<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model cms\models\Lo */
/* @var $form yii\widgets\ActiveForm */
    $disable = ($error == 3 ? 'disabled' : '');
?>

<div class="lo-form">
    <div class="box clear">
        <?= \common\widgets\Alert::widget() ?>
        <div class="box-header with-border">
            <h3 class="box-title"><?= $this->title ?></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="col-sm-8">
                <?php $form = ActiveForm::begin([
                    'enableAjaxValidation' => false,
                    'options'              => ['class' => "form-horizontal"]
                ]); ?>

                <?php $customers = \yii\helpers\ArrayHelper::map(\common\models\TbCustomers::find()->select(['id', 'username'])->asArray()->all(), 'id', 'username');
                    echo $form->field($model, 'customerID',[
                        'template' => '{label}<div class="col-sm-8">{input}{error}</div>'
                    ])->dropDownList($customers,[
                        'disabled' => ($disable ? true : false),
                        'class' => 'select2 form-control', 'prompt' => '','style'=>'width:100%','data-placeholder'=>'Chọn tài khách hàng'
                    ])->label('Khách hàng',['class'=>"col-sm-4 control-label"]);
                ?>

                <?php
                echo $form->field($model, 'payType',[
                    'template' => '{label}<div class="col-sm-8">{input}{error}</div>'
                ])->dropDownList(\common\components\CommonLib::paymentType(),[
                    'disabled' => ($disable ? true : false),
                    'class' => 'select2 form-control', 'prompt' => '','style'=>'width:100%','data-placeholder'=>'Phương thức thanh toán'
                ])->label('Phương thức thanh toán',['class'=>"col-sm-4 control-label"]);
                ?>

                <?php
                echo $form->field($model, 'payStatus',[
                    'template' => '{label}<div class="col-sm-8">{input}{error}</div>'
                ])->dropDownList(\common\components\CommonLib::paymentStatus(),[
                    'disabled' => ($disable ? true : false),
                    'class' => 'select2 form-control', 'prompt' => '','style'=>'width:100%','data-placeholder'=>'Hình thức giao hàng'
                ])->label('Hình thức giao hàng',['class'=>"col-sm-4 control-label"]);
                ?>

                <?= $form->field($model, 'address',[
                    'template' => '{label}<div class="col-sm-8">{input}{error}</div>'])->textarea([
                        'id'=>'editor1','class'=>'editor form-control','rows'=>"2",
                        'disabled' => ($disable ? true : false),
                ])->label('Địa chỉ giao hàng:',['class'=>"col-sm-4 control-label"]) ?>

                <?= $form->field($model, 'note',[
                    'template' => '{label}<div class="col-sm-8">{input}{error}</div>'])->textarea([
                            'id'=>'editor2','class'=>'editor form-control','rows'=>"2",
                            'disabled' => ($disable ? true : false),
                ])->label('Ghi chú:',['class'=>"col-sm-4 control-label"]) ?>
                    <label class="control-label pull-right">
                        <?php if(!$disable){ ?>
                            <?= Html::submitButton('Tạo phiếu xuất', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            <a href="<?= \yii\helpers\Url::toRoute(['lo/index']) ?>" class="btn btn-danger">Thoát</a>
                        <?php } ?>
                    </label>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-sm-4">
                <?php if($model->customerID){ ?>
                    <script>
                        Main.load_customer('<?= (int)$model->customerID ?>');
                    </script>
                <?php } ?>
                    <div class="cusomerInfo">

                    </div>
            </div>
        </div>
    </div>

<?php  if(!$model->isNewRecord){ ?>
        <div class="tb-orders-return clear">
            <div class="box-bodys">
                <div id="list-shop-result">
                    <?= $dataRender ?>
                </div>
            </div>
        </div>
        <script>
            $(function() {
                $("#returnBarcode").focus();
            });

            if (window.location.hash) {
                var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
                var tags = document.getElementById(hash);
                if (tags !== null) {
                    tags.focus();
                    tags.style.color = 'red';
                    tags.style.border = '1px solid red';
                }
            }
        </script>
      <?php } ?>
</div>
<?php if ($error == 1){ ?>
    <script>
        $(function () {
            $('#myModal').modal('show').find('.modal-title').html('<h4><i class="icon fa fa-warning"></i> Thông báo!</h4>');
            $('.modal-container').css({
                'overflow': 'hidden',
                'width': 'auto',
                'min-height': '80px',
                'text-align':'center'
            });
            $('.modal-container').html('<h4 class="text-red"><i class="icon fa fa-warning"></i> Không tìm thấy hàng ở kho Việt Nam.</h4>');
            setTimeout(function () {
                $('#myModal').modal('hide');
            }, 3000);
        });
    </script>
<?php } ?>
<?php if ($error == 2){ ?>
    <!--<script>
        $(function () {
            $('#myModal').modal('show').find('.modal-title').html('<h4><i class="icon fa fa-warning"></i> Thông báo!</h4>');
            $('.modal-container').css({
                'overflow': 'hidden',
                'width': 'auto',
                'min-height': '80px',
                'text-align':'center'
            });
            $('.modal-container').html('<h4 class="text-red"><i class="icon fa fa-check-circle"></i> Số tiền trong ví của khách hàng không đủ thực hiện giao dịch!</h4>');
            setTimeout(function () {
                $('#myModal').modal('hide');
            }, 3000);
        });
    </script>-->
<?php } ?>
