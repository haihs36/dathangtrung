<?php

use yii\helpers\Html;

$province = \yii\helpers\ArrayHelper::map(\common\models\Province::find()->select(['id', 'name'])->all(), 'id', 'name');
$this->title = 'Giỏ hàng';
$this->params['breadcrumbs'][] = $this->title;
$setting = \Yii::$app->controller->setting;
$totalOrder = 0;
$totalOrderTQ = 0;
$totalOrderFee = 0;
$phitamtinh = 0;
$orderingFee = 0; //phi dat hang
$uLogin = Yii::$app->user->identity;
$access_token = $uLogin->access_token;

if (Yii::$app->session->hasFlash('success') || Yii::$app->session->hasFlash('error')) {
    $success = Yii::$app->session->hasFlash('success');
    $error = Yii::$app->session->hasFlash('error');
    $text_ss = Yii::$app->session->getFlash('success');
    $text_err = Yii::$app->session->getFlash('error');

    $type = '';
    if ($success) {
        $type = 'success';
        $message = $text_ss;
    } else if ($error) {
        $type = 'error';
        $message = $text_err;
    }

    if (empty($type)) return '';

    ?>
    <script>
        $(function () {
            swal({
                    title: "Thông báo",
                    text: "<?= $message ?>",
                    type: "<?= $type ?>",
                    confirmButtonClass: "btn-success"
                },
                function(){
                    location.reload();
                }
            );
        });
    </script>
<?php } ?>


