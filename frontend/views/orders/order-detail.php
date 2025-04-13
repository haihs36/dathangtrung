<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$setting = \Yii::$app->controller->setting;
$totalOrder = 0;
$totalOrderFee = 0;
$phitamtinh = 0;
$orderingFee = 0; //phi dat hang
$this->title = 'Chi tiết đơn hàng - ' . $currentOrder->identify;


    $this->params['breadcrumbs'][] = ['label' => 'Tất cả đơn hàng', 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;

$customer = $currentOrder->customer;
$phidv = 0;
if (isset($customer->discountRate)) {
    $phidv = $customer->discountRate;
}

?>

<div class="pt15 clearfix ">
    <div class="box-body pd0">
        <?php echo $this->render('@app/views/orders/_order_status_bar', ['status' => $currentOrder->status]); ?>
        <?php
        if (isset($order) && $order) {
            $totalkgShop = 0;
            $totalShipmentFee = 0;
            $suplier = current($order);
            $shop = reset($suplier);
            $tbOrderSupplier = \common\models\TbOrderSupplier::findOne(['orderID' => $shop['orderID'], 'supplierID' => $shop['supplierID']]);
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
                                                <label class="pull-left">Tên khách hàng: </label>
                                                <a class="underline" target="_blank"
                                                        href="<?= Url::to(['customer/view', 'id' => $currentOrder->customerID]) ?>"><b><?= $customer->fullname ?></b></a> (<i><b><?= $customer->username ?></b></i>)
                                            </div>
                                            <div class="row last">
                                                <label>Nhân viên quản lý:</label><br>
                                                <?php
                                                $businuss = \common\components\CommonLib::listUser(0, [ADMIN, WAREHOUSE, WAREHOUSETQ]);
                                                echo (isset($businuss[$currentOrder->businessID]) ?  '<label class="label label-success"> <i>KD:'.$businuss[$currentOrder->businessID].'</i></label>' : '').
                                                    (isset($businuss[$currentOrder->orderStaff]) ?  '<label class="label label-danger"> <i>ĐH:'.$businuss[$currentOrder->orderStaff].'</i></label>' : '');
                                                ?>
                                            </div>
                                        </td>
                                        <td style="border-bottom:2px solid #dddddd ">
                                            <div class="row">
                                                <?php
                                                $cny = \common\components\CommonLib::getCNY($setting['CNY'],Yii::$app->user->identity->cny,$currentOrder->cny);
                                                ?>
                                                <label class="pull-left">Tỷ giá áp dụng:</label>
                                                <label class="pull-right"><b class="vnd-unit"><?= number_format($cny) ?>
                                                        <em>đ</em></b></label>
                                            </div>
                                            <div class="row">
                                                <label class="pull-left">Số lượng sản phẩm:</label>
                                                <?php
                                                $soluong = '_';
                                                if($currentOrder->quantity)
                                                    $soluong = (int)$currentOrder->quantity;
                                                ?>
                                                <label class="pull-right"><b style="color: #000"><?= $currentOrder->totalQuantity; ?></b> / <b style="color: green"><?= $soluong ?></b></label>
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
                                            <div class="row">
                                                <label class="pull-left">Còn thiếu:</label> <label class="pull-right">
                                                    <b class="vnd-unit currency"><?= $currentOrder->debtAmount; ?></b><em
                                                            class="vnd-unit">đ</em> </label>
                                            </div>
                                            <div class="row last">
                                                <label class="pull-left">Ghi chú:&nbsp;</label>
                                                <i><?= $currentOrder->noteOrder; ?></i>
                                            </div>


                                        </td>
                                    </tr>
                                    <?php
                                    $totalOrderPrice = $currentOrder->totalPayment;//tong don hang
                                    $totalPaid = $currentOrder->totalPaid;//tong tien coc
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
                                <i class="fa fa-cart-arrow-down" aria-hidden="true"></i> Danh sách mã vận đơn</h3>
                            <label class="pull-right collap-shop-price">
                                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Chi tiết">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </label>
                        </div>
                        <?php
                            $transfercode = \common\models\TbTransfercode::getAllBarcodeByOrderId($currentOrder->orderID);

                        ?>
                        <div class="box-body  pt0 form-horizontal">
                            <div class="rows" style="overflow-y: auto;min-height: 375px;max-height: 375px;">
                                <table class="data-item table table-striped table-hover table-bordered text-center">
                                    <thead>
                                    <tr>
                                        <td>Mã vận đơn</td>
                                        <td>Cân tính tiền</td>
                                        <td>Trạng thái</td>
                                    </tr>
                                    </thead>
                                    <?php
                                    if(!empty($transfercode)){
                                        foreach ($transfercode as $item) { ?>
                                            <tr>
                                                <td><?= $item['transferID'] ?></td>
                                                <td><?= $item['kgPay'] ?></td>
                                                <td><?php echo \common\components\CommonLib::getShippingStatusByShop($item['shipStatus']); ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>


                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <div class="clear">
                <?= \common\widgets\Alert::widget() ?>
            </div>
            <div class="shop-item clearfix row" id="shop-<?= $tbOrderSupplier->id ?>">

                <div class="col-md-7">
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
                                        <th width="15%" class="text-right">Giá</th>
                                        <th width="15%" class="text-right">Thành tiền</th>
                                        <th width="5%"></th>
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
                                        $bgcl ='';
                                        if($item['status']==3){
                                        $bgcl = 'outof-stock';
                                        }

                                        ?>

                                        <tr data-shop_id="<?php echo $shop['supplierID'] ?>"
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
                                                                <span class="label-warning"><?= trim($item['noteProduct']) ?></span>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
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
                    <div class="box box-primary direct-chat direct-chat-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Trao đổi đơn hàng</h3>

                            <div class="box-tools pull-right">
                                <span data-toggle="tooltip" title="" class="badge bg-green new-messages" data-original-title="0 tin nhắn">0</span>
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

<script>
    $(document).ready(function () {
        Chatbox.init(<?= (int)$currentOrder->orderID ?>);
        setInterval(function(){
            Chatbox.chat_count();
            Chatbox.fetch_chat_history('fetch_data',<?= (int)$currentOrder->orderID ?>,'');
        }, 5000);


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
