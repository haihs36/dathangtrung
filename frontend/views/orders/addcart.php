<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use common\components\CommonLib;
    $setting = \Yii::$app->controller->setting;
    $totalOrder = 0;
    $totalOrderTQ = 0;
    $totalOrderFee = 0;
    $phitamtinh = 0;
    $orderingFee = 0; //phi dat hang

?>
<div class="clear-fix">
    <div class="CenterHead">
        <a href="/gio-hang">Trang chủ/Giỏ hàng</a>
    </div>
</div>
<?php echo $this->render('@app/views/templates/_order_status_bar',['status' => 0]); ?>
<div class="region region-content">
    <div class="hide" style="display: none">
        <span id="orderFee">0</span> <span id="freeCount">0</span> <span id="shipmentFee">0</span>
    </div>
    <?php
        if (isset($order) && $order) {
            ?>
            <form class="don-hang-gio-hang-add-form" action="/gio-hang" method="post" id="don-hang-gio-hang-add-form" accept-charset="UTF-8">
                <div>
                    <span class="ti-gia" style="display:none;"><?php echo $setting['CNY'] ?></span>
                    <?php
                        $stt = 0;
                        $amount = 0;
                        foreach ($order as $key => $suplier) {
                            $stt++;
                            $item = reset($suplier);
                            ?>
                            <div class="shop-item" data-shop_id="<?php echo $key; ?>">
                                <h3>
                                    <input class="shop-check-all" checked="checked" type="checkbox">Shop: <?php echo htmlspecialchars($item['shop_name']) ?>
                                </h3>
                                <table class="data-item">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th width="45%">Sản phẩm</th>
                                        <th>SL</th>
                                        <th>Giá</th>
                                        <th>Thành tiền</th>
                                        <th width="50px">#</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $totalPriceVN = 0;
                                        $totalPriceTQ = 0;
                                        $totalQuantity = 0;
                                        foreach ($suplier as $k => $item) {
                                            $totalPriceVN += $item['totalPriceVn'];
                                            $totalPriceTQ += $item['totalPrice'];
                                            $totalQuantity += $item['quantity'];
                                            ?>
                                            <tr data-shop_id="<?php echo $key ?>" class="row-shop">
                                                <td>
                                                    <input type="checkbox" id="edit-shop-cart-item-0-item-<?php echo $item['id'] ?>-product-check" name="shop_cart_item[<?php echo $key ?>][item][<?php echo $item['id'] ?>][product_check]" value="1" checked="checked" class="form-checkbox check-item" />
                                                    <div class="san-pham-item-image">
                                                        <div class="image">
                                                            <a href="<?php echo htmlspecialchars($item['image']) ?>" target="_blank"><img width="75" height="75" src="<?php echo htmlspecialchars($item['image']) ?>"></a>
                                                            <div class="image-hover">
                                                                <img width="300" src="<?php echo htmlspecialchars($item['image']) ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>

                                                    <div class="content-detail-product">
                                                        <a href="<?php echo htmlspecialchars($item['link']) ?>" target="_bank"><?php echo htmlspecialchars($item['title']) ?></a>
                                                        <div class="property">
                                                            <div style="margin-top: 5px; font-size: 12px" class="ng-scope">
                                                                <span class="ng-binding">Kích thước</span>:
                                                                <strong class="ng-binding"><?= $item['size'] ?></strong>
                                                            </div>
                                                            <div style="margin-top: 5px; font-size: 12px" ng-repeat="propuctProp in product.CartProductPropDto" class="ng-scope">
                                                                <span class="ng-binding">Màu sắc</span>:
                                                                <strong class="ng-binding"><?= $item['color'] ?></strong>
                                                            </div>
                                                            <div class="form-item form-type-textarea form-item-shop-cart-item-0-item-1-ghi-chu">
                                                                <div class="textarea-processed">
                                                                    <input type="text"  placeholder="Ghi chú sản phẩm" name="shop_cart_item[<?php echo $key ?>][item][<?php echo $item['id'] ?>][ghi_chu]" class="form-textarea" value="<?= $item['noteProduct'] ?>" />
                                                                    <div class="grippie"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td valign="top">
                                                    <div class="form-item text-center  form-type-textfield ">
                                                        <input onkeyup="main.cart_update('<?php echo $item['id'] ?>','<?php echo $key ?>')" class="item-qty qty-shop-<?php echo $item['shop_id'] ?> qty-item-<?php echo $item['id'] ?> form-text" type="text" id="edit-shop-cart-item-0-item-<?php echo $item['id'] ?>-qty" name="shop_cart_item[<?php echo $key ?>][item][<?php echo $item['id'] ?>][qty]" value="<?php echo htmlspecialchars($item['quantity']) ?>" size="60" maxlength="128">
                                                    </div>
                                                </td>
                                                <td valign="top">
                                                    <div class=" text-center">
                                                    <span data="<?php echo round($item['unitPriceVn']) ?>" class="vnd-unit vnd-unit-<?php echo $item['id'] ?>">
                                                        <b><?php echo number_format(round($item['unitPriceVn'])) ?></b>
                                                        <em>đ</em>
                                                    </span><br>
                                                        <span data="<?php echo $item['unitPrice'] ?>" class="china-unit china-unit-<?php echo $item['id'] ?>"><em>¥</em><?php echo $item['unitPrice'] ?></span>
                                                    </div>
                                                </td>

                                                <td valign="top">
                                                    <div class="thanh-tien text-center">
                                                       <span class="vnd-unit vnd-unit-total vnd-unit-total-<?php echo $item['id'] ?>" data-vnd=" <?php echo round($item['totalPriceVn']); ?>"><b>
                                                               <?php echo number_format(round($item['totalPriceVn'])); ?>
                                                           </b><em>đ</em></span><br>
                                                        <span class="china-unit china-unit-total china-unit-total-<?php echo $item['id'] ?>"><em>¥</em><b><?php echo $item['totalPrice'] ?></b></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-block clear-fix">
                                                        <a data-id="<?php echo $item['id'] ?>" class="edit" href="<?= \yii\helpers\Url::toRoute(['orders/update', 'id' => $item['id']]) ?>" title="Sửa">
                                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                                        </a>
                                                        <a data-id="<?php echo $item['id'] ?>" class="cart-delete-item delete" href="javascript:void(0);" title="Xóa"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php }
                                        $totalOrder += $totalPriceVN; //tong tien cac shop
                                        $totalOrderTQ += $totalPriceTQ; //tong tien cac shop tq
                                        $amount += $totalQuantity; //tong san pham cac shop
                                    ?>
                                    </tbody>
                                </table>
                                <!--don hang ncc-->
                                <div class="shop-tong shop-tong-stt-<?php echo $key ?>">
                                    <ul class="table-price">
                                        <li class="tong-sanpham">
                                            <span>∑ Sản phẩm: </span>
                                            <b class="value"><?= $totalQuantity ?></b>
                                        </li>
                                        <li class="thanh-tien-tq">
                                            <span>∑ Tiền hàng (¥):</span>
                                            <b class="price value"><?php echo $totalPriceTQ ?></b><em>¥</em>
                                        </li>
                                        <li class="thanh-tien">
                                            <span>∑ Tiền hàng:</span>
                                            <b class="price value"><?php echo number_format(round($totalPriceVN)) ?></b><em>đ</em>
                                        </li>
                                        <!-- <li class="tong-tien-theo-shop">
                                            <span>∑ Tiền shop:</span>
                                            <b class="value"><?php /*echo number_format(round($totalPriceVN)) */?></b><em>đ</em>
                                        </li>-->
                                    </ul>
                                    <div class="shop-del">
                                        <a data-shop="<?php echo $key ?>"  class="shop_cart_delete delete-shop delete-shop-0" href="javascript:void(0);">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i> Xóa shop
                                        </a>
                                        <input placeholder="Ghi chú shop" name="shop_cart_item[<?php echo $key ?>][ghi_chu]" type="text" />
                                    </div>
                                </div>


                            </div>
                        <?php  } ?>
                </div>
                <div class="info">
                    <div class="address">
                        <div class="wrap-title"><i class="fa fa-map-marker" style="margin-right: 10px;"></i> Địa chỉ nhận hàng</div>
                        <div class="wrap-content">
                            <div class="form-item form-type-textfield form-item-fullname">
                                <label for="edit-fullname">Họ và tên <span class="form-required" title="Trường dữ liệu này là bắt buộc.">*</span></label>
                                <input type="text" id="edit-fullname" name="fullname" value="<?php echo htmlspecialchars($customer->fullname) ?>" size="60" maxlength="128" class="form-text required">
                            </div>
                            <div class="form-item form-type-textfield form-item-phone">
                                <label for="edit-phone">Số điện thoại <span class="form-required" title="Trường dữ liệu này là bắt buộc.">*</span></label>
                                <input type="text" id="edit-phone" name="phone" value="<?php echo htmlspecialchars($customer->phone) ?>" size="60" maxlength="128" class="form-text required">
                            </div>
                            <div class="form-item-list">
                                <div class="form-item form-type-textfield form-item-address">
                                    <label for="edit-address">Địa chỉ nhận hàng <span class="form-required" title="Trường dữ liệu này là bắt buộc.">*</span></label>
                                    <input type="text" id="edit-address" name="address" value="<?php echo htmlspecialchars($customer->billingAddress) ?>" size="60" maxlength="128" class="form-text required">
                                </div>

                            </div>
                            <div class="form-item form-type-textarea form-item-ghichu">
                                <label for="edit-ghichu">Tỉnh/Tp </label>
                                <div class="form-item form-type-select form-item-city ">
                                    <?php
                                        $city = \yii\helpers\ArrayHelper::map(\cms\models\TbCities::find()->select(['CityCode', 'CityName'])->asArray()->all(), 'CityCode', 'CityName');
                                        $customer->cityCode = $customer->cityCode ? $customer->cityCode : null;
                                        echo Html::dropDownList('city', $customer->cityCode,$city);
                                    ?>
                                </div>
                            </div>
                            <div class="form-item form-type-textarea form-item-ghichu">
                                <label for="edit-ghichu">Quận/Huyện </label>
                                <div class="form-item form-type-select form-item-city ">
                                    <?php
                                        $district = \yii\helpers\ArrayHelper::map(\cms\models\TbDistricts::find()->select(['DistrictId', 'DistrictName'])->asArray()->all(), 'DistrictId', 'DistrictName');
                                        $customer->districtId = $customer->districtId ? $customer->districtId : null;
                                        echo Html::dropDownList('district', $customer->districtId,$district);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--tong don hang-->
                    <div class="box-result">
                        <div data-product-count="1" class="cart-total-money tong-tien-tam-tinh">
                            <p class="tong-sanpham">
                                <label class="txt">∑ Sản phẩm:</label>
                                <label class="info"><span><b class="value"><?= $amount ?></b></span></label>
                            </p>
                            <p class="phi-dat-hang">
                                <?php
                                    $trans = CommonLib::getFeeDV($totalOrder); //$setting['TRANS'];//
                                    $totalOrderFee = round(($totalOrder*$trans)/100);
                                ?>
                                <label class="txt">∑ Tiền phí đặt hàng (<?= $trans ?>%):</label>
                                <label class="info">
                                    <span class="price"><b data-price="<?= $totalOrderFee ?>"><?= number_format($totalOrderFee) ?></b><em>đ</em></span>
                                </label>
                            </p>
                            <p class="tong-tien-hang-tq">
                                <label class="txt">∑ Tiền đặt hàng(¥):</label>
                                <label class="info">
                                    <span><b><?php echo $totalOrderTQ ?></b><em>¥</em></span>
                                </label>
                            </p>
                            <p class="tong-tien-hang">
                                <label class="txt">∑ Tiền đặt hàng:</label>
                                <label class="info"><span><b><?php echo number_format($totalOrder) ?></b><em>đ</em></span></label>
                            </p>
                            <p class="tong-tien">
                                <label class="txt">∑ Tiền đơn hàng:</label>
                                <label class="info">
                                    <span><b><?php echo number_format(round($totalOrder + $totalOrderFee)) ?></b><em>đ</em></span>
                                </label>
                            </p>
                        </div>
                        <div class="pull-right">
                            <div class="btnSubmit form-actions">
                                <input type="submit" id="edit-submit" name="op" value="Gửi đơn hàng" class="form-submit">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php } else {
            ?>
            <form class="don-hang-gio-hang-add-form" action="/gio-hang" method="post" id="don-hang-gio-hang-add-form" accept-charset="UTF-8">
                <div>
                    <span class="ti-gia" style="display:none;"><?php echo $setting['CNY'] ?></span>
                    <p class="cart-empty">
                        Không có sản phẩm nào trong giỏ hàng<br>
                        <a href="/dat-hang"><i class="fa fa-undo" aria-hidden="true"></i> Quay lại trang đặt hàng</a>
                    </p>
                </div>
            </form>
        <?php } ?>


        <div class="input-group">
                <input required="" class="form-control" placeholder="Tên shop" type="text" >
            </div>
</div>

