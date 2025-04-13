<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$setting = \Yii::$app->controller->setting;
$totalOrder = 0;
$totalOrderFee = 0;
$phitamtinh = 0;
$orderingFee = 0; //phi dat hang->role;
$userAd = Yii::$app->user->identity;

$role = $userAd->role;
$this->title = 'Chi tiết đơn hàng - ' . $currentOrder->identify;

if ($role == WAREHOUSE || $role == WAREHOUSETQ) {
    $this->params['breadcrumbs'][] = ['label' => 'Tất cả đơn hàng', 'url' => ['approved']];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'Tất cả đơn hàng', 'url' => ['index']];
}

$this->params['breadcrumbs'][] = $this->title;
$disable = '';// ($currentOrder->status == 6) ? 'disabled' : ''; //da tra hang disable all form
//$disableOS = ($currentOrder->status == 7 or $currentOrder->status == 1) ? 'disabled' : '';

if ($role == WAREHOUSE) {
    $disable = 'disabled';
}

$isBlock = false;
if (in_array($currentOrder->status, [1, 5, 6]) || $role == BUSINESS || $role == CLERK) {
    $isBlock = true;
}

$customer = $currentOrder->customer;
$phidv = 0;
if (isset($customer->discountRate)) {
    $phidv = $customer->discountRate;
}

