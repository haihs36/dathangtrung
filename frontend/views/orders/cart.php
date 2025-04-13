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

   if (Yii::$app->session->hasFlash('success')):

   ?>
    <script>
        $(function () {
            swal({
                title: "Thông báo",
                text: "<?= Yii::$app->session->getFlash('success') ?>",
                type: "success",
                confirmButtonClass: "btn-success"
            });

            setTimeout(function() {
                location.reload();
            }, 3000);
        });
    </script>
<?php endif; ?>
<div class="cart-index">
    <div class=" total-price  clearfix text-right">
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
                <form action="/gio-hang" method="post" class="frm-order don-hang-gio-hang-add-form" id="cart-add-form-<?php echo $stt ?>" accept-charset="UTF-8">
                    <div class="shop-item clearfix" data-shop_id="<?php echo $key; ?>">
                        <div class="box-header with-border">
                            <h3 class="box-title shop-title">
                                <label class="checkbox icheck">
                                    <label>
                                        <input class="shop-check-all" id="ckb_shop_<?= $stt ?>" name="shop_cart_item[<?php echo $key ?>][checked]" type="checkbox" <?php echo ($checkAll == 1) ? 'checked' : '' ?> >
                                        Shop: <?= \yii\helpers\Html::encode($shop['shop_name']) ?>
                                    </label>
                                </label>
                            </h3>
                            <div class="box-tools pull-right ">
                                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Chi tiết"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body pd0 ">
                            <div class="cart-content">
                                <div class="col-sm-8 pl0">
                                    <div class="list-product">
                                        <table class="data-item table table-striped table-hover table-bordered">
                                            <thead>
                                            <tr class="text-center">
                                                <th class="text-center" width="1%">
                                                </th>
                                                <th width="25%">Sản phẩm</th>
                                                <th width="15%" class="text-center">Số lượng</th>
                                                <th width="25%" class="text-center">Giá (¥)</th>
                                                <th width="25%" class="text-center">Thành tiền (¥)</th>
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
                                                $index++;
                                                if($item['isCheck'] == 1) {
                                                    $isCheck = 1;
                                                    $totalPriceVN += $item['totalPriceVn'];
                                                    $totalPriceTQ += $item['totalPrice'];
                                                    $totalQuantity += $item['quantity'];
                                                }

                                                ?>
                                                <div class="data-item">
                                                    <table class="table table-hover">
                                                        <tbody>
                                                        <tr data-shop_id="<?php echo $item['shop_id'] ?>" class="row-shop <?= ($index % 2 == 0 ? 'even' : 'odd') ?>">
                                                            <td width="1%" align="center" class="first-checkbox">
                                                                <div class="checkbox mgt0">
                                                                    <input <?php echo ($item['isCheck']==1 ? 'checked' : '') ?>  data-shopid="<?php echo $item['shop_id'] ?>" data-pid="<?php echo $item['id'] ?>"
                                                                           id="shop-cart-item-<?php echo $item['id'] ?>-product-check"
                                                                           type="checkbox"
                                                                           name="shop_cart_item[<?php echo $item['shop_id'] ?>][item][<?php echo $item['id'] ?>][product_check]"
                                                                           value="1" class="form-checkbox check-item">
                                                                    <label for="shop-cart-item-<?php echo $item['id'] ?>-product-check"></label>
                                                                </div>
                                                            </td>
                                                            <td width="25%">
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
                                                                    <input <?php echo ($isCheck == 0) ? 'disabled':'' ?> onblur="main.cart_update(<?php echo $item['id'] ?>,'<?php echo $item['shop_id'] ?>')"
                                                                           name="shop_cart_item[<?php echo $item['shop_id'] ?>][item][<?php echo $item['id'] ?>][qty]"
                                                                           type="number" min="1" max="500000" value="<?php echo $item['quantity'] ?>" class="choose-quantity qty-item-<?php echo $item['id'] ?>">

                                                                    <span class="up_num" data-shopid="<?= $item['shop_id'] ?>" data-pid="<?php echo $item['id'] ?>" title="Nhấn lên để cập nhật số lượng"></span>
                                                                    <span class="down_num" data-shopid="<?= $item['shop_id'] ?>" data-pid="<?php echo $item['id'] ?>" title="Nhấn xuống để cập nhật số lượng"></span>
                                                                </div>

                                                            </td>
                                                            <td width="25%" class="text-center" data-th="Giá">
                                                                <div class="media-heading te china-unit-<?php echo $item['id'] ?>"
                                                                     data="<?php echo $item['unitPrice'] ?>">
                                                                    <b class=""><?php echo($item['unitPrice']) ?></b>
                                                                </div>
                                                                <div class=" media-heading vnd-unit vnd-unit-<?php echo $item['id'] ?>"
                                                                     data="<?php echo round($item['unitPriceVn']) ?>">
                                                                    <b><?php echo number_format(round($item['unitPriceVn'])) ?></b>
                                                                </div>
                                                            </td>
                                                            <td width="25%" class="text-center thanh-tien" data-th="Thành tiền">
                                                                <div class="clear media-heading china-unit-total china-unit-total-<?php echo $item['id'] ?>"
                                                                     data-te="<?php echo $item['totalPrice'] ?>">
                                                                    <b><?php echo $item['totalPrice'] ?></b>
                                                                </div>
                                                                <div class="media-heading vnd-unit vnd-unit-total vnd-unit-total-<?php echo $item['id'] ?>"
                                                                     data-vnd="<?php echo round($item['totalPriceVn']); ?>">
                                                                    <b><?php echo number_format(round($item['totalPriceVn'])); ?></b>
                                                                </div>
                                                            </td>
                                                            <td width="10%" class="text-center">

                                                                <a data-id="<?php echo $item['id'] ?>" class="cart-delete-item delete btn-actions" data-toggle="tooltip" data-original-title="Xóa"
                                                                   href="javascript:void(0);" title="Xóa">
                                                                    <span class="btn bg-orange btn-xs"><i class="fa fa-trash"></i> Xóa</span>
                                                                </a>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td colspan="5">
                                                                <textarea <?php echo ($isCheck == 0) ? 'disabled':'' ?> onblur="main.cart_update(<?php echo $item['id'] ?>,'<?php echo $item['shop_id'] ?>')" class="desc-<?php echo $item['id']  ?> form-control border-none" name="shop_cart_item[<?php echo $item['shop_id'] ?>][item][<?php echo $item['id'] ?>][ghi_chu]" rows="1" placeholder="Ghi chú cho sản phẩm" style=""><?= $item['noteProduct']  ?></textarea>
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
                                    <div class="pull-right ">
                                        <a data-toggle="tooltip" data-original-title="Xóa shop" data-shop="<?php echo $key ?>" class="shop_cart_delete delete-shop btn " href="javascript:void(0);">
                                            <span class="btn bg-orange btn-xs"><i class="fa fa-trash"></i> Xóa shop</span>

                                        </a>
                                    </div>
                                </div>
                                <div class="col-sm-4 box-money pr0">
                                    <div class="box thanh-tien shop-tong-stt-<?php echo $key ?> ">
                                        <div class="box-calculate text-right">
                                            <div class="clearfix">
                                                <label class="col-sm-4 text-right">Số lượng:</label>
                                                <label class="col-sm-8 text-bold text-left">
                                                    <b class="qty"><?= $totalQuantity ?></b>
                                                </label>
                                            </div>
                                            <div class="clearfix">
                                                <label class="col-sm-4 text-right">Phí kiểm đếm:</label>
                                                <label class="col-sm-8 text-bold text-left">
                                                    <b class="vnd-unit">0</b> đ
                                                </label>
                                            </div>
                                            <div class="clearfix">
                                                <label class="col-sm-4 text-right">Phí đóng gỗ:</label>
                                                <label class="col-sm-8 text-bold text-left">
                                                    <b class="vnd-unit">0</b> đ
                                                </label>
                                            </div>
                                            <div class="clearfix">
                                                <label class="col-sm-4 text-right">Tổng Tiền:</label>
                                                <label class="col-sm-8 text-bold text-left">
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
<!--                                                        <label class="checkbox icheck">-->
<!--                                                            <label>-->
<!--                                                                <input type="checkbox" value="1" name="isBad">-->
<!--                                                                Hàng dễ vỡ-->
<!--                                                            </label>-->
<!--                                                        </label>-->
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
<span class="ti-gia" style="display:none;"><?php echo  \common\components\CommonLib::getCNY($setting['CNY'],Yii::$app->user->identity->cny);  ?></span>


