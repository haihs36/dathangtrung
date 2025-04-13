<?php
use yii\helpers\Url;
$this->title = 'Trả hàng thành công';
$this->params['breadcrumbs'][] = ['label' => 'Trả hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$disable                       = 'disabled';
$role                          = Yii::$app->user->identity->role;
$action = Yii::$app->controller->action->id;
?>

<?php //echo $this->render('@app/views/payment/_steps', ['action' =>$action]); ?>
<form>
    <div class="box-body pd0">
        <?php
        if (isset($order) && $order) {
            $total_shop       = 0; //tien thieu
            $total_coc        = 0; //tien thieu
            $money_shortage   = 0; //tien thieu
            $totalKg          = 0;
            $totalOrder       = 0;
            $stt              = 0;
            $totalkgShop      = 0;
            $totalShipmentFee = 0;

            foreach ($order as $key => $suplier) {
                $stt++;
                $shop            = reset($suplier);
                $currentOrder    = \common\models\TbOrders::findOne($shop['orderID']);
                $checked         = (isset($post[$shop['id']]['isCheck'])) ? true : false;
                $tbOrderSupplier = \common\models\TbOrderSupplier::findOne(['orderID' => $shop['orderID'], 'id' => $shop['id']]);
                if(!$tbOrderSupplier)
                    continue;
                ?>
                <div class="box shop-item clearfix" id="shop-<?= $shop['id'] ?>" data-order="<?= $shop['orderID'] ?>">
                    <input type="hidden" name="shop[<?= $shop['id'] ?>][orderID]" value="<?= $shop['orderID'] ?>">
                    <div class="box-header">
                        <h3 class="box-title shop-title">
                            <label><a target="_blank" href="<?= Url::toRoute(['orders/view', 'id' => $currentOrder->orderID]) ?>"><b >MĐH: <?= $currentOrder->identify ?></b></a>, <b >MKH: <?= $currentOrder->customerID ?></b></label>
                        </h3>
                        <div class="box-tools pull-right ">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body pd0">
                        <div class="shop-content">
                            <table class="data-item table table-striped table-hover table-bordered">
                                <thead style="display: none">
                                <tr>
                                    <th width="5%">Image</th>
                                    <th width="35%">Sản phẩm</th>
                                    <th width="15%" class="text-center">Giá</th>
                                    <th width="15%" class="text-center">Thành tiền</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                /*$totalPriceVN  = 0;
                                $totalQuantity = 0;
                                $totalPriceTQ  = 0;*/
                                $index = 0;
                                foreach ($suplier as $k => $item) {
                                    $index++;
                                    /* $totalPriceVN  += $item['totalPriceVn'];
                                     $totalPriceTQ  += $item['totalPrice'];
                                     $totalQuantity += $item['quantity'];*/
                                    ?>

                                    <tr data-shop_id="<?php echo $shop['id'] ?>" class="row-shop <?= ($index % 2 == 0 ? 'even' : 'odd') ?>">
                                        <td align="center">
                                            <div class="san-pham-item-image">
                                                <div class="image">
                                                    <a href="<?php echo htmlspecialchars($item['image']) ?>" target="_blank"><img width="50" height="50" src="<?php echo htmlspecialchars($item['image']) ?>"></a>
                                                    <div class="image-hover">
                                                        <img width="300px" src="<?php echo htmlspecialchars($item['image']) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="media">
                                                <div class="media-body">
                                                    <h4 class="media-heading">
                                                        <a href="<?= ($item['link']) ?>"><?php echo htmlspecialchars($item['title']) ?></a>
                                                    </h4>
                                                    <?php if (!empty($item['size'])) { ?>
                                                        <h5 class="media-heading">Kích thước:<span class="text-success"><strong><?= $item['size'] ?></strong>
                                                        </h5>
                                                    <?php } ?>
                                                    <?php if (!empty($item['color'])) { ?>
                                                        <h5 class="media-heading">Màu sắc:<span class="text-success"><strong><?= $item['color'] ?></strong>
                                                        </h5>
                                                    <?php } ?>
                                                    <?php if (!empty($item['status'])) { ?>
                                                        <span>Tình trạng hàng: </span>
                                                        <span class="text-success"><strong><?= \common\components\CommonLib::statusProduct($item['status']) ?></strong></span>
                                                    <?php } ?>
                                                    <?php if (!empty($item['noteProduct'])) { ?>
                                                        <div>
                                                            <span>Ghi chú: <?= trim($item['noteProduct']) ?></span>
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
                                                <h5 class="media-heading te">x<?php echo $item['quantity'] ?></h5>
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
                                                <h5 class="media-heading te">x<?php echo $item['quantity'] ?></h5>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (($role == ADMIN || $role == BUSINESS) && !in_array($currentOrder->status, [4, 6])) { ?>
                                                <div class="text-center">
                                                    <a data-id="<?php echo $item['id'] ?>" class="edit"
                                                            href="<?= Url::toRoute(['orders-detail/update', 'id' => $item['id']]) ?>"
                                                            title="Sửa"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                                    <a data-id="<?php echo $item['id'] ?>" class="confirm-delete"
                                                            href="<?= Url::toRoute(['orders-detail/delete', 'id' => $item['id'], 'orderSupplierID' => $tbOrderSupplier->id]) ?>"
                                                            title="Xóa"><i class="glyphicon glyphicon-trash"
                                                                aria-hidden="true"></i></a>
                                                </div>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="clear">
                        <table class="table table-bordered mb0">
                            <tfoot>
                            <tr>
                                <td colspan="5" class="border-top pd0 collapsed-b">
                                    <div class="box text-right none-border none-shadow">
                                        <!--thong ke tung shop-->
                                        <div class="box-header pl5">
                                            <input type="hidden" value="<?= $tbOrderSupplier->shippingStatus ?>" name="shop[<?= $shop['id'] ?>][shippingStatus]" id="shippingStatus-<?= $shop['id'] ?>">
                                            <label class="pull-right collap-shop-price">
                                                <?= \common\components\CommonLib::getIconStatus($tbOrderSupplier->status) ?>
                                                <b><?= \common\components\CommonLib::getShippingStatusByShop($tbOrderSupplier->shippingStatus) ?></b>
                                                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Chi tiết">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </label>
                                        </div>
                                        <!--xu ly shop-->
                                        <div class="box-body pd0" style="display: block;">
                                            <?php
                                            $disable_shop = '';
                                            if ($tbOrderSupplier->status == 4 || $tbOrderSupplier->status == 3 || ($currentOrder->status == 6 || $currentOrder->status == 5)) { // shop het hang va don hang o trang thai da tra hang
                                                $disable_shop = 'disabled';//het hang
                                            }

                                            $status = (!is_null($tbOrderSupplier->status) ? (int)$tbOrderSupplier->status : null);
                                            ?>
                                            <div class="rows">
                                                <table class="shop-process table table-bordered table-striped">
                                                    <tr>
                                                        <td>
                                                            <div class="grid-action">
                                                                <p>
                                                                    <i style="color: red">Chú ý: có nhiều mã vận đơn thì phải cách nhau dấu ;</i>
                                                                </p>
                                                                <p>
                                                                    <label>Order Number:</label>
                                                                    <label class="txt-cont">
                                                                        <input  <?= $disable_shop ?> <?= $disable ?>
                                                                                id="orderNumber-<?= $shop['id'] ?>" <?= ($role == WAREHOUSE ? 'disabled' : '') ?>
                                                                                class="<?= empty($tbOrderSupplier->shopProductID) ? 'billLadin' : '' ?>  input-xlarge form-control" name="shop[<?= $shop['id'] ?>][shopProductID]" type="text" value="<?= isset($tbOrderSupplier->shopProductID) ? $tbOrderSupplier->shopProductID : '' ?>" />
                                                                    </label>
                                                                </p>
                                                                <p>
                                                                    <label>Mã vận đơn:</label> <label class="txt-cont">
                                                                        <input  <?= $disable ?> <?= $disable_shop ?>
                                                                                id="mvd-<?= $shop['id'] ?>" <?= ($role == WAREHOUSE ? 'disabled' : '') ?>
                                                                                class="input-xlarge form-control <?= empty($tbOrderSupplier->billLadinID) ? 'billLadin' : '' ?> form-control" type="text" name="shop[<?= $shop['id'] ?>][billLadinID]" value="<?= !empty($tbOrderSupplier->billLadinID) ? $tbOrderSupplier->billLadinID : '' ?>" />

                                                                    </label>
                                                                </p>

                                                                <?php //} ?>
                                                                <p>
                                                                    <label> Phí phát sinh:(VND)</label>
                                                                    <label class="txt-cont">
                                                                        <input <?= $disable_shop ?>
                                                                                id="incurredFee-<?= $shop['id'] ?>" class="currency form-control" name="shop[<?= $shop['id'] ?>][incurredFee]" type="text" value="<?= isset($tbOrderSupplier->incurredFee) ? $tbOrderSupplier->incurredFee : 0 ?>" />
                                                                    </label>
                                                                </p>
                                                                <p>
                                                                    <label>Cân nặng(kg):</label>
                                                                    <label class="txt-cont">
                                                                        <input <?= $disable_shop ?> id="kg-<?= $shop['id'] ?>" class="form-control" <?= ($role == BUSINESS ? 'disabled' : '') ?>
                                                                                name="shop[<?= $shop['id'] ?>][weight]" type="text" value="<?= $tbOrderSupplier->weight >= 0 ? $tbOrderSupplier->weight : '' ?>" />
                                                                        <?php $totalKg += $tbOrderSupplier->weight ?>
                                                                    </label>
                                                                </p>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <p>
                                                                <label>Phí ship nội địa (¥):</label>
                                                                <label class="txt-cont">
                                                                    <input <?= $disable ?> <?= $disable_shop ?>
                                                                            id="shipFee-<?= $shop['id'] ?>" <?= (($role == WAREHOUSE || $role == WAREHOUSETQ) ? 'disabled' : '') ?>
                                                                            class="input-xlarge form-control" name="shop[<?= $shop['id'] ?>][shipmentFee]" type="text" value="<?= isset($tbOrderSupplier->shipmentFee) ? $tbOrderSupplier->shipmentFee : 0 ?>" />
                                                                </label>
                                                            </p>
                                                            <?php //} ?>
                                                            <?php if ($role == ADMIN || $role == BUSINESS) { ?>
                                                                <p>
                                                                    <label>Thanh toán thực tế:(¥)</label>
                                                                    <label class="txt-cont"><input <?= $disable ?> <?= $disable_shop ?>
                                                                                id="actualPayment-<?= $shop['id'] ?>" <?= ($role == WAREHOUSE || $role == WAREHOUSETQ) ? 'disabled' : '' ?>
                                                                                class="input-xlarge form-control" name="shop[<?= $shop['id'] ?>][actualPayment]" type="text" value="<?= $tbOrderSupplier->actualPayment > 0 ? $tbOrderSupplier->actualPayment : $tbOrderSupplier->shopPriceTQ ?>" /></label>
                                                                    <input type="hidden" id="actualChina-<?= $shop['id'] ?>" value="<?= $tbOrderSupplier->shopPriceTQ ?>" />

                                                                </p>
                                                            <?php } ?>
                                                            <!--het hang-->
                                                            <?php if ($tbOrderSupplier->status != 4) { ?>
                                                                <p>
                                                                    <?php
                                                                    //th da ve kho vn hoac huy don thi ko show shippingStatus = 3=> kho vn,status  = 5=>huy don,6=da tra hang

                                                                    if ($tbOrderSupplier->shippingStatus != 3 && !in_array($currentOrder->status, [5, 6])) {
                                                                        ?>
                                                                        <label class="checkbox checkbox-primary">
                                                                            <input <?= $disable ?> <?= $disable_shop ?> <?= (($role == WAREHOUSE || $role == WAREHOUSETQ) ? 'disabled' : '') ?>
                                                                                    class="shop-check-all" name="shop[<?= $shop['id'] ?>][isStock]" id="shop[<?= $shop['id'] ?>][isStock]" type="checkbox" <?= (($status == 3 || $tbOrderSupplier->isStock > 0) ? 'checked' : '') ?>>
                                                                            <label for="shop[<?= $shop['id'] ?>][isStock]">Hết hàng</label>
                                                                        </label>
                                                                    <?php } ?>

                                                                </p>
                                                            <?php } ?>
                                                            <p>
                                                                <?php //if (!in_array($currentOrder->status, [5, 6])) { ?>
                                                                <label class="checkbox checkbox-primary">
                                                                    <input <?= $disable ?> <?= $disable_shop ?> <?= (($role == WAREHOUSE || $role == WAREHOUSETQ) ? 'disabled' : '') ?>
                                                                            class="shop-check-all" name="shop[<?= $shop['id'] ?>][kgFee]" id="shop[<?= $shop['id'] ?>][kgFee]" type="checkbox" <?= ($tbOrderSupplier->kgFee > 0 ? 'checked' : '') ?>>
                                                                    <label for="shop[<?= $shop['id'] ?>][kgFee]">Mã vận đơn <= 1,5kg</label>
                                                                </label>
                                                            </p>
                                                            <p>
                                                                        <textarea <?= $disable ?> <?= $disable_shop ?> class="form-control" style="min-width: 50%" id="note-<?= $shop['id'] ?>" placeholder="Ghi chú đơn hàng"
                                                                                name="shop[<?= $shop['id'] ?>][noteInsite] ?>]"><?php echo !empty($tbOrderSupplier->noteInsite) ? $tbOrderSupplier->noteInsite : '' ?></textarea>
                                                            </p>
                                                        </td>
                                                        <td class="shop-result">
                                                            <div class="cart_option clear-fix box pad-10">
                                                                <?php if ($tbOrderSupplier->quantity) { ?>
                                                                    <p class="tienhang">Tổng Sản phẩm:
                                                                        <span><?php echo $tbOrderSupplier->quantity ?> </span>
                                                                    </p>
                                                                <?php } ?>
                                                                <?php if ($tbOrderSupplier->shopPriceTQ) { ?>
                                                                    <p class="tienhang">Tổng tiền hàng(¥):
                                                                        <span class="vnd-unit"><?php echo $tbOrderSupplier->shopPriceTQ ?> ¥</span>
                                                                    </p>
                                                                <?php } ?>

                                                                <?php if ($tbOrderSupplier->shipmentVn) { ?>
                                                                    <p class="tienhang">Tổng tiền ship:
                                                                        <span class="vnd-unit"><?php echo number_format(round($tbOrderSupplier->shipmentVn)) ?> </span>
                                                                    </p>
                                                                <?php } ?>
                                                                <?php if ($tbOrderSupplier->shopPrice) { ?>
                                                                    <p class="tienhang">Tổng phí dịch vụ:
                                                                        <span class="vnd-unit">
                                                                                <?php
                                                                                $phiDV = \common\components\CommonLib::getFeeSV($tbOrderSupplier->shopPrice + $tbOrderSupplier->shipmentVn, $currentOrder->discountDeals);
                                                                                echo number_format($phiDV);
                                                                                ?>
                                                                                </span>
                                                                    </p>
                                                                <?php } ?>
                                                                <?php if ($tbOrderSupplier->incurredFee) { ?>
                                                                    <p class="tienhang">Tổng phí phát sinh:
                                                                        <span class="vnd-unit"><?php echo number_format(round($tbOrderSupplier->incurredFee)) ?>
                                                                                                </span>
                                                                    </p>
                                                                <?php } ?>
                                                                <?php if ($tbOrderSupplier->kgFee) { ?>
                                                                    <p class="tienhang">Tổng phí mvd <= 1.5kg:
                                                                        <span class="vnd-unit"><?php echo number_format(round($tbOrderSupplier->kgFee)) ?>
                                                                                                </span>
                                                                    </p>
                                                                <?php } ?>
                                                                <?php if ($tbOrderSupplier->shopPriceKg) { ?>
                                                                    <p class="last">Tổng tiền cân nặng:
                                                                        <span class="vnd-unit"><?= number_format(round($tbOrderSupplier->shopPriceKg)) ?>
                                                                                                </span></p>
                                                                <?php } ?>
                                                                <p class="tienhang last"><b>Tổng tiền hàng</b>:
                                                                    <span class="vnd-unit">
                                                                        <?php echo number_format(round($tbOrderSupplier->shopPrice)) ?>

                                                                        <?php  $totalOrder += $tbOrderSupplier->shopPrice;  ?>
                                                                     </span>
                                                                </p>
                                                                <p>
                                                                    <?php
                                                                        $total_shop += $tbOrderSupplier->shopPriceTotal;
                                                                    ?>
                                                                    <b>Tổng tiền đơn</b>
                                                                    <span class="vnd-unit"><?php echo number_format(round($tbOrderSupplier->shopPriceTotal)) ?></span>
                                                                </p>
                                                                <?php if ($tbOrderSupplier->status != 4) { ?>
                                                                    <p>
                                                                        <?php
                                                                        //tinh % tien coc hang
                                                                        $datcoc   = round($tbOrderSupplier->shopPrice * $currentOrder->perCent / 100);
                                                                        $totalPay = $datcoc < $tbOrderSupplier->shopPriceTotal ? $tbOrderSupplier->shopPriceTotal - $datcoc : 0;
                                                                        if ($checked) {
                                                                            $total_coc += $datcoc;
                                                                        }
                                                                        ?>
                                                                        <b>∑ Tiền cọc:</b>
                                                                        <span class="vnd-unit"><?= number_format($datcoc) ?></span>
                                                                    </p>
                                                                    <p class="last">
                                                                        <?php //if($totalPay && $tbOrderSupplier->status != 4) { ?>
                                                                        <b>∑ Tiền còn thiếu:</b>
                                                                        <span class="vnd-unit">
                                                                                <?= number_format(round($totalPay)); ?>
                                                                                <?php
                                                                                if ($checked) {
                                                                                    $money_shortage += $totalPay;
                                                                                }
                                                                                ?>
                                                                        </span>
                                                                        <?php //} ?>
                                                                    </p>
                                                                <?php } ?>

                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php } ?>

            <div class="clear pb100" id="ResultCurrency">
                <div class="col-lg-12 text-right">
                    <p class="tienhang"> <b>Tổng cân nặng: <?= $totalKg ?></b> </p>
<!--                    <p class="tienhang">Tổng Tiền hàng: <b class="vnd-unit">--><?//= number_format(round($totalOrder)) ?><!-- </b> </p>-->
                    <p class="tienhang"><b>Tổng Tiền thanh toán: </b><b class="vnd-unit"><?= number_format(round($total_shop)) ?> </b> </p>
                    <p class="tienhang"><b>Tiền tiền còn nợ:</b> <b class="vnd-unit"><?= number_format(round($money_shortage)) ?> </b> </p>
                    <input  id="totalShop" type="hidden" value="<?= $total_shop ?>" />
                    <input id="totalShortage" type="hidden" value="<?= $money_shortage ?>" />
                    <p class="text-right">
                        <a target="_blank" data-url="<?= Url::toRoute(['payment/print','loid'=>$loID,'uid'=>$uid]) ?>" title="print" href="javascript:void(0)" class="btn-print btn btn-info">
                            <i class="glyphicon glyphicon-print" aria-hidden="true"></i> Print
                        </a>
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>
</form>
