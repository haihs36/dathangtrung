<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;

    $this->title = 'Thông tin phiếu xuất: ' . $phieuXuat->name;
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách phiếu xuất kho', 'url' => ['lo/index']];
    $setting                       = \Yii::$app->controller->setting;
    $role                          = Yii::$app->user->identity->role;
    $disable                       = 'disabled';
    $action                        = Yii::$app->controller->action->id;
?>
<?php //echo $this->render('@app/views/payment/_steps', ['action' => $action]); ?>

<div class="box clear">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $this->title ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">        
        <div class="col-lg-6">
            <?php if($phieuXuat->customerID){ ?>
                <script>
                    Main.load_customer('<?= (int)$phieuXuat->customerID ?>');
                </script>
            <?php } ?>
            <div class="cusomerInfo">

            </div>
            <!--<table class="table table-striped table-bordered detail-view">
                <tbody>
                <tr>
                    <th>Mã phiếu xuất:</th>
                    <td><b>dung</b></td>
                </tr>
                <tr>
                    <th>Tài khoản</th>
                    <td><b>dungna</b></td>
                </tr>
                <tr>
                    <th>Điện thoại:</th>
                    <td><b>0909089978</b></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><b>dungn@gmail.com</b></td>
                </tr>
                </tbody>
            </table>-->
        </div>
    <div class="col-lg-6">
            <?php $form = ActiveForm::begin([
                'action'  => ['lo/update','id'=>$phieuXuat->id],
                'enableAjaxValidation' => false,
                'options'              => ['class' => "form-horizontal"]
            ]);
                $customers = \yii\helpers\ArrayHelper::map(\common\models\TbCustomers::find()->select(['id', 'username'])->asArray()->all(), 'id', 'username');
            ?>
            <?= $form->field($phieuXuat, 'customerID')->textInput()->dropDownList($customers, [
                'disabled' => ($disable ? true : false),
                'class' => 'select2 form-control', 'prompt' => '','style'=>'width:100%','data-placeholder'=>'Chọn tài khách hàng'])->label(false)
            ?>
            <?= $form->field($phieuXuat, 'address', ['template' => '{label}<div class="controls">{input}{error}</div>'])->textarea(['maxlength' => true, 'rows' => 2])->label('Địa chỉ giao hàng:', ['class' => "control-label"]) ?>
            <?= $form->field($phieuXuat, 'note', ['template' => '{label}<div class="controls">{input}{error}</div>'])->textarea(['maxlength' => true, 'rows' => 2])->label('Ghi chú:', ['class' => "control-label"]) ?>

            <div class="form-group">
                <label class="control-label" for="tbcustomers-fullname"></label>
                <div class="controls text-right">
                    <?= Html::submitButton($phieuXuat->isNewRecord ? 'Tạo mới' : 'Lưu phiếu xuất', ['class' => $phieuXuat->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    <a href="<?= \yii\helpers\Url::toRoute(['lo/index']) ?>" class="btn btn-danger">Thoát</a>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
 <?= \common\widgets\Alert::widget() ?>
<?php if(!$phieuXuat->status){ ?>
<div class="box">
    <div class="box-header">
            <?php
                $form = ActiveForm::begin([
                    'id'      => 'frmCheck',
                    'action'  => ['payment/index'],
                    'method'  => 'post',
                    'options' => [
                        'class' => 'form-horizontal',
                    ]
                ]); ?>
                <?= $form->field($model, 'shippingCode',[
                    'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
                ])->textInput(['id' => 'orderNumber', 'placeholder' => 'Nhập mã vận đơn..'])->label('Mã vận đơn',['class'=>"col-sm-2 control-label"]) ?>
            <?php ActiveForm::end(); ?>
    </div>
</div>
<?php } ?>

<div class="tb-orders-return clear">
    <div class="box-body">
        <div id="list-shop-result" style="display: none">
            <div class="text-center"><img src="/images/loader.gif"><br> Đang xử lý</div>
        </div>
        <!--end-->
    </div>
    <div class="text-right" id="btnOption" style="display: none">
        <div class="pull-left">
            <a class="btn btn-primary" href="javascript:void(0)">
                <input id="check_all" class="check-all" style="margin: 0px;padding: 0px" type="checkbox">
                <label style="padding: 0px;margin: 0px" for="check_all">Tất cả</label> </a>
        </div>
        <div class="pull-right">
            <a class="btn-update btn btn-primary" href="javascript:void(0)">
                <i class="fa fa-edit" aria-hidden="true"></i> Tính tiền </a>
            <button disabled class="btn-pay-all btn btn-success">
                <i class="fa fa-cart-arrow-down" aria-hidden="true"></i> Trả hàng
            </button>
        </div>
    </div>
</div>

<script>
    if (window.location.hash) {
        var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        var tags = document.getElementById(hash);
        if (tags !== null) {
            tags.focus();
            tags.style.color = 'red';
            tags.style.border = '1px solid red';
        }

        // hash found
    }
    <?php if($phieuXuat->customerID){ ?>
    //ajax load shop ban ma vach
    $(function () {
        Main.loadShop('#list-shop-result', '<?= (int)$phieuXuat->customerID ?>', '<?= (int)$phieuXuat->id ?>','<?= (int)$phieuXuat->status ?>');
//        $('#payment-customerid, #payment-loid').change(function () {
//            customerID = $('#payment-customerid').val();
//            /*if (history.pushState) {
//                history.pushState(null, null, encodeURI('/payment/index?Payment[customerID]=' + customerID));
//            }*/
//            if (customerID) {
//                Main.loadShop('#list-shop-result', customerID, loID);
//            }
//        });
        <?php } ?>
    });
</script>