<div class="cart-index">
    <div class=" total-price  clearfix text-right">
        <div class="pull-left">
            <div class="delete-all" style="line-height: 50px;">
                <label class="checkbox icheck"> <label> <input class="btn-check-all" type="checkbox"/>
                        <b>Chọn tất cả</b> </label> </label>
            </div>
        </div>
        <div class="pull-right">
            <ul class="list-inline">
                <li>Tổng số shop đã chọn: <span class="number-pr text-bold totalShopAll"><?= count($arrShopChecked) ?></span></li>
                <li>Tổng số sản phẩm :
                    <span class="number-pr text-bold totalQuantityAll"><?= $totalQuantityAll ?></span></li>
                <li>Tổng tiền tệ :
                    <span class="number-pr text-bold totalFinalTQAmountAll"><?= $totalFinalTQAmountAll ?></span>¥
                </li>
                <li>Tổng tiền :
                    <span class="number-pr text-bold price totalFinalAmountAll"><?= number_format($totalFinalAmountAll) ?></span>đ
                </li>
                <li>
                    <button type="button" class="btn-submit-all btn btn-lg btn-danger"
                            style="min-width: 200px;margin-left: 15px;">
                        <span class="glyphicon glyphicon-shopping-cart"></span> Gửi tất cả đơn
                    </button>
                </li>
            </ul>
        </div>
    </div>
    <?php
    if (isset($orderCart) && $orderCart) {
        ?>
        <?php
        $stt = 0;
        $amount = 0;
        $totalkgShop = 0;
        $totalShipmentFee = 0;
        foreach ($orderCart as $key => $suplier) {
            $checkAll = (isset($arrShopChecked[$key]) && count($arrShopChecked[$key]) == count($suplier)) ? 1 : 0;
            $stt++;
            $shop = reset($suplier);
            ?>
            <form action="<?php echo \yii\helpers\Url::toRoute(['/ecommerce/cart/index','page'=>$page]) ?>" method="post" class="frm-order don-hang-gio-hang-add-form" rel="<?php echo $stt ?>"
                  id="cart-add-form-<?php echo $stt ?>" accept-charset="UTF-8">
                <input type="hidden" name="_csrf" value="<?= $token ?>">
                <div class="shop-item box clearfix" data-shop_id="<?php echo Html::encode($key); ?>">
                    <div class="box-header with-border">
                        <h3 class="box-title shop-title">
                            <label class="checkbox icheck"> <label>
                                    <input class="shop-check-all" id="ckb_shop_<?= $stt ?>"
                                           name="shop_cart_item[<?php echo $key ?>][checked]"
                                           type="checkbox" <?php echo ($checkAll == 1) ? 'checked' : '' ?> >
                                    Shop: <?= Html::encode($shop['shop_name']) ?>
                                </label> </label>
                        </h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove">
                                <i class="fa fa-remove"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="cart-content">
                            <div class="col-sm-8 pl0">
                                <div class="list-product">
                                    <table class="data-item table table-striped table-hover table-bordered">
                                        <thead>
                                        <tr class="text-center">
                                            <th class="text-center" width="1%"></th>
                                            <th width="25%">Sản phẩm</th>
                                            <th width="15%" class="text-center">Số lượng</th>
                                            <th width="25%" class="text-center">Giá (¥)</th>
                                            <th width="25%" class="text-center">Thành tiền (¥)</th>
                                            <th width="10%" class="text-center">#</th>
                                        </tr>
                                        </thead>
                                    </table>
                                    <?php
                                    $totalPriceVN = 0;
                                    $totalQuantity = 0;
                                    $totalPriceTQ = 0;
                                    $index = 0;
                                    $isCheck = 0;
                                    foreach ($suplier as $k => $item) {
                                        $index++;
                                        if ($item['isCheck'] == 1) {
                                            $isCheck = 1;
                                            $totalPriceVN += $item['totalPriceVn'];
                                            $totalPriceTQ += $item['totalPrice'];
                                            $totalQuantity += $item['quantity'];
                                        }

                                        ?>
                                        <div class="data-item">
                                            <table class="table table-hover">
                                                <tbody>
                                                <tr data-shop_id="<?php echo $item['shop_id'] ?>"
                                                    class="row-shop <?= ($index % 2 == 0 ? 'even' : 'odd') ?>">
                                                    <td width="1%" align="center" class="first-checkbox">
                                                        <div class="checkbox mgt0">
                                                            <input <?php echo($item['isCheck'] == 1 ? 'checked' : '') ?>
                                                                    data-shopid="<?php echo $item['shop_id'] ?>"
                                                                    data-pid="<?php echo $item['id'] ?>"
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
                                                            <a target="_bank"
                                                               href="<?= htmlspecialchars($item['link']) ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                                                        </div>
                                                        <div class="media-heading">
                                                            Kích thước:
                                                            <span class="text-success"><b
                                                                        class="result"><?= $item['size'] ?></b></span>
                                                            <a href="javascript:void(0)" title="Sửa kích thước"
                                                               class="edit" data-pk="<?= $item['id'] ?>"
                                                               data-type="text" data-name="size" data-url="/update-cart"
                                                               data-value="<?= $item['size'] ?>"><i
                                                                        title="Sửa kích thước" data-toggle="tooltip"
                                                                        class="fa fa-fw fa-pencil"></i>
                                                            </a>

                                                        </div>
                                                        <div class="media-heading">
                                                            Màu sắc:
                                                            <span class="text-success"><b
                                                                        class="result"><?= $item['color'] ?></b></span>
                                                            <a href="javascript:void(0)" title="Sửa màu sắc"
                                                               class="edit" data-pk="<?= $item['id'] ?>"
                                                               data-type="text" data-name="color"
                                                               data-url="/update-cart"
                                                               data-value="<?= $item['color'] ?>"><i title="Sửa màu sắc"
                                                                                                     data-toggle="tooltip"
                                                                                                     class="fa fa-fw fa-pencil"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td width="15%" valign="top" class="calculate text-center">
                                                        <div class="input-quantity" data-toggle="tooltip"
                                                             data-original-title="Nhập số lượng bạn muốn mua">
                                                            <input <?php echo ($isCheck == 0) ? 'disabled' : '' ?>
                                                                    onblur="main.cart_update(<?php echo $item['id'] ?>,'<?php echo $item['shop_id'] ?>')"
                                                                    name="shop_cart_item[<?php echo $item['shop_id'] ?>][item][<?php echo $item['id'] ?>][qty]"
                                                                    type="number" min="1" max="500000"
                                                                    value="<?php echo $item['quantity'] ?>"
                                                                    class="choose-quantity qty-item-<?php echo $item['id'] ?>">

                                                            <span class="up_num" data-shopid="<?= $item['shop_id'] ?>"
                                                                  data-pid="<?php echo $item['id'] ?>"
                                                                  title="Nhấn lên để cập nhật số lượng"></span>
                                                            <span class="down_num" data-shopid="<?= $item['shop_id'] ?>"
                                                                  data-pid="<?php echo $item['id'] ?>"
                                                                  title="Nhấn xuống để cập nhật số lượng"></span>
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

                                                        <a data-id="<?php echo $item['id'] ?>"
                                                           class="cart-delete-item delete btn-actions"
                                                           data-toggle="tooltip" data-original-title="Xóa"
                                                           href="javascript:void(0);" title="Xóa">
                                                            <span class="btn bg-orange btn-xs"><i
                                                                        class="fa fa-trash"></i> Xóa</span>
                                                        </a>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="5">
                                                        <textarea <?php echo ($isCheck == 0) ? 'disabled' : '' ?>
                                                                onblur="main.cart_update(<?php echo $item['id'] ?>,'<?php echo $item['shop_id'] ?>')"
                                                                class="desc-<?php echo $item['id'] ?> form-control border-none"
                                                                name="shop_cart_item[<?php echo $item['shop_id'] ?>][item][<?php echo $item['id'] ?>][ghi_chu]"
                                                                rows="1" placeholder="Ghi chú cho sản phẩm"
                                                                style=""><?= $item['noteProduct'] ?></textarea>
                                                        <div class="module-float line-bottom"></div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php }
                                    $totalOrder += $totalPriceVN; //tong tien cac shop
                                    $totalOrderTQ += $totalPriceTQ; //tong tien cac shop tq
                                    $amount += $totalQuantity; //tong san pham cac shop
                                    ?>
                                </div>

                            </div>
                            <div class="col-sm-4 box-money ">
                                <div class=" thanh-tien shop-tong-stt-<?php echo $key ?> ">
                                    <div class="box-calculate text-right">
                                        <div class="clearfix line-bottom">
                                            <label class="col-sm-4 text-right">Số lượng:</label>
                                            <label class="col-sm-8 text-bold text-left pd0">
                                                <b class="qty"><?= $totalQuantity ?></b> </label>
                                        </div>
                                        <div class="clearfix line-bottom">
                                            <label class="col-sm-4 text-right">Phí kiểm đếm:</label>
                                            <label class="col-sm-8 text-bold text-left pd0"> <b class="vnd-unit">0</b> đ
                                            </label>
                                        </div>
                                        <div class="clearfix line-bottom">
                                            <label class="col-sm-4 text-right">Phí đóng gỗ:</label>
                                            <label class="col-sm-8 text-bold text-left pd0"> <b class="vnd-unit">0</b> đ
                                            </label>
                                        </div>
                                        <div class="clearfix line-bottom">
                                            <label class="col-sm-4 text-right">Tổng Tiền:</label>
                                            <label class="col-sm-8 text-bold text-left pd0">
                                                <b class="tt-te"><?= $totalPriceTQ ?></b><em>¥</em> =
                                                <b class="vnd-unit tt-vn"><?php echo number_format(round($totalPriceVN)) ?></b>
                                                đ
                                            </label>
                                        </div>
                                    </div>
                                    <div class="box-body no-padding">
                                        <div class="leftmain">
                                            <div class="form-group clearfix">
                                                <label class="col-sm-4 text-right">Kho đích: </label>
                                                <div class="col-sm-8 pd0">
                                                    <?php
                                                    $provinID = isset($uLogin->provinID) ? $uLogin->provinID : '';
                                                    echo Html::dropDownList('provinceID', $provinID,$province ,
                                                        ['prompt' => 'Chọn kho đích', 'class' => 'select2 required form-control', 'data-placeholder' => 'Chọn kho đích..', 'id' => 'provinceID-' . $stt]) ?>
                                                </div>
                                            </div>
                                            <div class="form-group clearfix">
                                                <label class="col-sm-4 text-right">Địa chỉ: </label>
                                                <div class="col-sm-8 pd0">
                                                    <input type="text" name="shipAddress"
                                                           value="<?php echo isset($uLogin->billingAddress) ? Html::encode($uLogin->billingAddress) : '' ?>"
                                                           id="shipAddress-<?php echo $stt ?>"
                                                           placeholder="Địa chỉ giao hàng"
                                                           maxlength="128"
                                                           class="form-control required">
                                                </div>
                                            </div>
                                            <div class="form-group clearfix">
                                                <label class="col-sm-4 text-right">Ghi chú: </label>
                                                <div class="col-sm-8 pd0">
                                                        <textarea class="form-control" style="margin-bottom: 10px"
                                                                  rows="2"
                                                                  placeholder="Ghi chú đơn hàng"
                                                                  name="ghi_chu"
                                                                  id="ghi_chu-<?php echo $stt ?>"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group clearfix text-right box-count">
                                                <label class="checkbox icheck"> <label>
                                                        <input type="checkbox" value="1" name="isBad"> Đơn săn sale
                                                    </label> </label> <label class="checkbox icheck"> <label>
                                                        <input type="checkbox" value="1" name="isBox"> Đóng gỗ </label>
                                                </label> <label class="checkbox icheck"> <label>
                                                        <input type="checkbox" value="1" name="isCheck"> Kiểm đếm
                                                    </label> </label>
                                            </div>
                                            <?php if (isset($setting['note_cart'])) { ?>
                                                <div class=" text-left">
                                                    <label>Lưu ý:</label><br>
                                                    <div style="margin: 10px 0;padding: 10px;text-align: justify;border: 1px solid #CCCCCC;color:#ff0000">
                                                        <?= trim($setting['note_cart']) ?>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="form-group clearfix mb15 mt15 text-right">
                                                <label class="pull-left"><a data-toggle="tooltip"
                                                                            data-original-title="Xóa shop"
                                                                            data-shop="<?php echo $key ?>"
                                                                            class="shop_cart_delete delete-shop btn btn-danger"
                                                                            href="javascript:void(0);">
                                                        <i class="fa fa-trash"></i> Xóa shop </a></label>
                                                <label class="pull-right">
                                                    <button <?php echo ($isCheck == 0) ? 'disabled' : '' ?>
                                                            class="btn btn-primary btnSendBuyNow">
                                                        <span class="glyphicon glyphicon-shopping-cart"></span> Gửi đơn
                                                    </button>
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php } ?>
        <div class="box-footer">
            <div class="text-center">
                <?php echo \yii\widgets\LinkPager::widget(['pagination'=>$pagination]);?>
            </div>
        </div>
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

<?php

$js = <<<JS
var site_name = location.origin;
main.getListCart(site_name,'$access_token');

main.editTable();
users.validQuantity("input.item-qty");

$('.btnSendBuyNow').on('click', function () {
    var form = $(this).closest('form.frm-order');
    var note = form.find('[name=ghi_chu]').val();
    note = removeXSS(note);
    
    form.find('[name=ghi_chu]').val(note);
    
    form.validate({
        onfocusout: function (e) {
            this.element(e);
        }
    });
    
    return true;
    
});

function removeXSS(content) {
    var arr_script_hack = ['onbeforecopy', 'onbeforecut', 'onbeforepaste', 'oncopy','oncut', 'oninput', 'onkeydown', 'onkeypress', 'onkeyup', 'onpaste', 'textInput', 'onabort', 'onbeforeunload', 'onhashchange', 'onload', 'onoffline', 'ononline', 'onreadystatechange', 'onstop', 'onunload', 'onreset', 'onsubmit', 'onclick', 'oncontextmenu', 'ondblclick', 'onlosecapture', 'onmouseenter', 'onmousedown', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onscroll', 'onmove', 'onmoveend', 'onmovestart', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onresize', 'onresizeend', 'onresizestart', 'onactivate', 'onbeforeactivate', 'onbeforedeactivate', 'onbeforeeditfocus', 'onblur', 'ondeactivate', 'onfocus', 'onfocusin', 'onfocusout', 'oncontrolselect', 'onselect', 'onselectionchange', 'onselectstart', 'onafterprint', 'onbeforeprint', 'onhelp', 'onerror', 'onerrorupdate', 'onafterupdate', 'onbeforeupdate', 'oncellchange', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onbounce', 'onfinish', 'onstart', 'onchange', 'onfilterchange', 'onpropertychange', 'onsearch', 'onmessage', 'CheckboxStateChange', 'DOMActivate', 'DOMAttrModified', 'DOMCharacterDataModified', 'DOMFocusIn', 'DOMFocusOut', 'DOMMouseScroll', 'DOMNodeInserted', 'DOMNodeInsertedIntoDocument', 'DOMNodeRemoved', 'DOMNodeRemovedFromDocument', 'DOMSubtreeModified', 'dragdrop', 'dragexit', 'draggesture', 'overflow', 'overflowchanged', 'RadioStateChange', 'underflow', 'FSCommand', 'onAbort', 'onActivate', 'onAfterPrint', 'onAfterUpdate', 'onBeforeActivate', 'onBeforeCopy', 'onBeforeCut', 'onBeforeDeactivate', 'onBeforeEditFocus', 'onBeforePaste', 'onBeforePrint', 'onBeforeUnload', 'onBeforeUpdate', 'onBegin', 'onBlur', 'onBouncewindow', 'onCellChange', 'onChange', 'onClick', 'onContextMenu', 'onControlSelect', 'onCopy', 'onCut', 'onDataAvailable', 'onDataSetChanged', 'onDataSetComplete', 'onDblClick', 'onDeactivate', 'onDrag', 'onDragEnd', 'onDragLeave', 'onDragEnter', 'onDragOver', 'onDragDrop', 'onDragStart', 'onDrop', 'onEnd', 'onError', 'onErrorUpdate', 'onFilterChange', 'onFinish', 'onFocus', 'onFocusIn', 'onFocusOut', 'onHashChange', 'onHelp', 'onInput', 'onKeyDown', 'onKeyPress', 'onKeyUp', 'onLayoutComplete', 'onLoad', 'onLoseCapture', 'onMediaComplete', 'onMediaError', 'onMessage', 'onMouseDown', 'onMouseEnter', 'onMouseLeave', 'onMouseMove', 'onMouseOut', 'onMouseOver', 'onMouseUp', 'onMouseWheel', 'onMove', 'onMoveEnd', 'onMoveStart', 'onOffline', 'onOnline', 'onOutOfSync', 'onPaste', 'onPause', 'onPopState', 'onProgress', 'onPropertyChange', 'onReadyStateChange', 'onRedo', 'onRepeat', 'onReset', 'onResize', 'onResizeEnd', 'onResizeStart', 'onResume', 'onReverse', 'onRowsEnter', 'onRowExit', 'onRowDelete', 'onRowInserted', 'onScroll', 'onSeek', 'onSelect', 'onSelectionChange', 'onSelectStart', 'onStart', 'onStop', 'onStorage', 'onSyncRestored', 'onSubmit', 'onTimeError', 'onTrackChange', 'onUndo', 'onUnload', 'onURLFlip', 'seekSegmentTime', 'behavior', 'xss:expression', 'javascript', 'alert', '<script>', '/script>','<','>','Cookies','cookies',';','(',')','+',']','[','"'];

    for (var i = 0; i < arr_script_hack.length; i++) {
        content = content.replace(arr_script_hack[i], '');
    }
    return content;
}

JS;
$this->registerJs($js);
?>