?>
<?php if (!$isBlock) { ?>
    <div class="pull-right">
        <?php if ($role == ADMIN || $role == STAFFS) { ?>
            <a class="btn btn-success" target="_blank"
                    href="<?= Url::toRoute(['orders/costs', 'id' => $currentOrder->orderID]) ?>">Cài đặt phí đơn hàng</a>
            <a class="btn btn-success" target="_blank"
                    href="<?= Url::toRoute(['orders/approval', 'id' => $currentOrder->orderID]) ?>">Duyệt đơn hàng</a>
        <?php } ?>
    </div>
<?php } ?>
<div class="pt15 clearfix ">
    <div class="box-body pd0">
        <?php echo $this->render('@app/views/orders/_order_status_bar', ['status' => $currentOrder->status]); ?>
        <?php
        if (isset($order) && $order) { ?>


            <?php

            $totalkgShop = 0;
            $totalShipmentFee = 0;
            $suplier = current($order);
            $shop = reset($suplier);
            $tbOrderSupplier = \common\models\TbOrderSupplier::findOne(['orderID' => $shop['orderID']]);

            $disable_shop = '';
            if (($role != ADMIN && in_array($currentOrder->status, [3, 4, 5, 6, 8, 9])) || $role == BUSINESS  || ($currentOrder->status == 6 || $currentOrder->status == 5)) { //|| $tbOrderSupplier->status == 4 || $tbOrderSupplier->status == 3
                // shop het hang va don hang o trang thai da tra hang
                $disable_shop = 'disabled';//het hang
            }
            ?>
            <br>
            <br>
            <br>
            <div class="clear row">
                <!--tong don hang-->
                <div class="tong-tien-don col-sm-7">
                    <div class="box">
                        <div class="box-header pr0 pb0">
                            <h3 class="box-title">
                                <span class="glyphicon glyphicon-eye-open"></span> Thông tin đơn hàng
                            </h3>
                            <div class="pull-right collap-shop-price">
                                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                        title="Chi tiết">
                                    <i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <!--chi tiet don-->
                        <div class="box-body pb0 pt0">
                            <div class="order-info">
                                <!--thong tin don-->
                                <table class="table table-bordered table-summary table-hover" id="total-order" style="margin-bottom: 10px">
                                    <tbody>
                                    <tr>
                                        <td style="border-bottom:2px solid #dddddd ">
                                            <div class="row">
                                                <label class="pull-left">Mã đơn hàng:</label> <label class="pull-right"><b><?= trim($currentOrder->identify); ?></b></label>
                                            </div>
                                            <div class="row">
                                                <label class="pull-left" style="margin-right: 5px">Tên khách hàng: </label>
                                                <label> <a class="underline" target="_blank"
                                                            href="<?= Url::to(['customer/view', 'id' => $currentOrder->customerID]) ?>"><b><?= $customer->fullname ?></b></a> (<i><b><?= $customer->username ?></b></i>)</label>
                                            </div>
                                            <div class="row last">
                                                <label>Nhân viên quản lý:</label><br>
                                                <?php
                                                $businuss = \common\components\CommonLib::listUser(0, [ADMIN, WAREHOUSE, WAREHOUSETQ]);
                                                echo (isset($businuss[$currentOrder->businessID]) ? '<label class="label label-success"> <i>KD:' . $businuss[$currentOrder->businessID] . '</i></label>' : '') .
                                                    (isset($businuss[$currentOrder->orderStaff]) ? '<label class="label label-danger"> <i>ĐH:' . $businuss[$currentOrder->orderStaff] . '</i></label>' : '');
                                                ?>
                                            </div>
                                        </td>
                                        <td style="border-bottom:2px solid #dddddd ">
                                            <div class="row">
                                                <?php
                                                $customer = $currentOrder->customer;
                                                $cnys = isset($customer->cny) ? $customer->cny : '';
                                                $cny = \common\components\CommonLib::getCNY($setting['CNY'],$cnys,$currentOrder->cny);

                                                ?>
                                                <label class="pull-left">Tỷ giá áp dụng:</label>
                                                <label class="pull-right"><b class="vnd-unit"><?= number_format($cny) ?>
                                                        <em>đ</em></b></label>
                                            </div>
                                            <!--<div class="row">
                                                <label class="bold">Tiền cân nặng</label>
                                            </div>-->
                                            <div class="row">
                                                <label class="pull-left">Số lượng sản phẩm:</label>
                                                <?php
                                                $soluong = '_';
                                                if ($currentOrder->quantity)
                                                    $soluong = (int)$currentOrder->quantity;
                                                ?>
                                                <label class="pull-right"><b style="color: #000"><?= $currentOrder->totalQuantity; ?></b> /
                                                    <b style="color: green"><?= $soluong ?></b></label>
                                            </div>
                                            <div class="row last">
                                                <label class="pull-left">Cân nặng</label>
                                                <label class="pull-right"><b><?= $currentOrder->totalWeight; ?>
                                                        kg/<span class="vnd-unit"><?= number_format(round($currentOrder->weightCharge)) ?>
                                                            <em>đ</em></span> </b></label>
                                            </div>
                                        </td>

                                    </tr>
                                    <tr style="background: #f5f5f5">
                                        <td style="padding: 0px;">
                                            <div class="row">
                                                <h4 style="text-align: center;font-size: 15px;text-transform: uppercase;" class="bold">
                                                    Tiền hàng</h4></div>
                                            <div class="row">
                                                <label class="pull-left">(1) Tiền hàng: <?= $currentOrder->totalOrderTQ; ?>
                                                    <em>¥</em></label> <label class="pull-right">
                                                    <b class="currency vnd-unit"><?= $currentOrder->totalOrder; ?></b><em
                                                            class="vnd-unit">đ</em> </label>
                                            </div>
                                            <div class="row">
                                                <label class="pull-left">(2) Phí ship nội địa TQ: <?= $currentOrder->totalShip; ?>
                                                    <em>¥</em></label> <label class="pull-right"><b
                                                            class="currency vnd-unit"><?= ($currentOrder->totalShipVn > 0 ? $currentOrder->totalShipVn : 0); ?></b><em
                                                            class="vnd-unit">đ</em></label>
                                            </div>
                                            <div class="row">
                                                <label class="pull-left">(3) Phí mua hàng (<?= $currentOrder->discountDeals ?>%) :</label>
                                                <label class="pull-right">
                                                    <b class="vnd-unit currency"><?= $currentOrder->orderFee; ?></b><em
                                                            class="vnd-unit">đ</em> </label>
                                            </div>
                                            <div class="row">
                                                <label class="pull-left">(4) Phí phát sinh:</label>
                                                <label class="pull-right"><b
                                                            class="vnd-unit currency"><?= $currentOrder->totalIncurred; ?></b><em
                                                            class="vnd-unit">đ</em></label>
                                            </div>
                                            <!--<div class="row last">
                                                <label class="pull-left">(5) Tổng:</label> <label class="pull-right">
                                                    <b class="vnd-unit currency"><? /*= $currentOrder->totalOrder + $currentOrder->orderFee + $currentOrder->totalIncurred + $currentOrder->incurredFee + $currentOrder->totalShipVn; */ ?></b><em
                                                            class="vnd-unit">đ</em> </label>
                                            </div>-->
                                            <div class="row">
                                                <label class="pull-left">(5) Tổng tiền cân nặng:</label>
                                                <label class="pull-right"><b
                                                            class="vnd-unit currency"><?= $currentOrder->totalWeightPrice; ?></b><em
                                                            class="vnd-unit">đ</em></label>
                                            </div>
                                            <div class="row">
                                                <label class="pull-left">(6) Kiểm đếm:</label>
                                                <label class="pull-right"><b class="vnd-unit currency"><?= $currentOrder->phikiemhang ?></b><em
                                                            class="vnd-unit">đ</em></label>
                                            </div>
                                            <div class="row">
                                                <label class="pull-left">(7) Đóng gỗ:</label> <label class="pull-right"><b
                                                            class="vnd-unit currency"><?= ($currentOrder->phidonggo) ?></b><em
                                                            class="vnd-unit">đ</em></label>
                                            </div>
                                            <div class="row last">
                                                <label class="pull-left">(8) Phí ship VN:</label>
                                                <!--Phí phát sinh đơn hàng-->
                                                <label class="pull-right"><b class="vnd-unit currency">0</b><em
                                                            class="vnd-unit">đ</em></label>
                                            </div>
                                        </td>
                                        <td style="padding: 0px;">
                                            <div class="row">
                                                <h4 style="text-align: center;font-size: 15px;text-transform: uppercase;" class="bold">
                                                    Tất toán đơn hàng</h4>
                                            </div>
                                            <div class="row">
                                                <label class="pull-left">Tổng phí:</label> <label class="pull-right"><b
                                                            class="vnd-unit currency"><?= $currentOrder->totalPayment ?></b><em
                                                            class="vnd-unit">đ</em></label>
                                            </div>
                                            <div class="row">
                                                <label class="pull-left">Đã thanh toán:</label>
                                                <label class="pull-right"><b
                                                            class="vnd-unit currency"><?= $currentOrder->totalPaid; ?></b><em
                                                            class="vnd-unit">đ</em></label>
                                            </div>
                                            <div class="row ">
                                                <span class="pull-left">Còn thiếu:</span> <label class="pull-right">
                                                    <b class="vnd-unit currency"><?= round($currentOrder->debtAmount); ?></b><em
                                                            class="vnd-unit">đ</em> </label>
                                            </div>
                                            <div class="row last">
                                                <span class="pull-left">Ghi chú:</span> 
                                                <div >
                                                    <textarea rows="5" name="noteOrder" disabled class="form-control"><?= trim($currentOrder['noteOrder']) ?> </textarea>
                                                     
                                                </div>
                                            </div
                                        </td>
                                    </tr>
                                    <?php
                                    $totalOrderPrice = round($currentOrder->totalPayment);//tong don hang
                                    $totalPaid = round($currentOrder->totalPaid);//tong tien coc
                                    $totalMoney = 0;
                                    $is_cart_pay = false;
                                    if ($totalOrderPrice >= $totalPaid) {
                                        $label = '∑ Tiền thiếu';
                                        $totalMoney = $totalOrderPrice - $totalPaid;
                                    } else {
                                        $is_cart_pay = true;
                                        $label = '∑ Tiền thừa';
                                        $totalMoney = $totalPaid - $totalOrderPrice;
                                    }
                                    ?>


                                    </tbody>
                                </table>
                                <?php if ($is_cart_pay) { ?>
                                    <div class="text-right mb10 box-wallet">
                                        <label align="right"><?= $label ?>:</label>
                                        <span class="vnd-unit"><?= number_format(round($totalMoney)); ?></span>
                                        <?php if ($role == ADMIN) { ?>
                                            <a style="margin-left: 20px"
                                                    class="btn btn-success return-bank"
                                                    data-oid="<?= $currentOrder->orderID ?>"
                                                    data-price="<?= $totalMoney ?>"
                                                    data-url="<?= Url::toRoute(['bank/return', 'id' => $currentOrder->customerID]) ?>"> Hoàn lại ví điện tử</a>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <?php $form = ActiveForm::begin([
                        'action' => ['orders/update'],
                        'enableAjaxValidation' => false,
                        'options' => ['class' => "don-hang-gio-hang-add-form"]
                    ]); ?>

                    <input type="hidden" value="<?= $currentOrder->orderID ?>" name="orderID"/>
                    <div class="box">
                        <div class="box-header pr0 pb0">
                            <h3 class="box-title">
                                <i class="fa fa-cart-arrow-down" aria-hidden="true"></i> Quản lý đơn hàng</h3>
                            <label class="pull-right collap-shop-price">
                                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Chi tiết">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </label>
                        </div>
                        <div class="box-body  pt0 form-horizontal">
                            <table class="table table-bordered table-summary table-hover">
                                <tfoot>
                                <tr>
                                    <td class="border-top  collapsed-b">
                                        <?php
                                        $status = (isset($tbOrderSupplier->status) ? (int)$tbOrderSupplier->status : null);
                                        ?>
                                        <div class="rows" style="overflow-y: auto;max-height: 360px;">
                                            <table class="shop-process table-striped">
                                                <tr>
                                                    <td valign="top" style="height: 300px;">
                                                        <p class="clear">
                                                            <label class="col-sm-6">Tiền hàng(¥):</label>
                                                            <label class="col-sm-6 text-bold">
                                                                <input disabled <?= $disable ?> <?= $disable_shop ?>
                                                                        id="monneyTq-<?= $shop['supplierID'] ?>"
                                                                        class="input-xlarge form-control"
                                                                        type="text"
                                                                        value="<?= $currentOrder->totalOrderTQ ?>"/>
                                                            </label>
                                                        </p>
                                                        <p class="clear">
                                                            <label class="col-sm-6">Phí nội địa(¥):</label>
                                                            <label class="col-sm-6 text-bold">
                                                                <input <?= $disable ?> <?= $disable_shop ?>
                                                                        id="shipFee-<?= $shop['supplierID'] ?>" <?= (($role == WAREHOUSE || $role == WAREHOUSETQ) ? 'disabled' : '') ?>
                                                                        class="input-xlarge form-control"
                                                                        name="shop[<?= $shop['supplierID'] ?>][shipmentFee]"
                                                                        type="text"
                                                                        value="<?= isset($tbOrderSupplier->shipmentFee) ? $tbOrderSupplier->shipmentFee : 0 ?>"/>
                                                            </label>

                                                        </p>
                                                        <?php if ($role == ADMIN || $role == STAFFS) { ?>
                                                            <p class="clear">
                                                                <label class="col-sm-6">TT thực(¥):</label>
                                                                <label class="col-sm-6 text-bold">
                                                                    <input <?= $disable ?> <?= $disable_shop ?>
                                                                            id="actualPayment-<?= $shop['supplierID'] ?>" <?= ($role == WAREHOUSE || $role == WAREHOUSETQ) ? 'disabled' : '' ?>
                                                                            class="input-xlarge form-control"
                                                                            name="shop[<?= $shop['supplierID'] ?>][actualPayment]"
                                                                            type="text"
                                                                            value="<?= (isset($tbOrderSupplier->actualPayment) && $tbOrderSupplier->actualPayment > 0) ? $tbOrderSupplier->actualPayment : (isset($tbOrderSupplier->shopPriceTQ) ? $tbOrderSupplier->shopPriceTQ : '') ?>"/>
                                                                </label>
                                                                <input type="hidden" id="actualChina-<?= $shop['supplierID'] ?>"
                                                                        value="<?= isset($tbOrderSupplier->shopPriceTQ) ? $tbOrderSupplier->shopPriceTQ : '' ?>"/>

                                                            </p>
                                                            <p class="clear">
                                                                <label class="col-sm-6">CK được(¥):</label>
                                                                <label class="col-sm-6 text-bold">
                                                                    <input id="actualPayments-<?= $shop['supplierID'] ?>" disabled class="input-xlarge form-control" name="shop[<?= $shop['supplierID'] ?>][discount]" type="text" value="<?= isset($tbOrderSupplier->discount) ? $tbOrderSupplier->discount : '' ?>"/>
                                                                </label>

                                                            </p>
                                                        <?php } ?>
                                                       
                                                        <div class="clear text-right  box-kien pt15">
                                                            <label class="checkbox icheck">
                                                                <input <?= $disable_shop ?> type="checkbox" value="1" <?= ($currentOrder->isBox) ? 'checked' : '' ?> name="isBox"> Đóng gỗ
                                                            </label>
                                                            <label class="checkbox icheck">

                                                                <input <?= $disable_shop ?> type="checkbox" value="1" <?= ($currentOrder->isCheck) ? 'checked' : '' ?> name="isCheck"> Kiểm đếm
                                                            </label>
                                                            <?php if (isset($tbOrderSupplier->status) && $tbOrderSupplier->status != 4) { ?>
                                                                <label class="checkbox icheck">

                                                                    <input <?= $disable ?> <?= $disable_shop ?> <?= (($role == WAREHOUSE || $role == WAREHOUSETQ) ? 'disabled' : '') ?>
                                                                            class="shop-check-all"
                                                                            name="shop[<?= $shop['supplierID'] ?>][isStock]"
                                                                            id="shop[<?= $shop['supplierID'] ?>][isStock]"
                                                                            type="checkbox" <?= (($status == 3 || $tbOrderSupplier->isStock > 0) ? 'checked' : '') ?>> Hủy đơn
                                                                </label>
                                                            <?php } ?>
                                                        </div>

                                                    </td>
                                                    <td valign="top">
                                                        <p>
                                                            <label class="col-sm-5">Mã NCC:</label>
                                                            <label class="col-sm-7 txt-cont">
                                                                <input <?= $disable_shop ?> <?= $disable ?>
                                                                        id="orderNumber-<?= $shop['supplierID'] ?>" <?= ($role == WAREHOUSE ? 'disabled' : '') ?>
                                                                        class="<?= (isset($tbOrderSupplier->shopProductID) && empty($tbOrderSupplier->shopProductID)) ? 'billLadin' : '' ?>  input-xlarge form-control"
                                                                        name="shop[<?= $shop['supplierID'] ?>][shopProductID]"
                                                                        type="text"
                                                                        value="<?= isset($tbOrderSupplier->shopProductID) ? $tbOrderSupplier->shopProductID : '' ?>"/>
                                                            </label>
                                                        </p>
                                                        <?php //} ?>
                                                        <p class="clear">
                                                            <label class="col-sm-5"> Phí phát sinh:</label>
                                                            <label class="col-sm-7 txt-cont">
                                                                <input <?= $disable ?> <?= $disable_shop ?>
                                                                        id="incurredFee-<?= $shop['supplierID'] ?>"
                                                                        class="currency input-xlarge form-control"
                                                                        name="shop[<?= $shop['supplierID'] ?>][incurredFee]"
                                                                        type="text"
                                                                        value="<?= isset($tbOrderSupplier->incurredFee) ? $tbOrderSupplier->incurredFee : 0 ?>"/>
                                                            </label>
                                                        </p>
                                                        <?php
                                                        $disableNotCoc = ($currentOrder->status == 1) ? 'disabled' : '';
                                                        if (in_array($currentOrder->status, [3, 4, 5, 6, 8, 9])) {
                                                            //get ma van don
                                                            $listMvd = \common\models\TbTransfercode::find()->select('transferID,shipStatus')->where(['orderID' => $currentOrder->orderID])->orderBy(['id' => 'DESC'])->asArray()->all();
                                                            if ($listMvd) {
                                                                foreach ($listMvd as $value) {
                                                                    if (empty($value['transferID'])) continue;
                                                                    $alert = '';
                                                                    $title = '';
                                                                    $shipStatus = \common\components\CommonLib::getShippingStatusByShop($value['shipStatus']);

                                                                    ?>
                                                                    <p class="clear">
                                                                        <label class="col-sm-5 " style="font-size: 11px;"><?= $shipStatus ?></label>
                                                                        <label class="col-sm-7 txt-cont input_mvd_wrap">
                                                                            <input class="form-control" disabled type="text" value="<?= $value['transferID'] ?>">
                                                                        </label>
                                                                    </p>

                                                                <?php }
                                                            } else { ?>
                                                                <p class="clear">
                                                                    <label class="col-sm-5">MVĐ:</label>
                                                                    <label class="col-sm-7 txt-cont">
                                                                        <input <?= $disable ?>
                                                                                class="form-control" <?= $disableNotCoc ?>
                                                                                placeholder="Nhập mã vận đơn"
                                                                                type="text"
                                                                                name="shop[<?= $shop['supplierID'] ?>][mvd][]">
                                                                    </label>
                                                                </p>
                                                            <?php } ?>
                                                            <div class="clear input_mvd_wrap"></div>
                                                            <?php if (!$isBlock) { ?>
                                                                <p class="clear">
                                                                    <label class="col-sm-5"></label>
                                                                    <label class="pull-right">
                                                                        <input <?= $disableNotCoc ?> <?= $disable ?>
                                                                                data-shopid="<?= $shop['supplierID'] ?>"
                                                                                type="button"
                                                                                class="btn btn-success add_mvd_button"
                                                                                value="+ Thêm"> </label>
                                                                </p>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <?php
                                                        $kho_val = $khoqc = '';
                                                        $kho_order = isset($currentOrder->provin) ? $currentOrder->provin : [];
                                                        if (isset($kho_order['name']) && !empty($kho_order['name'])) {
                                                            $kho_val = \common\components\CommonLib::_utf8($kho_order['name']);
                                                            $kho_val = strtoupper($kho_val);
                                                        }
                                                        if (!empty($setting['KHO_QC'])) {
                                                            $khoqc = strip_tags($setting['KHO_QC']);
                                                            $khoqc = str_replace('$KHO', $kho_val, $khoqc);
                                                            $khoqc = str_replace('$MDH', $currentOrder->identify, $khoqc);
                                                        }

                                                        $kho_hk = '';
                                                        if (!empty($setting['KHO_HK'])) {
                                                            $kho_hk = strip_tags($setting['KHO_HK']);
                                                            $kho_hk = str_replace('$KHO', $kho_val, $kho_hk);
                                                            $kho_hk = str_replace('$MDH', $currentOrder->identify, $kho_hk);
                                                        }
                                                        ?>
                                                        <!-- <textarea id="KHO_QC"><?php $khoqc  ?></textarea>
                                                        <textarea id="KHO_HK"><?php $kho_hk  ?></textarea> -->


                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="" style="height: 33px;">
                                            <!-- <a onclick="copy('KHO_HK')" class="btn  btn-success btn-flat">Hà Khẩu</a> -->
                                            <!-- <a onclick="copy('KHO_QC')" class="btn  btn-danger btn-flat">Quảng Châu</a> -->
                                            <?php if (!$isBlock) { ?>
                                                <button rel="<?= $shop['supplierID'] ?>" type="submit"
                                                        class="pull-right  btnUpdateOrder btn-flat  btn-submit btn btn-primary">
                                                    Lưu đơn

                                                </button>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>

                                </tfoot>
                            </table>
                        </div>

                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <div class="clear">
                <?= \common\widgets\Alert::widget() ?>
            </div>
            <div class="shop-item clearfix row" id="shop-<?= isset($tbOrderSupplier->id) ? $tbOrderSupplier->id :'' ?>">

                <div class="col-md-8">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title shop-title">
                                <label> <?= \yii\helpers\Html::encode($shop['shopName']) ?>
                                    (<?= count($suplier) ?> SP)</label>
                            </h3>
                            <div class="box-tools pull-right ">
                                <a href="<?= Url::to(['complain/create', 'id' => $currentOrder->orderID])?>" title="Khiếu nại"  class="btn btn-danger" id="khieunai" data-toggle="tooltip"><i class="fa fa-warning text-yellow"></i>Gửi khiếu nại</a>

                                <input type="hidden" value="<?= isset($tbOrderSupplier->shippingStatus) ? $tbOrderSupplier->shippingStatus : '' ?>"
                                        name="shippingStatus-<?= $shop['supplierID'] ?>" id="shippingStatus-<?= $shop['supplierID'] ?>">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body " style="display: block;">
                            <div class="shop-content"> <!-- style=" max-height: 545px;overflow-y: auto;" -->
                                <table class="data-item table table-striped table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="5%">TT</th>
                                        <th width="5%">Image</th>
                                        <th width="">Sản phẩm</th>
                                        <th width="10%">Sl đặt</th>
                                        <th width="15%">SL nhận</th>
                                        <th width="10%" class="text-right">Giá</th>
                                        <th width="10%" class="text-right">Thành tiền</th>
                                        <th width="5%"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $totalPriceVN = 0;
                                    $totalQuantity = 0;
                                    $totalPriceTQ = 0;
                                    $index = 0;

                                     $site_id = 2;

                                   
                                    $arrLinkCoupon = \cms\modules\coupons\models\Chietkhau::getLinkCoupon($suplier,$site_id);


                                    foreach ($suplier as $k => $item) {
                                        $index++;
                                        $totalPriceVN += $item['totalPriceVn'];
                                        $totalPriceTQ += $item['totalPrice'];
                                        $totalQuantity += $item['quantity'];
                                        $bgcl = '';
                                        if ($item['status'] == 3) {
                                            $bgcl = 'outof-stock';
                                        }

                                        ?>

                                        <tr  site-id="<?= $site_id ?>"  data-shop_id="<?php echo $shop['supplierID'] ?>"
                                                class="<?= $bgcl ?> row-shop <?= ($index % 2 == 0 ? 'even' : 'odd') ?>">
                                            <td><?= $index ?></td>
                                            <td align="center">
                                                <div class="san-pham-item-image">
                                                    <div class="image">
                                                        <a href="<?php echo htmlspecialchars($item['image']) ?>"
                                                                target="_blank"><img width="80" height="80"
                                                                    src="<?php echo htmlspecialchars($item['image']) ?>"></a>
                                                        <div class="image-hover">
                                                            <img width="300px"
                                                                    src="<?php echo htmlspecialchars($item['image']) ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="media">
                                                    <div class="media-body">
                                                       <h4 class="media-heading">
                                                            <a target="_blank"
                                                               href="<?= (isset($arrLinkCoupon[$item['shopProductID']]) && !empty($arrLinkCoupon[$item['shopProductID']])) ? $arrLinkCoupon[$item['shopProductID']] : $item['link'] ?>"><?php echo $item['title'] ?></a>
                                                        </h4>
                                                        <?php if (!empty($item['size'])) { ?>
                                                            <h5 class="media-heading">Kích thước: <span
                                                                        class="text-success"><strong><?= $item['size'] ?></strong>
                                                            </h5>
                                                        <?php } ?>
                                                        <?php if (!empty($item['color'])) { ?>
                                                            <h5 class="media-heading">Màu sắc: <span
                                                                        class="text-success"><strong><?= $item['color'] ?></strong>
                                                            </h5>
                                                        <?php } ?>
                                                        <?php if (!empty($item['status'])) { ?>
                                                            <span>Tình trạng hàng: </span>
                                                            <span class="label label-danger"><?= \common\components\CommonLib::statusProduct($item['status']) ?></span>
                                                        <?php } ?>
                                                        <?php if (!empty($item['noteProduct'])) { ?>
                                                            <div>
                                                                <span>Ghi chú: </span>
                                                               <span class="label label-warning"><?= $item['noteProduct'] ?></span>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="red" id="sl_nhan_<?php echo $item['id'] ?>" data-toggle="tooltip" data-original-title="Số lượng đã nhận"><i class="fa fa-fw fa-cubes"></i> <?php echo (int)$item['qty_receive'] ?></span> / <span class="sl_dat" id="sl_dat_<?php echo $item['id'] ?>"  data-toggle="tooltip" data-original-title="Số lượng đã đặt"><i class="fa fa-fw fa-database"></i> <?php echo (int)$item['quantity'] ?></span></td>
                                              <td>
                                                  <input data-toggle="tooltip" data-original-title="Số lượng đã kiểm hàng" data-pid="<?php echo $item['id'] ?>" data-qty="<?php echo (int)$item['quantity'] ?>" type="number" name="sl_nhan" min="0" max="<?php echo (int)$item['quantity'] ?>" value="<?php echo (int)$item['qty_receive'] ?>" class="form-control allownumeric">
                                                  <textarea class="form-control note-received" data-pid="<?php echo $item['id'] ?>"  data-toggle="tooltip" data-original-title="Ghi chú kiểm hàng" placeholder="Ghi chú kiểm"><?php echo $item['note_receive'] ?></textarea>
                                              </td>
                                            <td valign="top">
                                                <div class="text-right">
                                                    <h5 class="media-heading vnd-unit">
                                                        <b><?php echo number_format(round($item['unitPriceVn'])) ?></b>
                                                    </h5>
                                                    <h5 class="media-heading te">
                                                        <b><?php echo $item['unitPrice'] ?></b><em>¥</em>
                                                    </h5>
                                                    <h5 class="media-heading te">
                                                        x<?php echo $item['quantity'] ?></h5>
                                                </div>
                                            </td>
                                            <td valign="top">
                                                <div class="thanh-tien text-right">
                                                    <h5 class="media-heading vnd-unit">
                                                        <b><?php echo number_format(round($item['totalPriceVn'])) ?></b>
                                                    </h5>
                                                    <h5 class="media-heading te">
                                                        <b><?php echo $item['totalPrice'] ?></b><em>¥</em>
                                                    </h5>
                                                    <!--                                                                <h5 class="media-heading te">x-->
                                                    <?php //echo $item['quantity']  ?><!--</h5>-->
                                                </div>
                                            </td>

                                            <td width="5%">
                                                <?php //if (!$isBlock) { ?>
                                                <?php if (($role == ADMIN || $role == STAFFS) && in_array($currentOrder->status, [1, 2, 11]) || $userAd->username ==ADMINISTRATOR) { ?>
                                                    <div class="text-center">
                                                        <a data-id="<?php echo $item['id'] ?>" class="edit"
                                                                href="<?= Url::toRoute(['orders-detail/update', 'id' => $item['id']]) ?>"
                                                                data-toggle="tooltip" data-original-title="Sửa sản phẩm" ><i class="fa fa-edit" aria-hidden="true"></i></a>
                                                        
                                                    </div>
                                                <?php } ?>
                                                <?php //} ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box direct-chat direct-chat-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Trao đổi đơn hàng</h3>

                            <div class="box-tools pull-right">
                                <!-- <span data-toggle="tooltip" title="" class="badge bg-green new-messages" data-original-title="0 tin nhắn">0</span> -->
                                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <!--<button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Contacts">
                                    <i class="fa fa-comments"></i></button>-->
                                <button type="button" class="btn btn-box-tool" data-widget="remove">
                                    <i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="direct-chat-messages chat_history"></div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer" style=" margin-bottom: 120px">
                            <div class="form-group">
                                <div class="chat_message_area">
                                    <div id="group_chat_message" contenteditable class="form-control chat_message"></div>
                                    <div class="form-group text-right" style="margin-top:5px;position: relative">
                                        <div class="emotion-Icon">
                                            <i class="fa fa-smile-o" aria-hidden="true"></i>
                                            <div class="emotion-area"></div>
                                        </div>
                                        <div class="image_upload">
                                           
                                            <!--<form id="suploadImage" method="post" action="/up-file">
                                                <label for="uploadFile"><img src="/images/upload.png" /></label>
                                                <input type="file" name="uploadFile" id="uploadFile" accept=".jpg, .png" />
                                            </form>-->
                                        </div>

                                        <button type="button" name="send_group_chat" id="send_group_chat" class="btn btn-primary">Gửi</button>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <!-- /.box-footer-->
                    </div>
                </div>
            </div>


        <?php } ?>
    </div>
</div>

<!--<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>
    $('.editd').editable({
        url: 'post.php',
        type: 'text',
        name: 'username',
        title: 'Enter username',
        pk: 1,
        ajaxOptions:{
            type:'post'
        }
    });
</script>-->
<script>
    $(document).ready(function () {

         $(".allownumeric").on("keypress keyup blur",function (event) {
           $(this).val($(this).val().replace(/[^\d].+/, ""));

            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

        $(".allownumeric").bind('change', function (e) {
            if (! $(this).data("previousValue") ||  $(this).data("previousValue") != $(this).val() )
           {
                var pid = $(this).data('pid');
                var qty_recei = parseInt($(this).val()) ;
                var qty = $(this).data('qty');

                if(qty_recei <= qty){
                    $(this).data("previousValue", $(this).val());
                    Main.updateQuantityReceived(pid,qty_recei);
                }

           }
        });

        $(".allownumeric").each(function () {
                $(this).data("previousValue", $(this).val());
        });

        $(".note-received").bind('change', function (e) {
            var note = $(this).val();
            var pid = $(this).data('pid');
            Main.updateQuantityReceived(pid,'',note);
        });


        Chatbox.init(<?= (int)$currentOrder->orderID ?>);
       /* setInterval(function () {
            Chatbox.chat_count();
            Chatbox.fetch_chat_history('fetch_data',<?= (int)$currentOrder->orderID ?>, '');
        }, 10000);*/

        $(".select2").select2({
            placeholder: function () {
                $(this).data('placeholder');
            },
            allowClear: true
        });
    });

    /* if (window.location.hash) {
         var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
         var mvd = document.getElementById(hash);
         if (mvd !== null) {
             mvd.focus();
             mvd.style.color = 'red';
             mvd.style.border = '1px solid red';
         }
     }*/
</script>
