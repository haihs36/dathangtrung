<?php

    use yii\helpers\Html;

    $this->title                   = 'Giỏ hàng';
    $this->params['breadcrumbs'][] = $this->title;
    $setting                       = \Yii::$app->controller->setting;
    $totalOrder                    = 0;
    $totalOrderTQ                  = 0;
    $totalOrderFee                 = 0;
    $phitamtinh                    = 0;
    $orderingFee                   = 0; //phi dat hang
    $uLogin                        = Yii::$app->user->identity;

?>
<?= \common\widgets\Alert::widget() ?>
<!--<script src="../js/jquery.validate.min.js"></script>-->
<?php //echo $this->render('@app/views/templates/_order_status_bar', ['status' => 0]); ?>

<div class="row clearfix">
    <div class="box-body">
        <div class="pd10 total-price box clearfix text-right">
            <div class="pull-left">
                <div class="delete-all" style="line-height: 50px;">
                    <label class="checkbox icheck">
                        <label>
                            <input class="btn-check-all" type="checkbox"/> <b>Chọn tất cả</b>
                        </label>
                    </label>
                </div><!-- end .delete-all-->
            </div>
            <div class="pull-right">
                <ul class="list-inline">
                    <li>Tổng số shop: <span class="number-pr text-bold totalShopAll"><?= $totalShopAll ?></span></li>
                    <li>Tổng số sản phẩm :
                        <span class="number-pr text-bold totalQuantityAll"><?= $totalQuantityAll ?></span></li>
                    <li>Tổng tiền tệ :
                        <span class="number-pr text-bold totalFinalTQAmountAll"><?= $totalFinalTQAmountAll ?></span>¥
                    </li>
                    <li>Tổng tiền :
                        <span class="number-pr text-bold price totalFinalAmountAll"><?= number_format($totalFinalAmountAll) ?></span>đ
                    </li>
                    <li>
                        <button  type="button" class="btn-submit-all btn btn-lg btn-danger" style="min-width: 200px;margin-left: 15px;">
                          <span class="glyphicon glyphicon-shopping-cart"></span>  Đặt tất cả
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <?php
            if (isset($order) && $order) {
                ?>
                <?php
                $stt              = 0;
                $amount           = 0;
                $totalkgShop      = 0;
                $totalShipmentFee = 0;
                foreach ($order as $key => $suplier) {
                    $checkAll = (isset($arrShopChecked[$key]) && count($arrShopChecked[$key]) == count($suplier)) ? 1 : 0;
                    $stt++;
                    $shop = reset($suplier);
                    ?>
                    <form action="/gio-hang" method="post" class="frm-order don-hang-gio-hang-add-form"
                          id="cart-add-form-<?php echo $stt ?>" accept-charset="UTF-8">
                        <div class="box shop-item clearfix" data-shop_id="<?php echo $key; ?>">
                            <div class="box-header">
                                <h3 class="box-title shop-title">
                                    <label class="checkbox icheck">
                                        <label>
                                            <input class="shop-check-all" id="ckb_shop_<?= $stt ?>" name="shop_cart_item[<?php echo $key ?>][checked]" type="checkbox" <?php echo ($checkAll == 1) ? 'checked' : '' ?> >
                                            Shop: <?= \yii\helpers\Html::encode($shop['shop_name']) ?>
                                        </label>
                                    </label>
                                </h3>
                                <div class="box-tools pull-right ">
                                    <a data-toggle="tooltip" data-original-title="Xóa shop" data-shop="<?php echo $key ?>" class="shop_cart_delete delete-shop btn " href="javascript:void(0);">
                                        <i style="color: red;font-size: 20px;" class="fa fa-trash-o" aria-hidden="true"></i>
                                        Xóa shop
                                    </a>
                                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                            title="Chi tiết">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body pd0 ">
                                <div class="cart-content">
                                    <div class="col-sm-8">
                                        <div class="box list-product">
                                            <table class="data-item table table-striped table-hover table-bordered">
                                                <thead>
                                                <tr class="text-center">
                                                    <th class="text-center" width="1%">
                                                    </th>
                                                    <th width="42%">Sản phẩm</th>
                                                    <th width="15%" class="text-center">Số lượng</th>
                                                    <th width="20%" class="text-center">Giá (¥)</th>
                                                    <th width="20%" class="text-center">Thành tiền (¥)</th>
                                                    <th width="10%" class="text-center">#</th>
                                                </tr>
                                                </thead>
                                            </table>
                                            <?php
                                                $totalPriceVN  = 0;
                                                $totalQuantity = 0;
                                                $totalPriceTQ  = 0;
                                                $index         = 0;
                                                $isCheck = 0;
                                                foreach ($suplier as $k => $item) {
                                                 //   pr($item['md5']);die;
                                                    $index++;
                                                    if($item['isCheck'] == 1) {
                                                        $isCheck = 1;
                                                        $totalPriceVN += $item['totalPriceVn'];
                                                        $totalPriceTQ += $item['totalPrice'];
                                                        $totalQuantity += $item['quantity'];
                                                    }
                                                    ?>
                                                    <div class="data-item">
                                                        <table class="table table-striped table-hover table-bordered">
                                                            <tbody>
                                                            <tr data-shop_id="<?php echo $item['shop_id'] ?>" class="row-shop <?= ($index % 2 == 0 ? 'even' : 'odd') ?>">
                                                                <td width="1%" align="center" class="first-checkbox">
                                                                    <div class="checkbox mgt0">
                                                                        <input <?php echo ($item['isCheck']==1 ? 'checked' : '') ?>  data-shopid="<?php echo $item['shop_id'] ?>" data-pid="<?php echo $item['md5'] ?>"
                                                                               id="shop-cart-item-<?php echo $item['md5'] ?>-product-check"
                                                                               type="checkbox"
                                                                               name="shop_cart_item[<?php echo $item['shop_id'] ?>][item][<?php echo $item['md5'] ?>][product_check]"
                                                                               value="1" class="form-checkbox check-item">
                                                                        <label for="shop-cart-item-<?php echo $item['md5'] ?>-product-check"></label>
                                                                    </div>
                                                                </td>
                                                                <td width="42%">
                                                                    <div class="pull-left mr10 mb10 san-pham-item-image">
                                                                        <div class="image">
                                                                            <a href="<?php echo htmlspecialchars($item['image']) ?>"
                                                                               target="_blank"><img width="50" height="50"
                                                                                                    src="<?php echo htmlspecialchars($item['image']) ?>"></a>
                                                                            <div class="image-hover">
                                                                                <img width="300px"
                                                                                     src="<?php echo htmlspecialchars($item['image']) ?>">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="media-heading">
                                                                        <a target="_bank" href="<?= ($item['link']) ?>"><?php echo \yii\helpers\StringHelper::truncate(htmlspecialchars($item['title']), 20) ?></a>
                                                                    </div>
                                                                    <div class="media-heading">
                                                                        Kích thước:
                                                                        <span class="text-success"><b><?= $item['size'] ?></b></span>
                                                                    </div>
                                                                    <div class="media-heading">
                                                                        Màu sắc:
                                                                        <span class="text-success"><b><?= $item['color'] ?></b></span>
                                                                    </div>
                                                                </td>
                                                                <td width="15%" valign="top" class="calculate text-center">
                                                                    <div class="input-quantity" data-toggle="tooltip" data-original-title="Nhập số lượng bạn muốn mua">
                                                                        <input <?php echo ($isCheck == 0) ? 'disabled':'' ?> onblur="main.cart_update('<?php echo $item['md5'] ?>','<?php echo $item['shop_id'] ?>')"
                                                                               name="shop_cart_item[<?php echo $item['shop_id'] ?>][item][<?php echo $item['md5'] ?>][qty]"
                                                                               type="number" min="1" max="50000" value="<?php echo $item['quantity'] ?>" class="choose-quantity qty-item-<?php echo $item['md5'] ?>">

                                                                        <span class="up_num" data-shopid="<?= $item['shop_id'] ?>" data-pid="<?php echo $item['md5'] ?>" title="Nhấn lên để cập nhật số lượng"></span>
                                                                        <span class="down_num" data-shopid="<?= $item['shop_id'] ?>" data-pid="<?php echo $item['md5'] ?>" title="Nhấn xuống để cập nhật số lượng"></span>
                                                                    </div>

                                                                </td>
                                                                <td width="20%" class="text-center" data-th="Giá">
                                                                    <div class="media-heading te china-unit-<?php echo $item['md5'] ?>"
                                                                         data="<?php echo $item['unitPrice'] ?>">
                                                                        <b class=""><?php echo($item['unitPrice']) ?></b>
                                                                    </div>
                                                                    <div class=" media-heading vnd-unit vnd-unit-<?php echo $item['md5'] ?>"
                                                                         data="<?php echo round($item['unitPriceVn']) ?>">
                                                                        <b><?php echo number_format(round($item['unitPriceVn'])) ?></b>
                                                                    </div>
                                                                </td>
                                                                <td width="20%" class="text-center thanh-tien" data-th="Thành tiền">
                                                                    <div class="clear media-heading china-unit-total china-unit-total-<?php echo $item['md5'] ?>"
                                                                         data-te="<?php echo $item['totalPrice'] ?>">
                                                                        <b><?php echo $item['totalPrice'] ?></b>
                                                                    </div>
                                                                    <div class="media-heading vnd-unit vnd-unit-total vnd-unit-total-<?php echo $item['md5'] ?>"
                                                                         data-vnd="<?php echo round($item['totalPriceVn']); ?>">
                                                                        <b><?php echo number_format(round($item['totalPriceVn'])); ?></b>
                                                                    </div>
                                                                </td>
                                                                <td width="10%" class="text-center">

                                                                    <a data-id="<?php echo $item['md5'] ?>" class="cart-delete-item delete btn-actions" data-toggle="tooltip" data-original-title="Xóa"
                                                                       href="javascript:void(0);" title="Xóa">
                                                                        <span class="btn bg-orange btn-xs"><i class="fa fa-trash"></i> Xóa</span>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td colspan="5">
                                                                    <textarea <?php echo ($isCheck == 0) ? 'disabled':'' ?> onblur="main.cart_update('<?php echo $item['md5'] ?>','<?php echo $item['shop_id'] ?>')" class="desc-<?php echo $item['md5']  ?> form-control border-none" name="shop_cart_item[<?php echo $item['shop_id'] ?>][item][<?php echo $item['md5'] ?>][ghi_chu]" rows="1" placeholder="Ghi chú cho sản phẩm" style=""><?= isset($item['noteProduct']) ? $item['noteProduct'] : '' ?></textarea>
                                                                    <div class="module-float line-bottom"></div>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php }
                                                $totalOrder   += $totalPriceVN; //tong tien cac shop
                                                $totalOrderTQ += $totalPriceTQ; //tong tien cac shop tq
                                                $amount       += $totalQuantity; //tong san pham cac shop
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 box-money">
                                        <div class="box thanh-tien shop-tong-stt-<?php echo $key ?> ">
                                            <div class="box-calculate text-right">
                                                <div class="clearfix">
                                                    <label class="col-sm-4 text-right">Số lượng:</label>
                                                    <label class="col-sm-8 text-bold">
                                                        <b class="qty"><?= $totalQuantity ?></b>
                                                    </label>
                                                </div>
                                                <div class="clearfix">
                                                    <label class="col-sm-4 text-right">Phí kiểm đếm:</label>
                                                    <label class="col-sm-8 text-bold">
                                                        <b class="vnd-unit">0</b> đ
                                                    </label>
                                                </div>
                                                <div class="clearfix">
                                                    <label class="col-sm-4 text-right">Phí đóng gỗ:</label>
                                                    <label class="col-sm-8 text-bold">
                                                        <b class="vnd-unit">0</b> đ
                                                    </label>
                                                </div>
                                                <div class="clearfix">
                                                    <label class="col-sm-4 text-right">Tổng Tiền:</label>
                                                    <label class="col-sm-8 text-bold">
                                                        <b class="tt-te"><?= $totalPriceTQ ?></b><em>¥</em>  = <b class="vnd-unit tt-vn"><?php echo number_format(round($totalPriceVN)) ?></b> đ
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="box-body">
                                                <div class="text-right">
                                                    <div class="form-group clearfix">
                                                        <label class="col-sm-4">Kho đích: </label>
                                                        <div class="col-sm-8 pd0">
                                                            <?php
                                                                $provinID = isset($uLogin->provinID) ? $uLogin->provinID : '';
                                                                echo Html::dropDownList('provinceID', $provinID, \yii\helpers\ArrayHelper::map(\common\models\Province::find()->select(['id', 'name'])->all(), 'id', 'name'),
                                                                    ['prompt' => 'Chọn kho đích', 'class' => ' required form-control', 'data-placeholder' => 'Chọn kho đích..', 'id' => 'provinceID-' . $stt]) ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group clearfix">
                                                        <label class="col-sm-4">Địa chỉ: </label>
                                                        <div class="col-sm-8 pd0">
                                                            <input type="text" name="shipAddress"
                                                                   value="<?php echo isset($uLogin->billingAddress) ? $uLogin->billingAddress : '' ?>"
                                                                   id="shipAddress-<?php echo $stt ?>"
                                                                   placeholder="Địa chỉ giao hàng"
                                                                   maxlength="128"
                                                                   class="form-control required">
                                                        </div>
                                                    </div>
                                                    <div class="form-group clearfix">
                                                        <label class="col-sm-4">Ghi chú: </label>
                                                        <div class="col-sm-8 pd0">
                                                            <textarea class="form-control" style="margin-bottom: 10px" rows="2"
                                                                      placeholder="Ghi chú đơn hàng"
                                                                      name="ghi_chu"  id="ghi_chu-<?php echo $stt ?>"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group clearfix text-right box-count">
                                                        <label class="checkbox icheck">
                                                            <label>
                                                                <input type="checkbox" value="1" name="isBad">
                                                                Hàng dễ vỡ
                                                            </label>
                                                        </label>
                                                        <label class="checkbox icheck">
                                                            <label>
                                                                <input type="checkbox" value="1" name="isBox">
                                                                Đóng gỗ
                                                            </label>
                                                        </label>
                                                        <label class="checkbox icheck">
                                                            <label>
                                                                <input type="checkbox" value="1" name="isCheck">
                                                                Kiểm đếm
                                                            </label>
                                                        </label>
                                                    </div>
                                                    <?php if (isset($setting['note_cart'])) { ?>
                                                        <div class=" text-left">
                                                            <label>Lưu ý:</label><br>
                                                            <div style="margin: 10px 0;padding: 10px;text-align: justify;border: 1px solid #CCCCCC;color:#ff0000">
                                                                <?= trim($setting['note_cart']) ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                                <!-- <input class="pull-left form-control" style="width: 50%" placeholder="Ghi chú shop" name="shop_cart_item[<?php echo $key ?>][ghi_chu]" type="text" />        -->

                                            </div>
                                            <div class="module-float line-bottom" style="margin: 10px 0"></div>


                                        </div>
                                        <div class="pull-right mb15">
                                            <button <?php echo ($isCheck == 0) ? 'disabled':'' ?> class="btn btn-danger btnSendBuyNow">
                                                <span class="glyphicon glyphicon-shopping-cart"></span>
                                                Đặt hàng
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php } ?>

            <?php } else {
                ?>
                <div class="callout callout-danger lead text-center">
                    <h4><i class="fa fa-fw fa-warning"></i> Thông báo</h4>
                    <p class="cart-empty">
                        Không có sản phẩm nào trong giỏ hàng<br>
                        <a href="/dat-hang"><i class="fa fa-undo" aria-hidden="true"></i> Quay lại trang đặt hàng</a>
                    </p>
                </div>
            <?php } ?>
    </div>
</div>
<span class="ti-gia" style="display:none;"><?php echo $setting['CNY'] ?></span>
<script>

    $(document).ready(function () {

        users.validQuantity("input.item-qty");


        $('.btnSendBuyNow').on('click', function () {
            var form = $(this).closest('form.frm-order');
            form.validate({
                onfocusout: function (e) {
                    this.element(e);
                }
            });
        });


        jQuery.extend(jQuery.validator.messages, {
            required: "Trường này là bắt buộc",
            remote: "Please fix this field.",
            email: "Please enter a valid email address.",
            url: "Please enter a valid URL.",
            date: "Please enter a valid date.",
            dateISO: "Please enter a valid date (ISO).",
            number: "Vui lòng nhập một số điện thoại hợp lệ.",
            digits: "Please enter only digits.",
            creditcard: "Please enter a valid credit card number.",
            equalTo: "Please enter the same value again.",
            accept: "Please enter a value with a valid extension.",
            maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
            minlength: jQuery.validator.format("Please enter at least {0} characters."),
            rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
            range: jQuery.validator.format("Please enter a value between {0} and {1}."),
            max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
            min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
        });
    });
</script>
