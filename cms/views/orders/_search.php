<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\CommonLib;

$orderStatus = \common\models\TbOrders::getOrderCount($isBook);
$role = Yii::$app->user->identity->role;

?>

<div class="menu-don-hang">
    <ul class="step-action">
        <!--        --><?php //if($active !== 1){ ?>
        <li class="<?php echo ($status === 0) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action]) ?>" class="active">Tất cả
                <span class="badge <?= $orderStatus[0] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[0] ?></span></a>
        </li>

        <li class="<?php echo ($status == 1) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action, 'status' => 1]) ?>" class="active">Chờ đặt cọc<span class="badge <?= $orderStatus[1] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[1] ?></span></a>
        </li>
        <li class="<?php echo ($status == 11) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action, 'status' => 11]) ?>" class="active">Đã đặt cọc<span class="badge <?= $orderStatus[7] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[11] ?></span></a>
        </li>
        <li class="<?php echo ($status == 2) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action, 'status' => 2]) ?>" class="active">Đang đặt hàng
                <span class="badge <?= $orderStatus[2] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[2] ?></span></a>
        </li>
        <!--        --><?php //} ?>
        <li class="<?php echo ($status == 3) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action, 'status' => 3]) ?>" class="active">Đã đặt hàng<span class="badge <?= $orderStatus[3] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[3] ?></span></a>
        </li>
        <li class="<?php echo ($status == 4) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action, 'status' => 4]) ?>" class="active">Shop xưởng giao<span class="badge <?= $orderStatus[4] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[4] ?></span></a>
        </li>
        <li class="<?php echo ($status == 8) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action, 'status' => 8]) ?>" class="active">Đang vận chuyển<span class="badge <?= $orderStatus[8] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[8] ?></span></a>
        </li>
        <li class="<?php echo ($status == 9) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action, 'status' => 9]) ?>" class="active">Kho VN nhận<span class="badge <?= $orderStatus[9] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[9] ?></span></a>
        </li>
        <li class="<?php echo ($status == 6) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action, 'status' => 6]) ?>" class="active">Đã trả hàng<span class="badge <?= $orderStatus[6] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[6] ?></span></a>
        </li>

        <?php //if (!in_array($role,[WAREHOUSE,WAREHOUSETQ])) { ?>
        <li class="<?php echo ($status == 5) ? 'active' : '' ?>">
            <a href="<?php echo Url::toRoute([$action, 'status' => 5]) ?>" class="active">Đã Hủy<span class="badge <?= $orderStatus[5] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[5] ?></span></a>
        </li>
        <?php //} ?>
    </ul>
</div>
<div class="tb-orders-search clear">
    <div class="tb-orders-search">
        <?php
        $form = ActiveForm::begin([
            'id' => 'order-search-form',
            'action' => [$action],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>

<!--        <div class="form-text ext-full " style="width: 370px">-->
<!--            <div class="form-group field-tbordersearch-identify">-->
<!--                <input autocomplete="off" type="text" value="--><?//= !empty($params['productname']) ? $params['productname'] : '' ?><!--" class="form-control" name="productname" placeholder="Tên sản phẩm">-->
<!--            </div>-->
<!--        </div>-->
        <div class="form-text ext-full ">
            <div class="form-group field-tbordersearch-identify">
                <input autocomplete="off" type="text" id="orderNumber" value="<?= !empty($params['orderNumber']) ? $params['orderNumber'] : '' ?>" class="form-control" name="orderNumber" placeholder="Mã đơn hàng">
            </div>
        </div>
        <div class="form-text ext-full ">
            <div class="form-group field-tbordersearch-barcode">
                <input autocomplete="off" type="text" id="barcode" value="<?= !empty($params['barcode']) ? $params['barcode'] : '' ?>" class="form-control" name="barcode" placeholder="Mã vận đơn">
            </div>
        </div>
        <div class="form-text ext-full ">
            <div class="form-group field-tbordersearch-shopProductID">
                <input autocomplete="off" type="text" id="shopProductID" value="<?= !empty($params['shopProductID']) ? $params['shopProductID'] : '' ?>" class="form-control" name="shopProductID" placeholder="Mã order number">
            </div>
        </div>
        <div class="form-text ext-full ">
            <?= $form->field($model, 'startDate', [
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'autocomplete' => 'off', 'id' => 'startDate'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($model, 'endDate', [
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'autocomplete' => 'off', 'id' => 'endDate'])->label(false) ?>
        </div>

        <div class="form-text ext-full clear">
            <?php
            echo $form->field($model, 'status')->dropDownList(CommonLib::statusText(), [
                'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Tình trạng đơn hàng'])->label(false);
            ?>
        </div>
        <!--<div class="form-text ext-full">
            <?php
        /*                echo $form->field($model, 'shippingStatus')->dropDownList(CommonLib::statusShippingText(), [
                            'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Tình trạng vận chuyển'])->label(false);
                    */ ?>
        </div>-->
        <div class="form-text ext-full ">
            <?php
            echo $form->field($model, 'businessID')->dropDownList($businuss, [
                'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn nhân viên'])->label(false);
            ?>
        </div>
        <div class="form-text ext-full ">
            <?php
            $customers = \common\models\TbCustomers::getInfoByCondition(['status' => 1]);
            echo $form->field($model, 'customerID')->dropDownList($customers, [
                'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn khách hàng'])->label(false);
            ?>
        </div>
        <div class="form-text ext-full ">
            <?php
            $province = ArrayHelper::map(\common\models\Province::getAll(), 'id', 'name');
            echo $form->field($model, 'provinID')->dropDownList($province, [
                'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn kho đích'])->label(false);
            ?>
        </div>
        <div class="form-text ext-full">
            <button type="submit" id="btn-search-order" name="op" value="Tìm kiếm" class="btn btn-primary">
                <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
            </button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="clear"></div>
<?php
$this->registerJs("$('ready pjax:success').ready(function(){
        $('.select2').select2({
            placeholder: function () {
                $(this).data('placeholder');
            },
            allowClear: true
        });
        $('#startDate').datepicker({
        format: 'dd/mm/yyyy'
        });
        $('#endDate').datepicker({
            format: 'dd/mm/yyyy'
        });
    });
");
?>

