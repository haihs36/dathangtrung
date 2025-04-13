<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$setting = \Yii::$app->controller->setting;
$totalOrder = 0;
$totalOrderFee = 0;
$phitamtinh = 0;
$orderingFee = 0; //phi dat hang
$role = Yii::$app->user->identity->role;
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
if (in_array($currentOrder->status, [1, 5, 6]) || $role == BUSINESS) {
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
<div class="pt15 clearfix row">
    <div class="box-body">
        <?php echo $this->render('@app/views/orders/_order_status_bar', ['status' => $currentOrder->status]); ?>
        <?php
        if (isset($order) && $order) { ?>

            <?php $form = ActiveForm::begin([
                'action' => ['orders/update'],
                'enableAjaxValidation' => false,
                'options' => ['class' => "don-hang-gio-hang-add-form"]
            ]); ?>

            <input type="hidden" value="<?= $currentOrder->orderID ?>" name="orderID"/>
            <?php

            $totalkgShop = 0;
            $totalShipmentFee = 0;
            $suplier = current($order);
            $shop = reset($suplier);
            $tbOrderSupplier = \common\models\TbOrderSupplier::findOne(['orderID' => $shop['orderID'], 'supplierID' => $shop['supplierID']]);

            $disable_shop = '';
            if (($role != ADMIN && in_array($currentOrder->status, [3, 4, 5, 6, 8, 9])) || $role == BUSINESS || $tbOrderSupplier->status == 4 || $tbOrderSupplier->status == 3 || ($currentOrder->status == 6 || $currentOrder->status == 5)) {
                // shop het hang va don hang o trang thai da tra hang
                $disable_shop = 'disabled';//het hang
            }
            ?>
            <br>
            <br>
            <br>
            <div class="clear ">
                <div class="clear mt15">
                    <!--tong don hang-->
                    <div class="tong-tien-don clearfix">
                        <div class="box">
                            <div class="box-header pr0 pb0">
                                <h3 class="box-title">
                                    <span class="glyphicon glyphicon-eye-open"></span> Thông tin đơn hàng
                                </h3>
                                <div class="pull-right collap-shop-price">
                                    <?php
                                    // $status     = $currentOrder->getStatus($currentOrder->status);
                                    // $shipStatus = \common\components\CommonLib::getStatusShipping($currentOrder->shippingStatus);
                                    // echo $status.$shipStatus;
                                    ?>
                                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                            title="Chi tiết">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <!--chi tiet don-->
                            <div class="box-body pb0 pt0">
                                <div class="order-info">
                                    <!--thong tin don-->
                                    <table class="table table-bordered table-summary table-hover" id="total-order">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <div class="row">
                                                    <label>Mã đơn hàng:</label>
                                                    <b><?= $currentOrder->identify; ?></b>
                                                </div>
                                                <div class="row">
                                                    <label>Tên khách hàng: </label>
                                                    <b><?= $customer->fullname ?></b>
                                                    (<i><b><?= $customer->username ?></b></i>)
                                                </div>
                                                <div class="row">
                                                    <label>Ngày lên đơn:</label>
                                                    <b><?= date('d-m-Y H:i:s', strtotime($currentOrder->orderDate)); ?></b><br/>
                                                </div>
                                                <div class="row">
                                                    <?php
                                                    $cny = ($currentOrder->cny) ? $currentOrder->cny : $setting['CNY'];
                                                    ?>
                                                    <label>Tỷ giá áp dụng:</label>
                                                    <b class="vnd-unit"><?= number_format($cny) ?><em>đ</em></b>
                                                </div>
                                                <div class="row last">
                                                    <p><label>Địa chỉ giao hàng: </label> <?= $customer->billingAddress ?></p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <label class="bold">Tiền hàng</label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">(1) Tiền hàng: <?= $currentOrder->totalOrderTQ; ?>
                                                        <em>¥</em></label>
                                                    <label class="pull-right">
                                                        <b class="currency vnd-unit"><?= $currentOrder->totalOrder; ?></b><em
                                                                class="vnd-unit">đ</em>
                                                    </label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">(2) Phí ship nội địa
                                                        TQ: <?= $currentOrder->totalShip; ?><em>¥</em></label>
                                                    <label class="pull-right"><b
                                                                class="vnd-unit"><?= number_format($currentOrder->totalShipVn); ?></b><em
                                                                class="vnd-unit">đ</em></label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">(3) Phí mua hàng (<?= $currentOrder->discountDeals ?>%) :</label>
                                                    <label class="pull-right">
                                                        <b class="vnd-unit currency"><?= $currentOrder->orderFee; ?></b><em
                                                                class="vnd-unit">đ</em>
                                                    </label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">(4) Phí phát sinh:</label>
                                                    <label class="pull-right"><b
                                                                class="vnd-unit currency"><?= $currentOrder->totalIncurred; ?></b><em
                                                                class="vnd-unit">đ</em></label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">(5) Tổng:</label> <label class="pull-right">
                                                        <b class="vnd-unit currency"><?= $currentOrder->totalOrder + $currentOrder->orderFee + $currentOrder->totalIncurred + $currentOrder->incurredFee + $currentOrder->totalShipVn; ?></b><em
                                                                class="vnd-unit">đ</em>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <label class="bold">Tiền cân nặng</label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">Số lượng sản phẩm:</label>
                                                    <label class="pull-right"><b><?= $currentOrder->totalQuantity; ?></b></label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">Cân nặng</label>
                                                    <label class="pull-right"><b><?= $currentOrder->totalWeight; ?>kg/<span class="vnd-unit"><?= number_format(round($currentOrder->weightCharge)) ?><em>đ</em></span>
                                                        </b></label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">(6) Tổng tiền cân nặng:</label>
                                                    <label class="pull-right"><b
                                                                class="vnd-unit currency"><?= $currentOrder->totalWeightPrice; ?></b><em
                                                                class="vnd-unit">đ</em></label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">(7) Kiểm đếm:</label>
                                                    <label class="pull-right"><b class="vnd-unit currency"><?= $currentOrder->phikiemhang ?></b><em
                                                                class="vnd-unit">đ</em></label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">(8) Đóng gỗ:</label> <label class="pull-right"><b
                                                                class="vnd-unit currency"><?= ($currentOrder->phidonggo) ?></b><em
                                                                class="vnd-unit">đ</em></label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">(9) Phí ship VN:</label>
                                                    <!--Phí phát sinh đơn hàng-->
                                                    <label class="pull-right"><b class="vnd-unit currency">0</b><em
                                                                class="vnd-unit">đ</em></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <label class="bold">Tất toán đơn hàng</label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">Tổng phí (5+6+7+8+9):</label>
                                                    <label class="pull-right"><b
                                                                class="vnd-unit currency"><?= $currentOrder->totalPayment ?></b><em
                                                                class="vnd-unit">đ</em></label>
                                                </div>
                                                <div class="row">
                                                    <label class="pull-left">Đã thanh toán:</label>
                                                    <label class="pull-right"><b
                                                                class="vnd-unit currency"><?= $currentOrder->totalPaid; ?></b><em
                                                                class="vnd-unit">đ</em></label>
                                                </div>
                                                <div class="row last">
                                                    <label class="pull-left">Còn thiếu:</label>
                                                    <label class="pull-right">
                                                        <b class="vnd-unit currency"><?= $currentOrder->debtAmount; ?></b><em class="vnd-unit">đ</em>
                                                    </label>
                                                </div>
                                                <div class="row last">
                                                    <p><label class="pull-left">Ghi chú: </label> <?= $currentOrder->noteOrder; ?></p>
                                                </div>
                                                <div class="row last" style="padding: 5px 0">
                                                    <?php $listMvd = \common\models\TbTransfercode::find()->select('transferID')->where(['orderID' => $currentOrder->orderID])->asArray()->all();
                                                    if ($listMvd) {
                                                        $listMvd = array_column($listMvd, 'transferID');
                                                        ?>
                                                        <label class="pull-left">Mã vận đơn:</label>
                                                        <ul class="tags">
                                                            <?php foreach ($listMvd as $barcode){ ?>
                                                                <li> <span class="label label-success"><?= $barcode ?></span></a></li>
                                                            <?php } ?>
                                                        </ul>
                                                    <?php } ?>
                                                </div>
                                                <div class="row text-right">
                                                        <label class="checkbox icheck pr5">
                                                            <label>
                                                                <input disabled type="checkbox" value="1" <?= ($currentOrder->isBox) ? 'checked' : '' ?> name="isBox"> Đóng gỗ
                                                            </label>
                                                        </label>
                                                        <label class="checkbox icheck">
                                                            <label>
                                                                <input disabled type="checkbox" value="1" <?= ($currentOrder->isCheck) ? 'checked' : '' ?> name="isCheck"> Kiểm đếm
                                                            </label>
                                                        </label>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clear">
                <?= \common\widgets\Alert::widget() ?>
            </div>
            <div class="shop-item clearfix row" id="shop-<?= $tbOrderSupplier->id ?>">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title shop-title">
                                <label> <?= \yii\helpers\Html::encode($shop['shopName']) ?>
                                    (<?= count($suplier) ?> SP)</label>
                            </h3>
                            <div class="box-tools pull-right ">
                                <input type="hidden" value="<?= $tbOrderSupplier->shippingStatus ?>"
                                        name="shippingStatus-<?= $shop['supplierID'] ?>" id="shippingStatus-<?= $shop['supplierID'] ?>">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body " style="display: block;">
                            <div class="shop-content" > <!-- style=" max-height: 545px;overflow-y: auto;" -->
                                <table class="data-item table table-striped table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="5%">STT</th>
                                        <th width="5%">Image</th>
                                        <th width="25%">Sản phẩm</th>
                                        <th width="15%" class="text-center">Giá</th>
                                        <th width="15%" class="text-center">Thành tiền</th>
                                        <th width="5%" class="text-center">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $totalPriceVN = 0;
                                    $totalQuantity = 0;
                                    $totalPriceTQ = 0;
                                    $index = 0;
                                    foreach ($suplier as $k => $item) {
                                        $index++;
                                        $totalPriceVN += $item['totalPriceVn'];
                                        $totalPriceTQ += $item['totalPrice'];
                                        $totalQuantity += $item['quantity'];
                                        ?>

                                        <tr data-shop_id="<?php echo $shop['supplierID'] ?>"
                                                class="row-shop <?= ($index % 2 == 0 ? 'even' : 'odd') ?>">
                                            <td width="5%"><?= $index ?></td>
                                            <td width="5%" align="center">
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
                                            <td width="25%">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <h4 class="media-heading">
                                                            <a target="_blank"
                                                                    href="<?= ($item['link']) ?>"><?php echo $item['title'] ?></a>
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
                                                                <span class="label label-warning"><?= trim($item['noteProduct']) ?></span>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td width="15%" valign="top">
                                                <div class="text-center">
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
                                            <td width="15%" valign="top">
                                                <div class="thanh-tien text-center">
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

                                            <td width="12%">
                                                <?php if ($currentOrder->status === 1) { ?>
                                                    <div class="text-center">
                                                        <a data-toggle="tooltip" data-original-title="Sửa sản phẩm" data-id="<?php echo $item['id'] ?>" class="edit"
                                                                href="<?= \yii\helpers\Url::toRoute(['orders-detail/update', 'id' => $item['id']]) ?>"
                                                               ><i class="fa fa-edit" aria-hidden="true"></i> Sửa</a>

                                                        <a data-toggle="tooltip" data-original-title="Xóa sản phẩm" data-id="<?php echo $item['id']  ?>" class="confirm-delete"
                                                                onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');"
                                                                href="<?= \yii\helpers\Url::toRoute(['orders-detail/delete', 'id' => $item['id']]) ?>"
                                                                ><i class="glyphicon glyphicon-trash"
                                                                    aria-hidden="true"></i> Xóa</a>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">

                </div>
            </div>

            <?php ActiveForm::end(); ?>
        <?php } ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(".select2").select2({
            placeholder: function () {
                $(this).data('placeholder');
            },
            allowClear: true
        });
    });

    if (window.location.hash) {
        var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        var mvd = document.getElementById(hash);
        if (mvd !== null) {
            mvd.focus();
            mvd.style.color = 'red';
            mvd.style.border = '1px solid red';
        }
    }
</script>