<?php
$token = Yii::$app->user->identity->access_token;
$js = <<<JS
      var site_name = location.origin;
      var token = '$token' ;

    window.postMessage({ type: "REQUEST_DATA"}, site_name);

        if (window.addEventListener) {
            window.addEventListener("message", listenSMS, false);
        } else {
            window.attachEvent("onmessage", listenSMS);
        }

        function listenSMS(evt){
            if (evt.origin !== site_name) {
                console.log('update error');
            }
            else {
                if(evt.data.error == 0){
                    var products = evt.data.item;
                    if(products.order_products.length > 0){
                        $.ajax({
                            url: "/list-cart",
                            type: 'POST',
                            async: false,
                            headers: {
                                'Authorization': 'Bearer '+token,
                                'contentType': 'application/json; charset=utf-8'
                            },
                            data: JSON.stringify(products.order_products),
                            contentType:"application/json; charset=utf-8",
                            dataType:"json",
                            success: function (res) {
                                if(res.success){
                                    window.postMessage({ type: "CLEAR_DATA"}, site_name);
                                    window.location.reload(true);
                                }
                            },
                            error: function (xmlRequest) {
                                console.log("Error");
                                console.log(xmlRequest);
                            }
                        });
                    }
                }
            }
        }

JS;
$this->registerJs($js);
?>

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
